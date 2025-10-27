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
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ContentFreaks専用ヘッダー -->
<header id="contentfreaks-header">
    <div class="header-container">
        <!-- サイトブランディング -->
        <div class="site-branding">
            <?php
            $custom_logo_id = get_theme_mod('custom_logo');
            if ($custom_logo_id) {
                $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
                ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>" class="logo-image">
                </a>
                <?php
            } else {
                // ロゴがない場合はプレースホルダー
                ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
                    <div class="logo-placeholder" style="background: var(--primary); color: var(--black); padding: 10px 15px; border-radius: 8px; font-weight: 800; font-size: 1.2rem;">
                        CF
                    </div>
                </a>
                <?php
            }
            ?>
            
            <div class="site-info">
                <h1 class="site-title">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <?php bloginfo('name'); ?>
                    </a>
                </h1>
                <p class="site-tagline"><?php bloginfo('description'); ?></p>
            </div>
        </div>

        <!-- ナビゲーションメニュー -->
        <nav class="main-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class' => 'nav-menu',
                'container' => false,
                'fallback_cb' => 'contentfreaks_fallback_menu',
            ));
            ?>
        </nav>

        <!-- CTA・検索・モバイルメニュー -->
        <div class="header-cta">
            <!-- 検索ボタン -->
            <button class="search-toggle" aria-label="検索を開く">
                <i class="fas fa-search"></i>
            </button>
            
            <!-- CTAボタン -->
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('episodes'))); ?>" class="header-cta-btn secondary">
                エピソード一覧
            </a>
            <a href="#podcast-listen" class="header-cta-btn primary">
                <i class="fas fa-play"></i>
                ポッドキャストを聴く
            </a>
            
            <!-- モバイルハンバーガーメニュー -->
            <button class="mobile-menu-toggle" aria-label="メニューを開く">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<!-- モバイルナビメニュー -->
<div class="mobile-nav-menu">
    <?php
    wp_nav_menu(array(
        'theme_location' => 'primary',
        'menu_class' => 'mobile-nav-list',
        'container' => 'ul',
        'fallback_cb' => 'contentfreaks_mobile_fallback_menu',
    ));
    ?>
    <div class="mobile-cta">
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('episodes'))); ?>" class="header-cta-btn secondary">
            エピソード一覧
        </a>
        <a href="#podcast-listen" class="header-cta-btn primary">
            <i class="fas fa-play"></i>
            ポッドキャストを聴く
        </a>
    </div>
</div>

<!-- 検索モーダル -->
<div class="search-modal">
    <div class="search-modal-content">
        <button class="search-close" aria-label="検索を閉じる">&times;</button>
        <form class="search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="text" class="search-input" name="s" placeholder="検索キーワードを入力..." value="<?php echo get_search_query(); ?>">
            <button type="submit" class="search-submit" aria-label="検索実行">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <div class="search-suggestions">
            <h4>おすすめタグ</h4>
            <div class="search-tags">
                <?php
                // 人気のタグを表示
                $popular_tags = get_tags(array(
                    'orderby' => 'count',
                    'order' => 'DESC',
                    'number' => 6
                ));
                
                if ($popular_tags) {
                    foreach ($popular_tags as $tag) {
                        echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="search-tag">' . esc_html($tag->name) . '</a>';
                    }
                } else {
                    // フォールバック
                    $fallback_tags = array('アニメ', 'ドラマ', '映画', 'ゲーム', 'エンタメ', 'レビュー');
                    foreach ($fallback_tags as $tag) {
                        echo '<span class="search-tag">' . esc_html($tag) . '</span>';
                    }
                }
                ?>
            </div>
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
    
    // 固定ページを動的に取得
    $pages = get_pages(array(
        'post_status' => 'publish',
        'number' => 5,
        'sort_column' => 'menu_order'
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
    
    $pages = get_pages(array(
        'post_status' => 'publish',
        'number' => 5,
        'sort_column' => 'menu_order'
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
