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
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>コンテンツ</h3>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('episodes'))); ?>">最新エピソード</a></li>
                <li><a href="<?php echo esc_url(home_url('/category/popular/')); ?>">人気エピソード</a></li>
                <li><a href="<?php echo esc_url(home_url('/archives/')); ?>">アーカイブ</a></li>
                <li><a href="<?php echo esc_url(get_feed_link()); ?>">RSS</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>プラットフォーム</h3>
            <ul class="footer-links">
                <li><a href="https://open.spotify.com/show/podcast-id" target="_blank" rel="noopener">Spotify</a></li>
                <li><a href="https://podcasts.apple.com/podcast/podcast-id" target="_blank" rel="noopener">Apple Podcasts</a></li>
                <li><a href="https://www.youtube.com/channel/channel-id" target="_blank" rel="noopener">YouTube</a></li>
                <li><a href="https://podcasts.google.com/feed/feed-url" target="_blank" rel="noopener">Google Podcasts</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>お問い合わせ</h3>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>">お問い合わせフォーム</a></li>
                <li><a href="mailto:info@contentfreaks.com">メール</a></li>
                <li><a href="https://twitter.com/contentfreaks" target="_blank" rel="noopener">Twitter</a></li>
                <li><a href="<?php echo esc_url(get_privacy_policy_url()); ?>">プライバシーポリシー</a></li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
    </div>
</footer>

<?php wp_footer(); ?>

<!-- ContentFreaks専用JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // モバイルメニューの制御
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-nav-menu');
    const body = document.body;
    
    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            body.classList.toggle('mobile-menu-open');
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
    
    // デバッグ情報をコンソールに出力
    console.log('ContentFreaks Theme Loaded');
    console.log('Header:', header ? 'Found' : 'Not Found');
    console.log('Mobile Menu:', mobileMenu ? 'Found' : 'Not Found');
    console.log('Search Modal:', searchModal ? 'Found' : 'Not Found');
});
</script>

</body>
</html>
