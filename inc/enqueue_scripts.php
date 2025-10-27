<?php
/**
 * 子テーマのスタイルとスクリプトを読み込み
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
    
    // 子テーマのメインスタイル（WordPressの標準）
    wp_enqueue_style('contentfreaks-main-style', get_stylesheet_directory_uri() . '/style.css', array('cocoon-style', 'contentfreaks-fonts'), '1.2.0');
    
    // 共通コンポーネントのスタイル（フッター等）
    wp_enqueue_style('contentfreaks-components', get_stylesheet_directory_uri() . '/components.css', array('contentfreaks-main-style'), '2.0.2');
    
    // 存在しないファイルの読み込みを無効化
    // wp_enqueue_style('contentfreaks-final-style', get_stylesheet_directory_uri() . '/contentfreaks-final.css', array('contentfreaks-components'), '2.0.0');
    
    // 存在しないJavaScriptファイルの読み込みを無効化
    // wp_enqueue_script('contentfreaks-script', get_stylesheet_directory_uri() . '/javascript.js', array('jquery'), '2.0.0', true);
    
    // 基本的なJQueryのみ利用可能にする
    wp_enqueue_script('jquery');
    
    // AJAX用の設定を追加（必要に応じて有効化）
    // wp_localize_script('contentfreaks-script', 'contentfreaks_ajax', array(
    //     'ajax_url' => admin_url('admin-ajax.php'),
    //     'nonce' => wp_create_nonce('contentfreaks_ajax_nonce')
    // ));
}
add_action('wp_enqueue_scripts', 'contentfreaks_enqueue_scripts');
