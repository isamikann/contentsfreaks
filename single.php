<?php
/**
 * 個別投稿ページ（エピソード詳細ページ）
 */

get_header(); ?>

<div class="single-episode-container site-main">
    <?php while (have_posts()) : the_post(); ?>
        
        <?php 
        // ポッドキャストエピソードかどうかをチェック
        $post_id = get_the_ID();
        $is_podcast_episode = get_post_meta($post_id, 'is_podcast_episode', true);
        $episode_number = get_post_meta($post_id, 'episode_number', true);
        $duration = get_post_meta($post_id, 'episode_duration', true);
        $original_url = get_post_meta($post_id, 'episode_original_url', true);
        $episode_category = get_post_meta($post_id, 'episode_category', true) ?: 'エピソード';
        ?>

        <article class="single-episode">
            <!-- エピソードヘッダー -->
            <header class="episode-header">
                <div class="episode-header-content">
                    <div class="episode-featured-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', array(
                                'alt' => get_the_title(),
                                'loading' => 'eager' // メイン画像は即座に読み込み
                            )); ?>
                        <?php else : ?>
                            <div class="default-episode-image">
                                <div style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; border-radius: 15px;">🎙️</div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($episode_number) : ?>
                            <div class="episode-number-large">EP.<?php echo esc_html($episode_number); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="episode-info">
                        <div class="episode-meta">
                            <span class="episode-date"><?php echo get_the_date('Y年n月j日'); ?></span>
                            <?php if ($duration) : ?>
                                <span class="episode-duration">⏱️ <?php echo esc_html($duration); ?></span>
                            <?php endif; ?>
                            
                            <?php 
                            // タグを取得・表示（メタ情報のspanと統一）
                            $tags = get_the_tags();
                            if ($tags && !is_wp_error($tags)) : ?>
                                <span class="episode-tags">
                                    <?php foreach ($tags as $tag) : ?>
                                        <a href="<?php echo get_tag_link($tag->term_id); ?>" class="episode-tag">
                                            🏷️ <?php echo esc_html($tag->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="episode-title"><?php the_title(); ?></h1>
                    </div>
                </div>
                
                <!-- ポッドキャストプラットフォームリンク -->
                <?php if ($is_podcast_episode) : ?>
                <div class="episode-platform-links">
                    <h3 class="platform-links-title">🎧 お好みのアプリで聴く</h3>
                    <?php echo do_shortcode('[podcast_platforms]'); ?>
                </div>
                <?php endif; ?>
            </header>

            <!-- エピソード本文 -->
            <div class="episode-content">
                <div class="episode-content-wrapper">
                    <div class="content-text">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <!-- 紹介した作品 -->
            <?php 
            $mentioned_works = get_post_meta($post_id, 'mentioned_works', true);
            if ($mentioned_works && is_array($mentioned_works) && count($mentioned_works) > 0) : 
            ?>
            <div class="mentioned-works-section">
                <h3 class="mentioned-works-title">🎬 このエピソードで紹介した作品</h3>
                <div class="mentioned-works-grid">
                    <?php foreach ($mentioned_works as $work) : 
                        $work_title = isset($work['title']) ? $work['title'] : '';
                        $work_genre = isset($work['genre']) ? $work['genre'] : 'その他';
                        $work_year = isset($work['year']) ? $work['year'] : '';
                        $work_rating = isset($work['rating']) ? $work['rating'] : 0;
                        $work_image = isset($work['image']) ? $work['image'] : '';
                        $work_url = isset($work['url']) ? $work['url'] : '';
                        
                        if (!$work_title) continue;
                    ?>
                        <div class="mentioned-work-card">
                            <?php if ($work_image) : ?>
                                <div class="mentioned-work-image">
                                    <img src="<?php echo esc_url($work_image); ?>" 
                                         alt="<?php echo esc_attr($work_title); ?>"
                                         loading="lazy">
                                </div>
                            <?php else : ?>
                                <div class="mentioned-work-placeholder">
                                    <span>
                                        <?php 
                                        switch($work_genre) {
                                            case '映画': echo '🎬'; break;
                                            case 'ドラマ': echo '📺'; break;
                                            case 'アニメ': echo '🎨'; break;
                                            case 'ゲーム': echo '🎮'; break;
                                            case '書籍': echo '📚'; break;
                                            case '漫画': echo '📖'; break;
                                            default: echo '🎭';
                                        }
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mentioned-work-info">
                                <div class="mentioned-work-meta">
                                    <span class="mentioned-work-genre"><?php echo esc_html($work_genre); ?></span>
                                    <?php if ($work_year) : ?>
                                        <span class="mentioned-work-year"><?php echo esc_html($work_year); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <h4 class="mentioned-work-title"><?php echo esc_html($work_title); ?></h4>
                                
                                <?php if ($work_rating > 0) : ?>
                                    <div class="mentioned-work-rating">
                                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                                            <span class="star <?php echo $i <= $work_rating ? 'filled' : ''; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($work_url) : ?>
                                    <a href="<?php echo esc_url($work_url); ?>" 
                                       class="mentioned-work-link" 
                                       target="_blank" 
                                       rel="noopener">
                                        詳細を見る →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="works-database-cta">
                    <a href="/works/" class="works-database-btn">
                        📚 すべての作品を見る
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- 関連エピソード -->
            <?php if ($is_podcast_episode) : ?>
            <div class="related-episodes">
                <h3 class="related-episodes-title">🎵 関連エピソード</h3>
                <div class="related-episodes-grid">
                    <?php
                    // 関連エピソードを取得（同じカテゴリーから3件）
                    $related_query = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'meta_key' => 'is_podcast_episode',
                        'meta_value' => '1',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));

                    if ($related_query->have_posts()) :
                        while ($related_query->have_posts()) : $related_query->the_post();
                            $related_episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                            $related_duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                    ?>
                        <article class="related-episode-card">
                            <div class="related-episode-thumbnail">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', array(
                                            'alt' => get_the_title(),
                                            'loading' => 'lazy' // 関連記事は遅延読み込み
                                        )); ?>
                                    </a>
                                <?php else : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <div style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); width: 100%; height: 150px; display: flex; align-items: center; justify-content: center; font-size: 2rem; border-radius: 10px;">🎙️</div>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($related_episode_number) : ?>
                                <div class="episode-number-small">EP.<?php echo esc_html($related_episode_number); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="related-episode-info">
                                <div class="related-episode-date"><?php echo get_the_date('Y年n月j日'); ?></div>
                                <h4 class="related-episode-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <?php if ($related_duration) : ?>
                                <div class="related-episode-duration">⏱️ <?php echo esc_html($related_duration); ?></div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- ナビゲーション -->
            <div class="episode-navigation">
                <div class="episode-nav-links">
                    <div class="nav-previous">
                        <?php 
                        $prev_post = get_previous_post();
                        if ($prev_post) : ?>
                            <a href="<?php echo get_permalink($prev_post->ID); ?>" class="episode-nav-link prev">
                                <span class="nav-label">← 前のエピソード</span>
                                <span class="nav-title"><?php echo esc_html($prev_post->post_title); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="nav-center">
                        <a href="/episodes/" class="episode-nav-link episodes-list">
                            🎧 エピソード一覧
                        </a>
                    </div>
                    
                    <div class="nav-next">
                        <?php 
                        $next_post = get_next_post();
                        if ($next_post) : ?>
                            <a href="<?php echo get_permalink($next_post->ID); ?>" class="episode-nav-link next">
                                <span class="nav-label">次のエピソード →</span>
                                <span class="nav-title"><?php echo esc_html($next_post->post_title); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </article>

    <?php endwhile; ?>

    <!-- コメント欄 -->
    <?php if (comments_open() || get_comments_number()) : ?>
        <div class="episode-comments">
            <h3 class="comments-title">💬 コメント</h3>
            <?php comments_template(); ?>
        </div>
    <?php endif; ?>
</div>




<?php get_footer(); ?>
