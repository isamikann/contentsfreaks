<?php
/**
 * Template Name: タグアーカイブ
 * タグページ用のテンプレート
 */

get_header(); ?>

<main id="main" class="site-main contentfreaks-episodes-page">
    <!-- ヒーローセクション -->
    <section class="episodes-hero">
        <div class="episodes-hero-bg">
            <div class="hero-pattern"></div>
        </div>
        
        <div class="episodes-hero-particles">
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
        </div>
        
        <div class="episodes-hero-content">
            <div class="episodes-hero-icon">🏷️</div>
            <h1><?php single_tag_title(); ?></h1>
            <p class="episodes-hero-description">
                <?php echo tag_description(); ?>
            </p>
            
            <div class="episodes-hero-stats">
                <div class="episodes-stat">
                    <span class="episodes-stat-number"><?php 
                        echo $wp_query->found_posts;
                    ?></span>
                    <span class="episodes-stat-label">エピソード</span>
                </div>
            </div>
        </div>
    </section>

    <!-- エピソードコンテンツ -->
    <section class="episodes-content-section">
        <div class="episodes-container">
            <div class="episodes-grid" id="episodes-grid">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post();
                    // カスタムフィールドを取得
                    $audio_url = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                    $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                    $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                    $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
            ?>
                <article class="episode-card" data-category="<?php echo esc_attr($episode_category); ?>">
                    <div class="episode-card-header">
                        <div class="episode-thumbnail">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium', array(
                                        'alt' => get_the_title(),
                                        'loading' => 'lazy'
                                    )); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <div class="default-thumbnail">
                                        <div style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem;">🎙️</div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="episode-card-content">
                        <div class="episode-meta">
                            <div class="episode-meta-left">
                                <span class="episode-date"><?php echo get_the_date('Y年n月j日'); ?></span>
                                
                                <?php 
                                // タグを取得・表示
                                $tags = get_the_tags();
                                if ($tags && !is_wp_error($tags)) : ?>
                                <div class="episode-tags">
                                    <?php foreach ($tags as $tag) : ?>
                                        <a href="<?php echo get_tag_link($tag->term_id); ?>" class="episode-tag">
                                            #<?php echo esc_html($tag->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <h3 class="episode-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                    </div>
                </article>
            <?php 
                endwhile;
            else :
            ?>
                <div class="no-episodes">
                    <div class="no-episodes-icon">🏷️</div>
                    <h3>エピソードが見つかりません</h3>
                    <p>このタグに関連するエピソードはまだありません。</p>
                </div>
            <?php endif; ?>
            </div>
            
            <?php
            // ページネーション
            if (function_exists('wp_pagenavi')) {
                wp_pagenavi();
            } else {
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => __('« 前へ', 'contentfreaks'),
                    'next_text' => __('次へ »', 'contentfreaks'),
                ));
            }
            ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
