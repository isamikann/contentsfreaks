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

    <!-- ヘッダーCSSは header.css として外部化済み -->

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
        </div>
        
        <div class="menu-footer">
            <p class="copyright-text">© 2025 ContentFreaks</p>
        </div>
    </div>
</nav>

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
