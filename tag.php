<?php
/**
 * Template Name: タグアーカイブ
 * タグページ用のテンプレート
 */

get_header(); ?>

<main id="main" class="site-main contentfreaks-episodes-page">
    <?php contentfreaks_breadcrumb(); ?>
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
                <?php get_template_part('template-parts/episode-card'); ?>
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
