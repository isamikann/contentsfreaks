<?php
/**
 * 子テーマのスタイルとスクリプトを読み込み
 * HTTP/2 Server Push最適化対応
 */
function contentfreaks_enqueue_scripts() {
    // Cocoon親テーマによるstyle.css重複読み込みを解除（自前でver管理するため）
    wp_dequeue_style('cocoon-child');
    wp_deregister_style('cocoon-child');
    
    // Cocoon親テーマによるjavascript.js自動読み込みを解除
    wp_dequeue_script('cocoon-child');
    wp_deregister_script('cocoon-child');

    // Google Fontsの読み込み（一箇所に統一）
    wp_enqueue_style(
        'contentfreaks-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;700&display=swap',
        array(),
        null
    );
    // フォントのpreconnectヒント
    wp_style_add_data('contentfreaks-fonts', 'crossorigin', 'anonymous');
    
    // 親テーマのスタイルを読み込み
    wp_enqueue_style('cocoon-style', get_template_directory_uri() . '/style.css');
    
    // デザインシステム（最優先で読み込み）
    wp_enqueue_style('contentfreaks-design-system', get_stylesheet_directory_uri() . '/design-system.css', array('cocoon-style'), '1.0.0');
    wp_style_add_data('contentfreaks-design-system', 'priority', 'high');
    
    // 子テーマのメインスタイル（WordPressの標準）- 高優先度
    wp_enqueue_style('contentfreaks-main-style', get_stylesheet_directory_uri() . '/style.css', array('contentfreaks-design-system'), '1.4.1');
    wp_style_add_data('contentfreaks-main-style', 'priority', 'high');
    
    // 共通コンポーネントのスタイル（フッター等）- 高優先度
    wp_enqueue_style('contentfreaks-components', get_stylesheet_directory_uri() . '/components.css', array('contentfreaks-main-style'), '2.0.2');
    wp_style_add_data('contentfreaks-components', 'priority', 'high');
    
    // ヘッダー専用CSS（header.phpから外部化）
    wp_enqueue_style('contentfreaks-header', get_stylesheet_directory_uri() . '/header.css', array('contentfreaks-components'), '1.0.0');
    wp_style_add_data('contentfreaks-header', 'priority', 'high');
    
    // ローディング & インタラクションフィードバック
    wp_enqueue_style('contentfreaks-loading', get_stylesheet_directory_uri() . '/loading.css', array('contentfreaks-components'), '1.0.0');
    
    // マイクロインタラクション（UX向上）
    wp_enqueue_style('contentfreaks-microinteractions', get_stylesheet_directory_uri() . '/microinteractions.css', array('contentfreaks-components'), '1.0.0');

    // UI拡張（トップに戻る、シェア、お気に入り、パンくず）
    wp_enqueue_style('contentfreaks-ui-enhancements', get_stylesheet_directory_uri() . '/ui-enhancements.css', array('contentfreaks-components'), '1.0.0');
    
    // ページ別専用CSS（パフォーマンス最適化：必要なページでのみ読み込み）
    if (is_front_page()) {
        // エピソードカード用のスタイル（フロントページでも使用）- 先に読み込む
        wp_enqueue_style('contentfreaks-episodes', get_stylesheet_directory_uri() . '/page-episodes.css', array('contentfreaks-components'), '1.2.1');
        wp_enqueue_style('contentfreaks-front-page', get_stylesheet_directory_uri() . '/front-page.css', array('contentfreaks-episodes'), '1.2.1');
        wp_style_add_data('contentfreaks-front-page', 'priority', 'high');
        // フロントページアニメーション（カウントアップ等）
        wp_enqueue_script('contentfreaks-front-page-animations', get_stylesheet_directory_uri() . '/front-page-animations.js', array(), '1.0.0', true);
    } elseif (is_page('episodes')) {
        wp_enqueue_style('contentfreaks-episodes', get_stylesheet_directory_uri() . '/page-episodes.css', array('contentfreaks-components'), '1.2.1');
        // エピソード一覧ページ専用JS（Load More、パララックス、アニメーション）
        wp_enqueue_script('contentfreaks-page-episodes', get_stylesheet_directory_uri() . '/page-episodes.js', array(), '1.0.0', true);
    } elseif (is_page('blog')) {
        wp_enqueue_style('contentfreaks-blog', get_stylesheet_directory_uri() . '/page-blog.css', array('contentfreaks-components'), '1.1.0');
    } elseif (is_page('history')) {
        wp_enqueue_style('contentfreaks-history', get_stylesheet_directory_uri() . '/page-history.css', array('contentfreaks-components'), '1.2.1');
    } elseif (is_page('profile')) {
        wp_enqueue_style('contentfreaks-profile', get_stylesheet_directory_uri() . '/page-profile.css', array('contentfreaks-components'), '1.1.0');
    } elseif (is_page('media-kit')) {
        wp_enqueue_style('contentfreaks-media-kit', get_stylesheet_directory_uri() . '/page-media-kit.css', array('contentfreaks-components'), '1.0.0');
    } elseif (is_page('contact')) {
        wp_enqueue_style('contentfreaks-contact', get_stylesheet_directory_uri() . '/page-contact.css', array('contentfreaks-components'), '1.0.0');
        wp_enqueue_script('contentfreaks-contact-js', get_stylesheet_directory_uri() . '/page-contact.js', array(), '1.0.0', true);
    } elseif (is_single()) {
        wp_enqueue_style('contentfreaks-single', get_stylesheet_directory_uri() . '/single.css', array('contentfreaks-components'), '1.0.0');
    } elseif (is_page('works')) {
        wp_enqueue_style('contentfreaks-works', get_stylesheet_directory_uri() . '/page-works.css', array('contentfreaks-components'), '1.0.0');
        wp_enqueue_script('contentfreaks-page-works', get_stylesheet_directory_uri() . '/page-works.js', array(), '1.0.0', true);
    } elseif (is_archive() || is_tag() || is_category()) {
        // タグアーカイブ、カテゴリーアーカイブページ用
        wp_enqueue_style('contentfreaks-episodes', get_stylesheet_directory_uri() . '/page-episodes.css', array('contentfreaks-components'), '1.2.1');
    }

    // 404ページ用CSS
    if (is_404()) {
        wp_enqueue_style('contentfreaks-404', get_stylesheet_directory_uri() . '/404.css', array('contentfreaks-components'), '1.0.0');
    }
    
    // マイクロインタラクションのJavaScript
    wp_enqueue_script(
        'contentfreaks-microinteractions',
        get_stylesheet_directory_uri() . '/microinteractions.js',
        array(), // jQueryに依存しない
        '1.0.0',
        true // フッターで読み込み
    );

    // UI拡張JavaScript（トップに戻る、シェア、お気に入り、AJAX検索）
    wp_enqueue_script(
        'contentfreaks-ui-enhancements',
        get_stylesheet_directory_uri() . '/ui-enhancements.js',
        array(),
        '1.0.0',
        true
    );

    // AJAX用の設定（無限スクロール+検索用nonce含む）
    $ajax_data = array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('contentfreaks_load_more'),
    );
    if (is_single()) {
        $ajax_data['post_id'] = get_the_ID();
    }
    wp_localize_script('contentfreaks-ui-enhancements', 'contentfreaks_ajax', $ajax_data);
}
add_action('wp_enqueue_scripts', 'contentfreaks_enqueue_scripts');

/**
 * リソースヒントを追加してパフォーマンスを最適化
 */
function contentfreaks_resource_hints($hints, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        $hints[] = '//fonts.googleapis.com';
        $hints[] = '//fonts.gstatic.com';
        $hints[] = '//d3ctxlq1ktw2nl.cloudfront.net';
    }
    
    if ('preconnect' === $relation_type) {
        $hints[] = array(
            'href' => 'https://fonts.googleapis.com',
            'crossorigin',
        );
        $hints[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
        $hints[] = array(
            'href' => 'https://d3ctxlq1ktw2nl.cloudfront.net',
        );
    }
    
    return $hints;
}
add_filter('wp_resource_hints', 'contentfreaks_resource_hints', 10, 2);

/**
 * クリティカルCSSの後に非クリティカルCSSを非同期で読み込む
 */
function contentfreaks_async_styles($html, $handle) {
    // 特定のスタイルを非同期で読み込む（優先度が低いもの）
    $async_styles = array(
        'cocoon-style',
    );
    
    if (in_array($handle, $async_styles)) {
        $html = str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
        $html .= '<noscript><link rel="stylesheet" href="' . esc_url(get_template_directory_uri() . '/style.css') . '"></noscript>';
    }
    
    return $html;
}
add_filter('style_loader_tag', 'contentfreaks_async_styles', 10, 2);
