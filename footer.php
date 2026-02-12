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
