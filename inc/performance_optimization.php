<?php
/**
 * クリティカルCSSのインライン化
 * Above the Foldの高速レンダリング
 */

/**
 * クリティカルCSSをヘッダーにインライン出力
 */
function contentfreaks_inline_critical_css() {
    $critical_css_file = get_stylesheet_directory() . '/critical.css';
    
    if (file_exists($critical_css_file)) {
        $critical_css = file_get_contents($critical_css_file);
        
        // CSS最小化（コメント削除、空白削減）
        $critical_css = preg_replace('/\/\*.*?\*\//s', '', $critical_css); // コメント削除
        $critical_css = preg_replace('/\s+/', ' ', $critical_css); // 連続空白を1つに
        $critical_css = str_replace(array(' {', '{ ', ' }', '} ', ': ', ' ;', '; '), 
                                   array('{', '{', '}', '}', ':', ';', ';'), 
                                   $critical_css);
        
        echo '<style id="critical-css">' . $critical_css . '</style>';
    }
}
add_action('wp_head', 'contentfreaks_inline_critical_css', 1);

/**
 * 非クリティカルCSSの遅延読み込み
 */
function contentfreaks_defer_non_critical_css($html, $handle) {
    // クリティカルでないスタイルシートを遅延読み込み
    $defer_styles = array(
        'contentfreaks-loading',
        'contentfreaks-microinteractions',
        'contentfreaks-front-page',
        'contentfreaks-episodes',
        'contentfreaks-blog',
        'contentfreaks-history',
        'contentfreaks-profile',
        'contentfreaks-single'
    );
    
    if (in_array($handle, $defer_styles)) {
        // preload + 非同期読み込み
        $html = str_replace(
            "rel='stylesheet'",
            "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"",
            $html
        );
        
        // noscript フォールバック
        $href = '';
        if (preg_match('/href=[\'"]([^\'"]+)[\'"]/', $html, $matches)) {
            $href = $matches[1];
            $html .= '<noscript><link rel="stylesheet" href="' . esc_url($href) . '"></noscript>';
        }
    }
    
    return $html;
}
add_filter('style_loader_tag', 'contentfreaks_defer_non_critical_css', 10, 2);

/**
 * Google Fontsの最適化
 */
function contentfreaks_optimize_google_fonts() {
    ?>
    <!-- Google Fonts preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Font display swap for better performance -->
    <link rel="stylesheet" 
          href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+JP:wght@400;500;700;900&display=swap" 
          media="print" 
          onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" 
              href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+JP:wght@400;500;700;900&display=swap">
    </noscript>
    <?php
}
add_action('wp_head', 'contentfreaks_optimize_google_fonts', 2);

/**
 * JavaScriptの遅延実行
 */
function contentfreaks_defer_scripts($tag, $handle, $src) {
    // 遅延実行するスクリプト
    $defer_scripts = array(
        'contentfreaks-microinteractions'
    );
    
    if (in_array($handle, $defer_scripts)) {
        // async属性を追加（DOM構築を待たない）
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'contentfreaks_defer_scripts', 10, 3);

/**
 * DNS Prefetch / Preconnect
 */
function contentfreaks_resource_hints_optimization($hints, $relation_type) {
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
add_filter('wp_resource_hints', 'contentfreaks_resource_hints_optimization', 10, 2);

/**
 * Above the Fold画像のプリロード
 */
function contentfreaks_preload_hero_image() {
    if (is_front_page()) {
        // ヒーロー画像をプリロード
        $hero_image = get_theme_mod('hero_background_image');
        if ($hero_image) {
            echo '<link rel="preload" as="image" href="' . esc_url($hero_image) . '" fetchpriority="high">';
        }
        
        // ロゴ画像をプリロード
        $logo = get_theme_mod('custom_logo');
        if ($logo) {
            $logo_url = wp_get_attachment_image_src($logo, 'full');
            if ($logo_url) {
                echo '<link rel="preload" as="image" href="' . esc_url($logo_url[0]) . '" fetchpriority="high">';
            }
        }
    }
}
add_action('wp_head', 'contentfreaks_preload_hero_image', 3);

/**
 * 不要なブロックスタイルを削除
 */
function contentfreaks_remove_wp_block_styles() {
    // 使用していないブロックスタイルを削除
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style'); // WooCommerce
    wp_dequeue_style('global-styles'); // WordPress 5.9+
}
add_action('wp_enqueue_scripts', 'contentfreaks_remove_wp_block_styles', 100);

/**
 * 絵文字スクリプト削除（不要な場合）
 */
function contentfreaks_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'contentfreaks_disable_emojis');

/**
 * Embedスクリプト削除（必要に応じて）
 */
function contentfreaks_disable_embeds() {
    // wp-embed.jsの読み込みを停止
    wp_deregister_script('wp-embed');
}
add_action('wp_footer', 'contentfreaks_disable_embeds');

/**
 * Query Monitor等のデバッグツール削除（本番環境）
 */
function contentfreaks_remove_query_strings($src) {
    // クエリ文字列を削除してキャッシュヒット率向上
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'contentfreaks_remove_query_strings', 10, 1);
add_filter('script_loader_src', 'contentfreaks_remove_query_strings', 10, 1);
