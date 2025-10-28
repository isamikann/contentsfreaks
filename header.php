<?php
/**
 * ContentFreaks専用ヘッダーテンプレート
 * Cocoonのデフォルトヘッダーを無効化してContentFreaks専用ヘッダーを表示
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="Z9v6pZ2Afg4DhkWq57tbHZYr9xo78IqWw3k1tTBNvDA" />
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- DNS Prefetch for external resources -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    
    <!-- フォントプリロード（パフォーマンス最適化） -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+JP:wght@400;500;700;900&display=swap">
    
    <!-- クリティカルCSS（インライン） -->
    <style>
    /* Above-the-fold Critical CSS */
    :root {
        --primary: #f7ff0b;
        --accent: #ff6b35;
        --black: #1a1a1a;
        --white: #ffffff;
        --text-primary: #1a1a1a;
    }
    body {
        margin: 0;
        font-family: 'Inter', 'Noto Sans JP', sans-serif;
        color: var(--text-primary);
        background: var(--white);
    }
    .minimal-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        z-index: 1000;
        height: 70px;
        display: flex;
        align-items: center;
    }
    .header-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    /* アクセシビリティ: スキップリンク */
    .skip-link {
        position: absolute;
        top: -100px;
        left: 0;
        background: var(--primary);
        color: var(--black);
        padding: 12px 20px;
        text-decoration: none;
        font-weight: 600;
        z-index: 10000;
        border-radius: 0 0 8px 0;
        transition: top 0.3s ease;
    }
    .skip-link:focus {
        top: 0;
        outline: 3px solid var(--accent);
        outline-offset: 2px;
    }
    @media (max-width: 768px) {
        .minimal-header {
            height: 60px;
        }
        .header-container {
            padding: 0 1.5rem;
        }
    }
    @media (max-width: 480px) {
        .minimal-header {
            height: 55px;
        }
        .header-container {
            padding: 0 1rem;
        }
    }
    </style>
    
    <?php wp_head(); ?>
    
    <style>
    /* ==========================================================================
       ContentFreaks モダンミニマルヘッダー専用CSS
       ========================================================================== */

    /* ヘッダー基本スタイル */
    .minimal-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        z-index: 1000;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .minimal-header.scrolled {
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    /* ブランド/ホームリンク */
    .brand-home {
        flex-shrink: 0;
        display: flex;
        align-items: center;
    }

    .brand-link {
        text-decoration: none;
        color: inherit;
        display: flex;
        align-items: center;
        transition: transform 0.2s ease;
    }

    .brand-link:hover {
        transform: translateY(-1px);
    }

    .brand-container {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .brand-icon {
        font-size: var(--nav-primary);
        line-height: 1;
        transition: transform 0.3s ease;
    }

    .brand-link:hover .brand-icon {
        transform: rotate(5deg);
    }

    .brand-logo-image {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        object-fit: cover;
    }

    .brand-text {
        font-size: var(--card-title);
        font-weight: 600;
        color: #1a1a1a;
        letter-spacing: -0.02em;
        line-height: 1;
        margin: 0;
    }

    /* 現在ページ表示 */
    .current-page-indicator {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-title {
        display: none;
        margin: 0;
        line-height: 1;
    }

    /* ハンバーガーメニュー */
    .menu-trigger {
        flex-shrink: 0;
        display: flex;
        align-items: center;
    }

    .minimal-hamburger {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        transition: all 0.3s ease;
        height: 44px; /* タッチターゲット最小サイズ */
        width: 44px;
    }

    .minimal-hamburger:hover {
        transform: translateY(-1px);
    }

    .hamburger-icon {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        position: relative;
        width: 20px;
        height: 14px;
    }

    .hamburger-icon .line {
        width: 100%;
        height: 2px;
        background: #1a1a1a;
        border-radius: 1px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: center;
    }

    /* メニューアクティブ時のハンバーガーアニメーション */
    .minimal-hamburger.active .line-1 {
        transform: rotate(45deg) translate(5px, 5px);
    }

    .minimal-hamburger.active .line-2 {
        opacity: 0;
        transform: scaleX(0);
    }

    .minimal-hamburger.active .line-3 {
        transform: rotate(-45deg) translate(5px, -5px);
    }

    /* スライドメニュー */
    .menu-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1001;
        backdrop-filter: blur(4px);
    }

    .menu-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .slide-menu-container {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: min(400px, 85vw);
        background: #ffffff;
        transform: translateX(100%);
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1002;
        display: flex;
        flex-direction: column;
        box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
    }

    .slide-menu-container.active {
        transform: translateX(0);
    }

    .slide-menu-content {
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* メニューヘッダー */
    .menu-header {
        padding: 2rem 2rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .menu-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .menu-brand-icon {
        font-size: 1.5rem;
        line-height: 1;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .menu-brand-icon .brand-logo-image {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        object-fit: cover;
    }

    .menu-brand-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
        letter-spacing: -0.02em;
    }

    .menu-close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        position: relative;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        transition: background-color 0.2s ease;
    }

    .menu-close:hover {
        background: rgba(0, 0, 0, 0.05);
    }

    .close-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .close-line {
        position: absolute;
        width: 16px;
        height: 2px;
        background: #666;
        border-radius: 1px;
        transition: all 0.3s ease;
    }

    .close-line:first-child {
        transform: rotate(45deg);
    }

    .close-line:last-child {
        transform: rotate(-45deg);
    }

    /* メニューナビゲーション */
    .menu-navigation {
        flex: 1;
        overflow-y: auto;
        padding: 1rem 0;
    }

    .nav-section {
        margin-bottom: 2rem;
    }

    .nav-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin: 0 2rem 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .nav-list {
        list-style: none;
        margin: 0;
        padding: 0;
        text-align: left;
    }

    .nav-item {
        margin: 0;
        text-align: left;
    }

    .nav-link {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 1rem 2rem;
        text-decoration: none;
        color: #333;
        transition: all 0.2s ease;
        position: relative;
        text-align: left;
    }

    .nav-link:hover {
        background: rgba(0, 0, 0, 0.03);
        color: #1a1a1a;
    }

    .nav-link:active {
        background: rgba(0, 0, 0, 0.05);
    }

    .nav-icon {
        font-size: 1.2rem;
        margin-right: 1rem;
        width: 24px;
        text-align: center;
        line-height: 1;
    }

    .nav-text {
        font-size: 1rem;
        font-weight: 500;
        flex: 1;
    }

    /* プラットフォームリンク */
    .platform-nav .nav-link {
        padding: 0.75rem 2rem;
    }

    .platform-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        margin-right: 1rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 4px;
    }

    .spotify-icon {
        background: #1DB954;
        color: white;
    }

    .apple-icon {
        background: #A855F7;
        color: white;
    }

    .youtube-icon {
        background: #FF0000;
        color: white;
    }

    .platform-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }

    .platform-text {
        font-size: 0.95rem;
        font-weight: 500;
    }

    .external-indicator {
        font-size: 0.8rem;
        color: #999;
        margin-left: auto;
    }

    /* メニューフッター */
    .menu-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid rgba(0, 0, 0, 0.08);
        background: rgba(0, 0, 0, 0.02);
    }

    .copyright-text {
        font-size: 0.8rem;
        color: #999;
        text-align: center;
        margin: 0;
    }

    /* レスポンシブデザイン */
    @media (max-width: 768px) {
        .header-container {
            padding: 0 1.5rem;
            height: 60px;
            display: flex;
            align-items: center;
        }
        
        .minimal-hamburger {
            height: 40px;
            width: 40px;
            padding: 0.375rem;
        }
        
        .brand-text {
            font-size: 1.1rem;
            line-height: 1;
        }
        
        .brand-icon {
            font-size: 1.3rem;
            line-height: 1;
        }
        
        .brand-logo-image {
            width: 28px;
            height: 28px;
        }
        
        .current-page-indicator {
            display: none; /* モバイルでは非表示 */
        }
        
        .slide-menu-container {
            width: min(350px, 90vw);
        }
        
        .menu-header {
            padding: 1.5rem 1.5rem 1rem;
        }
        
        .nav-link {
            padding: 0.875rem 1.5rem;
        }
        
        .platform-nav .nav-link {
            padding: 0.75rem 1.5rem;
        }
        
        .section-title {
            margin: 0 1.5rem 1rem;
        }
        
        .menu-footer {
            padding: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .header-container {
            padding: 0 1rem;
            height: 55px;
            display: flex;
            align-items: center;
        }
        
        .minimal-hamburger {
            height: 36px;
            width: 36px;
            padding: 0.25rem;
        }
        
        .hamburger-icon {
            width: 18px;
            height: 12px;
        }
        
        .brand-container {
            gap: 0.5rem;
        }
        
        .brand-text {
            font-size: 1rem;
            line-height: 1;
        }
        
        .brand-icon {
            font-size: 1.2rem;
            line-height: 1;
        }
        
        .brand-logo-image {
            width: 24px;
            height: 24px;
        }
        
        .slide-menu-container {
            width: 100vw;
        }
    }

    /* アニメーション */
    @media (prefers-reduced-motion: no-preference) {
        .nav-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-link:hover {
            transform: translateX(4px);
        }
        
        .platform-link:hover .external-indicator {
            transform: translate(2px, -2px);
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .minimal-header {
            background: rgba(26, 26, 26, 0.75);
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }
        
        .minimal-header.scrolled {
            background: rgba(26, 26, 26, 0.85);
        }
        
        .brand-text,
        .menu-brand-name {
            color: #ffffff;
        }
        
        .page-title {
            display: none;
        }
        
        .hamburger-icon .line {
            background: #ffffff;
        }
        
        .slide-menu-container {
            background: #1a1a1a;
        }
        
        .menu-header {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link {
            color: #e0e0e0;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }
        
        .section-title {
            color: #666;
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }
        
        .menu-footer {
            border-top-color: rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.02);
        }
        
        .close-line {
            background: #999;
        }
    }

    /* フォーカス表示（アクセシビリティ） */
    .brand-link:focus,
    .minimal-hamburger:focus,
    .menu-close:focus,
    .nav-link:focus {
        outline: 2px solid #007cba;
        outline-offset: 2px;
        border-radius: 4px;
    }

    /* スクロール時のヘッダー効果用JavaScript */
    body {
        padding-top: 70px; /* ヘッダーの高さ分のパディング */
    }

    /* フロントページ専用調整 - コンテンツとヘッダーを重ねて表示 */
    body.home {
        padding-top: 0; /* フロントページではパディングを削除 */
    }

    /* フロントページのヒーローセクションをヘッダーと重ねる */
    body.home .podcast-hero {
        margin-top: 0;
        padding-top: 70px; /* ヘッダーの高さ分の内部パディング */
    }

    @media (max-width: 768px) {
        body {
            padding-top: 60px;
        }
        
        body.home {
            padding-top: 0;
        }
        
        body.home .podcast-hero {
            padding-top: 60px;
        }
    }

    @media (max-width: 480px) {
        body {
            padding-top: 55px;
        }
        
        body.home {
            padding-top: 0;
        }
        
        body.home .podcast-hero {
            padding-top: 55px;
        }
    }
    </style>

    <script>
    // ヘッダーのスクロール効果とメニュー制御
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.querySelector('.minimal-header');
        const hamburger = document.querySelector('.minimal-hamburger');
        const overlay = document.querySelector('.menu-overlay');
        const slideMenu = document.querySelector('.slide-menu-container');
        const closeBtn = document.querySelector('.menu-close');
        let isMenuOpen = false;

        // スクロール時のヘッダー効果
        let lastScrollY = window.scrollY;
        
        function updateHeader() {
            const currentScrollY = window.scrollY;
            
            if (currentScrollY > 10) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            lastScrollY = currentScrollY;
        }

        // メニュー開閉
        function toggleMenu() {
            isMenuOpen = !isMenuOpen;
            
            hamburger.classList.toggle('active', isMenuOpen);
            overlay.classList.toggle('active', isMenuOpen);
            slideMenu.classList.toggle('active', isMenuOpen);
            hamburger.setAttribute('aria-expanded', isMenuOpen);
            
            // ボディのスクロールを制御
            document.body.style.overflow = isMenuOpen ? 'hidden' : '';
        }

        function closeMenu() {
            if (isMenuOpen) {
                toggleMenu();
            }
        }

        // イベントリスナー
        window.addEventListener('scroll', updateHeader, { passive: true });
        hamburger.addEventListener('click', toggleMenu);
        closeBtn.addEventListener('click', closeMenu);
        overlay.addEventListener('click', closeMenu);

        // ESCキーでメニューを閉じる
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isMenuOpen) {
                closeMenu();
            }
        });

        // メニュー内のリンククリック時にメニューを閉じる
        const menuLinks = document.querySelectorAll('.nav-link');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                // 外部リンクでない場合のみメニューを閉じる
                if (!this.hasAttribute('target')) {
                    setTimeout(closeMenu, 100);
                }
            });
        });

        // 初期化
        updateHeader();
    });
    </script>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- スキップリンク（アクセシビリティ向上） -->
<a href="#main-content" class="skip-link">メインコンテンツへスキップ</a>

<!-- ContentFreaks専用モダンミニマルヘッダー -->
<header id="contentfreaks-header" class="minimal-header" role="banner">
    <div class="header-container">
        <!-- ブランドロゴ/ホーム（左端） -->
        <div class="brand-home">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="brand-link" aria-label="ContentFreaks - ホームに戻る">
                <div class="brand-container">
                    <?php
                    // カスタマイザーで設定されたホームアイコンを取得
                    $home_icon_image = get_theme_mod('home_icon_image');
                    if ($home_icon_image) {
                        // カスタム画像が設定されている場合
                        echo '<img src="' . esc_url($home_icon_image) . '" alt="ContentFreaksロゴ" class="brand-logo-image">';
                    } else {
                        // デフォルトのモダンなアイコン
                        echo '<div class="brand-icon" aria-hidden="true">🎙</div>';
                    }
                    ?>
                    <span class="brand-text">ContentFreaks</span>
                </div>
            </a>
        </div>

        <!-- 中央の現在ページ表示 -->
        <div class="current-page-indicator" aria-live="polite">
            <span class="page-title">
                <?php
                if (is_home() || is_front_page()) {
                    echo 'ホーム';
                } elseif (is_single()) {
                    echo 'エピソード';
                } elseif (is_page()) {
                    echo get_the_title();
                } else {
                    echo get_the_archive_title();
                }
                ?>
            </span>
        </div>

        <!-- ミニマルハンバーガーメニュー（右端） -->
        <div class="menu-trigger">
            <button class="minimal-hamburger" aria-label="メニューを開く" aria-expanded="false" aria-controls="minimal-menu">
                <span class="hamburger-icon">
                    <span class="line line-1"></span>
                    <span class="line line-2"></span>
                    <span class="line line-3"></span>
                </span>
            </button>
        </div>
    </div>
</header>

<!-- モダンミニマルスライドメニュー -->
<div class="menu-overlay" aria-hidden="true"></div>
<nav id="minimal-menu" class="slide-menu-container" role="navigation" aria-label="メインメニュー">
    <div class="slide-menu-content">
        <div class="menu-header">
            <div class="menu-brand">
                <div class="menu-brand-icon">
                    <?php
                    // カスタマイザーで設定されたホームアイコンを取得（ブランドアイコンと同じ）
                    $home_icon_image = get_theme_mod('home_icon_image');
                    if ($home_icon_image) {
                        // カスタム画像が設定されている場合
                        echo '<img src="' . esc_url($home_icon_image) . '" alt="ContentFreaksロゴ" class="brand-logo-image">';
                    } else {
                        // デフォルトのモダンなアイコン
                        echo '<span aria-hidden="true">🎙</span>';
                    }
                    ?>
                </div>
                <span class="menu-brand-name">ContentFreaks</span>
            </div>
            <button class="menu-close" aria-label="メニューを閉じる">
                <span class="close-icon">
                    <span class="close-line"></span>
                    <span class="close-line"></span>
                </span>
            </button>
        </div>
        
        <div class="menu-navigation">
            <!-- メインナビゲーション -->
            <div class="nav-section main-nav">
                <ul class="nav-list" role="list">
                    <li class="nav-item">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link">
                            <span class="nav-icon" aria-hidden="true">🏠</span>
                            <span class="nav-text">ホーム</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('episodes'))); ?>" class="nav-link">
                            <span class="nav-icon" aria-hidden="true">🎙</span>
                            <span class="nav-text">エピソード</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('blog'))); ?>" class="nav-link">
                            <span class="nav-icon">📝</span>
                            <span class="nav-text">ブログ</span>
                        </a>
                    </li>
                    <?php
                    $profile_page = get_page_by_path('profile');
                    if ($profile_page) :
                    ?>
                    <li class="nav-item">
                        <a href="<?php echo esc_url(get_permalink($profile_page->ID)); ?>" class="nav-link">
                            <span class="nav-icon">👤</span>
                            <span class="nav-text">プロフィール</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php
                    $history_page = get_page_by_path('history');
                    if ($history_page) :
                    ?>
                    <li class="nav-item">
                        <a href="<?php echo esc_url(get_permalink($history_page->ID)); ?>" class="nav-link">
                            <span class="nav-icon">📚</span>
                            <span class="nav-text">コンフリの歩み</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- プラットフォームリンク -->
            <div class="nav-section platform-nav">
                <h3 class="section-title">聴く</h3>
                <ul class="nav-list platform-list">
                    <li class="nav-item platform-item">
                        <a href="https://open.spotify.com/show/20otj7CiCZ0hcWYkkEpnLL" class="nav-link platform-link" target="_blank" rel="noopener">
                            <span class="platform-icon spotify-icon">
                                <?php
                                $spotify_icon = get_theme_mod('spotify_icon');
                                if ($spotify_icon) {
                                    echo '<img src="' . esc_url($spotify_icon) . '" alt="Spotify" class="platform-image">';
                                } else {
                                    echo 'S';
                                }
                                ?>
                            </span>
                            <span class="platform-text">Spotify</span>
                        </a>
                    </li>
                    <li class="nav-item platform-item">
                        <a href="https://podcasts.apple.com/jp/podcast/%E3%82%B3%E3%83%B3%E3%83%86%E3%83%B3%E3%83%84%E3%83%95%E3%83%AA%E3%83%BC%E3%82%AF%E3%82%B9/id1692185758" class="nav-link platform-link" target="_blank" rel="noopener">
                            <span class="platform-icon apple-icon">
                                <?php
                                $apple_icon = get_theme_mod('apple_podcasts_icon');
                                if ($apple_icon) {
                                    echo '<img src="' . esc_url($apple_icon) . '" alt="Apple Podcasts" class="platform-image">';
                                } else {
                                    echo '';
                                }
                                ?>
                            </span>
                            <span class="platform-text">Apple Podcasts</span>
                        </a>
                    </li>
                    <li class="nav-item platform-item">
                        <a href="https://youtube.com/@contentfreaks" class="nav-link platform-link" target="_blank" rel="noopener">
                            <span class="platform-icon youtube-icon">
                                <?php
                                $youtube_icon = get_theme_mod('youtube_icon');
                                if ($youtube_icon) {
                                    echo '<img src="' . esc_url($youtube_icon) . '" alt="YouTube" class="platform-image">';
                                } else {
                                    echo '▶';
                                }
                                ?>
                            </span>
                            <span class="platform-text">YouTube</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <div class="menu-footer">
            <p class="copyright-text">© 2025 ContentFreaks</p>
        </div>
    </div>
</div>

<?php
/**
 * フォールバックメニュー（メニューが設定されていない場合）
 */
function contentfreaks_fallback_menu() {
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '" class="current-menu-item">ホーム</a></li>';
    
    // 主要ページへの直接リンク
    $episodes_page = get_page_by_path('episodes');
    if ($episodes_page) {
        echo '<li><a href="' . esc_url(get_permalink($episodes_page->ID)) . '">ポッドキャスト</a></li>';
    }
    
    $blog_page = get_page_by_path('blog');
    if ($blog_page) {
        echo '<li><a href="' . esc_url(get_permalink($blog_page->ID)) . '">ブログ</a></li>';
    }
    
    // その他の固定ページを動的に取得
    $pages = get_pages(array(
        'post_status' => 'publish',
        'number' => 5,
        'sort_column' => 'menu_order',
        'exclude' => array(
            $episodes_page ? $episodes_page->ID : 0,
            $blog_page ? $blog_page->ID : 0
        )
    ));
    
    foreach ($pages as $page) {
        if ($page->post_name !== 'home') { // ホームページは除外
            echo '<li><a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html($page->post_title) . '</a></li>';
        }
    }
    
    echo '</ul>';
}

/**
 * モバイル用フォールバックメニュー
 */
function contentfreaks_mobile_fallback_menu() {
    echo '<ul class="mobile-nav-list">';
    echo '<li><a href="' . esc_url(home_url('/')) . '" class="current-menu-item">ホーム</a></li>';
    
    // 主要ページへの直接リンク
    $episodes_page = get_page_by_path('episodes');
    if ($episodes_page) {
        echo '<li><a href="' . esc_url(get_permalink($episodes_page->ID)) . '">ポッドキャスト</a></li>';
    }
    
    $blog_page = get_page_by_path('blog');
    if ($blog_page) {
        echo '<li><a href="' . esc_url(get_permalink($blog_page->ID)) . '">ブログ</a></li>';
    }
    
    // その他の固定ページを動的に取得
    $pages = get_pages(array(
        'post_status' => 'publish',
        'number' => 5,
        'sort_column' => 'menu_order',
        'exclude' => array(
            $episodes_page ? $episodes_page->ID : 0,
            $blog_page ? $blog_page->ID : 0
        )
    ));
    
    foreach ($pages as $page) {
        if ($page->post_name !== 'home') {
            echo '<li><a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html($page->post_title) . '</a></li>';
        }
    }
    
    echo '</ul>';
}
?>

<div id="page" class="site">
    <div id="content" class="site-content">
