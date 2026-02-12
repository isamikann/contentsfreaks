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
                <li><a href="https://open.spotify.com/show/20otj7CiCZ0hcWYkkEpnLL?si=w3Jlrpg5Ssmk0TGa_Flb8g" target="_blank" rel="noopener">Spotify</a></li>
                <li><a href="https://podcasts.apple.com/jp/podcast/%E3%82%B3%E3%83%B3%E3%83%86%E3%83%B3%E3%83%84%E3%83%95%E3%83%AA%E3%83%BC%E3%82%AF%E3%82%B9/id1692185758" target="_blank" rel="noopener">Apple Podcasts</a></li>
                <li><a href="https://youtube.com/@contentfreaks" target="_blank" rel="noopener">YouTube</a></li>
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
    <a href="https://open.spotify.com/show/20otj7CiCZ0hcWYkkEpnLL?si=w3Jlrpg5Ssmk0TGa_Flb8g" target="_blank" rel="noopener" class="listen-bar-btn listen-bar-spotify">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
        Spotify
    </a>
    <a href="https://podcasts.apple.com/jp/podcast/%E3%82%B3%E3%83%B3%E3%83%86%E3%83%B3%E3%83%84%E3%83%95%E3%83%AA%E3%83%BC%E3%82%AF%E3%82%B9/id1692185758" target="_blank" rel="noopener" class="listen-bar-btn listen-bar-apple">
        <svg width="18" height="18" viewBox="0 0 300 300" fill="currentColor"><path d="M150 0C67.16 0 0 67.16 0 150s67.16 150 150 150 150-67.16 150-150S232.84 0 150 0zm0 28.95c66.86 0 121.05 54.19 121.05 121.05S216.86 271.05 150 271.05 28.95 216.86 28.95 150 83.14 28.95 150 28.95zm0 38.68c-8.59 0-15.54 6.96-15.54 15.54 0 8.59 6.96 15.54 15.54 15.54 8.59 0 15.54-6.96 15.54-15.54 0-8.59-6.96-15.54-15.54-15.54zm-35.27 28.2c-27.97 16.98-37.08 53.53-20.28 81.64l.18.3c3.48 5.74 9.33 9.35 15.84 10.08l1.2.09c5.59.17 10.9-1.99 14.81-6.02l.24-.27c3.72-4.14 5.53-9.56 5.03-15.1l-.06-.63c-.62-5.27-3.36-10.05-7.67-13.27-8.79-6.57-11.72-18.4-6.81-28.1 4.19-8.26 12.72-13.29 21.91-13.29h1.66c9.2 0 17.72 5.03 21.91 13.29 4.91 9.7 1.98 21.53-6.81 28.1-4.3 3.22-7.05 8-7.67 13.27l-.06.63c-.5 5.54 1.31 10.96 5.03 15.1l.24.27c3.91 4.03 9.22 6.19 14.81 6.02l1.2-.09c6.51-.73 12.36-4.34 15.84-10.08l.18-.3c16.8-28.11 7.69-64.66-20.28-81.64a59.56 59.56 0 00-30.16-8.18h-4.92a59.56 59.56 0 00-30.16 8.18zm35.27 75.3c-11.08 0-20.07 8.99-20.07 20.07v28.63c0 11.08 8.99 20.07 20.07 20.07s20.07-8.99 20.07-20.07V191.2c0-11.08-8.99-20.07-20.07-20.07z"/></svg>
        Apple
    </a>
    <a href="https://youtube.com/@contentfreaks" target="_blank" rel="noopener" class="listen-bar-btn listen-bar-youtube">
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
