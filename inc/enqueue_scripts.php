<?php
/**
 * 子テーマのスタイルとスクリプトを読み込み
 * HTTP/2 Server Push最適化対応
 */
function contentfreaks_enqueue_scripts() {
    // Google Fontsの読み込み（パフォーマンス最適化済み）
    wp_enqueue_style(
        'contentfreaks-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+JP:wght@400;500;700;900&display=swap',
        array(),
        null
    );
    
    // 親テーマのスタイルを読み込み
    wp_enqueue_style('cocoon-style', get_template_directory_uri() . '/style.css');
    
    // デザインシステム（最優先で読み込み）
    wp_enqueue_style('contentfreaks-design-system', get_stylesheet_directory_uri() . '/design-system.css', array('cocoon-style'), '1.0.0');
    wp_style_add_data('contentfreaks-design-system', 'priority', 'high');
    
    // 子テーマのメインスタイル（WordPressの標準）- 高優先度
    wp_enqueue_style('contentfreaks-main-style', get_stylesheet_directory_uri() . '/style.css', array('contentfreaks-design-system'), '1.3.0');
    wp_style_add_data('contentfreaks-main-style', 'priority', 'high');
    
    // 共通コンポーネントのスタイル（フッター等）- 高優先度
    wp_enqueue_style('contentfreaks-components', get_stylesheet_directory_uri() . '/components.css', array('contentfreaks-main-style'), '2.0.2');
    wp_style_add_data('contentfreaks-components', 'priority', 'high');
    
    // ローディング & インタラクションフィードバック
    wp_enqueue_style('contentfreaks-loading', get_stylesheet_directory_uri() . '/loading.css', array('contentfreaks-components'), '1.0.0');
    
    // マイクロインタラクション（UX向上）
    wp_enqueue_style('contentfreaks-microinteractions', get_stylesheet_directory_uri() . '/microinteractions.css', array('contentfreaks-components'), '1.0.0');
    
    // ページ別専用CSS（パフォーマンス最適化：必要なページでのみ読み込み）
    if (is_front_page()) {
        // エピソードカード用のスタイル（フロントページでも使用）- 先に読み込む
        wp_enqueue_style('contentfreaks-episodes', get_stylesheet_directory_uri() . '/page-episodes.css', array('contentfreaks-components'), '1.2.0');
        wp_enqueue_style('contentfreaks-front-page', get_stylesheet_directory_uri() . '/front-page.css', array('contentfreaks-episodes'), '1.2.0');
        wp_style_add_data('contentfreaks-front-page', 'priority', 'high');
    } elseif (is_page('episodes')) {
        wp_enqueue_style('contentfreaks-episodes', get_stylesheet_directory_uri() . '/page-episodes.css', array('contentfreaks-components'), '1.2.0');
    } elseif (is_page('blog')) {
        wp_enqueue_style('contentfreaks-blog', get_stylesheet_directory_uri() . '/page-blog.css', array('contentfreaks-components'), '1.1.0');
    } elseif (is_page('history')) {
        wp_enqueue_style('contentfreaks-history', get_stylesheet_directory_uri() . '/page-history.css', array('contentfreaks-components'), '1.2.1');
    } elseif (is_page('profile')) {
        wp_enqueue_style('contentfreaks-profile', get_stylesheet_directory_uri() . '/page-profile.css', array('contentfreaks-components'), '1.1.0');
    } elseif (is_single()) {
        wp_enqueue_style('contentfreaks-single', get_stylesheet_directory_uri() . '/single.css', array('contentfreaks-components'), '1.0.0');
    }
    
    // 存在しないファイルの読み込みを無効化
    // wp_enqueue_style('contentfreaks-final-style', get_stylesheet_directory_uri() . '/contentfreaks-final.css', array('contentfreaks-components'), '2.0.0');
    
    // 存在しないJavaScriptファイルの読み込みを無効化
    // wp_enqueue_script('contentfreaks-script', get_stylesheet_directory_uri() . '/javascript.js', array('jquery'), '2.0.0', true);
    
    // 基本的なJQueryのみ利用可能にする
    wp_enqueue_script('jquery');
    
    // マイクロインタラクションのJavaScript
    wp_enqueue_script(
        'contentfreaks-microinteractions',
        get_stylesheet_directory_uri() . '/microinteractions.js',
        array(), // jQueryに依存しない
        '1.0.0',
        true // フッターで読み込み
    );
    
    // AJAX用の設定を追加（必要に応じて有効化）
    // wp_localize_script('contentfreaks-script', 'contentfreaks_ajax', array(
    //     'ajax_url' => admin_url('admin-ajax.php'),
    //     'nonce' => wp_create_nonce('contentfreaks_ajax_nonce')
    // ));
}
add_action('wp_enqueue_scripts', 'contentfreaks_enqueue_scripts');

/**
 * リソースヒントを追加してパフォーマンスを最適化
 */
function contentfreaks_resource_hints($hints, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        $hints[] = '//fonts.googleapis.com';
        $hints[] = '//fonts.gstatic.com';
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
