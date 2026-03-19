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
                <li class="footer-rss-item">
                    <span class="footer-rss-label">RSSフィードURL</span>
                    <a class="footer-rss-url" href="<?php echo esc_url(get_feed_link()); ?>" target="_blank" rel="noopener"><?php echo esc_html(get_feed_link()); ?></a>
                </li>
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


<?php wp_footer(); ?>

</body>
</html>
