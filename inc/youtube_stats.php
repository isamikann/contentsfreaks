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

    $api_key    = defined('CONTENTFREAKS_YOUTUBE_API_KEY')    ? CONTENTFREAKS_YOUTUBE_API_KEY    : '';
    $channel_id = defined('CONTENTFREAKS_YOUTUBE_CHANNEL_ID') ? CONTENTFREAKS_YOUTUBE_CHANNEL_ID : '';

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
