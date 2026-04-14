<?php
/**
 * Gemini AI 音声文字起こし・記事生成機能
 * Google Gemini 2.5 Flash-Lite (無料枠) を使用してポッドキャスト音声を文字起こしし、ブログ記事化します。
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
        error_log( "Gemini Files API error HTTP {$up_code}: {$err}" );
        return new WP_Error( 'upload_api_error', "Files API エラー (HTTP {$up_code}): " . substr($err, 0, 300) );
    }

    return array(
        'uri'       => $up_data['file']['uri'],
        'name'      => $up_data['file']['name'],
        'mime_type' => $mime_type,
    );
}

// ============================================================
// Gemini generateContent API — 共通呼び出しヘルパー
// ============================================================

/**
 * Gemini API を呼び出して text を返す汎用ヘルパー。
 * $parts は generateContent の contents[0].parts 配列。
 *
 * @param array  $parts
 * @param array  $generation_config
 * @return string|WP_Error
 */
function contentfreaks_gemini_call( array $parts, array $generation_config = array() ) {
    $api_key = contentfreaks_get_gemini_api_key();
    if ( empty( $api_key ) ) {
        return new WP_Error( 'no_api_key', 'Gemini API Key が設定されていません。' );
    }

    $defaults = array(
        'temperature'     => 0.3,
        'maxOutputTokens' => 65536,
    );
    $config = array_merge( $defaults, $generation_config );

    $request_body = array(
        'contents'         => array( array( 'parts' => $parts ) ),
        'generationConfig' => $config,
    );

    $api_url  = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent'
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

    if ( $http_code === 429 ) {
        $err = isset( $data['error']['message'] ) ? $data['error']['message'] : $resp_body;
        $retry_sec = 60;
        if ( preg_match( '/retry in ([0-9.]+)s/i', $err, $m ) ) {
            $retry_sec = (int) ceil( (float) $m[1] );
        }
        error_log( "Gemini 429 detail: {$err}" );
        return new WP_Error( 'rate_limit', "レート制限です。{$retry_sec}秒後に自動リトライされます。(429: " . substr( $err, 0, 200 ) . ")" );
    }

    if ( $http_code !== 200 ) {
        $err = isset( $data['error']['message'] ) ? $data['error']['message'] : $resp_body;
        error_log( "Gemini API error HTTP {$http_code}: {$err}" );
        return new WP_Error( 'generate_api_error', "Gemini API エラー (HTTP {$http_code}): " . substr( $err, 0, 300 ) );
    }

    // 思考モデル対策: thought=true のパートを除いた最後のテキストを取得
    $generated_text = '';
    $parts_resp = $data['candidates'][0]['content']['parts'] ?? array();
    foreach ( array_reverse( $parts_resp ) as $part ) {
        if ( ! empty( $part['text'] ) && empty( $part['thought'] ) ) {
            $generated_text = $part['text'];
            break;
        }
    }
    if ( empty( $generated_text ) ) {
        foreach ( $parts_resp as $part ) {
            if ( ! empty( $part['text'] ) ) {
                $generated_text = $part['text'];
                break;
            }
        }
    }

    $finish_reason = $data['candidates'][0]['finishReason'] ?? '';
    if ( $finish_reason === 'MAX_TOKENS' ) {
        error_log( 'Gemini: finishReason=MAX_TOKENS — 出力がトークン制限で切断されました。' );
    }

    if ( empty( $generated_text ) ) {
        return new WP_Error( 'empty_response', 'Gemini API から空のレスポンスが返されました。parts=' . count( $parts_resp ) );
    }

    return $generated_text;
}

// ============================================================
// Step 1: 音声 → 文字起こし
// ============================================================

/**
 * 音声ファイルを文字起こしして日本語テキストを返す。
 *
 * @param string $file_uri  Files API URI
 * @param string $mime_type
 * @return string|WP_Error
 */
function contentfreaks_gemini_transcribe( $file_uri, $mime_type ) {
    $parts = array(
        array(
            'file_data' => array(
                'mime_type' => $mime_type,
                'file_uri'  => $file_uri,
            ),
        ),
        array(
            'text' => <<<PROMPT
この音声ファイルは日本語のポッドキャストです。
音声の内容を日本語で正確に文字起こしてください。

ルール:
- 話者の発言をそのまま書き起こすこと（話し言葉のまま）
- 聞き取れない部分は「（聞き取れず）」と書くこと
- 音声にない内容を補完・追加しないこと
- 話者が2人いる場合、発言が変わったら改行すること
- 余計な注釈や説明を加えないこと
- 日本語以外（韓国語等）で出力しないこと
PROMPT
        ),
    );

    return contentfreaks_gemini_call( $parts, array( 'temperature' => 0.1, 'maxOutputTokens' => 65536 ) );
}

// ============================================================
// Step 2: 文字起こしテキスト → 記事生成
// ============================================================

/**
 * 文字起こしテキストからブログ記事データを生成する。
 *
 * @param string $transcription  文字起こし済みテキスト
 * @param string $episode_title
 * @param string $episode_description
 * @return array|WP_Error
 */
function contentfreaks_gemini_generate_from_transcript( $transcription, $episode_title, $episode_description, $post_id = 0 ) {
    $safe_title = wp_strip_all_tags( $episode_title );
    $safe_desc  = wp_strip_all_tags( wp_trim_words( $episode_description, 200, '' ) );
    $safe_trans = mb_substr( $transcription, 0, 30000 ); // 長すぎる場合に切り詰め

    // 固有名詞コンテキストを取得
    $proper_noun_context = '';
    if ( $post_id > 0 && function_exists( 'contentfreaks_build_gemini_proper_noun_context' ) ) {
        $proper_noun_context = contentfreaks_build_gemini_proper_noun_context( $post_id );
    }
    $proper_noun_section = ! empty( $proper_noun_context ) ? "\n" . $proper_noun_context . "\n" : '';

    $prompt = <<<PROMPT
あなたはエンタメ系ブログ「コンテンツフリークス」のライターです。
以下のポッドキャスト文字起こしをもとに、読者向けのブログ記事を書いてください。

■ ポッドキャスト情報
- 番組名: コンテンツフリークス
- ホスト: みっくんとあっきーの2人
- ジャンル: 映画・ドラマの感想・考察
- エピソードタイトル: {$safe_title}
- RSS概要: {$safe_desc}
{$proper_noun_section}
■ 文字起こし
{$safe_trans}

■ 最重要ルール（厳守）
- 上記の文字起こしに書かれた内容だけを記事にすること。文字起こしにない情報を追加・捏造してはいけない。
- 登場人物名・俳優名・作品名は、文字起こしまたはタイトル・RSS概要に明記されているものだけ記載すること。
- 点数評価やランキング付けはしない

■ 記事ルール
- 文体: ですます調で親しみやすく
- 読者層: その作品を観た人向け（ネタバレあり前提）
- 分量: 文字起こしの内容量に応じて自動調整する
- 構成: 導入 → トピックごとのセクション → まとめ
- h2見出しを5〜8個使い、必要に応じてh3も使用する
- みっくんやあっきーの意見・発言を「みっくんは〜と語りました」のように引用すること
- 適宜リスト（ul/li）も使ってよい
- 記事の最後に以下のポッドキャスト誘導文をそのまま入れること:
  <p>この話題はポッドキャスト「コンテンツフリークス」でさらに詳しく語っています。ぜひお聴きください！</p>

■ 出力（以下のJSONのみ返すこと。前後に余計なテキスト不要）
{
  "article_body": "HTML形式（h2, h3, p, ul, li タグを使用）",
  "summary": "meta description 用の日本語概要（100〜150文字）",
  "tags": ["作品名", "出演者名1", "出演者名2", "..."]
}
PROMPT;

    $parts = array( array( 'text' => $prompt ) );
    $result = contentfreaks_gemini_call( $parts, array(
        'temperature'      => 0.7,
        'maxOutputTokens'  => 32768,
        'responseMimeType' => 'application/json',
    ) );

    if ( is_wp_error( $result ) ) {
        return $result;
    }

    $generated_text = $result;

    // コードブロック除去
    $generated_text = preg_replace( '/^```(?:json)?\s*/i', '', trim( $generated_text ) );
    $generated_text = preg_replace( '/\s*```$/', '', $generated_text );

    $article_data = json_decode( $generated_text, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        if ( preg_match( '/\{.*\}/s', $generated_text, $m ) ) {
            $article_data = json_decode( $m[0], true );
        }
    }

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        $repaired = contentfreaks_repair_truncated_json( $generated_text );
        if ( $repaired !== null ) {
            $article_data = $repaired;
            error_log( 'Gemini: 切れたJSONを修復しました。' );
        }
    }

    if ( json_last_error() !== JSON_ERROR_NONE || empty( $article_data ) ) {
        return new WP_Error( 'json_parse_error', 'JSON パース失敗: ' . substr( $generated_text, 0, 200 ) );
    }

    if ( ! empty( $article_data['article_body'] ) ) {
        $article_data['article_body'] = contentfreaks_remove_repetition( $article_data['article_body'] );
    }

    return $article_data;
}

// ============================================================
// 後方互換: contentfreaks_gemini_generate_article（旧シグネチャ対応）
// ============================================================
function contentfreaks_gemini_generate_article( $file_uri, $mime_type, $episode_title, $episode_description ) {
    // 旧シグネチャを呼び出している箇所がある場合の互換ラッパー
    // 実際の処理は _inner() 内の2ステップで行われるため、ここは使われない想定
    return new WP_Error( 'deprecated', 'contentfreaks_gemini_generate_article は廃止されました。2ステップ処理を使用してください。' );
}

    $api_key = contentfreaks_get_gemini_api_key();
    if ( empty( $api_key ) ) {
        return new WP_Error( 'no_api_key', 'Gemini API Key が設定されていません。' );
    }

    $safe_title = wp_strip_all_tags( $episode_title );
    $safe_desc  = wp_strip_all_tags( wp_trim_words( $episode_description, 200, '' ) );

    $prompt = <<<PROMPT
あなたはエンタメ系ブログ「コンテンツフリークス」のライターです。
以下のポッドキャスト音声を聞き取り、読者向けのブログ記事を日本語で書いてください。
音声の言語は日本語です。韓国語や他の言語で出力しないでください。

■ ポッドキャスト情報
- 番組名: コンテンツフリークス
- ホスト: みっくんとあっきーの2人
- ジャンル: 映画・ドラマの感想・考察
- エピソードタイトル: {$safe_title}
- RSS概要: {$safe_desc}

■ 最重要ルール（厳守）
- 音声で実際に話された内容だけを記事にすること。音声にない情報を追加・捏造してはいけない。
- 登場人物名・俳優名・作品名は、音声で明確に聞き取れた場合のみ記載すること。
- 聞き取りに自信がない固有名詞は書かないこと。推測で名前を当てはめない。
- エピソードタイトルやRSS概要に含まれる固有名詞は正確なので、参考にしてよい。

■ 記事ルール
- 文体: ですます調で親しみやすく
- 読者層: その作品を観た人向け（ネタバレあり前提）
- 分量: ポッドキャストの長さに応じて自動調整する
- 構成: 導入 → トピックごとのセクション → まとめ
- h2見出しを5〜8個使い、必要に応じてh3も使用する
- みっくんやあっきーの意見・発言を「みっくんは〜と語りました」のように引用すること
- 適宜リスト（ul/li）も使ってよい
- 点数評価やランキング付けはしない
- 記事の最後に以下のポッドキャスト誘導文をそのまま入れること:
  <p>この話題はポッドキャスト「コンテンツフリークス」でさらに詳しく語っています。ぜひお聴きください！</p>

■ 出力（以下のJSONのみ返すこと。前後に余計なテキスト不要）
{
  "article_body": "HTML形式（h2, h3, p, ul, li タグを使用）",
  "summary": "meta description 用の日本語概要（100〜150文字）",
  "tags": ["作品名", "出演者名1", "出演者名2", "..."]
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
            'temperature'      => 0.7,
            'maxOutputTokens'  => 65536,
            'responseMimeType' => 'application/json',
        ),
    );

    $api_url  = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent'
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
        error_log( "Gemini 429 detail: {$err}" );
        return new WP_Error( 'rate_limit', "レート制限です。{$retry_sec}秒後に自動リトライされます。(429: " . substr($err, 0, 200) . ")" );
    }

    if ( $http_code !== 200 ) {
        $err = isset( $data['error']['message'] ) ? $data['error']['message'] : $resp_body;
        error_log( "Gemini API error HTTP {$http_code}: {$err}" );
        return new WP_Error( 'generate_api_error', "Gemini API エラー (HTTP {$http_code}): " . substr($err, 0, 300) );
    }

// ============================================================
// 繰り返しループ除去
// ============================================================

/**
 * LLMが同じフレーズを繰り返すバグを検出し、最初の出現で切り詰める。
 * 20文字以上のフレーズが3回以上連続で繰り返される場合に除去する。
 */
function contentfreaks_remove_repetition( $text ) {
    // HTMLタグを除いたテキストで判定する
    $plain = wp_strip_all_tags( $text );

    // 文区切り（句点・改行・</p>）で分割
    $sentences = preg_split( '/(?<=。)|(?<=\n)|(?<=<\/p>)/u', $text );
    if ( count( $sentences ) < 4 ) {
        return $text;
    }

    $seen   = array();
    $result = array();

    foreach ( $sentences as $i => $sentence ) {
        $key = trim( wp_strip_all_tags( $sentence ) );
        if ( mb_strlen( $key ) < 15 ) {
            // 短すぎる断片はスキップ（繰り返し判定しない）
            $result[] = $sentence;
            continue;
        }
        if ( isset( $seen[ $key ] ) && $seen[ $key ] >= 2 ) {
            // 同じ文が3回目以降 → ここで打ち切り
            error_log( 'Gemini: 繰り返しループを検出・除去しました。key=' . mb_substr( $key, 0, 40 ) );
            break;
        }
        $seen[ $key ] = ( $seen[ $key ] ?? 0 ) + 1;
        $result[]     = $sentence;
    }

    return implode( '', $result );
}

// ============================================================
// Gemini Files API — ファイル削除（容量節約）
// ============================================================
// 切れたJSONの修復
// ============================================================

function contentfreaks_repair_truncated_json( $text ) {
    $text = trim( $text );
    // 先頭が { でなければ抽出
    if ( $text[0] !== '{' && preg_match( '/\{.*/s', $text, $m ) ) {
        $text = $m[0];
    }
    // 末尾に閉じ括弧を段階的に追加して試行
    $closers = array( '"', '"]', '"}', ']}', '"}' );
    foreach ( $closers as $c ) {
        $try = $text . $c;
        $decoded = json_decode( $try, true );
        if ( json_last_error() === JSON_ERROR_NONE && ! empty( $decoded ) ) {
            return $decoded;
        }
    }
    // ブルートフォース: 開き括弧を数えて閉じる
    $opens = substr_count( $text, '{' ) - substr_count( $text, '}' );
    $open_arr = substr_count( $text, '[' ) - substr_count( $text, ']' );
    $suffix = str_repeat( ']', max( 0, $open_arr ) ) . str_repeat( '}', max( 0, $opens ) );
    // 文字列が途中で切れていたら閉じる
    $try = $text . '"' . $suffix;
    $decoded = json_decode( $try, true );
    if ( json_last_error() === JSON_ERROR_NONE && ! empty( $decoded ) ) {
        return $decoded;
    }
    $try = $text . $suffix;
    $decoded = json_decode( $try, true );
    if ( json_last_error() === JSON_ERROR_NONE && ! empty( $decoded ) ) {
        return $decoded;
    }
    return null;
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
    try {
        return contentfreaks_generate_episode_article_inner( $post_id );
    } catch ( \Throwable $e ) {
        $msg = 'PHP例外: ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')';
        $prev_debug = get_post_meta( $post_id, 'episode_ai_debug', true );
        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', $msg );
        update_post_meta( $post_id, 'episode_ai_debug', $prev_debug . ' → EXCEPTION: ' . $e->getMessage() );
        error_log( "Gemini: 例外 Post ID={$post_id}: {$msg}" );
        return false;
    }
}

function contentfreaks_generate_episode_article_inner( $post_id ) {
    // デバッグ: 各ステップの到達記録
    update_post_meta( $post_id, 'episode_ai_debug', 'step0:start' );

    $audio_url = get_post_meta( $post_id, 'episode_audio_url', true );
    if ( empty( $audio_url ) ) {
        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', '音声 URL が見つかりません' );
        update_post_meta( $post_id, 'episode_ai_debug', 'step0:no_audio_url' );
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
        update_post_meta( $post_id, 'episode_ai_debug', 'step0:url_empty_after_fix' );
        return false;
    }

    // 処理中フラグ
    update_post_meta( $post_id, 'episode_ai_status', 'processing' );
    update_post_meta( $post_id, 'episode_ai_started_at', current_time( 'mysql' ) );
    delete_post_meta( $post_id, 'episode_ai_error' );
    update_post_meta( $post_id, 'episode_ai_debug', 'step1:uploading url=' . substr($audio_url, 0, 80) );

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
        update_post_meta( $post_id, 'episode_ai_debug', 'step1:upload_error: ' . substr($msg, 0, 100) );
        error_log( "Gemini: アップロードエラー Post ID={$post_id}: {$msg}" );
        return false;
    }

    update_post_meta( $post_id, 'episode_ai_debug', 'step2:transcribing uri=' . substr($upload['uri'], 0, 60) );
    error_log( "Gemini: 文字起こし開始 Post ID={$post_id}" );

    // Step 2: 音声 → 文字起こし
    $transcription = contentfreaks_gemini_transcribe( $upload['uri'], $upload['mime_type'] );

    // Step 3: ファイル削除（文字起こし後すぐ削除）
    contentfreaks_gemini_delete_file( $upload['name'] );

    if ( is_wp_error( $transcription ) ) {
        $msg  = $transcription->get_error_message();
        $code = $transcription->get_error_code();
        $msg = mb_convert_encoding( $msg, 'UTF-8', 'UTF-8' );
        $msg = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $msg );
        if ( empty( $msg ) ) { $msg = "文字起こしエラー (code: {$code})"; }
        update_post_meta( $post_id, 'episode_ai_debug', 'step2:transcribe_error' );
        if ( $code === 'rate_limit' ) {
            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                update_post_meta( $post_id, 'episode_ai_status', 'error' );
                update_post_meta( $post_id, 'episode_ai_error', $msg );
            } else {
                update_post_meta( $post_id, 'episode_ai_status', 'pending' );
            }
            error_log( "Gemini: レート制限(文字起こし) Post ID={$post_id}: {$msg}" );
            return false;
        }
        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', $msg );
        error_log( "Gemini: 文字起こしエラー Post ID={$post_id}: {$msg}" );
        return false;
    }

    update_post_meta( $post_id, 'episode_ai_transcription', sanitize_textarea_field( $transcription ) );
    update_post_meta( $post_id, 'episode_ai_debug', 'step3:generating_article len=' . mb_strlen($transcription) );
    error_log( "Gemini: 記事生成開始 Post ID={$post_id} 文字起こし文字数=" . mb_strlen($transcription) );

    // Step 4: 文字起こし → 記事生成（固有名詞コンテキスト付き）
    $article_data = contentfreaks_gemini_generate_from_transcript( $transcription, $title, $description, $post_id );

    update_post_meta( $post_id, 'episode_ai_debug', 'step4:article_done is_error=' . (is_wp_error($article_data) ? 'yes:' . $article_data->get_error_code() : 'no') );

    if ( is_wp_error( $article_data ) ) {
        $msg  = $article_data->get_error_message();
        $code = $article_data->get_error_code();
        $msg = mb_convert_encoding( $msg, 'UTF-8', 'UTF-8' );
        $msg = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $msg );
        if ( empty( $msg ) ) { $msg = "記事生成エラー (code: {$code})"; }

        if ( $code === 'rate_limit' ) {
            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                update_post_meta( $post_id, 'episode_ai_status', 'error' );
                update_post_meta( $post_id, 'episode_ai_error', $msg );
            } else {
                update_post_meta( $post_id, 'episode_ai_status', 'pending' );
            }
            error_log( "Gemini: レート制限(記事生成) Post ID={$post_id}: {$msg}" );
            return false;
        }

        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', $msg );
        error_log( "Gemini: 記事生成エラー Post ID={$post_id}: {$msg}" );
        return false;
    }

    // Step 5: 投稿を更新
    update_post_meta( $post_id, 'episode_ai_debug', 'step5:parsing_article keys=' . implode(',', array_keys($article_data)) );

    $article_body  = $article_data['article_body']  ?? '';
    $summary       = $article_data['summary']       ?? '';
    $tags          = $article_data['tags']          ?? array();

    // 固有名詞の後処理修正
    if ( ! empty( $article_body ) && function_exists( 'contentfreaks_verify_and_fix_proper_nouns' ) ) {
        $work_data    = function_exists( 'contentfreaks_get_work_meta_from_post' ) ? contentfreaks_get_work_meta_from_post( $post_id ) : null;
        $article_body = contentfreaks_verify_and_fix_proper_nouns( $article_body, $work_data );
    }

    if ( empty( $article_body ) ) {
        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', 'article_body が空でした (keys: ' . implode(',', array_keys($article_data)) . ')' );
        update_post_meta( $post_id, 'episode_ai_debug', 'step4:empty_body' );
        return false;
    }

    $update = array(
        'ID'           => $post_id,
        'post_content' => wp_kses_post( $article_body ),
    );

    // タイトルはRSSのものをそのまま使う（AI上書きしない）

    if ( ! empty( $summary ) && empty( trim( $post->post_excerpt ) ) ) {
        $update['post_excerpt'] = sanitize_textarea_field( $summary );
    }

    update_post_meta( $post_id, 'episode_ai_debug', 'step6:updating_post' );
    $wp_result = wp_update_post( $update, true );
    if ( is_wp_error( $wp_result ) ) {
        $msg = 'wp_update_post失敗: ' . $wp_result->get_error_message();
        update_post_meta( $post_id, 'episode_ai_status', 'error' );
        update_post_meta( $post_id, 'episode_ai_error', $msg );
        update_post_meta( $post_id, 'episode_ai_debug', 'step6:update_post_error' );
        error_log( "Gemini: {$msg} Post ID={$post_id}" );
        return false;
    }

    update_post_meta( $post_id, 'episode_ai_debug', 'step7:setting_tags' );
    if ( ! empty( $tags ) && is_array( $tags ) ) {
        wp_set_post_tags( $post_id, array_map( 'sanitize_text_field', $tags ), true );
    }

    update_post_meta( $post_id, 'episode_ai_debug', 'step8:done' );
    update_post_meta( $post_id, 'episode_ai_status', 'done' );
    update_post_meta( $post_id, 'episode_ai_generated_at', current_time( 'mysql' ) );
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
