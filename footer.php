    </div><!-- #content -->
</div><!-- #page -->

<!-- ContentFreaks専用フッター -->
<footer id="contentfreaks-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3><?php bloginfo('name'); ?></h3>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a></li>
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('episodes'))); ?>">エピソード</a></li>
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('blog'))); ?>">ブログ</a></li>
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('profile'))); ?>">プロフィール</a></li>
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('history'))); ?>">コンフリの歩み</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>コンテンツ</h3>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('episodes'))); ?>">最新エピソード</a></li>
                <li><a href="<?php echo esc_url(get_feed_link()); ?>">RSS</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>プラットフォーム</h3>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(CONTENTFREAKS_SPOTIFY_URL); ?>" target="_blank" rel="noopener">Spotify</a></li>
                <li><a href="<?php echo esc_url(CONTENTFREAKS_APPLE_URL); ?>" target="_blank" rel="noopener">Apple Podcasts</a></li>
                <li><a href="<?php echo esc_url(CONTENTFREAKS_YOUTUBE_URL); ?>" target="_blank" rel="noopener">YouTube</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>お問い合わせ</h3>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>">お問い合わせフォーム</a></li>
                <li><a href="<?php echo esc_url(get_privacy_policy_url()); ?>">プライバシーポリシー</a></li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo wp_date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
    </div>
</footer>

<!-- モバイルフローティングCTA（プラットフォーム直リンクバー） -->
<div class="mobile-listen-bar" id="mobile-listen-bar">
    <a href="<?php echo esc_url(CONTENTFREAKS_SPOTIFY_URL); ?>" target="_blank" rel="noopener" class="listen-bar-btn listen-bar-spotify">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
        Spotify
    </a>
    <a href="<?php echo esc_url(CONTENTFREAKS_APPLE_URL); ?>" target="_blank" rel="noopener" class="listen-bar-btn listen-bar-apple">
        <?php
        $apple_cta_icon = get_theme_mod('apple_podcasts_icon');
        if ($apple_cta_icon) {
            echo '<img src="' . esc_url($apple_cta_icon) . '" alt="Apple Podcasts" width="18" height="18" style="border-radius:3px;">';
        } else {
            echo '🎧';
        }
        ?>
        Apple
    </a>
    <a href="<?php echo esc_url(CONTENTFREAKS_YOUTUBE_URL); ?>" target="_blank" rel="noopener" class="listen-bar-btn listen-bar-youtube">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
        YouTube
    </a>
</div>

<?php wp_footer(); ?>

<!-- ContentFreaks専用JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 不要なmobile-menu-toggleがあれば除去
    const oldMobileToggle = document.querySelector('.mobile-menu-toggle');
    if (oldMobileToggle) {
        oldMobileToggle.style.display = 'none';
        oldMobileToggle.remove();
    }

    // ハンバーガーメニューの制御（統合版）
    const hamburgerToggle = document.querySelector('.hamburger-toggle');
    const slideMenu = document.querySelector('.slide-menu');
    const slideMenuOverlay = document.querySelector('.slide-menu-overlay');
    const slideMenuClose = document.querySelector('.slide-menu-close');
    const body = document.body;
    
    // ハンバーガーメニューの開閉
    if (hamburgerToggle && slideMenu && slideMenuOverlay) {
        hamburgerToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            slideMenu.classList.toggle('active');
            slideMenuOverlay.classList.toggle('active');
            body.classList.toggle('mobile-menu-open');
        });
        
        // オーバーレイクリックで閉じる
        slideMenuOverlay.addEventListener('click', function() {
            hamburgerToggle.classList.remove('active');
            slideMenu.classList.remove('active');
            slideMenuOverlay.classList.remove('active');
            body.classList.remove('mobile-menu-open');
        });
        
        // 閉じるボタンで閉じる
        if (slideMenuClose) {
            slideMenuClose.addEventListener('click', function() {
                hamburgerToggle.classList.remove('active');
                slideMenu.classList.remove('active');
                slideMenuOverlay.classList.remove('active');
                body.classList.remove('mobile-menu-open');
            });
        }

        // Escキーで閉じる
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && slideMenu.classList.contains('active')) {
                hamburgerToggle.classList.remove('active');
                slideMenu.classList.remove('active');
                slideMenuOverlay.classList.remove('active');
                body.classList.remove('mobile-menu-open');
                hamburgerToggle.focus();
            }
        });

        // フォーカストラップ：メニュー内でTabキーを循環
        slideMenu.addEventListener('keydown', function(e) {
            if (e.key !== 'Tab' || !slideMenu.classList.contains('active')) return;
            var focusable = slideMenu.querySelectorAll('a[href], button, input, [tabindex]:not([tabindex="-1"])');
            if (focusable.length === 0) return;
            var first = focusable[0];
            var last = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        });
    }
    
    // 検索モーダルの制御
    const searchToggle = document.querySelector('.search-toggle');
    const searchModal = document.querySelector('.search-modal');
    const searchClose = document.querySelector('.search-close');
    
    if (searchToggle && searchModal && searchClose) {
        searchToggle.addEventListener('click', function() {
            searchModal.classList.add('active');
            const searchInput = searchModal.querySelector('.search-input');
            if (searchInput) {
                setTimeout(() => searchInput.focus(), 300);
            }
        });
        
        searchClose.addEventListener('click', function() {
            searchModal.classList.remove('active');
        });
        
        searchModal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    }
    
    // ヘッダーのスクロール効果
    const header = document.getElementById('contentfreaks-header');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // スクロール時の背景効果
        if (scrollTop > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScrollTop = scrollTop;
    });
    
    // 外部リンクの処理
    const externalLinks = document.querySelectorAll('a[href^="http"]:not([href*="' + window.location.hostname + '"])');
    externalLinks.forEach(function(link) {
        link.setAttribute('target', '_blank');
        link.setAttribute('rel', 'noopener noreferrer');
    });
});
</script>

</body>
</html>
