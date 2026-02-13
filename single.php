<?php
/**
 * 個別投稿ページ（エピソード詳細ページ）
 */

get_header(); ?>

<div class="single-episode-container site-main">
    <?php contentfreaks_breadcrumb(); ?>
    <?php while (have_posts()) : the_post(); ?>
        
        <?php 
        // ポッドキャストエピソードかどうかをチェック
        $post_id = get_the_ID();
        $is_podcast_episode = get_post_meta($post_id, 'is_podcast_episode', true);
        $episode_number = get_post_meta($post_id, 'episode_number', true);
        $duration = get_post_meta($post_id, 'episode_duration', true);
        $audio_url_raw = get_post_meta($post_id, 'episode_audio_url', true);
        
        // 音声URLの修正処理（ヘルパー関数で統一）
        $audio_url = contentfreaks_fix_audio_url($audio_url_raw);
        
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
                <?php if ($audio_url) : ?>
                <div class="episode-inline-player">
                    <audio controls preload="metadata" class="episode-audio-player">
                        <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
                        お使いのブラウザは音声再生に対応していません。
                    </audio>
                    <div class="playback-speed-controls">
                        <span class="speed-label">再生速度</span>
                        <button type="button" class="speed-btn" data-speed="0.75">0.75x</button>
                        <button type="button" class="speed-btn active" data-speed="1">1x</button>
                        <button type="button" class="speed-btn" data-speed="1.25">1.25x</button>
                        <button type="button" class="speed-btn" data-speed="1.5">1.5x</button>
                        <button type="button" class="speed-btn" data-speed="2">2x</button>
                    </div>
                </div>
                <?php endif; ?>
                <div class="episode-platform-links">
                    <h3 class="platform-links-title">🎧 お好みのアプリで聴く</h3>
                    <?php echo do_shortcode('[podcast_platforms]'); ?>
                </div>
                <?php endif; ?>
            </header>

            <!-- エピソード本文 -->
            <div class="episode-content">
                <?php if ($is_podcast_episode) : ?>
                <!-- 話題チャプター（タイムスタンプ目次） -->
                <?php
                $chapters = get_post_meta($post_id, 'episode_chapters', true);
                // メタフィールドがなければ本文からタイムスタンプを自動抽出
                if (empty($chapters)) {
                    $content_raw = get_the_content();
                    preg_match_all('/(\d{1,2}:\d{2}(?::\d{2})?)\s*[–\-:：]\s*(.+)/u', $content_raw, $matches, PREG_SET_ORDER);
                    if (!empty($matches)) {
                        $chapters = array();
                        foreach ($matches as $m) {
                            $chapters[] = array('time' => $m[1], 'title' => trim(wp_strip_all_tags($m[2])));
                        }
                    }
                } else {
                    // メタフィールドの場合 "00:00 タイトル" 形式で改行区切り
                    $lines = explode("\n", $chapters);
                    $parsed = array();
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (preg_match('/^(\d{1,2}:\d{2}(?::\d{2})?)\s+(.+)/u', $line, $m)) {
                            $parsed[] = array('time' => $m[1], 'title' => $m[2]);
                        }
                    }
                    $chapters = $parsed;
                }

                if (!empty($chapters)) :
                ?>
                <div class="episode-chapters">
                    <h3 class="chapters-title">📋 話題チャプター</h3>
                    <ol class="chapters-list">
                        <?php foreach ($chapters as $ch) : ?>
                        <li class="chapter-item">
                            <button type="button" class="chapter-time chapter-seek" data-time="<?php echo esc_attr($ch['time']); ?>"><?php echo esc_html($ch['time']); ?></button>
                            <span class="chapter-name"><?php echo esc_html($ch['title']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <div class="episode-content-wrapper">
                    <div class="content-text">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <!-- エピソードリアクション -->
            <?php if ($is_podcast_episode) : ?>
            <div class="episode-reactions" id="episode-reactions" data-post-id="<?php echo esc_attr($post_id); ?>">
                <h3 class="reactions-title">このエピソードの感想は？</h3>
                <div class="reactions-buttons" role="group" aria-label="エピソードリアクション">
                    <button type="button" class="reaction-btn" data-reaction="fire" title="熱い！" aria-pressed="false" aria-label="熱い！ リアクション">
                        <span class="reaction-emoji" aria-hidden="true">🔥</span>
                        <span class="reaction-count" data-count="fire">0</span>
                    </button>
                    <button type="button" class="reaction-btn" data-reaction="laugh" title="笑った！" aria-pressed="false" aria-label="笑った！ リアクション">
                        <span class="reaction-emoji" aria-hidden="true">🤣</span>
                        <span class="reaction-count" data-count="laugh">0</span>
                    </button>
                    <button type="button" class="reaction-btn" data-reaction="idea" title="なるほど！" aria-pressed="false" aria-label="なるほど！ リアクション">
                        <span class="reaction-emoji" aria-hidden="true">💡</span>
                        <span class="reaction-count" data-count="idea">0</span>
                    </button>
                    <button type="button" class="reaction-btn" data-reaction="cry" title="泣ける…" aria-pressed="false" aria-label="泣ける… リアクション">
                        <span class="reaction-emoji" aria-hidden="true">😢</span>
                        <span class="reaction-count" data-count="cry">0</span>
                    </button>
                    <button type="button" class="reaction-btn" data-reaction="heart" title="好き！" aria-pressed="false" aria-label="好き！ リアクション">
                        <span class="reaction-emoji" aria-hidden="true">❤️</span>
                        <span class="reaction-count" data-count="heart">0</span>
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- 今回紹介した作品（タグから自動取得） -->
            <?php
            $post_tags = wp_get_post_tags($post_id, array('fields' => 'names'));
            if (!empty($post_tags)) :
                $featured_works = contentfreaks_get_works_by_tags($post_tags);
                if (!empty($featured_works)) :
            ?>
            <div class="episode-featured-works">
                <h3 class="featured-works-title">📚 今回紹介した作品</h3>
                <div class="featured-works-grid">
                    <?php foreach ($featured_works as $work) : ?>
                    <div class="featured-work-card">
                        <?php if (has_post_thumbnail($work->ID)) : ?>
                        <div class="fw-thumbnail">
                            <?php echo get_the_post_thumbnail($work->ID, 'thumbnail', array('loading' => 'lazy', 'alt' => esc_attr($work->post_title))); ?>
                        </div>
                        <?php endif; ?>
                        <div class="fw-info">
                            <h4 class="fw-title"><?php echo esc_html($work->post_title); ?></h4>
                            <?php
                            $w_type = get_post_meta($work->ID, 'work_type', true);
                            $w_rating = get_post_meta($work->ID, 'work_rating', true);
                            ?>
                            <?php if ($w_type) : ?>
                            <span class="fw-type"><?php echo esc_html($w_type); ?></span>
                            <?php endif; ?>
                            <?php if ($w_rating) : ?>
                            <span class="fw-rating"><?php echo str_repeat('⭐', intval($w_rating)); ?></span>
                            <?php endif; ?>
                            <div class="fw-links">
                                <?php
                                $amazon_url = get_post_meta($work->ID, 'work_amazon_url', true);
                                $affiliate_url = get_post_meta($work->ID, 'work_affiliate_url', true);
                                $amazon_tag = get_theme_mod('mk_amazon_tag', '');
                                // Amazon URLにタグを自動付与
                                if ($amazon_url && $amazon_tag && strpos($amazon_url, 'tag=') === false) {
                                    $separator = (strpos($amazon_url, '?') !== false) ? '&' : '?';
                                    $amazon_url .= $separator . 'tag=' . urlencode($amazon_tag);
                                }
                                if ($amazon_url) : ?>
                                <a href="<?php echo esc_url($amazon_url); ?>" target="_blank" rel="noopener sponsored" class="fw-link fw-link-amazon">Amazonで見る</a>
                                <?php endif; ?>
                                <?php if ($affiliate_url) : ?>
                                <a href="<?php echo esc_url($affiliate_url); ?>" target="_blank" rel="noopener sponsored" class="fw-link fw-link-other">詳細を見る</a>
                                <?php endif; ?>
                                <a href="<?php echo esc_url(get_permalink($work->ID)); ?>" class="fw-link fw-link-db">作品DB</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; endif; ?>

            <!-- 関連エピソード -->
            <?php if ($is_podcast_episode) : ?>
            <div class="related-episodes">
                <h3 class="related-episodes-title">🎵 関連エピソード</h3>
                <div class="related-episodes-grid">
                    <?php
                    // 関連エピソードを取得（タグベースで関連性の高いものを優先）
                    $current_tags = wp_get_post_tags(get_the_ID(), array('fields' => 'ids'));
                    $related_args = array(
                        'post_type' => 'post',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'meta_key' => 'is_podcast_episode',
                        'meta_value' => '1',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    );
                    if (!empty($current_tags)) {
                        $related_args['tag__in'] = $current_tags;
                    }
                    $related_query = new WP_Query($related_args);
                    // タグで見つからない場合は最新でフォールバック
                    if (!$related_query->have_posts() && !empty($current_tags)) {
                        $related_args_fallback = $related_args;
                        unset($related_args_fallback['tag__in']);
                        $related_query = new WP_Query($related_args_fallback);
                    }

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
                                <div class="related-episode-date"><?php echo get_the_date('Y.n.j'); ?></div>
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
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('episodes'))); ?>" class="episode-nav-link episodes-list">
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
