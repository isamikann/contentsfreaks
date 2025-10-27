<?php
/**
 * 個別投稿ページ（エピソード詳細ページ）
 */

get_header(); ?>

<div class="single-episode-container">
    <?php while (have_posts()) : the_post(); ?>
        
        <?php 
        // ポッドキャストエピソードかどうかをチェック
        $is_podcast_episode = get_post_meta(get_the_ID(), 'is_podcast_episode', true);
        $audio_url = get_post_meta(get_the_ID(), 'episode_audio_url', true);
        $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
        $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
        $original_url = get_post_meta(get_the_ID(), 'episode_original_url', true);
        $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
        ?>

        <article class="single-episode">
            <!-- エピソードヘッダー -->
            <header class="episode-header">
                <div class="episode-header-content">
                    <div class="episode-featured-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
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
                            <span class="episode-category">🏷️ <?php echo esc_html($episode_category); ?></span>
                        </div>
                        
                        <h1 class="episode-title"><?php the_title(); ?></h1>
                        
                        <div class="episode-excerpt">
                            <?php echo get_the_excerpt(); ?>
                        </div>
                        
                        <!-- オーディオプレイヤー -->
                        <?php if ($audio_url) : ?>
                        <div class="episode-audio-player">
                            <audio controls preload="metadata" style="width: 100%; max-width: 500px;">
                                <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
                                お使いのブラウザはオーディオ要素をサポートしていません。
                            </audio>
                        </div>
                        <?php endif; ?>
                        
                        <!-- ポッドキャストプラットフォームリンク -->
                        <?php if ($is_podcast_episode) : ?>
                        <div class="episode-platform-links">
                            <h3 class="platform-links-title">🎧 お好みのアプリで聴く</h3>
                            <div class="platform-links-grid">
                                <a href="https://open.spotify.com/show/20otj7CiCZ0hcWYkkEpnLL" class="platform-link spotify" target="_blank" rel="noopener">
                                    <div class="platform-icon"><?php echo get_theme_mod('spotify_icon') ? '<img src="' . esc_url(get_theme_mod('spotify_icon')) . '" alt="Spotify" style="width: 32px; height: 32px; object-fit: contain;">' : '🎧'; ?></div>
                                    <div class="platform-name">Spotify</div>
                                    <div class="platform-action">開く</div>
                                </a>
                                
                                <a href="https://podcasts.apple.com/jp/podcast/%E3%82%B3%E3%83%B3%E3%83%86%E3%83%B3%E3%83%84%E3%83%95%E3%83%AA%E3%83%BC%E3%82%AF%E3%82%B9/id1692185758" class="platform-link apple" target="_blank" rel="noopener">
                                    <div class="platform-icon"><?php echo get_theme_mod('apple_podcasts_icon') ? '<img src="' . esc_url(get_theme_mod('apple_podcasts_icon')) . '" alt="Apple Podcasts" style="width: 32px; height: 32px; object-fit: contain;">' : '🍎'; ?></div>
                                    <div class="platform-name">Apple Podcasts</div>
                                    <div class="platform-action">開く</div>
                                </a>
                                
                                <a href="https://youtube.com/@contentfreaks" class="platform-link youtube" target="_blank" rel="noopener">
                                    <div class="platform-icon"><?php echo get_theme_mod('youtube_icon') ? '<img src="' . esc_url(get_theme_mod('youtube_icon')) . '" alt="YouTube" style="width: 32px; height: 32px; object-fit: contain;">' : '📺'; ?></div>
                                    <div class="platform-name">YouTube</div>
                                    <div class="platform-action">開く</div>
                                </a>
                                
                                <div class="platform-link rss">
                                    <div class="platform-icon">📡</div>
                                    <div class="platform-name">RSS</div>
                                    <div class="platform-action">
                                        <input type="text" value="https://anchor.fm/s/d8cfdc48/podcast/rss" readonly onclick="this.select(); document.execCommand('copy'); alert('RSSフィードURLをコピーしました！');" style="font-size: 0.8rem; padding: 2px; border: 1px solid #ccc; border-radius: 3px; width: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <!-- エピソード本文 -->
            <div class="episode-content">
                <div class="episode-content-wrapper">
                    <?php the_content(); ?>
                </div>
                
                <!-- シェアボタン -->
                <div class="episode-share">
                    <h4 class="share-title">このエピソードをシェア</h4>
                    <div class="share-buttons">
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title() . ' - コンテンツフリークス'); ?>&url=<?php echo urlencode(get_permalink()); ?>" class="share-btn twitter" target="_blank" rel="noopener">
                            🐦 Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" class="share-btn facebook" target="_blank" rel="noopener">
                            📘 Facebook
                        </a>
                        <a href="https://line.me/R/msg/text/?<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" class="share-btn line" target="_blank" rel="noopener">
                            💬 LINE
                        </a>
                        <button class="share-btn copy" onclick="navigator.clipboard.writeText('<?php echo get_permalink(); ?>'); alert('URLをコピーしました！');">
                            📋 URLコピー
                        </button>
                    </div>
                </div>
            </div>

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
                            $related_audio_url = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                            $related_episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                            $related_duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                    ?>
                        <article class="related-episode-card">
                            <div class="related-episode-thumbnail">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
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
                <div class="nav-links">
                    <div class="nav-previous">
                        <?php 
                        $prev_post = get_previous_post();
                        if ($prev_post) : ?>
                            <a href="<?php echo get_permalink($prev_post->ID); ?>" class="nav-link prev">
                                <span class="nav-label">← 前のエピソード</span>
                                <span class="nav-title"><?php echo esc_html($prev_post->post_title); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="nav-center">
                        <a href="/episodes/" class="nav-link episodes-list">
                            🎧 エピソード一覧
                        </a>
                    </div>
                    
                    <div class="nav-next">
                        <?php 
                        $next_post = get_next_post();
                        if ($next_post) : ?>
                            <a href="<?php echo get_permalink($next_post->ID); ?>" class="nav-link next">
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

<style>
.single-episode-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

.episode-header {
    margin-bottom: 3rem;
}

.episode-header-content {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
    align-items: start;
}

.episode-featured-image {
    position: relative;
}

.episode-featured-image img {
    width: 100%;
    height: auto;
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.default-episode-image {
    width: 100%;
    height: 300px;
    border-radius: 15px;
    overflow: hidden;
}

.episode-number-large {
    position: absolute;
    top: 15px;
    left: 15px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 1.1rem;
}

.episode-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #666;
}

.episode-title {
    font-size: 2rem;
    margin-bottom: 1rem;
    line-height: 1.3;
    color: #333;
}

.episode-excerpt {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #555;
    margin-bottom: 2rem;
}

.episode-audio-player {
    margin: 2rem 0;
}

.episode-audio-player audio {
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* ポッドキャストプラットフォームリンク */
.episode-platform-links {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    margin: 2rem 0;
}

.platform-links-title {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #333;
    font-size: 1.3rem;
}

.platform-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.platform-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem 1rem;
    background: white;
    border-radius: 10px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.platform-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    text-decoration: none;
    color: #333;
}

.platform-link.spotify:hover { background: #1DB954; color: white; }
.platform-link.apple:hover { background: #A855F7; color: white; }
.platform-link.youtube:hover { background: #FF0000; color: white; }
.platform-link.google:hover { background: #4285F4; color: white; }
.platform-link.anchor:hover { background: #5000B8; color: white; }
.platform-link.rss:hover { background: #FF6600; color: white; }

.platform-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.platform-name {
    font-weight: bold;
    margin-bottom: 0.3rem;
}

.platform-action {
    font-size: 0.9rem;
    opacity: 0.8;
}

.platform-link.rss .platform-action {
    width: 100%;
}

/* エピソード本文 */
.episode-content {
    margin: 3rem 0;
}

.episode-content-wrapper {
    line-height: 1.8;
    font-size: 1.1rem;
}

/* シェアボタン */
.episode-share {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    margin: 2rem 0;
    text-align: center;
}

.share-title {
    margin-bottom: 1rem;
    color: #333;
}

.share-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.share-btn {
    padding: 0.8rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
}

.share-btn.twitter { background: #1DA1F2; color: white; }
.share-btn.facebook { background: #1877F2; color: white; }
.share-btn.line { background: #00B900; color: white; }
.share-btn.copy { background: #6c757d; color: white; }

.share-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    text-decoration: none;
    color: white;
}

/* 関連エピソード */
.related-episodes {
    margin: 3rem 0;
}

.related-episodes-title {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 1.5rem;
    color: #333;
}

.related-episodes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.related-episode-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.related-episode-card:hover {
    transform: translateY(-5px);
}

.related-episode-thumbnail {
    position: relative;
    height: 150px;
    overflow: hidden;
}

.related-episode-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.episode-number-small {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: bold;
}

.related-episode-info {
    padding: 1rem;
}

.related-episode-date {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.related-episode-title {
    margin: 0.5rem 0;
    font-size: 1rem;
    line-height: 1.4;
}

.related-episode-title a {
    color: #333;
    text-decoration: none;
}

.related-episode-title a:hover {
    color: var(--accent-color, #f7ff0b);
}

.related-episode-duration {
    font-size: 0.8rem;
    color: #666;
}

/* ナビゲーション */
.episode-navigation {
    margin: 3rem 0;
    padding: 2rem 0;
    border-top: 1px solid #e9ecef;
}

.nav-links {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 2rem;
    align-items: center;
}

.nav-link {
    display: block;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
}

.nav-link:hover {
    background: var(--accent-color, #f7ff0b);
    text-decoration: none;
    color: #333;
    transform: translateY(-2px);
}

.nav-link.prev { text-align: left; }
.nav-link.next { text-align: right; }
.nav-link.episodes-list { text-align: center; font-weight: bold; }

.nav-label {
    display: block;
    font-size: 0.9rem;
    font-weight: bold;
    margin-bottom: 0.3rem;
}

.nav-title {
    display: block;
    font-size: 0.9rem;
    opacity: 0.8;
}

/* コメント */
.episode-comments {
    margin: 3rem 0;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 15px;
}

.comments-title {
    text-align: center;
    margin-bottom: 2rem;
    color: #333;
}

/* レスポンシブ */
@media (max-width: 768px) {
    .single-episode-container {
        padding: 1rem;
    }
    
    .episode-header-content {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .episode-title {
        font-size: 1.5rem;
    }
    
    .platform-links-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    }
    
    .share-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .nav-links {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .related-episodes-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php get_footer(); ?>
