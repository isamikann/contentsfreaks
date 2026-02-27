    </div><!-- #content -->
</div><!-- #page -->

<!-- ContentFreaks専用フッター -->
<footer id="contentfreaks-footer" role="contentinfo" aria-label="サイトフッター">
    <div class="footer-content">
        <!-- ナビゲーション -->
        <div class="footer-section">
            <h3><?php bloginfo('name'); ?></h3>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(home_url('/')); ?>">ホーム</a></li>
                <li><a href="<?php echo esc_url(contentfreaks_get_page_url('episodes')); ?>">エピソード</a></li>
                <li><a href="<?php echo esc_url(contentfreaks_get_page_url('blog')); ?>">ブログ</a></li>
                <li><a href="<?php echo esc_url(contentfreaks_get_page_url('profile')); ?>">プロフィール</a></li>
                <li><a href="<?php echo esc_url(contentfreaks_get_page_url('history')); ?>">コンフリの歩み</a></li>
            </ul>
        </div>

        <!-- プラットフォーム＋RSS（旧「コンテンツ」セクションを統合） -->
        <div class="footer-section">
            <h3>聴く</h3>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(CONTENTFREAKS_SPOTIFY_URL); ?>" target="_blank" rel="noopener">Spotify</a></li>
                <li><a href="<?php echo esc_url(CONTENTFREAKS_APPLE_URL); ?>" target="_blank" rel="noopener">Apple Podcasts</a></li>
                <li><a href="<?php echo esc_url(CONTENTFREAKS_YOUTUBE_URL); ?>" target="_blank" rel="noopener">YouTube</a></li>
                <li><a href="<?php echo esc_url(get_feed_link()); ?>">RSS</a></li>
            </ul>
        </div>

        <!-- サポート -->
        <div class="footer-section">
            <h3>サポート</h3>
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(contentfreaks_get_page_url('contact')); ?>">お問い合わせ</a></li>
                <li><a href="<?php echo esc_url(contentfreaks_get_page_url('media-kit')); ?>" class="footer-business-link">お仕事のご依頼 / Media Kit</a></li>
                <li><a href="<?php echo esc_url(get_privacy_policy_url()); ?>">プライバシーポリシー</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo wp_date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
    </div>
</footer>

<!-- モバイルフローティングCTA（プラットフォーム直リンクバー） -->
<nav class="mobile-listen-bar" id="mobile-listen-bar" role="navigation" aria-label="ポッドキャストプラットフォーム">
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
</nav>

<?php wp_footer(); ?>

</body>
</html>
