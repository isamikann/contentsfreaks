<?php
/**
 * YouTube Data API v3 チャンネル統計取得
 * 24時間キャッシュ付き、APIキー未設定時はフォールバック
 */

if (!defined('ABSPATH')) {
    exit;
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

    // ---- 2. 50件ずつ statistics を取得 ----
    $video_stats = array(); // video_id => view_count
    foreach (array_chunk(array_keys($video_items), 50) as $chunk) {
        $resp = wp_remote_get(
            add_query_arg(array(
                'part' => 'statistics',
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
        }
    }

    // ---- 3. WP投稿とエピソード番号でマッチング ----
    $episodes = get_posts(array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'meta_key'       => 'is_podcast_episode',
        'meta_value'     => '1',
        'fields'         => 'ids',
    ));

    // YouTube動画タイトルからエピソード番号を抽出してインデックス作成
    $yt_ep_index = array(); // ep_number(int) => video_id
    foreach ($video_items as $vid => $title) {
        $ep = contentfreaks_extract_episode_number_from_yt_title($title);
        if ($ep !== null && !isset($yt_ep_index[$ep])) {
            $yt_ep_index[$ep] = $vid;
        }
    }

    $synced  = 0;
    $skipped = 0;
    foreach ($episodes as $post_id) {
        $ep_number = (int) get_post_meta($post_id, 'episode_number', true);
        if (!$ep_number || !isset($yt_ep_index[$ep_number])) {
            $skipped++;
            continue;
        }
        $vid = $yt_ep_index[$ep_number];
        update_post_meta($post_id, 'episode_youtube_id',    $vid);
        update_post_meta($post_id, 'episode_youtube_views', $video_stats[$vid] ?? 0);
        $synced++;
    }

    return array('synced' => $synced, 'skipped' => $skipped, 'errors' => array());
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
