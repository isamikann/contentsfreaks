<?php
/**
 * Cocoon Child Theme Functions
 * ポッドキャストサイト専用のカスタマイズ
 */

// 直接このファイルにアクセスすることを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cocoonのデフォルトヘッダーを無効化
 */
function contentfreaks_disable_default_header() {
    // Cocoonのヘッダー関連アクションを削除
    remove_action('wp_head', 'cocoon_header_meta');
    remove_action('get_header', 'cocoon_header_init');
    
    // ヘッダー関連のフィルターを削除
    remove_filter('wp_head', 'cocoon_meta_description');
    remove_filter('wp_head', 'cocoon_meta_keywords');
}
add_action('init', 'contentfreaks_disable_default_header', 1);

/**
 * body_classにカスタムヘッダークラスを追加
 */
function contentfreaks_body_class($classes) {
    $classes[] = 'contentfreaks-custom-header';
    return $classes;
}
add_filter('body_class', 'contentfreaks_body_class');

/**
 * 子テーマのスタイルとスクリプトを読み込み
 */
function contentfreaks_enqueue_scripts() {
    // 親テーマのスタイルを読み込み
    wp_enqueue_style('cocoon-style', get_template_directory_uri() . '/style.css');
    
    // 子テーマのメインスタイル（WordPressの標準）
    wp_enqueue_style('contentfreaks-main-style', get_stylesheet_directory_uri() . '/style.css', array('cocoon-style'), '1.1.4');
    
    // ContentFreaks専用拡張スタイル（重複削除済み・最適化版）
    wp_enqueue_style('contentfreaks-final-style', get_stylesheet_directory_uri() . '/contentfreaks-final.css', array('contentfreaks-main-style'), '1.0.0');
    
    // 子テーマのJavaScriptを読み込み
    wp_enqueue_script('contentfreaks-script', get_stylesheet_directory_uri() . '/javascript.js', array('jquery'), '1.0.0', true);
    
    // AJAX用の設定を追加
    wp_localize_script('contentfreaks-script', 'contentfreaks_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('contentfreaks_ajax_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'contentfreaks_enqueue_scripts');

// カスタムフィールド機能は削除：RSSから直接エピソードデータを表示するため不要

// latest_episodeショートコードは削除：front-page.phpで直接RSS表示を使用

/**
 * ショートコード: ポッドキャストプラットフォームリンク
 */
function contentfreaks_podcast_platforms_shortcode() {
    $platforms = array(
        'spotify' => array(
            'name' => 'Spotify', 
            'icon' => get_theme_mod('spotify_icon') ? '<img src="' . esc_url(get_theme_mod('spotify_icon')) . '" alt="Spotify" style="width: 24px; height: 24px; object-fit: contain;">' : '🎧',
            'url' => 'https://open.spotify.com/show/20otj7CiCZ0hcWYkkEpnLL?si=w3Jlrpg5Ssmk0TGa_Flb8g',
            'color' => '#1DB954'
        ),
        'apple' => array(
            'name' => 'Apple Podcasts', 
            'icon' => get_theme_mod('apple_podcasts_icon') ? '<img src="' . esc_url(get_theme_mod('apple_podcasts_icon')) . '" alt="Apple Podcasts" style="width: 24px; height: 24px; object-fit: contain;">' : '🍎',
            'url' => 'https://podcasts.apple.com/jp/podcast/%E3%82%B3%E3%83%B3%E3%83%86%E3%83%B3%E3%83%84%E3%83%95%E3%83%AA%E3%83%BC%E3%82%AF%E3%82%B9/id1692185758',
            'color' => '#A855F7'
        ),
        'youtube' => array(
            'name' => 'YouTube', 
            'icon' => get_theme_mod('youtube_icon') ? '<img src="' . esc_url(get_theme_mod('youtube_icon')) . '" alt="YouTube" style="width: 24px; height: 24px; object-fit: contain;">' : '📺',
            'url' => 'https://youtube.com/@contentfreaks',
            'color' => '#FF0000'
        ),
    );
    
    ob_start();
    echo '<div class="platforms-grid">';
    
    foreach ($platforms as $key => $platform) {
        echo '<a href="' . esc_url($platform['url']) . '" class="platform-link platform-' . esc_attr($key) . '" target="_blank" rel="noopener">';
        echo '<div class="platform-icon">' . $platform['icon'] . '</div>';
        echo '<div class="platform-name">' . esc_html($platform['name']) . '</div>';
        echo '<div class="platform-action">今すぐ聴く</div>';
        echo '</a>';
    }
    
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('podcast_platforms', 'contentfreaks_podcast_platforms_shortcode');

/**
 * ショートコード: ホスト紹介
 */
function contentfreaks_hosts_shortcode() {
    // カスタマイザーから2人分のホスト情報を取得
    $host1_name = get_theme_mod('host1_name', 'ホスト1');
    $host1_role = get_theme_mod('host1_role', 'メインホスト');
    $host1_bio = get_theme_mod('host1_bio', 'コンテンツ制作について語ります。');
    $host1_image = get_theme_mod('host1_image', '');
    $host1_twitter = get_theme_mod('host1_twitter', '');
    $host1_youtube = get_theme_mod('host1_youtube', '');
    
    $host2_name = get_theme_mod('host2_name', 'ホスト2');
    $host2_role = get_theme_mod('host2_role', 'コホスト');
    $host2_bio = get_theme_mod('host2_bio', 'コンテンツ制作について語ります。');
    $host2_image = get_theme_mod('host2_image', '');
    $host2_twitter = get_theme_mod('host2_twitter', '');
    $host2_youtube = get_theme_mod('host2_youtube', '');
    
    $hosts = array();
    
    // ホスト1の情報を追加（名前が入力されている場合のみ）
    if (!empty($host1_name) && $host1_name !== 'ホスト1') {
        $host1_social = array();
        if (!empty($host1_twitter)) $host1_social['twitter'] = $host1_twitter;
        if (!empty($host1_youtube)) $host1_social['youtube'] = $host1_youtube;
        
        $hosts[] = array(
            'name' => $host1_name,
            'role' => $host1_role,
            'bio' => $host1_bio,
            'image' => $host1_image,
            'social' => $host1_social
        );
    }
    
    // ホスト2の情報を追加（名前が入力されている場合のみ）
    if (!empty($host2_name) && $host2_name !== 'ホスト2') {
        $host2_social = array();
        if (!empty($host2_twitter)) $host2_social['twitter'] = $host2_twitter;
        if (!empty($host2_youtube)) $host2_social['youtube'] = $host2_youtube;
        
        $hosts[] = array(
            'name' => $host2_name,
            'role' => $host2_role,
            'bio' => $host2_bio,
            'image' => $host2_image,
            'social' => $host2_social
        );
    }
    
    // どちらも設定されていない場合はデフォルト表示
    if (empty($hosts)) {
        $hosts = array(
            array(
                'name' => 'コンテンツフリークス',
                'role' => 'メインホスト',
                'bio' => 'YouTuber、ブロガー、インフルエンサーなど様々なコンテンツクリエイターをゲストに迎え、制作の裏側や成功の秘訣を深掘りしています。',
                'image' => '',
                'social' => array('twitter' => 'https://twitter.com/contentfreaks', 'youtube' => 'https://youtube.com/@contentfreaks')
            )
        );
    }
    
    ob_start();
    echo '<div class="hosts-grid">';
    
    foreach ($hosts as $host) {
        echo '<div class="host-card">';
        
        if ($host['image']) {
            echo '<div class="host-image"><img src="' . esc_url($host['image']) . '" alt="' . esc_attr($host['name']) . '"></div>';
        } else {
            echo '<div class="host-image" style="background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 3rem;">🎙️</div>';
        }
        
        echo '<div class="host-content">';
        echo '<h3 class="host-name">' . esc_html($host['name']) . '</h3>';
        echo '<div class="host-role">' . esc_html($host['role']) . '</div>';
        echo '<div class="host-bio">' . esc_html($host['bio']) . '</div>';
        
        if (!empty($host['social'])) {
            echo '<div class="host-social">';
            foreach ($host['social'] as $platform => $url) {
                $icon = $platform === 'twitter' ? '🐦' : ($platform === 'youtube' ? '📺' : '🔗');
                echo '<a href="' . esc_url($url) . '" class="social-link" target="_blank" rel="noopener">' . $icon . '</a>';
            }
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('podcast_hosts', 'contentfreaks_hosts_shortcode');

/**
 * カスタマイザーにポッドキャスト設定を追加
 */
function contentfreaks_customize_register($wp_customize) {
    // ポッドキャスト設定セクション
    $wp_customize->add_section('contentfreaks_podcast_settings', array(
        'title' => 'ポッドキャスト設定',
        'priority' => 30,
    ));
    
    // ポッドキャスト名
    $wp_customize->add_setting('podcast_name', array(
        'default' => 'コンテンツフリークス',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('podcast_name', array(
        'label' => 'ポッドキャスト名',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'text',
    ));
    
    // ポッドキャスト説明
    $wp_customize->add_setting('podcast_description', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('podcast_description', array(
        'label' => 'ポッドキャスト説明',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'textarea',
    ));
    
    // ポッドキャストアートワーク
    $wp_customize->add_setting('podcast_artwork', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'podcast_artwork', array(
        'label' => 'ポッドキャストアートワーク',
        'section' => 'contentfreaks_podcast_settings',
    )));
    
    // ホスト1設定
    $wp_customize->add_setting('host1_name', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('host1_name', array(
        'label' => 'ホスト1 名前',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('host1_role', array(
        'default' => 'メインホスト',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('host1_role', array(
        'label' => 'ホスト1 役職',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('host1_bio', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('host1_bio', array(
        'label' => 'ホスト1 紹介文',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('host1_image', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'host1_image', array(
        'label' => 'ホスト1 画像',
        'section' => 'contentfreaks_podcast_settings',
    )));
    
    $wp_customize->add_setting('host1_twitter', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('host1_twitter', array(
        'label' => 'ホスト1 Twitter URL',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('host1_youtube', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('host1_youtube', array(
        'label' => 'ホスト1 YouTube URL',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'url',
    ));
    
    // ホスト2設定
    $wp_customize->add_setting('host2_name', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('host2_name', array(
        'label' => 'ホスト2 名前',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('host2_role', array(
        'default' => 'コホスト',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('host2_role', array(
        'label' => 'ホスト2 役職',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('host2_bio', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('host2_bio', array(
        'label' => 'ホスト2 紹介文',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('host2_image', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'host2_image', array(
        'label' => 'ホスト2 画像',
        'section' => 'contentfreaks_podcast_settings',
    )));
    
    $wp_customize->add_setting('host2_twitter', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('host2_twitter', array(
        'label' => 'ホスト2 Twitter URL',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('host2_youtube', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('host2_youtube', array(
        'label' => 'ホスト2 YouTube URL',
        'section' => 'contentfreaks_podcast_settings',
        'type' => 'url',
    ));
    
    // プラットフォームアイコン設定
    $wp_customize->add_setting('spotify_icon', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'spotify_icon', array(
        'label' => 'Spotify アイコン画像',
        'section' => 'contentfreaks_podcast_settings',
        'description' => 'Spotifyアイコン用の画像を選択してください（空の場合はデフォルト絵文字 🎧 を使用）',
    )));
    
    $wp_customize->add_setting('apple_podcasts_icon', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'apple_podcasts_icon', array(
        'label' => 'Apple Podcasts アイコン画像',
        'section' => 'contentfreaks_podcast_settings',
        'description' => 'Apple Podcastsアイコン用の画像を選択してください（空の場合はデフォルト絵文字 🍎 を使用）',
    )));
    
    $wp_customize->add_setting('youtube_icon', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'youtube_icon', array(
        'label' => 'YouTube アイコン画像',
        'section' => 'contentfreaks_podcast_settings',
        'description' => 'YouTubeアイコン用の画像を選択してください（空の場合はデフォルト絵文字 📺 を使用）',
    )));
}
add_action('customize_register', 'contentfreaks_customize_register');

/**
 * RSSエピソードを投稿として自動作成
 */
function contentfreaks_sync_rss_to_posts() {
    $episodes = contentfreaks_get_rss_episodes(0); // 全エピソード取得
    $synced_count = 0;
    $errors = array();
    
    // ポッドキャストカテゴリーを作成（存在しない場合）
    $podcast_category = get_category_by_slug('podcast');
    if (!$podcast_category) {
        $cat_id = wp_create_category('ポッドキャスト');
        $podcast_category = get_category($cat_id);
    }
    
    foreach ($episodes as $episode) {
        // 既存投稿チェック（タイトルで重複確認）
        $existing_post = get_posts(array(
            'title' => $episode['title'],
            'post_type' => 'post',
            'post_status' => array('publish', 'draft', 'private'),
            'numberposts' => 1
        ));
        
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
                // カスタムフィールド保存
                update_post_meta($post_id, 'episode_audio_url', $episode['audio_url']);
                update_post_meta($post_id, 'episode_number', $episode['episode_number']);
                update_post_meta($post_id, 'episode_duration', $episode['duration']);
                update_post_meta($post_id, 'episode_original_url', $episode['link']);
                update_post_meta($post_id, 'episode_category', $episode['category']);
                update_post_meta($post_id, 'is_podcast_episode', '1');
                
                // アイキャッチ画像設定
                if ($episode['thumbnail']) {
                    contentfreaks_set_featured_image_from_url($post_id, $episode['thumbnail']);
                }
                
                $synced_count++;
            } else {
                $errors[] = '投稿作成エラー: ' . $episode['title'];
            }
        }
    }
    
    // 同期結果を保存
    update_option('contentfreaks_last_sync_time', current_time('mysql'));
    update_option('contentfreaks_last_sync_count', $synced_count);
    update_option('contentfreaks_last_sync_errors', $errors);
    
    return array(
        'synced' => $synced_count,
        'errors' => $errors
    );
}

/**
 * 外部URLからアイキャッチ画像を設定
 */
function contentfreaks_set_featured_image_from_url($post_id, $image_url) {
    // 既にアイキャッチ画像が設定されている場合はスキップ
    if (has_post_thumbnail($post_id)) {
        return;
    }
    
    // media_sideload_image関数を使用するために必要なファイルをインクルード
    if (!function_exists('media_sideload_image')) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }
    
    // URL から画像をダウンロードしてメディアライブラリに追加
    $image_id = media_sideload_image($image_url, $post_id, null, 'id');
    
    if (!is_wp_error($image_id)) {
        set_post_thumbnail($post_id, $image_id);
        return true;
    } else {
        // エラーログに記録
        error_log('サムネイル設定エラー (Post ID: ' . $post_id . '): ' . $image_id->get_error_message());
        return false;
    }
}

/**
 * 定期同期スケジュール
 */
function contentfreaks_schedule_sync() {
    if (!wp_next_scheduled('contentfreaks_hourly_sync')) {
        wp_schedule_event(time(), 'hourly', 'contentfreaks_hourly_sync');
    }
}
add_action('wp', 'contentfreaks_schedule_sync');

add_action('contentfreaks_hourly_sync', 'contentfreaks_sync_rss_to_posts');

/**
 * 管理画面メニュー
 */
function contentfreaks_admin_menu() {
    add_management_page(
        'ポッドキャスト管理',
        'ポッドキャスト管理', 
        'manage_options',
        'contentfreaks-sync',
        'contentfreaks_sync_admin_page'
    );
}
add_action('admin_menu', 'contentfreaks_admin_menu');

/**
 * RSSキャッシュクリア機能
 */
function contentfreaks_clear_rss_cache() {
    // 現在使用中のキャッシュのみクリア
    delete_transient('contentfreaks_rss_episodes_1');
    delete_transient('contentfreaks_rss_episodes_6');
    delete_transient('contentfreaks_rss_episodes_all');
    delete_transient('contentfreaks_rss_count');
    
    // 古い同期関連のオプションも削除
    delete_option('contentfreaks_last_rss_sync');
    delete_option('contentfreaks_last_sync_count');
    delete_option('contentfreaks_last_sync_error');
    
    return true;
}

/**
 * 管理画面の同期ページ
 */
function contentfreaks_sync_admin_page() {
    // 手動同期処理
    if (isset($_POST['manual_sync']) && wp_verify_nonce($_POST['sync_nonce'], 'contentfreaks_sync')) {
        $result = contentfreaks_sync_rss_to_posts();
        if (!empty($result['errors'])) {
            echo '<div class="notice notice-warning"><p>' . $result['synced'] . ' 件のエピソードを同期しました。エラー: ' . count($result['errors']) . ' 件</p></div>';
        } else {
            echo '<div class="notice notice-success"><p>' . $result['synced'] . ' 件のエピソードを同期しました！</p></div>';
        }
    }

    // キャッシュクリア処理
    if (isset($_POST['clear_cache']) && wp_verify_nonce($_POST['clear_cache_nonce'], 'contentfreaks_clear_cache')) {
        contentfreaks_clear_rss_cache();
        echo '<div class="notice notice-success"><p>RSSキャッシュをクリアしました！</p></div>';
    }

    // RSSテスト処理
    if (isset($_POST['test_rss']) && wp_verify_nonce($_POST['test_rss_nonce'], 'contentfreaks_test_rss')) {
        echo '<div class="notice notice-info">';
        echo '<h3>RSSフィードテスト結果</h3>';
        
        // キャッシュをクリアしてから新規取得
        contentfreaks_clear_rss_cache();
        $episodes = contentfreaks_get_rss_episodes(5);
        
        if (!empty($episodes)) {
            echo '<p style="color: green;">✅ RSS取得成功！ ' . count($episodes) . ' 件のエピソードを取得</p>';
            echo '<ul>';
            foreach ($episodes as $episode) {
                echo '<li>';
                echo '<strong>' . esc_html($episode['title']) . '</strong><br>';
                echo '日付: ' . esc_html($episode['formatted_date']) . '<br>';
                echo '音声URL: ' . ($episode['audio_url'] ? '✅ あり' : '❌ なし') . '<br>';
                echo '再生時間: ' . ($episode['duration'] ? esc_html($episode['duration']) : '不明') . '<br>';
                echo '</li><hr>';
            }
            echo '</ul>';
        } else {
            echo '<p style="color: red;">❌ エラー: エピソードを取得できませんでした</p>';
        }
        echo '</div>';
    }
    
    // 現在の統計情報を取得
    $current_rss_count = contentfreaks_get_rss_episode_count();
    $post_count = wp_count_posts()->publish;
    $podcast_posts = get_posts(array(
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'post_status' => 'publish',
        'numberposts' => -1
    ));
    $podcast_post_count = count($podcast_posts);
    $last_sync_time = get_option('contentfreaks_last_sync_time', '未同期');
    $last_sync_count = get_option('contentfreaks_last_sync_count', 0);
    
    echo '<div class="wrap">';
    echo '<h1>ポッドキャスト管理</h1>';
    echo '<p>RSSフィードからエピソードを投稿として自動同期します。</p>';
    
    echo '<div style="background: white; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px;">';
    echo '<h3>📊 統計情報</h3>';
    echo '<p><strong>RSSエピソード数:</strong> ' . $current_rss_count . ' 件</p>';
    echo '<p><strong>WordPress投稿数:</strong> ' . $post_count . ' 件</p>';
    echo '<p><strong>ポッドキャスト投稿数:</strong> ' . $podcast_post_count . ' 件</p>';
    echo '<p><strong>最終同期:</strong> ' . $last_sync_time . '</p>';
    echo '<p><strong>前回同期数:</strong> ' . $last_sync_count . ' 件</p>';
    echo '</div>';
    
    echo '<div style="display: flex; gap: 10px; margin-bottom: 20px;">';
    
    // 手動同期ボタン
    echo '<form method="post" style="display: inline;">';
    wp_nonce_field('contentfreaks_sync', 'sync_nonce');
    echo '<input type="submit" name="manual_sync" class="button-primary" value="手動同期実行" />';
    echo '</form>';
    
    // キャッシュクリアボタン
    echo '<form method="post" style="display: inline;">';
    wp_nonce_field('contentfreaks_clear_cache', 'clear_cache_nonce');
    echo '<input type="submit" name="clear_cache" class="button-secondary" value="RSSキャッシュクリア" />';
    echo '</form>';
    
    // RSSテストボタン
    echo '<form method="post" style="display: inline;">';
    wp_nonce_field('contentfreaks_test_rss', 'test_rss_nonce');
    echo '<input type="submit" name="test_rss" class="button-secondary" value="RSS接続テスト" />';
    echo '</form>';
    
    echo '</div>';
    
    echo '<div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa;">';
    echo '<h4>ℹ️ 情報</h4>';
    echo '<p><strong>RSS URL:</strong> https://anchor.fm/s/d8cfdc48/podcast/rss</p>';
    echo '<p><strong>同期スケジュール:</strong> 1時間毎の自動同期</p>';
    echo '<p><strong>メリット:</strong> SEO効果、サイト内検索対応、コメント機能</p>';
    echo '<p><strong>エピソード一覧:</strong> <a href="' . home_url('/episodes/') . '" target="_blank">エピソード一覧ページ</a></p>';
    echo '</div>';
    
    echo '</div>';
}

/**
 * RSSから直接エピソードデータを取得（キャッシュ機能付き）
 */
function contentfreaks_get_rss_episodes($limit = 0) {
    $spotify_rss_url = 'https://anchor.fm/s/d8cfdc48/podcast/rss';
    
    // キャッシュキー（0は全件取得を意味する）
    $cache_key = $limit > 0 ? 'contentfreaks_rss_episodes_' . $limit : 'contentfreaks_rss_episodes_all';
    $cached_data = get_transient($cache_key);
    
    if ($cached_data !== false) {
        return $cached_data;
    }
    
    $feed = fetch_feed($spotify_rss_url);
    
    if (is_wp_error($feed)) {
        error_log('RSS取得エラー: ' . $feed->get_error_message());
        return array();
    }
    
    // 0を指定すると全件取得
    $items = $limit > 0 ? $feed->get_items(0, $limit) : $feed->get_items();
    $episodes = array();
    
    if (empty($items)) {
        error_log('RSSフィードにアイテムが見つかりません');
        return array();
    }
    
    foreach ($items as $item) {
        $title = $item->get_title();
        $description = $item->get_description();
        $pub_date = $item->get_date('Y-m-d H:i:s');
        $link = $item->get_link();
        
        // 音声ファイルURL取得
        $audio_url = '';
        $enclosure = $item->get_enclosure();
        if ($enclosure) {
            $original_url = $enclosure->get_link();
            if ($original_url) {
                // Anchor.fm URLをCloudFront URLに変換
                if (strpos($original_url, 'anchor.fm') !== false) {
                    $audio_url = str_replace('https://anchor.fm/s/d8cfdc48/podcast/play/', 'https://d3ctxlq1ktw2nl.cloudfront.net/', $original_url);
                    $audio_url = str_replace('/play/', '/', $audio_url);
                } else {
                    $audio_url = $original_url;
                }
            }
        }
        
        // エピソード番号を抽出
        $episode_number = '';
        if (preg_match('/[#＃](\d+)/', $title, $matches)) {
            $episode_number = $matches[1];
        }
        
        // 再生時間を抽出
        $duration = '';
        if ($enclosure && method_exists($enclosure, 'get_duration')) {
            $duration_seconds = $enclosure->get_duration();
            if ($duration_seconds) {
                $minutes = floor($duration_seconds / 60);
                $seconds = $duration_seconds % 60;
                $duration = sprintf('%d:%02d', $minutes, $seconds);
            }
        }
        
        // カテゴリーを抽出（簡単な分類）
        $category = 'エピソード';
        if (strpos(strtolower($title), 'special') !== false || strpos($title, 'スペシャル') !== false) {
            $category = 'スペシャル';
        }
        
        // サムネイル画像
        $thumbnail = '';
        // iTunesタグからサムネイルを取得
        if (method_exists($item, 'get_item_tags')) {
            $item_tags = $item->get_item_tags('http://www.itunes.com/dtds/podcast-1.0.dtd', 'image');
            if (!empty($item_tags[0]['attribs']['']['href'])) {
                $thumbnail = $item_tags[0]['attribs']['']['href'];
            }
        }
        
        // iTunesタグで見つからない場合、他の方法でサムネイルを探す
        if (empty($thumbnail)) {
            // メディア要素のサムネイルを検索
            $enclosure = $item->get_enclosure();
            if ($enclosure && method_exists($enclosure, 'get_thumbnail')) {
                $thumbnail = $enclosure->get_thumbnail();
            }
        }
        
        // まだ見つからない場合、descriptionからimg srcを抽出
        if (empty($thumbnail)) {
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $description, $matches)) {
                $thumbnail = $matches[1];
            }
        }
        
        $episodes[] = array(
            'title' => $title,
            'description' => wp_trim_words(strip_tags($description), 30),
            'full_description' => $description,
            'pub_date' => $pub_date,
            'formatted_date' => date('Y年n月j日', strtotime($pub_date)),
            'link' => $link,
            'audio_url' => $audio_url,
            'episode_number' => $episode_number,
            'duration' => $duration,
            'category' => $category,
            'thumbnail' => $thumbnail
        );
    }
    
    // 30分間キャッシュ
    set_transient($cache_key, $episodes, 30 * MINUTE_IN_SECONDS);
    
    return $episodes;
}

/**
 * RSSエピソード数を取得
 */
function contentfreaks_get_rss_episode_count() {
    $cache_key = 'contentfreaks_rss_count';
    $cached_count = get_transient($cache_key);
    
    if ($cached_count !== false) {
        return $cached_count;
    }
    
    $spotify_rss_url = 'https://anchor.fm/s/d8cfdc48/podcast/rss';
    $feed = fetch_feed($spotify_rss_url);
    
    if (is_wp_error($feed)) {
        return 0;
    }
    
    // 全エピソードを取得してカウント
    $items = $feed->get_items();
    $count = count($items);
    
    // 1時間キャッシュ
    set_transient($cache_key, $count, HOUR_IN_SECONDS);
    
    return $count;
}

/**
 * AJAX: ポッドキャスト投稿の追加読み込み
 */
function contentfreaks_load_more_podcast_episodes() {
    check_ajax_referer('contentfreaks_ajax_nonce', 'nonce');
    
    $offset = intval($_POST['offset']);
    $limit = intval($_POST['limit']);
    
    $episodes_query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => $limit,
        'offset' => $offset,
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'orderby' => 'date',
        'order' => 'DESC'
    ));

    if (!$episodes_query->have_posts()) {
        wp_die('no_more_episodes');
    }
    
    ob_start();
    while ($episodes_query->have_posts()) : $episodes_query->the_post();
        // カスタムフィールドを取得
        $audio_url = get_post_meta(get_the_ID(), 'episode_audio_url', true);
        $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
        $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
        $original_url = get_post_meta(get_the_ID(), 'episode_original_url', true);
        $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
?>
        <article class="episode-card" data-category="<?php echo esc_attr($episode_category); ?>">
            <div class="episode-thumbnail">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                <?php else : ?>
                    <div style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">🎙️</div>
                <?php endif; ?>
                
                <?php if ($episode_number) : ?>
                <div class="episode-number">EP.<?php echo esc_html($episode_number); ?></div>
                <?php endif; ?>
                
                <?php if ($duration) : ?>
                <div class="episode-duration-badge"><?php echo esc_html($duration); ?></div>
                <?php endif; ?>
                
                <?php if ($audio_url) : ?>
                <div class="episode-play-overlay" data-audio="<?php echo esc_url($audio_url); ?>">▶</div>
                <?php endif; ?>
            </div>
            
            <div class="episode-content">
                <div class="episode-date"><?php echo get_the_date('Y年n月j日'); ?></div>
                <h3 class="episode-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                
                <div class="episode-description">
                    <?php echo wp_trim_words(get_the_excerpt(), 30); ?>
                </div>
                
                <div class="episode-actions">
                    <?php if ($audio_url) : ?>
                    <button class="play-button" data-audio="<?php echo esc_url($audio_url); ?>">
                        ▶ 再生
                    </button>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>" class="read-more-btn">詳細</a>
                    <div class="episode-platforms">
                        <a href="https://open.spotify.com/show/20otj7CiCZ0hcWYkkEpnLL" class="mini-platform-link spotify" target="_blank" title="Spotifyで聴く"><?php echo get_theme_mod('spotify_icon') ? '<img src="' . esc_url(get_theme_mod('spotify_icon')) . '" alt="Spotify" style="width: 16px; height: 16px; object-fit: contain;">' : '🎧'; ?></a>
                        <a href="https://podcasts.apple.com/jp/podcast/%E3%82%B3%E3%83%B3%E3%83%86%E3%83%B3%E3%83%84%E3%83%95%E3%83%AA%E3%83%BC%E3%82%AF%E3%82%B9/id1692185758" class="mini-platform-link apple" target="_blank" title="Apple Podcastsで聴く"><?php echo get_theme_mod('apple_podcasts_icon') ? '<img src="' . esc_url(get_theme_mod('apple_podcasts_icon')) . '" alt="Apple Podcasts" style="width: 16px; height: 16px; object-fit: contain;">' : '🍎'; ?></a>
                        <a href="https://youtube.com/@contentfreaks" class="mini-platform-link youtube" target="_blank" title="YouTubeで聴く"><?php echo get_theme_mod('youtube_icon') ? '<img src="' . esc_url(get_theme_mod('youtube_icon')) . '" alt="YouTube" style="width: 16px; height: 16px; object-fit: contain;">' : '📺'; ?></a>
                    </div>
                </div>
            </div>
        </article>
<?php 
    endwhile;
    wp_reset_postdata();
    
    echo ob_get_clean();
    wp_die();
}
add_action('wp_ajax_load_more_podcast_episodes', 'contentfreaks_load_more_podcast_episodes');
add_action('wp_ajax_nopriv_load_more_podcast_episodes', 'contentfreaks_load_more_podcast_episodes');

/**
 * WordPressメニューの登録
 */
function contentfreaks_register_menus() {
    register_nav_menus(array(
        'primary' => 'プライマリメニュー（ヘッダー）',
        'footer' => 'フッターメニュー',
    ));
}
add_action('init', 'contentfreaks_register_menus');

/**
 * ContentFreaks専用のbody_classを追加
 */
function contentfreaks_body_classes($classes) {
    $classes[] = 'contentfreaks-theme';
    $classes[] = 'has-contentfreaks-header';
    
    if (wp_is_mobile()) {
        $classes[] = 'mobile';
    }
    
    return $classes;
}
add_filter('body_class', 'contentfreaks_body_classes');

/**
 * Cocoonのヘッダー・フッターを無効化
 */
function contentfreaks_disable_cocoon_elements() {
    // Cocoonのヘッダー要素を削除
    remove_action('wp_head', 'cocoon_header_meta_tags');
    remove_action('cocoon_header', 'cocoon_header_tag');
    
    // Cocoonのデフォルトナビゲーションを無効化
    add_filter('cocoon_is_header_enable', '__return_false');
    add_filter('cocoon_is_footer_enable', '__return_false');
    add_filter('cocoon_is_mobile_header_enable', '__return_false');
    add_filter('cocoon_is_mobile_footer_enable', '__return_false');
}
add_action('init', 'contentfreaks_disable_cocoon_elements', 1);

/**
 * テーマサポートとメニューの登録
 */
function contentfreaks_theme_setup() {
    // カスタムメニューのサポートを追加
    add_theme_support('menus');
    
    // メニューの場所を登録
    register_nav_menus(array(
        'primary' => 'プライマリメニュー',
        'header' => 'ヘッダーメニュー',
        'footer' => 'フッターメニュー',
    ));
}
add_action('after_setup_theme', 'contentfreaks_theme_setup');

/**
 * ページのURLを取得するヘルパー関数
 */
function contentfreaks_get_page_url($slug) {
    $page = get_page_by_path($slug);
    if ($page) {
        return get_permalink($page->ID);
    }
    return home_url('/' . $slug . '/');
}

/**
 * 必要なページが存在するかチェックし、なければ作成する
 */
function contentfreaks_create_pages() {
    $pages = array(
        'blog' => array(
            'title' => 'ブログ',
            'template' => 'page-blog.php'
        ),
        'episodes' => array(
            'title' => 'エピソード',
            'template' => 'page-episodes.php'
        ),
        'profile' => array(
            'title' => 'プロフィール',
            'template' => 'page-profile.php'
        ),
        'history' => array(
            'title' => '歴史',
            'template' => 'page-history.php'
        )
    );
    
    foreach ($pages as $slug => $page_data) {
        $existing_page = get_page_by_path($slug);
        if (!$existing_page) {
            $page_id = wp_insert_post(array(
                'post_title' => $page_data['title'],
                'post_name' => $slug,
                'post_status' => 'publish',
                'post_type' => 'page'
            ));
            
            if ($page_id && !is_wp_error($page_id)) {
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            }
        }
    }
}
add_action('init', 'contentfreaks_create_pages');

/**
 * クエリ変数を追加
 */
function contentfreaks_add_query_vars($vars) {
    $vars[] = 'episodes';
    return $vars;
}
add_filter('query_vars', 'contentfreaks_add_query_vars');

/**
 * テンプレート読み込み
 */
function contentfreaks_template_include($template) {
    if (get_query_var('episodes')) {
        $episodes_template = get_stylesheet_directory() . '/archive-episodes.php';
        if (file_exists($episodes_template)) {
            return $episodes_template;
        }
    }
    return $template;
}
add_filter('template_include', 'contentfreaks_template_include');

/**
 * リライトルールを初期化（テーマ有効化時）
 */
function contentfreaks_flush_rewrite_rules() {
    contentfreaks_add_rewrite_rules();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'contentfreaks_flush_rewrite_rules');

/**
 * リライトルールを強制フラッシュ（デバッグ用）
 * 注意: 本番環境では使用しないでください
 */
function contentfreaks_force_flush_rewrite_rules() {
    // URLパラメータでリライトルールをフラッシュ
    if (isset($_GET['flush_rewrite']) && $_GET['flush_rewrite'] === 'contentfreaks' && current_user_can('manage_options')) {
        contentfreaks_add_rewrite_rules();
        flush_rewrite_rules();
        wp_redirect(remove_query_arg('flush_rewrite'));
        exit;
    }
}
add_action('init', 'contentfreaks_force_flush_rewrite_rules');

/**
 * CSS読み込み状況をデバッグ（開発環境のみ）
 */
function contentfreaks_css_debug() {
    // 開発環境またはWP_DEBUGが有効な場合のみ実行
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }
    
    echo "<!-- ContentFreaks CSS Debug Info -->\n";
    echo "<script>\n";
    echo "console.log('ContentFreaks CSS Debug:');\n";
    echo "console.log('Theme Directory:', '" . get_stylesheet_directory_uri() . "');\n";
    echo "console.log('CSS Files:');\n";
    echo "console.log('1. Cocoon Style:', '" . get_template_directory_uri() . "/style.css');\n";
    echo "console.log('2. Child Main:', '" . get_stylesheet_directory_uri() . "/style.css');\n";
    echo "console.log('3. ContentFreaks Final:', '" . get_stylesheet_directory_uri() . "/contentfreaks-final.css');\n";
    
    // CSSファイルの存在確認
    $css_files = array(
        'style.css' => get_stylesheet_directory() . '/style.css',
        'contentfreaks-final.css' => get_stylesheet_directory() . '/contentfreaks-final.css'
    );
    
    foreach ($css_files as $name => $path) {
        $exists = file_exists($path) ? 'EXISTS' : 'MISSING';
        $size = file_exists($path) ? filesize($path) : 0;
        echo "console.log('$name: $exists ($size bytes)');\n";
    }
    
    echo "</script>\n";
    echo "<!-- End ContentFreaks CSS Debug -->\n";
}
add_action('wp_head', 'contentfreaks_css_debug');

/**
 * Cocoonの競合するスタイルを無効化
 */
function contentfreaks_disable_conflicting_styles() {
    // Cocoonの一部スタイルを無効化してContentFreaks専用スタイルを優先
    wp_dequeue_style('cocoon-child-style'); // 子テーマの自動読み込みを無効化
    
    // Cocoonのヘッダー関連CSSを無効化
    add_filter('cocoon_header_style_enable', '__return_false');
    add_filter('cocoon_header_layout_enable', '__return_false');
}
add_action('wp_enqueue_scripts', 'contentfreaks_disable_conflicting_styles', 5);

/**
 * ContentFreaks専用のボディクラスを追加
 */
function contentfreaks_enhanced_body_class($classes) {
    $classes[] = 'contentfreaks-custom-header';
    $classes[] = 'has-contentfreaks-header';
    
    // モバイル判定
    if (wp_is_mobile()) {
        $classes[] = 'mobile';
    }
    
    return $classes;
}
add_filter('body_class', 'contentfreaks_enhanced_body_class');
