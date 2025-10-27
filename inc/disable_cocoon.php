<?php
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
    
    // Cocoonのモバイルメニュー関連を完全無効化
    add_filter('cocoon_is_mobile_menu_enable', '__return_false');
    add_filter('cocoon_is_mobile_button_enable', '__return_false');
    add_filter('cocoon_is_mobile_header_menu_enable', '__return_false');
    
    // Cocoonのナビゲーション出力を無効化
    remove_action('cocoon_before_header', 'cocoon_mobile_header_tag');
    remove_action('cocoon_after_header', 'cocoon_mobile_menu_tag');
}
add_action('init', 'contentfreaks_disable_cocoon_elements', 1);
