<?php
/**
 * Gemini AI 音声文字起こし・記事生成機能
 * Google Gemini 2.0 Flash Lite (無料枠) を使用してポッドキャスト音声を文字起こしし、ブログ記事化します。
 *
 * 使い方:
 *   wp-config.php に以下を追加してください（Git に含まれません）:
 *   define('CONTENTFREAKS_GEMINI_API_KEY', 'AIzaSy...');
 *
 *   または管理画面 → 設定タブから入力できます。
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ============================================================
// API キー取得
// ============================================================

function contentfreaks_get_gemini_api_key() {
    if ( defined( 'CONTENTFREAKS_GEMINI_API_KEY' ) && CONTENTFREAKS_GEMINI_API_KEY !== '' ) {
        return CONTENTFREAKS_GEMINI_API_KEY;
    }
    return get_option( 'contentfreaks_gemini_api_key', '' );
}

// ============================================================
// Gemini Files API — 音声ファイルのアップロード
// ============================================================

/**
 * 音声ファイルを URL からダウンロードし Gemini Files API にアップロードします。
 *
 * @param string $audio_url 音声ファイルの URL
 * @return array|WP_Error  成功時: array( 'uri', 'name', 'mime_type' ) / 失敗時: WP_Error
 */
function contentfreaks_gemini_upload_audio( $audio_url ) {
    $api_key = contentfreaks_get_gemini_api_key();
    if ( empty( $api_key ) ) {
        return new WP_Error( 'no_api_key', 'Gemini API Key が設定されていません。管理画面 → 設定 から入力してください。' );
    }

    // Anchor.fm / CloudFront は Referer なしを 403 で弾く
    $response = wp_remote_get( $audio_url, array(
        'timeout'     => 120,
        'sslverify'   => true,
        'redirection' => 10,
        'headers'     => array(
            'User-Agent' => 'Mozilla/5.0 (compatible; ContentFreaks-Bot/1.0; +https://contentsfreaks.com)',
            'Referer'    => 'https://anchor.fm/',
            'Accept'     => 'audio/mpeg, audio/*;q=0.9, */*;q=0.8',
        ),
    ) );

    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'download_failed', '音声ファイルのダウンロード失敗: ' . $response->get_error_message() );
    }

    $http_code = wp_remote_retrieve_response_code( $response );

    // 403 の場合は Spotify Referer でリトライ
    if ( $http_code === 403 ) {
        error_log( "Gemini: 403 → Spotify Referer でリトライ URL={$audio_url}" );
        $response = wp_remote_get( $audio_url, array(
            'timeout'     => 120,
            'sslverify'   => true,
            'redirection' => 10,
            'headers'     => array(
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
                'Referer'    => 'https://open.spotify.com/',
                'Accept'     => 'audio/mpeg, audio/*;q=0.9, */*;q=0.8',
            ),
        ) );
        if ( ! is_wp_error( $response ) ) {
            $http_code = wp_remote_retrieve_response_code( $response );
        }
    }

    if ( $http_code === 403 ) {
        return new WP_Error( 'download_http_error', "音声ファイルへのアクセスが拒否されました (HTTP 403)。Anchor.fm の配信 URL が期限切れか認証が必要な可能性があります。RSS 再同期後にリトライしてください。" );
    }

    if ( $http_code !== 200 ) {
        return new WP_Error( 'download_http_error', "音声ファイルの取得に失敗しました (HTTP {$http_code})" );
    }

    $audio_data = wp_remote_retrieve_body( $response );
    $file_size  = strlen( $audio_data );

    // 150 MB を超えるファイルはスキップ
    if ( $file_size > 150 * 1024 * 1024 ) {
        return new WP_Error(
            'file_too_large',
            sprintf( 'ファイルサイズが大きすぎます (%.1f MB)。150 MB 以下のエピソードのみ処理できます。', $file_size / 1024 / 1024 )
        );
    }

    // MIME タイプを URL 拡張子から判断
    $ext     = strtolower( pathinfo( wp_parse_url( $audio_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
    $ext_map = array(
        'mp3'  => 'audio/mpeg',
        'wav'  => 'audio/wav',
        'ogg'  => 'audio/ogg',
        'aac'  => 'audio/aac',
        'flac' => 'audio/flac',
        'm4a'  => 'audio/mp4',
        'mp4'  => 'audio/mp4',
    );
    $mime_type = $ext_map[ $ext ] ?? 'audio/mpeg';

    $ct = trim( wp_remote_retrieve_header( $response, 'content-type' ) );
    if ( $ct && strpos( $ct, 'audio/' ) === 0 ) {
        $mime_type = trim( explode( ';', $ct )[0] );
    }

    error_log( sprintf( 'Gemini upload: %.1f MB, %s', $file_size / 1024 / 1024, $mime_type ) );

    // multipart/related ボディを構築
    $boundary  = 'gemini_' . bin2hex( random_bytes( 8 ) );
    $file_name = basename( wp_parse_url( $audio_url, PHP_URL_PATH ) ) ?: 'episode.mp3';
    $meta_json = wp_json_encode( array( 'file' => array( 'display_name' => $file_name ) ) );

    $body  = "--{$boundary}\r\n";
    $body .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
    $body .= $meta_json . "\r\n";
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: {$mime_type}\r\n\r\n";
    $body .= $audio_data . "\r\n";
    $body .= "--{$boundary}--";

    unset( $audio_data );

    $upload_url = 'https://generativelanguage.googleapis.com/upload/v1beta/files'
                . '?uploadType=multipart&key=' . rawurlencode( $api_key );

    $upload_resp = wp_remote_post( $upload_url, array(
        'timeout'   => 180,
        'headers'   => array(
            'Content-Type'           => "multipart/related; boundary={$boundary}",
            'X-Goog-Upload-Protocol' => 'multipart',
        ),
        'body'      => $body,
        'sslverify' => true,
    ) );

    unset( $body );

    if ( is_wp_error( $upload_resp ) ) {
        return new WP_Error( 'upload_failed', 'Files API アップロード失敗: ' . $upload_resp->get_error_message() );
    }

    $up_code = wp_remote_retrieve_response_code( $upload_resp );
    $up_body = wp_remote_retrieve_body( $upload_resp );
    $up_data = json_decode( $up_body, true );

    if ( $up_code !== 200 || empty( $up_data['file']['uri'] ) ) {
        $err = isset( $up_data['error']['message'] ) ? $up_data['error']['message'] : $up_body;
        return new WP_Error( 'upload_api_error', "Files API エラー (HTTP {$up_code}): {$err}" );
    }

    return array(
        'uri'       => $up_data['file']['uri'],
        'name'      => $up_data['file']['name'],
        'mime_type' => $mime_type,
    );
}

// ============================================================
// Gemini generateContent API — 音声から記事生成
// ============================================================

/**
 * Gemini 2.0 Flash Lite に音声ファイルと指示を送り、ブログ記事を生成します。
 *
 * @param string $file_uri        Files API から取得した URI
 * @param string $mime_type       音声の MIME タイプ
 * @param string $episode_title   エピソードタイトル
 * @param string $episode_description RSS の概要テキスト
 * @return array|WP_Error
 */
function contentfreaks_gemini_generate_article( $file_uri, $mime_type, $episode_title, $episode_description ) {
    $api_key = contentfreaks_get_gemini_api_key();
    if ( empty( $api_key ) ) {
        return new WP_Error( 'no_api_key', 'Gemini API Key が設定されていません。' );
    }

    $safe_title = wp_strip_all_tags( $episode_title );
    $safe_desc  = wp_strip_all_tags( wp_trim_words( $episode_description, 200, '' ) );

    $prompt = <<<PROMPT
このポッドキャスト音声を文字起こしして、読者向けのブログ記事として整形してください。

エピソードタイトル: {$safe_title}
RSS概要: {$safe_desc}

必ず以下の JSON 形式だけで返してください（前後に余計なテキスト・コードブロック不要）:
{
  "transcription": "音声の全文テキスト（話し言葉のまま）",
  "article_title": "SEOを意識した日本語ブログ記事タイトル（30〜60文字）",
  "article_body": "<h2>導入見出し</h2>\n<p>本文...</p>\n<h2>...</h2>\n<p>...</p>\n<h2>まとめ</h2>\n<p>...</p> という HTML 形式。話し言葉を書き言葉に変換すること。",
  "summary": "meta description 用の日本語概要（100〜150文字）",
  "tags": ["タグ1", "タグ2", "タグ3", "タグ4", "タグ5"]
}
PROMPT;

    $request_body = array(
        'contents'         => array(
            array(
                'parts' => array(
                    array(
                        'file_data' => array(
                            'mime_type' => $mime_type,
                            'file_uri'  => $file_uri,
                        ),
                    ),
                    array( 'text' => $prompt ),
                ),
            ),
        ),
        'generationConfig' => array(
            'temperature'      => 0.3,
            'maxOutputTokens'  => 8192,
            'responseMimeType' => 'application/json',
        ),
    );

    $api_url  = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent'
              . '?key=' . rawurlencode( $api_key );

    $response = wp_remote_post( $api_url, array(
        'timeout'   => 300,
        'headers'   => array( 'Content-Type' => 'application/json' ),
        'body'      => wp_json_encode( $request_body ),
        'sslverify' => true,
    ) );

    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'generate_failed', 'Gemini API 呼び出し失敗: ' . $response->get_error_message() );
    }

    $http_code = wp_remote_retrieve_response_code( $response );
    $resp_body = wp_remote_retrieve_body( $response );
    $data      = json_decode( $resp_body, true );

    // 429 レート制限
    if ( $http_code === 429 ) {
        $err = isset( $data['error']['message'] ) ? $data['error']['message'] : $resp_body;
        $retry_sec = 60;
        if ( preg_match( '/retry in ([0-9.]+)s/i', $err, $m ) ) {
            $retry_sec = (int) ceil( (float) $m[1] );
        }
        return new WP_Error( 'rate_limit', "レート制限です。{$retry_sec}秒後に自動リトライされます。(429)" );
    }

    if ( $http_code !== 200 ) {
        $err = isset( $data['error']['message'] ) ? $data['error']['message'] : $resp_body;
        return new WP_Error( 'generate_api_error', "Gemini API エラー (HTTP {$http_code}): {$err}" );
    }

    $generated_text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    if ( empty( $generated_text ) ) {
        return new WP_Error( 'empty_response', 'Gemini API から空のレスポンスが返されました。' );
    }

    // コードブロック除去
    $generated_text = preg_replace( '/^```(?:json)?\s*/i', '', trim( $generated_text ) );
    $generated_text = preg_replace( '/\s*```$/', '', $generated_text );

    $article_data = json_decode( $generated_text, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        if ( preg_match( '/\{.*\}/s', $generated_text, $m ) ) {
            $article_data = json_decode( $m[0], true );
        }
    }

    if ( json_last_error() !== JSON_ERROR_NONE || empty( $article_data ) ) {
        return new WP_Error( 'json_parse_error', 'JSON パース失敗: ' . substr( $generated_text, 0, 200 ) );
    }

    return $article_data;
}

// ============================================================
// Gemini Files API — ファイル削除（容量節約）
// ============================================================

function contentfreaks_gemini_delete_file( $file_name ) {
    if ( empty( $file_name ) ) {
        return false;
    }
    $api_key = contentfreaks_get_gemini_api_key();
    if ( empty( $api_key ) ) {
        return false;
    }
    wp_remote_request(
        'https://generativelanguage.googleapis.com/v1beta/' . $file_name . '?key=' . rawurlencode( $api_key ),
        array(
            'method'    => 'DELETE',
            'timeout'   => 30,
            'sslverify' => true,
        )
    );
    return true;
}

// ============================================================
// メインオーケストレーター — 1 エピソード処理
// ============================================================

function contentfreaks_generate_episode_article( $post_id ) {
    $audio_url = get_post_meta( $post_id, 'episode_audio_url', true );
    if ( empty( $audio_url ) ) {
        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', '音声 URL が見つかりません' );
        error_log( "Gemini: 音声URL不明 Post ID={$post_id}" );
        return false;
    }

    // URL 修正（ダブルエンコード解消）
    if ( function_exists( 'contentfreaks_fix_audio_url' ) ) {
        $audio_url = contentfreaks_fix_audio_url( $audio_url );
    }

    if ( empty( $audio_url ) ) {
        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', '音声 URL の修正後に空になりました' );
        return false;
    }

    // 処理中フラグ
    update_post_meta( $post_id, 'episode_ai_status', 'processing' );
    update_post_meta( $post_id, 'episode_ai_started_at', current_time( 'mysql' ) );
    delete_post_meta( $post_id, 'episode_ai_error' );

    $post        = get_post( $post_id );
    $title       = $post->post_title;
    $description = $post->post_excerpt ?: wp_strip_all_tags( $post->post_content );

    // Step 1: 音声アップロード
    error_log( "Gemini: アップロード開始 Post ID={$post_id} URL={$audio_url}" );
    $upload = contentfreaks_gemini_upload_audio( $audio_url );

    if ( is_wp_error( $upload ) ) {
        $msg = $upload->get_error_message();
        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', $msg );
        error_log( "Gemini: アップロードエラー Post ID={$post_id}: {$msg}" );
        return false;
    }

    error_log( "Gemini: 記事生成開始 Post ID={$post_id}" );

    // Step 2: 記事生成
    $article_data = contentfreaks_gemini_generate_article(
        $upload['uri'],
        $upload['mime_type'],
        $title,
        $description
    );

    // Step 3: ファイル削除（成否問わず実行）
    contentfreaks_gemini_delete_file( $upload['name'] );

    if ( is_wp_error( $article_data ) ) {
        $msg  = $article_data->get_error_message();
        $code = $article_data->get_error_code();

        // 429: AJAXではerrorとして表示、Cronではpendingに戻す
        if ( $code === 'rate_limit' ) {
            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                update_post_meta( $post_id, 'episode_ai_status', 'error' );
                update_post_meta( $post_id, 'episode_ai_error', $msg );
            } else {
                update_post_meta( $post_id, 'episode_ai_status', 'pending' );
            }
            error_log( "Gemini: レート制限 Post ID={$post_id}: {$msg}" );
            return false;
        }

        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', $msg );
        error_log( "Gemini: 生成エラー Post ID={$post_id}: {$msg}" );
        return false;
    }

    // Step 4: 投稿を更新
    $article_body  = $article_data['article_body']  ?? '';
    $article_title = $article_data['article_title'] ?? '';
    $summary       = $article_data['summary']       ?? '';
    $tags          = $article_data['tags']          ?? array();
    $transcription = $article_data['transcription'] ?? '';

    if ( empty( $article_body ) ) {
        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', 'article_body が空でした' );
        return false;
    }

    $update = array(
        'ID'           => $post_id,
        'post_content' => wp_kses_post( $article_body ),
    );

    if ( ! empty( $article_title ) && $article_title !== $post->post_title ) {
        $update['post_title'] = sanitize_text_field( $article_title );
    }

    if ( ! empty( $summary ) && empty( trim( $post->post_excerpt ) ) ) {
        $update['post_excerpt'] = sanitize_textarea_field( $summary );
    }

    wp_update_post( $update );

    if ( ! empty( $tags ) && is_array( $tags ) ) {
        wp_add_post_tags( $post_id, array_map( 'sanitize_text_field', $tags ) );
    }

    update_post_meta( $post_id, 'episode_ai_status', 'done' );
    update_post_meta( $post_id, 'episode_ai_generated_at', current_time( 'mysql' ) );
    update_post_meta( $post_id, 'episode_ai_transcription', sanitize_textarea_field( $transcription ) );
    update_post_meta( $post_id, 'episode_ai_summary', sanitize_textarea_field( $summary ) );

    error_log( "Gemini: 完了 Post ID={$post_id}" );
    return true;
}

// ============================================================
// Cron バッチ処理
// ============================================================

function contentfreaks_gemini_is_paused() {
    return (bool) get_option( 'contentfreaks_gemini_paused', false );
}

function contentfreaks_process_pending_transcriptions() {
    if ( empty( contentfreaks_get_gemini_api_key() ) ) {
        return;
    }

    // 一時停止中はスキップ
    if ( contentfreaks_gemini_is_paused() ) {
        return;
    }

    // 10 分以上 processing のままの投稿をリセット
    global $wpdb;
    $stuck_ids = $wpdb->get_col( $wpdb->prepare(
        "SELECT pm.post_id
           FROM {$wpdb->postmeta} pm
           INNER JOIN {$wpdb->postmeta} pm2
             ON pm.post_id = pm2.post_id
            AND pm2.meta_key = 'episode_ai_started_at'
            AND pm2.meta_value < %s
          WHERE pm.meta_key = 'episode_ai_status'
            AND pm.meta_value = 'processing'",
        gmdate( 'Y-m-d H:i:s', time() - 600 )
    ) );

    foreach ( $stuck_ids as $stuck_id ) {
        update_post_meta( (int) $stuck_id, 'episode_ai_status', 'pending' );
        error_log( 'Gemini: スタックリセット Post ID=' . $stuck_id );
    }

    $posts = get_posts( array(
        'post_type'   => 'post',
        'post_status' => array( 'publish', 'draft' ),
        'meta_key'    => 'episode_ai_status',
        'meta_value'  => 'pending',
        'orderby'     => 'date',
        'order'       => 'DESC',
        'numberposts' => 1,
    ) );

    if ( empty( $posts ) ) {
        return;
    }

    contentfreaks_generate_episode_article( $posts[0]->ID );
}

// ============================================================
// 統計情報・ユーティリティ
// ============================================================

function contentfreaks_get_ai_stats() {
    global $wpdb;

    $counts = array(
        'pending'     => 0,
        'processing'  => 0,
        'done'        => 0,
        'error'       => 0,
        'unprocessed' => 0,
    );

    $rows = $wpdb->get_results(
        "SELECT meta_value, COUNT(*) AS cnt
           FROM {$wpdb->postmeta}
          WHERE meta_key = 'episode_ai_status'
          GROUP BY meta_value"
    );

    foreach ( $rows as $row ) {
        if ( array_key_exists( $row->meta_value, $counts ) ) {
            $counts[ $row->meta_value ] = (int) $row->cnt;
        }
    }

    $counts['unprocessed'] = (int) $wpdb->get_var(
        "SELECT COUNT(p.ID)
           FROM {$wpdb->posts} p
           INNER JOIN {$wpdb->postmeta} pm
             ON p.ID = pm.post_id
            AND pm.meta_key = 'is_podcast_episode'
            AND pm.meta_value = '1'
           LEFT JOIN {$wpdb->postmeta} ai
             ON p.ID = ai.post_id
            AND ai.meta_key = 'episode_ai_status'
          WHERE p.post_type = 'post'
            AND p.post_status IN ('publish', 'draft')
            AND ai.meta_value IS NULL"
    );

    return $counts;
}

function contentfreaks_queue_all_episodes_for_transcription() {
    $posts = get_posts( array(
        'post_type'   => 'post',
        'post_status' => array( 'publish', 'draft' ),
        'meta_key'    => 'is_podcast_episode',
        'meta_value'  => '1',
        'numberposts' => -1,
        'fields'      => 'ids',
    ) );

    $queued = 0;
    foreach ( $posts as $pid ) {
        $current_status = get_post_meta( $pid, 'episode_ai_status', true );
        if ( $current_status !== 'done' ) {
            update_post_meta( $pid, 'episode_ai_status', 'pending' );
            $queued++;
        }
    }

    return $queued;
}
