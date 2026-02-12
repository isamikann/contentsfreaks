<?php
/**
 * 404 エラーページ
 * コンテンツフリークス専用のカスタム404ページ
 */

get_header(); ?>

<main class="error-404-page site-main">
    <div class="error-404-container">
        <div class="error-404-icon">🎧</div>
        <h1 class="error-404-title">404</h1>
        <p class="error-404-subtitle">ページが見つかりませんでした</p>
        <p class="error-404-desc">
            お探しのページは移動または削除された可能性があります。<br>
            URLが正しいかご確認ください。
        </p>
        <div class="error-404-actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="error-404-btn error-404-btn--home">
                🏠 ホームに戻る
            </a>
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('episodes'))); ?>" class="error-404-btn error-404-btn--episodes">
                🎙️ エピソード一覧
            </a>
        </div>

        <!-- 最新エピソードを提案 -->
        <?php
        $latest = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => 3,
            'meta_key' => 'is_podcast_episode',
            'meta_value' => '1',
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        if ($latest->have_posts()) :
        ?>
        <div class="error-404-suggestions">
            <h2 class="error-404-suggestions-title">最新エピソードはいかがですか？</h2>
            <div class="error-404-episodes">
                <?php while ($latest->have_posts()) : $latest->the_post(); ?>
                <a href="<?php the_permalink(); ?>" class="error-404-episode-link">
                    <span class="error-404-episode-date"><?php echo get_the_date('n/j'); ?></span>
                    <span class="error-404-episode-title"><?php the_title(); ?></span>
                </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
