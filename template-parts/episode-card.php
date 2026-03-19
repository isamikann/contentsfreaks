<?php
/**
 * エピソードカード共通テンプレート
 * 使用箇所: front-page.php, page-episodes.php, functions.php (AJAX)
 * 
 * 利用可能変数（呼び出し前にセット不要、ループ内で使用）:
 * - the_permalink(), the_title(), get_the_date() 等のWordPressテンプレートタグ
 */

// 直接アクセス防止
if (!defined('ABSPATH')) {
    exit;
}

$episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
$episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
$duration = get_post_meta(get_the_ID(), 'episode_duration', true);
$youtube_id = get_post_meta(get_the_ID(), 'episode_youtube_id', true);
?>
<article class="episode-card" data-category="<?php echo esc_attr($episode_category); ?>">
    <div class="episode-card-header">
        <div class="episode-thumbnail">
            <?php if ($youtube_id) : ?>
                <a href="<?php the_permalink(); ?>">
                    <img
                        src="https://i.ytimg.com/vi/<?php echo esc_attr($youtube_id); ?>/maxresdefault.jpg"
                        alt="<?php echo esc_attr(get_the_title()); ?>"
                        loading="lazy"
                        onerror="this.src='https://i.ytimg.com/vi/<?php echo esc_attr($youtube_id); ?>/hqdefault.jpg'"
                    >
                </a>
            <?php elseif (has_post_thumbnail()) : ?>
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('medium', array(
                        'alt' => get_the_title(),
                        'loading' => 'lazy'
                    )); ?>
                </a>
            <?php else :
                $episode_image_url = get_post_meta(get_the_ID(), 'episode_image_url', true);
                if ($episode_image_url) : ?>
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo esc_url($episode_image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                    </a>
                <?php else : ?>
                    <a href="<?php the_permalink(); ?>">
                        <div class="default-thumbnail">
                            <div class="default-thumbnail-inner">🎙️</div>
                        </div>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($episode_number) : ?>
                <span class="episode-number-badge">EP.<?php echo esc_html($episode_number); ?></span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="episode-card-content">
        <h3 class="episode-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <div class="episode-meta">
            <span class="episode-date"><?php echo get_the_date('Y.n.j'); ?></span>
            <?php if ($duration) : ?>
                <span class="episode-duration-badge">⏱ <?php echo esc_html($duration); ?></span>
            <?php endif; ?>
            <?php $yt_views = get_post_meta(get_the_ID(), 'episode_youtube_views', true); ?>
            <?php if ($yt_views) : ?>
                <span class="episode-yt-views">▶ <?php echo esc_html(contentfreaks_format_yt_number((int) $yt_views)); ?></span>
            <?php endif; ?>
        </div>

        <?php
        $tags = get_the_tags();
        if ($tags && !is_wp_error($tags)) : ?>
        <div class="episode-tags">
            <?php foreach (array_slice($tags, 0, 3) as $tag) : ?>
                <a href="<?php echo get_tag_link($tag->term_id); ?>" class="episode-tag">
                    #<?php echo esc_html($tag->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</article>
