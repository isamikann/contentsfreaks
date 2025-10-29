<?php
/**
 * 動的ヘッダースタイル管理
 * 定数から値を動的計算してCSSを生成
 */

// 直接このファイルにアクセスすることを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ヘッダー動的スタイルの強制適用
 * （定数から値を動的計算してCSSを生成）
 */
function contentfreaks_force_header_styles() {
    // 定数から値を取得
    $header_height_desktop = CONTENTFREAKS_HEADER_HEIGHT_DESKTOP;
    $header_height_tablet = defined('CONTENTFREAKS_HEADER_HEIGHT_TABLET') ? CONTENTFREAKS_HEADER_HEIGHT_TABLET : 60;
    $header_height_mobile = CONTENTFREAKS_HEADER_HEIGHT_MOBILE;
    $header_border = CONTENTFREAKS_HEADER_BORDER;
    $admin_bar_desktop = CONTENTFREAKS_ADMIN_BAR_DESKTOP;
    $admin_bar_mobile = CONTENTFREAKS_ADMIN_BAR_MOBILE;
    $icon_size_desktop = CONTENTFREAKS_ICON_SIZE_DESKTOP;
    $icon_size_mobile = CONTENTFREAKS_ICON_SIZE_MOBILE;
    
    ?>
    <style id="contentfreaks-header-force-styles">
    /* Admin Bar強制margin-topの無効化 */
    html {
        margin-top: 0 !important;
    }
    
    /* Admin Bar自体は表示するが、htmlの余白は削除 */
    body.admin-bar #wpadminbar {
        position: fixed !important;
        top: 0 !important;
        z-index: 9999999 !important;
    }
    
    /* デスクトップでのAdmin Bar対応 - ヘッダー位置調整 */
    body.admin-bar #contentfreaks-header.modern-header,
    body.admin-bar html body #contentfreaks-header.modern-header,
    body.admin-bar #contentfreaks-header,
    body.admin_bar .modern-header#contentfreaks-header {
        top: <?php echo $admin_bar_desktop; ?>px !important;
    }
    
    /* ヘッダー灰色問題の強制解決 - 最高優先度（透明化対応） */
    body #contentfreaks-header.modern-header,
    html body #contentfreaks-header.modern-header,
    #contentfreaks-header.modern-header,
    .modern-header#contentfreaks-header {
        background: linear-gradient(135deg, rgba(26, 26, 26, 0.85) 0%, rgba(45, 45, 45, 0.85) 100%) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border-bottom: <?php echo $header_border; ?>px solid #f7ff0b !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        z-index: 999999 !important;
        box-sizing: border-box !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    body #contentfreaks-header.modern-header.scrolled,
    html body #contentfreaks-header.modern-header.scrolled,
    #contentfreaks-header.modern-header.scrolled,
    .modern-header#contentfreaks-header.scrolled {
        background: linear-gradient(135deg, rgba(26, 26, 26, 0.7) 0%, rgba(45, 45, 45, 0.7) 100%) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
    
    /* header-containerの背景色継承を防ぐ */
    #contentfreaks-header .header-container {
        background: transparent !important;
        background-color: transparent !important;
        height: <?php echo $header_height_desktop; ?>px !important;
    }
    
    /* ヘッダーコンテンツのサイズ調整 */
    #contentfreaks-header .home-link,
    #contentfreaks-header .hamburger-toggle {
        width: <?php echo $icon_size_desktop; ?>px !important;
        height: <?php echo $icon_size_desktop; ?>px !important;
    }
    
    #contentfreaks-header .home-icon-emoji {
        font-size: <?php echo floor($icon_size_desktop * 0.47); ?>px !important; /* アイコンサイズの47% */
    }
    
    #contentfreaks-header .home-icon-image {
        width: <?php echo floor($icon_size_desktop * 0.6); ?>px !important; /* アイコンサイズの60% */
        height: <?php echo floor($icon_size_desktop * 0.6); ?>px !important;
    }
    
    #contentfreaks-header .hamburger-line {
        width: <?php echo floor($icon_size_desktop * 0.43); ?>px !important; /* アイコンサイズの43% */
        height: 3px !important;
    }
    
    /* ページコンテンツ位置調整 - 動的計算 */
    /* フロントページ（ヒーローセクションがヘッダー直下に配置されるため margin-top不要） */
    body.home .site-main,
    body.front-page .site-main,
    body.home main.site-main,
    body.front-page main.site-main {
        margin-top: 0 !important; /* フロントページはヒーローが固定ヘッダー下に配置 */
    }
    
    /* 一般ページ（ブレッドクラムなし、ヘッダー分の余白が必要） */
    body:not(.home):not(.front-page) .site-main,
    body:not(.home):not(.front-page) main.site-main {
        margin-top: <?php echo $header_height_desktop + $header_border; ?>px !important;
    }
    
    <?php if (is_admin_bar_showing()): ?>
    /* Admin Bar表示時のみ適用 */
    /* フロントページ（Admin Bar分のみ追加） */
    body.admin-bar.home .site-main,
    body.admin-bar.front-page .site-main,
    body.admin-bar.home main.site-main,
    body.admin-bar.front-page main.site-main {
        margin-top: 0 !important; /* Admin Bar表示時もヒーローは固定ヘッダー下 */
    }
    
    /* 一般ページ（Admin Bar + ヘッダー） */
    body.admin-bar:not(.home):not(.front-page) .site-main,
    body.admin-bar:not(.home):not(.front-page) main.site-main {
        margin-top: <?php echo $admin_bar_desktop + $header_height_desktop + $header_border; ?>px !important;
    }
    <?php endif; ?>
    
    /* ===================================================================
       タブレットサイズでの高さ調整 (768px以下) - ヘッダー60px
       =================================================================== */
    @media (max-width: 768px) {
        body #contentfreaks-header.modern-header,
        html body #contentfreaks-header.modern-header,
        #contentfreaks-header.modern-header,
        .modern-header#contentfreaks-header {
            height: <?php echo $header_height_tablet; ?>px !important;
        }
        
        /* Admin Bar対応 - タブレット版ヘッダー位置調整 */
        body.admin-bar #contentfreaks-header.modern-header,
        body.admin-bar html body #contentfreaks-header.modern-header,
        body.admin-bar #contentfreaks-header,
        body.admin_bar .modern-header#contentfreaks-header {
            top: <?php echo $admin_bar_mobile; ?>px !important;
        }
        
        body #contentfreaks-header.modern-header.scrolled,
        html body #contentfreaks-header.modern-header.scrolled,
        #contentfreaks-header.modern-header.scrolled,
        .modern-header#contentfreaks-header.scrolled {
            height: <?php echo $header_height_tablet; ?>px !important;
        }
        
        /* タブレットでのヘッダーコンテンツサイズ調整 */
        #contentfreaks-header .header-container {
            height: <?php echo $header_height_tablet; ?>px !important;
        }
        
        /* タブレット版ページコンテンツ位置調整 */
        /* フロントページ（ヒーローセクションがヘッダー直下） */
        body.home .site-main,
        body.front-page .site-main,
        body.home main.site-main,
        body.front-page main.site-main {
            margin-top: 0 !important;
        }
        
        /* 一般ページ（ヘッダー分の余白が必要） */
        body:not(.home):not(.front-page) .site-main,
        body:not(.home):not(.front-page) main.site-main {
            margin-top: <?php echo $header_height_tablet + $header_border; ?>px !important;
        }
        
        <?php if (is_admin_bar_showing()): ?>
        /* Admin Bar表示時のみ - タブレット */
        body.admin-bar.home .site-main,
        body.admin-bar.front-page .site-main,
        body.admin-bar.home main.site-main,
        body.admin-bar.front-page main.site-main {
            margin-top: 0 !important;
        }
        
        body.admin-bar:not(.home):not(.front-page) .site-main,
        body.admin-bar:not(.home):not(.front-page) main.site-main {
            margin-top: <?php echo $admin_bar_mobile + $header_height_tablet + $header_border; ?>px !important;
        }
        <?php endif; ?>
    }
    
    /* ===================================================================
       モバイルサイズでの高さ調整 (480px以下) - ヘッダー55px
       =================================================================== */
    @media (max-width: 480px) {
        body #contentfreaks-header.modern-header,
        html body #contentfreaks-header.modern-header,
        #contentfreaks-header.modern-header,
        .modern-header#contentfreaks-header {
            height: <?php echo $header_height_mobile; ?>px !important;
        }
        
        body #contentfreaks-header.modern-header.scrolled,
        html body #contentfreaks-header.modern-header.scrolled,
        #contentfreaks-header.modern-header.scrolled,
        .modern-header#contentfreaks-header.scrolled {
            height: <?php echo $header_height_mobile; ?>px !important;
        }
        
        /* モバイルでのヘッダーコンテンツサイズ調整 */
        #contentfreaks-header .header-container {
            height: <?php echo $header_height_mobile; ?>px !important;
        }
        
        #contentfreaks-header .home-link,
        #contentfreaks-header .hamburger-toggle {
            width: <?php echo $icon_size_mobile; ?>px !important;
            height: <?php echo $icon_size_mobile; ?>px !important;
        }
        
        #contentfreaks-header .home-icon-emoji {
            font-size: <?php echo floor($icon_size_mobile * 0.5); ?>px !important; /* アイコンサイズの50% */
        }
        
        #contentfreaks-header .home-icon-image {
            width: <?php echo floor($icon_size_mobile * 0.6); ?>px !important; /* アイコンサイズの60% */
            height: <?php echo floor($icon_size_mobile * 0.6); ?>px !important;
        }
        
        #contentfreaks-header .hamburger-line {
            width: <?php echo floor($icon_size_mobile * 0.5); ?>px !important; /* アイコンサイズの50% */
            height: 2px !important;
        }
        
        /* モバイル版ページコンテンツ位置調整 */
        /* フロントページ（ヒーローセクションがヘッダー直下） */
        body.home .site-main,
        body.front-page .site-main,
        body.home main.site-main,
        body.front-page main.site-main {
            margin-top: 0 !important; /* モバイルでもヒーローは固定ヘッダー下 */
        }
        
        /* 一般ページ（ヘッダー分の余白が必要） */
        body:not(.home):not(.front-page) .site-main,
        body:not(.home):not(.front-page) main.site-main {
            margin-top: <?php echo $header_height_mobile + $header_border; ?>px !important;
        }
        
        <?php if (is_admin_bar_showing()): ?>
        /* Admin Bar表示時のみ - モバイル */
        /* フロントページ */
        body.admin-bar.home .site-main,
        body.admin-bar.front-page .site-main,
        body.admin-bar.home main.site-main,
        body.admin-bar.front-page main.site-main {
            margin-top: 0 !important; /* Admin Bar表示時もヒーローは固定ヘッダー下 */
        }
        
        /* 一般ページ */
        body.admin-bar:not(.home):not(.front-page) .site-main,
        body.admin-bar:not(.home):not(.front-page) main.site-main {
            margin-top: <?php echo $admin_bar_mobile + $header_height_mobile + $header_border; ?>px !important;
        }
        <?php endif; ?>
    }
    
    /* デスクトップ版スライドメニューの位置調整 - 動的計算 */
    .slide-menu {
        top: <?php echo $header_height_desktop + $header_border; ?>px !important;
        height: calc(100% - <?php echo $header_height_desktop + $header_border; ?>px) !important;
    }
    
    /* フロントページのデスクトップスライドメニュー */
    body.home .slide-menu,
    body.front-page .slide-menu,
    body.page-template-front-page .slide-menu {
        top: <?php echo $header_height_desktop + $header_border; ?>px !important;
        height: calc(100% - <?php echo $header_height_desktop + $header_border; ?>px) !important;
    }
    
    <?php if (is_admin_bar_showing()): ?>
    /* Admin Bar表示時のデスクトップスライドメニュー位置調整 */
    body.admin-bar .slide-menu {
        top: <?php echo $admin_bar_desktop + $header_height_desktop + $header_border; ?>px !important;
        height: calc(100% - <?php echo $admin_bar_desktop + $header_height_desktop + $header_border; ?>px) !important;
    }
    
    body.admin-bar.home .slide-menu,
    body.admin-bar.front-page .slide-menu,
    body.admin-bar.page-template-front-page .slide-menu {
        top: <?php echo $admin_bar_desktop + $header_height_desktop + $header_border; ?>px !important;
        height: calc(100% - <?php echo $admin_bar_desktop + $header_height_desktop + $header_border; ?>px) !important;
    }
    <?php endif; ?>
    
    
    /* モバイル版スライドメニューの位置調整（上書き） */
    @media (max-width: 768px) {
        .slide-menu {
            top: <?php echo $header_height_mobile + $header_border; ?>px !important;
            height: calc(100% - <?php echo $header_height_mobile + $header_border; ?>px) !important;
        }
        
        /* フロントページのモバイルスライドメニュー */
        body.home .slide-menu,
        body.front-page .slide-menu,
        body.page-template-front-page .slide-menu {
            top: <?php echo $header_height_mobile + $header_border; ?>px !important;
            height: calc(100% - <?php echo $header_height_mobile + $header_border; ?>px) !important;
        }
        
        <?php if (is_admin_bar_showing()): ?>
        /* Admin Bar表示時のモバイルスライドメニュー位置調整 */
        body.admin-bar .slide-menu {
            top: <?php echo $admin_bar_mobile + $header_height_mobile + $header_border; ?>px !important;
            height: calc(100% - <?php echo $admin_bar_mobile + $header_height_mobile + $header_border; ?>px) !important;
        }
        
        body.admin-bar.home .slide-menu,
        body.admin-bar.front-page .slide-menu,
        body.admin_bar.page-template-front-page .slide-menu {
            top: <?php echo $admin_bar_mobile + $header_height_mobile + $header_border; ?>px !important;
            height: calc(100% - <?php echo $admin_bar_mobile + $header_height_mobile + $header_border; ?>px) !important;
        }
        <?php endif; ?>
    }
    
    /* Cocoonテーマの干渉を完全ブロック */
    body .header:not(#contentfreaks-header),
    body #header:not(#contentfreaks-header),
    .cocoon-header,
    .site-header:not(#contentfreaks-header) {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        width: 0 !important;
        opacity: 0 !important;
        z-index: -1 !important;
    }
    
    /* CSSファイルからの重複するmargin-top設定を無効化 */
    main.main,
    div.sidebar {
        margin-top: 0 !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'contentfreaks_force_header_styles', 999);
