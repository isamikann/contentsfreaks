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

<style>
.error-404-page {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    margin-top: 80px;
}

.error-404-container {
    max-width: 600px;
    text-align: center;
}

.error-404-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    animation: float-icon 3s ease-in-out infinite;
}

@keyframes float-icon {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.error-404-title {
    font-size: 6rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--color-primary-400, #f7ff0b), var(--color-accent-400, #ff6b35));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.error-404-subtitle {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--color-text-primary, #1a202c);
    margin-bottom: 0.75rem;
}

.error-404-desc {
    color: var(--color-text-secondary, #4a5568);
    line-height: 1.7;
    margin-bottom: 2rem;
}

.error-404-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 3rem;
}

.error-404-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 9999px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.error-404-btn--home {
    background: var(--color-primary-400, #f7ff0b);
    color: var(--color-text-primary, #1a202c);
}

.error-404-btn--home:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(247, 255, 11, 0.3);
}

.error-404-btn--episodes {
    background: var(--color-bg-secondary, #f7fafc);
    color: var(--color-text-primary, #1a202c);
    border: 1px solid var(--color-border-light, #e2e8f0);
}

.error-404-btn--episodes:hover {
    background: var(--color-accent-400, #ff6b35);
    color: #fff;
    border-color: var(--color-accent-400, #ff6b35);
    transform: translateY(-2px);
}

.error-404-suggestions {
    border-top: 1px solid var(--color-border-light, #e2e8f0);
    padding-top: 2rem;
}

.error-404-suggestions-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--color-text-primary, #1a202c);
}

.error-404-episodes {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.error-404-episode-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 1rem;
    background: var(--color-bg-secondary, #f7fafc);
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    text-align: left;
}

.error-404-episode-link:hover {
    background: var(--color-primary-400, #f7ff0b);
    transform: translateX(4px);
}

.error-404-episode-date {
    flex-shrink: 0;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--color-text-secondary, #4a5568);
    min-width: 3rem;
}

.error-404-episode-title {
    font-weight: 500;
    color: var(--color-text-primary, #1a202c);
    font-size: 0.9rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

@media (max-width: 768px) {
    .error-404-title { font-size: 4rem; }
    .error-404-actions { flex-direction: column; align-items: center; }
}
</style>

<?php get_footer(); ?>
