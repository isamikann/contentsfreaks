<?php
/**
 * YouTube Data API v3 チャンネル統計取得
 * 24時間キャッシュ付き、APIキー未設定時はフォールバック
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * RSS同期後に呼ばれる: YouTube未紐付けの投稿だけを対象に紐付けを試みる
 * contentfreaks_hourly_sync フックから実行
 */
function contentfreaks_auto_link_new_episodes() {
    // 未紐付けのエピソードのみ対象
    $unlinked = get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array('key' => 'is_podcast_episode', 'value' => '1'),
            array('key' => 'episode_youtube_id',  'compare' => 'NOT EXISTS'),
        ),
        'fields' => 'ids',
    ));
    if (empty($unlinked)) {
        return;
    }
    contentfreaks_queue_youtube_sync_job('auto');
}

/**
 * YouTube紐付けジョブを非同期実行用にキューへ積む
 *
 * @param  string $reason
 * @return bool
 */
function contentfreaks_queue_youtube_sync_job($reason = 'manual') {
    if (wp_next_scheduled('contentfreaks_run_youtube_sync_job')) {
        return false;
    }

    update_option('contentfreaks_youtube_sync_job_pending', array(
        'reason'  => $reason,
        'queued'  => current_time('mysql'),
    ), false);

    wp_schedule_single_event(time() + 30, 'contentfreaks_run_youtube_sync_job');
    return true;
}

/**
 * YouTube紐付けジョブの現在状態を返す
 *
 * @return array
 */
function contentfreaks_get_youtube_sync_job_status() {
    $pending = get_option('contentfreaks_youtube_sync_job_pending', array());
    $last    = get_option('contentfreaks_youtube_sync_job_last', array());
    $next    = wp_next_scheduled('contentfreaks_run_youtube_sync_job');

    $status = 'idle';
    if (!empty($pending)) {
        $status = $next ? 'queued' : 'stale';
    }
    if (!empty($last['completed']) && empty($pending)) {
        $status = 'done';
    }

    return array(
        'status'   => $status,
        'pending'  => $pending,
        'last'     => $last,
        'next_run' => $next,
    );
}

/**
 * YouTube紐付けジョブ本体（WP-Cronで実行）
 */
function contentfreaks_run_youtube_sync_job() {
    $result = contentfreaks_sync_youtube_video_ids();
    update_option('contentfreaks_youtube_sync_job_last', array(
        'completed' => current_time('mysql'),
        'result'    => $result,
    ), false);
    delete_option('contentfreaks_youtube_sync_job_pending');

    if (!empty($result['synced'])) {
        error_log('YouTube自動紐付け: ' . $result['synced'] . '件紐付け完了');
    }
}
add_action('contentfreaks_run_youtube_sync_job', 'contentfreaks_run_youtube_sync_job');

/**
 * 紐付け済み投稿の再生数を一括更新（1日1回スケジュール実行）
 * APIコスト: 紐付け件数÷50 ユニット（147件なら3ユニット）
 */
function contentfreaks_refresh_youtube_views() {
    $linked = get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'meta_key'       => 'episode_youtube_id',
        'fields'         => 'ids',
    ));
    if (empty($linked)) {
        return;
    }

    $api_key = (defined('CONTENTFREAKS_YOUTUBE_API_KEY') && CONTENTFREAKS_YOUTUBE_API_KEY !== '')
                ? CONTENTFREAKS_YOUTUBE_API_KEY
                : get_option('contentfreaks_youtube_api_key', '');
    if (empty($api_key)) {
        return;
    }

    // post_id => video_id のマップを作成
    $id_map = array();
    foreach ($linked as $post_id) {
        $vid = get_post_meta($post_id, 'episode_youtube_id', true);
        if ($vid) {
            $id_map[$post_id] = $vid;
        }
    }

    // 50件ずつAPIを叩いて再生数を取得・更新
    $chunks = array_chunk($id_map, 50, true);
    $updated = 0;
    foreach ($chunks as $chunk) {
        $resp = wp_remote_get(
            add_query_arg(array(
                'part' => 'statistics',
                'id'   => implode(',', array_values($chunk)),
                'key'  => $api_key,
            ), 'https://www.googleapis.com/youtube/v3/videos'),
            array('timeout' => 15)
        );
        if (is_wp_error($resp) || wp_remote_retrieve_response_code($resp) !== 200) {
            continue;
        }
        $data = json_decode(wp_remote_retrieve_body($resp), true);
        // video_id => view_count のマップ
        $stats = array();
        foreach ($data['items'] as $item) {
            $stats[$item['id']] = (int) ($item['statistics']['viewCount'] ?? 0);
        }
        foreach ($chunk as $post_id => $vid) {
            if (isset($stats[$vid])) {
                update_post_meta($post_id, 'episode_youtube_views', $stats[$vid]);
                $updated++;
            }
        }
    }
    error_log('YouTube再生数更新: ' . $updated . '件完了');
}

/**
 * チャンネル統計をAPIから取得（24時間トランジェントキャッシュ）
 *
 * @return array|false { subscriber_count, view_count, video_count } or false on failure
 */
function contentfreaks_get_youtube_channel_stats() {
    $cache_key = 'contentfreaks_youtube_stats';
    $cached    = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    // 定数優先、未設定の場合はDBのオプションを使用
    $api_key    = (defined('CONTENTFREAKS_YOUTUBE_API_KEY')    && CONTENTFREAKS_YOUTUBE_API_KEY    !== '')
                    ? CONTENTFREAKS_YOUTUBE_API_KEY
                    : get_option('contentfreaks_youtube_api_key', '');
    $channel_id = (defined('CONTENTFREAKS_YOUTUBE_CHANNEL_ID') && CONTENTFREAKS_YOUTUBE_CHANNEL_ID !== '')
                    ? CONTENTFREAKS_YOUTUBE_CHANNEL_ID
                    : get_option('contentfreaks_youtube_channel_id', '');

    if (empty($api_key) || empty($channel_id)) {
        return false;
    }

    $url = add_query_arg(
        array(
            'part' => 'statistics',
            'id'   => $channel_id,
            'key'  => $api_key,
        ),
        'https://www.googleapis.com/youtube/v3/channels'
    );

    $response = wp_remote_get(
        $url,
        array(
            'timeout'    => 10,
            'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
        )
    );

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        error_log('YouTube Stats API Error: ' . (is_wp_error($response) ? $response->get_error_message() : wp_remote_retrieve_response_code($response)));
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['items'][0]['statistics'])) {
        error_log('YouTube Stats API: チャンネルが見つかりません。チャンネルIDを確認してください。');
        return false;
    }

    $stats  = $body['items'][0]['statistics'];
    $result = array(
        'subscriber_count' => isset($stats['subscriberCount']) ? (int) $stats['subscriberCount'] : 0,
        'view_count'       => isset($stats['viewCount'])       ? (int) $stats['viewCount']       : 0,
        'video_count'      => isset($stats['videoCount'])      ? (int) $stats['videoCount']      : 0,
    );

    set_transient($cache_key, $result, DAY_IN_SECONDS);

    return $result;
}

/**
 * 数値を日本語の短縮表記に変換
 * 例: 12345 → 1.2万、456789 → 45.7万
 *
 * @param  int    $number
 * @return string
 */
function contentfreaks_format_yt_number($number) {
    if ($number >= 100000000) {
        return number_format($number / 100000000, 1) . '億';
    }
    if ($number >= 10000) {
        $man = $number / 10000;
        return ($man >= 100 ? number_format($man, 0) : number_format($man, 1)) . '万';
    }
    return number_format($number);
}

/**
 * YouTube統計キャッシュを手動クリア（管理画面や開発時に利用）
 */
function contentfreaks_clear_youtube_stats_cache() {
    delete_transient('contentfreaks_youtube_stats');
}

/**
 * チャンネルの全動画を取得し、WP投稿の episode_youtube_id / episode_youtube_views を更新
 * アップロードプレイリスト経由（Search APIより低コスト: 約 4〜6 ユニット）
 *
 * @return array { synced: int, skipped: int, errors: string[] }
 */
function contentfreaks_sync_youtube_video_ids() {
    $api_key    = (defined('CONTENTFREAKS_YOUTUBE_API_KEY')    && CONTENTFREAKS_YOUTUBE_API_KEY    !== '')
                    ? CONTENTFREAKS_YOUTUBE_API_KEY
                    : get_option('contentfreaks_youtube_api_key', '');
    $channel_id = (defined('CONTENTFREAKS_YOUTUBE_CHANNEL_ID') && CONTENTFREAKS_YOUTUBE_CHANNEL_ID !== '')
                    ? CONTENTFREAKS_YOUTUBE_CHANNEL_ID
                    : get_option('contentfreaks_youtube_channel_id', '');

    if (empty($api_key) || empty($channel_id)) {
        return array('synced' => 0, 'skipped' => 0, 'errors' => array('APIキーまたはチャンネルIDが設定されていません。'));
    }

    // uploads プレイリストID = UC→UU
    $playlist_id = 'UU' . substr($channel_id, 2);

    // ---- 1. プレイリストから動画ID・タイトルを収集 ----
    $video_items = array();
    $page_token  = '';
    do {
        $params = array(
            'part'       => 'snippet',
            'playlistId' => $playlist_id,
            'maxResults' => 50,
            'key'        => $api_key,
        );
        if ($page_token) {
            $params['pageToken'] = $page_token;
        }
        $resp = wp_remote_get(
            add_query_arg($params, 'https://www.googleapis.com/youtube/v3/playlistItems'),
            array('timeout' => 15)
        );
        if (is_wp_error($resp) || wp_remote_retrieve_response_code($resp) !== 200) {
            return array('synced' => 0, 'skipped' => 0, 'errors' => array('プレイリスト取得失敗: ' . (is_wp_error($resp) ? $resp->get_error_message() : wp_remote_retrieve_response_code($resp))));
        }
        $data = json_decode(wp_remote_retrieve_body($resp), true);
        foreach ($data['items'] as $item) {
            $vid = $item['snippet']['resourceId']['videoId'] ?? '';
            if ($vid) {
                $video_items[$vid] = $item['snippet']['title'];
            }
        }
        $page_token = $data['nextPageToken'] ?? '';
    } while ($page_token);

    if (empty($video_items)) {
        return array('synced' => 0, 'skipped' => 0, 'errors' => array('動画が見つかりませんでした。'));
    }

    // ---- 2. 50件ずつ snippet + statistics を取得 ----
    $video_stats = array(); // video_id => view_count
    $video_thumbnails = array(); // video_id => thumbnail_url
    foreach (array_chunk(array_keys($video_items), 50) as $chunk) {
        $resp = wp_remote_get(
            add_query_arg(array(
                'part' => 'snippet,statistics',
                'id'   => implode(',', $chunk),
                'key'  => $api_key,
            ), 'https://www.googleapis.com/youtube/v3/videos'),
            array('timeout' => 15)
        );
        if (is_wp_error($resp) || wp_remote_retrieve_response_code($resp) !== 200) {
            continue;
        }
        $data = json_decode(wp_remote_retrieve_body($resp), true);
        foreach ($data['items'] as $item) {
            $video_stats[$item['id']] = (int) ($item['statistics']['viewCount'] ?? 0);
            $video_thumbnails[$item['id']] = contentfreaks_pick_youtube_thumbnail_url($item['snippet']['thumbnails'] ?? array());
        }
    }

    // ---- 3. WP投稿タイトルとYouTubeタイトルを「作品名::話数 / ::final / ::title」キーでマッチング ----
    $episodes = get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'meta_key'       => 'is_podcast_episode',
        'meta_value'     => '1',
        'fields'         => 'ids',
    ));

    // YouTube: マッチキー（話数 / 最終回 / タイトル）をインデックス化
    $yt_index = array(); // "作品名::..." => video_id
    foreach ($video_items as $vid => $title) {
        $key = contentfreaks_make_title_episode_key($title);
        if ($key && !isset($yt_index[$key])) {
            $yt_index[$key] = $vid;
        }
    }

    $synced  = 0;
    $skipped = 0;
    foreach ($episodes as $post_id) {
        $post_title = get_the_title($post_id);
        $key        = contentfreaks_make_title_episode_key($post_title);
        if (!$key || !isset($yt_index[$key])) {
            $skipped++;
            continue;
        }
        $vid = $yt_index[$key];
        update_post_meta($post_id, 'episode_youtube_id',    $vid);
        update_post_meta($post_id, 'episode_youtube_views', $video_stats[$vid] ?? 0);

        $youtube_thumbnail = $video_thumbnails[$vid] ?? 'https://i.ytimg.com/vi/' . $vid . '/hqdefault.jpg';
        update_post_meta($post_id, 'episode_image_url', $youtube_thumbnail);

        if (function_exists('contentfreaks_set_featured_image_from_url')) {
            contentfreaks_set_featured_image_from_url($post_id, $youtube_thumbnail, true);
        }
        $synced++;
    }

    return array('synced' => $synced, 'skipped' => $skipped, 'errors' => array());
}

/**
 * タイトルから「作品名::話数」のマッチングキーを生成
 * 例: 「『リブート』8話感想考察」→ "リブート::8"
 * 例: 「『再会』第7話」→ "再会::7"
 *
 * @param  string      $title
 * @return string|null
 */
function contentfreaks_make_title_episode_key($title) {
    $title = trim(wp_strip_all_tags($title));

    // 全角/半角の記号ゆれをある程度吸収
    $normalized = str_replace(array('　', '〜', '～', '‐', '－', '―', '—', '–', '—'), array(' ', '-', '-', '-', '-', '-', '-', '-', '-'), $title);

    // 作品名を抽出: 『』「」【】 の順に試す
    $work = '';
    if (preg_match('/[『「【]([^』」】\s]{1,40})[』」】]/u', $normalized, $m)) {
        $work = trim($m[1]);
    }
    if (empty($work) && preg_match('/^(.{1,30}?)(?:\s*[-|｜|\||:：]\s*|\s+)?(?:最終回|最終話|ラスト回|finale?|final)(?:\b|$)/iu', $normalized, $m)) {
        $work = trim($m[1]);
    }
    if (empty($work) && preg_match('/^(.{1,30}?)(?:\s*[-|｜|\||:：]\s*|\s+)?(?:第?\s*\d+\s*[回話]?|\d+話|EP\.?\s*\d+|#\d+|【\d+】|\[\d+\])/iu', $normalized, $m)) {
        $work = trim($m[1]);
    }
    if (empty($work) && preg_match('/^(.{1,40}?)(?:\s*[-|｜|\||:：]\s*|\s+)?(?:感想|レビュー|考察|解説|雑談|まとめ|語り|感想回|考察回|Episode)(?:\b|$)/iu', $normalized, $m)) {
        $work = trim($m[1]);
    }
    if (empty($work) && mb_strlen($normalized) <= 40 && !preg_match('/[。！？!?]/u', $normalized)) {
        $work = $normalized;
    }
    if (empty($work)) {
        return null;
    }

    $work = preg_replace('/[\s\p{P}\p{S}]+/u', '', $work);

    // 最終回・最終話系は番号ではなく final キーに寄せて一致させる
    if (preg_match('/(?:最終回|最終話|ラスト回|finale?|final)/iu', $title)) {
        return $work . '::final';
    }

    // 話数を抽出
    $ep = null;
    $patterns = array(
        '/第(\d+)[回話]/u',   // 第7話, 第7回
        '/(\d+)話/u',         // 7話, 8話
        '/\bep\.?\s*(\d+)/i', // EP.12
        '/#(\d+)/',           // #12
        '/【(\d+)】/',         // 【12】
        '/\[(\d+)\]/',        // [12]
        '/(?:^|\s)(\d+)(?:\s*[話回])?/u', // 1話, 1回, 単独の1
    );
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $title, $m)) {
            $ep = (int) $m[1];
            break;
        }
    }
    if ($ep === null) {
        return $work . '::title';
    }

    return $work . '::' . $ep;
}

/**
 * YouTubeタイトルからエピソード番号を抽出
 * 対応形式: EP.12 / Ep12 / #12 / 第12回 / 第12話 / 12話 / 【12】/ [12]
 *
 * @param  string   $title
 * @return int|null
 */
function contentfreaks_extract_episode_number_from_yt_title($title) {
    $patterns = array(
        '/\bep\.?\s*(\d+)/i',   // EP.12, ep12
        '/#(\d+)/',              // #12
        '/第(\d+)[回話]/u',      // 第12回, 第12話
        '/(\d+)話/u',            // 12話（「第」なし）
        '/【(\d+)】/',           // 【12】
        '/\[(\d+)\]/',           // [12]
    );
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $title, $m)) {
            return (int) $m[1];
        }
    }
    return null;
}

/**
 * YouTube API の thumbnails から使える画像URLを優先順で選ぶ
 * maxres -> standard -> high -> medium -> default の順
 *
 * @param  array $thumbnails
 * @return string
 */
function contentfreaks_pick_youtube_thumbnail_url($thumbnails) {
    $priority = array('maxres', 'standard', 'high', 'medium', 'default');
    foreach ($priority as $size) {
        if (!empty($thumbnails[$size]['url'])) {
            return $thumbnails[$size]['url'];
        }
    }

    return '';
}
