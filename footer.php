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
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M5.34 0A5.328 5.328 0 000 5.34v13.32A5.328 5.328 0 005.34 24h13.32A5.328 5.328 0 0024 18.66V5.34A5.328 5.328 0 0018.66 0H5.34zm6.525 2.568c2.336 0 4.448.902 6.053 2.507a8.487 8.487 0 012.508 6.052c0 .239-.192.431-.432.431a.429.429 0 01-.43-.431c0-4.456-3.661-8.126-8.13-8.126a.43.43 0 01-.431-.43c0-.24.192-.432.431-.432h.43v.43zm.005 3.239c1.456 0 2.815.566 3.834 1.585a5.38 5.38 0 011.585 3.834.43.43 0 01-.862 0 4.524 4.524 0 00-4.557-4.557.43.43 0 010-.862zm.015 3.24a2.18 2.18 0 012.178 2.177 2.18 2.18 0 01-2.178 2.178 2.18 2.18 0 01-2.177-2.178 2.18 2.18 0 012.177-2.178z"/></svg>
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
