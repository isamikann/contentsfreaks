<?php
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
    
    // ヘッダーセクションを追加
    $wp_customize->add_section('contentfreaks_header', array(
        'title' => 'ContentFreaks ヘッダー設定',
        'priority' => 30,
        'description' => 'ヘッダーのホームアイコンをカスタマイズできます'
    ));
    
    // ホームアイコン画像設定
    $wp_customize->add_setting('home_icon_image', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh'
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control(
        $wp_customize,
        'home_icon_image',
        array(
            'label' => 'ホームアイコン画像',
            'description' => 'ホームボタンに表示する画像を選択してください。設定しない場合は🏠アイコンが表示されます。推奨サイズ: 64x64px',
            'section' => 'contentfreaks_header',
            'settings' => 'home_icon_image'
        )
    ));

    // ===== ピックアップエピソード設定 =====
    $wp_customize->add_section('contentfreaks_pickup', array(
        'title' => '今週のピックアップ',
        'priority' => 35,
    ));

    $wp_customize->add_setting('contentfreaks_pickup_episodes', array(
        'type' => 'option',
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('contentfreaks_pickup_episodes', array(
        'label' => 'ピックアップ投稿ID',
        'description' => '表示したい投稿IDをカンマ区切りで入力（例: 123,456,789）。空にするとセクション非表示。',
        'section' => 'contentfreaks_pickup',
        'type' => 'text',
    ));
}
add_action('customize_register', 'contentfreaks_customize_register');
