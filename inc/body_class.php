<?php
/**
 * ContentFreaks専用のbody_classを追加（統合版）
 */
function contentfreaks_body_class($classes) {
    $classes[] = 'contentfreaks-custom-header';
    $classes[] = 'contentfreaks-theme';
    $classes[] = 'has-contentfreaks-header';
    
    // モバイル判定
    if (wp_is_mobile()) {
        $classes[] = 'mobile';
    }
    
    return $classes;
}
add_filter('body_class', 'contentfreaks_body_class');
