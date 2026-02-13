<?php
/**
 * RSS自動投稿関連の処理をまとめたファイル
 */

// RSSエピソードを投稿として自動作成（タグ抽出・サムネイル設定機能付き）
function contentfreaks_sync_rss_to_posts() {
    error_log('RSS自動同期開始');
    
    // RSS同期フラグを設定（ポッドキャストエピソード自動設定のため）
    if (!defined('CONTENTFREAKS_RSS_SYNC')) {
        define('CONTENTFREAKS_RSS_SYNC', true);
    }
    
    $episodes = contentfreaks_get_rss_episodes(0); // 全エピソード取得
    error_log('RSS取得完了: ' . count($episodes) . '件のエピソード');
    
    $synced_count = 0;
    $errors = array();
    // ポッドキャストカテゴリーを作成（存在しない場合）
    $podcast_category = get_category_by_slug('podcast');
    if (!$podcast_category) {
        $cat_id = wp_create_category('ポッドキャスト');
        $podcast_category = get_category($cat_id);
    }
    foreach ($episodes as $episode) {
        // 既存投稿チェック（GUIDまたは音声URLで重複確認）
        $existing_post = contentfreaks_find_existing_episode_post($episode);
        
        if (empty($existing_post)) {
            // 新規投稿作成
            $post_data = array(
                'post_title' => $episode['title'],
                'post_content' => $episode['full_description'],
                'post_excerpt' => $episode['description'],
                'post_status' => 'publish',
                'post_date' => $episode['pub_date'],
                'post_category' => array($podcast_category->term_id),
                'post_type' => 'post'
            );
            $post_id = wp_insert_post($post_data);
            if (!is_wp_error($post_id) && $post_id > 0) {
                // RSS同期関数からポッドキャストエピソード設定を呼び出し
                contentfreaks_mark_rss_posts_as_podcast($post_id);
                
                // カスタムフィールド保存
                update_post_meta($post_id, 'episode_audio_url', $episode['audio_url']);
                update_post_meta($post_id, 'episode_number', $episode['episode_number']);
                update_post_meta($post_id, 'episode_duration', $episode['duration']);
                update_post_meta($post_id, 'episode_original_url', $episode['link']);
                update_post_meta($post_id, 'episode_category', $episode['category']);
                update_post_meta($post_id, 'episode_guid', $episode['guid']); // GUID保存
                update_post_meta($post_id, 'episode_rss_hash', contentfreaks_generate_episode_hash($episode)); // 変更検知用ハッシュ
                // 画像URL保存（フォールバック用）
                if ($episode['thumbnail']) {
                    update_post_meta($post_id, 'episode_image_url', $episode['thumbnail']);
                    if (function_exists('contentfreaks_set_featured_image_from_url')) {
                        $image_result = contentfreaks_set_featured_image_from_url($post_id, $episode['thumbnail']);
                        if (!$image_result) {
                            error_log('サムネイル設定失敗: Post ID ' . $post_id . ', URL: ' . $episode['thumbnail']);
                        }
                    }
                } else {
                    error_log('サムネイルURLが見つかりません: Post ID ' . $post_id . ', Title: ' . $episode['title']);
                }
                // タイトルから『』内のテキストを抽出してタグ作成
                contentfreaks_extract_and_create_tags_from_title($post_id, $episode['title']);
                $synced_count++;
            } else {
                $errors[] = '投稿作成エラー: ' . $episode['title'];
            }
        } else {
            // 既存投稿の更新処理
            $existing_post_id = $existing_post[0]->ID;
            $updated = contentfreaks_update_existing_episode($existing_post_id, $episode);
            if ($updated) {
                $synced_count++;
            }
        }
    }
    // 同期結果を保存
    update_option('contentfreaks_last_sync_time', current_time('mysql'));
    update_option('contentfreaks_last_sync_count', $synced_count);
    update_option('contentfreaks_last_sync_errors', $errors);
    
    error_log('RSS自動同期完了: 同期数=' . $synced_count . ', エラー数=' . count($errors));
    
    return array(
        'synced' => $synced_count,
        'errors' => $errors
    );
}

// 既存エピソード投稿を検索
function contentfreaks_find_existing_episode_post($episode) {
    // 1. GUIDで検索
    if (!empty($episode['guid'])) {
        $posts = get_posts(array(
            'meta_key' => 'episode_guid',
            'meta_value' => $episode['guid'],
            'post_type' => 'post',
            'post_status' => array('publish', 'draft', 'private'),
            'numberposts' => 1
        ));
        if (!empty($posts)) {
            return $posts;
        }
    }
    
    // 2. 音声URLで検索
    if (!empty($episode['audio_url'])) {
        $posts = get_posts(array(
            'meta_key' => 'episode_audio_url',
            'meta_value' => $episode['audio_url'],
            'post_type' => 'post',
            'post_status' => array('publish', 'draft', 'private'),
            'numberposts' => 1
        ));
        if (!empty($posts)) {
            return $posts;
        }
    }
    
    // 3. タイトルで検索（フォールバック）
    $posts = get_posts(array(
        'title' => $episode['title'],
        'post_type' => 'post',
        'post_status' => array('publish', 'draft', 'private'),
        'numberposts' => 1
    ));
    
    return $posts;
}

// エピソードの変更検知用ハッシュ生成
function contentfreaks_generate_episode_hash($episode) {
    $hash_data = array(
        'title' => $episode['title'],
        'description' => $episode['description'],
        'full_description' => $episode['full_description'],
        'thumbnail' => $episode['thumbnail'],
        'duration' => $episode['duration'],
        'episode_number' => $episode['episode_number'],
        'category' => $episode['category'],
        'pub_date' => $episode['pub_date']
    );
    
    return md5(serialize($hash_data));
}

// 既存エピソードの更新処理
function contentfreaks_update_existing_episode($post_id, $episode) {
    $updated = false;
    
    // 現在のハッシュと比較
    $current_hash = get_post_meta($post_id, 'episode_rss_hash', true);
    $new_hash = contentfreaks_generate_episode_hash($episode);
    
    if ($current_hash === $new_hash) {
        // 変更がない場合は何もしない
        return false;
    }
    
    error_log('エピソードの更新を検出: Post ID ' . $post_id . ', Title: ' . $episode['title']);
    
    // 投稿データの更新
    $post_data = array(
        'ID' => $post_id,
        'post_title' => $episode['title'],
        'post_content' => $episode['full_description'],
        'post_excerpt' => $episode['description'],
        'post_date' => $episode['pub_date']
    );
    
    $current_post = get_post($post_id);
    $title_changed = ($current_post->post_title !== $episode['title']);
    $content_changed = ($current_post->post_content !== $episode['full_description']);
    $excerpt_changed = ($current_post->post_excerpt !== $episode['description']);
    
    $result = wp_update_post($post_data);
    if (!is_wp_error($result)) {
        $updated = true;
        
        // 変更内容をログに記録
        $changes = array();
        if ($title_changed) $changes[] = "タイトル: '{$current_post->post_title}' → '{$episode['title']}'";
        if ($content_changed) $changes[] = "コンテンツ更新";
        if ($excerpt_changed) $changes[] = "概要更新";
        
        if (!empty($changes)) {
            contentfreaks_log_episode_update($post_id, 'content_updated', implode(', ', $changes));
        }
    }
    
    // メタデータの更新
    $meta_updates = array(
        'episode_duration' => $episode['duration'],
        'episode_number' => $episode['episode_number'],
        'episode_original_url' => $episode['link'],
        'episode_category' => $episode['category'],
        'episode_guid' => $episode['guid'],
        'episode_rss_hash' => $new_hash
    );
    
    foreach ($meta_updates as $key => $value) {
        $current_value = get_post_meta($post_id, $key, true);
        if ($current_value !== $value) {
            update_post_meta($post_id, $key, $value);
            contentfreaks_log_episode_update($post_id, 'meta_updated', "{$key}: '{$current_value}' → '{$value}'");
            $updated = true;
        }
    }
    
    // 画像の更新（URLが変更された場合）
    if (contentfreaks_update_featured_image_if_changed($post_id, $episode['thumbnail'])) {
        $updated = true;
    }
    
    // タグの更新（タイトルが変更された場合）
    if ($title_changed) {
        // 既存のタグを削除
        wp_set_post_tags($post_id, '', false);
        
        // 新しいタイトルからタグを抽出
        contentfreaks_extract_and_create_tags_from_title($post_id, $episode['title']);
        contentfreaks_log_episode_update($post_id, 'tags_updated', "タグを再生成: '{$episode['title']}'");
        $updated = true;
    }
    
    if ($updated) {
        // 更新日時を記録
        update_post_meta($post_id, 'episode_last_updated', current_time('mysql'));
        contentfreaks_log_episode_update($post_id, 'sync_completed', "エピソード同期完了");
    }
    
    return $updated;
}

// アイキャッチ画像をURLから設定
function contentfreaks_set_featured_image_from_url($post_id, $image_url) {
    if (has_post_thumbnail($post_id)) {
        return true;
    }
    
    // 画像URLの有効性チェック
    if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
        error_log('無効な画像URL (Post ID: ' . $post_id . '): ' . $image_url);
        return false;
    }
    
    // HTTPSに変換（可能な場合）
    $image_url = str_replace('http://', 'https://', $image_url);
    
    // media_sideload_image関数を使用するために必要なファイルをインクルード
    if (!function_exists('media_sideload_image')) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }
    
    // タイムアウト設定を追加
    add_filter('http_request_timeout', function($timeout) {
        return 30; // 30秒のタイムアウト
    });
    
    $image_id = media_sideload_image($image_url, $post_id, null, 'id');
    
    // タイムアウトフィルターを削除
    remove_all_filters('http_request_timeout');
    
    if (!is_wp_error($image_id) && is_numeric($image_id)) {
        set_post_thumbnail($post_id, $image_id);
        error_log('サムネイル設定成功 (Post ID: ' . $post_id . ', Image ID: ' . $image_id . ')');
        return true;
    } else {
        // エラーログに記録
        $error_message = is_wp_error($image_id) ? $image_id->get_error_message() : 'Unknown error';
        error_log('サムネイル設定エラー (Post ID: ' . $post_id . ', URL: ' . $image_url . '): ' . $error_message);
        return false;
    }
}

// 更新詳細ログ機能
function contentfreaks_log_episode_update($post_id, $update_type, $details) {
    $log_entry = array(
        'post_id' => $post_id,
        'post_title' => get_the_title($post_id),
        'update_type' => $update_type,
        'details' => $details,
        'timestamp' => current_time('mysql')
    );
    
    // 既存のログを取得
    $existing_logs = get_option('contentfreaks_update_logs', array());
    
    // 新しいログを先頭に追加
    array_unshift($existing_logs, $log_entry);
    
    // 最大100件まで保持
    if (count($existing_logs) > 100) {
        $existing_logs = array_slice($existing_logs, 0, 100);
    }
    
    // ログを保存
    update_option('contentfreaks_update_logs', $existing_logs);
    
    // エラーログにも記録
    error_log("RSS更新: {$update_type} - Post ID: {$post_id} - {$details}");
}

// 画像更新の改良版
function contentfreaks_update_featured_image_if_changed($post_id, $new_image_url) {
    $current_image_url = get_post_meta($post_id, 'episode_image_url', true);
    
    if ($new_image_url && $current_image_url !== $new_image_url) {
        // 古い画像の削除
        $current_thumbnail_id = get_post_thumbnail_id($post_id);
        if ($current_thumbnail_id) {
            delete_post_thumbnail($post_id);
        }
        
        // 新しい画像の設定
        update_post_meta($post_id, 'episode_image_url', $new_image_url);
        
        if (function_exists('contentfreaks_set_featured_image_from_url')) {
            $image_result = contentfreaks_set_featured_image_from_url($post_id, $new_image_url);
            if ($image_result) {
                contentfreaks_log_episode_update($post_id, 'image_updated', "画像URL変更: {$current_image_url} → {$new_image_url}");
                return true;
            } else {
                contentfreaks_log_episode_update($post_id, 'image_update_failed', "画像更新失敗: {$new_image_url}");
                return false;
            }
        }
    }
    
    return false;
}

// 統一された管理画面はfunctions.phpに移動されました
