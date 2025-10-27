<?php
/**
 * Template Name: エピソード一覧
 * ポッドキャストエピソード一覧を表示
 */

get_header(); ?>

<div class="content-area">
    <main class="main-content">
        <div class="episodes-header">
            <h1 class="page-title">🎙️ エピソード一覧</h1>
            <p class="page-description">コンテンツフリークスの全エピソードをお楽しみください。</p>
        </div>

        <div class="episodes-filters">
            <button class="filter-btn active" data-filter="all">すべて</button>
            <button class="filter-btn" data-filter="エピソード">エピソード</button>
            <button class="filter-btn" data-filter="スペシャル">スペシャル</button>
        </div>

        <div class="episodes-grid" id="episodes-grid">
            <?php
            // ポッドキャスト投稿を取得（カスタムフィールドでフィルタ）
            $episodes_query = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 12,
                'meta_key' => 'is_podcast_episode',
                'meta_value' => '1',
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            if ($episodes_query->have_posts()) :
                while ($episodes_query->have_posts()) : $episodes_query->the_post();
                    // カスタムフィールドを取得
                    $audio_url = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                    $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                    $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                    $original_url = get_post_meta(get_the_ID(), 'episode_original_url', true);
                    $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
            ?>
                <article class="episode-card" data-category="<?php echo esc_attr($episode_category); ?>">
                    <div class="episode-thumbnail">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                        <?php else : ?>
                            <div style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">🎙️</div>
                        <?php endif; ?>
                        
                        <?php if ($episode_number) : ?>
                        <div class="episode-number">EP.<?php echo esc_html($episode_number); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($duration) : ?>
                        <div class="episode-duration-badge"><?php echo esc_html($duration); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="episode-content">
                        <div class="episode-date"><?php echo get_the_date('Y年n月j日'); ?></div>
                        <h3 class="episode-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        
                        <div class="episode-description">
                            <?php echo wp_trim_words(get_the_excerpt(), 30); ?>
                        </div>
                        
                        <div class="episode-actions">
                            <a href="<?php the_permalink(); ?>" class="read-more-btn">詳細</a>
                            <div class="episode-platforms">
                                <a href="https://open.spotify.com/show/20otj7CiCZ0hcWYkkEpnLL" class="mini-platform-link spotify" target="_blank" title="Spotifyで聴く"><?php echo get_theme_mod('spotify_icon') ? '<img src="' . esc_url(get_theme_mod('spotify_icon')) . '" alt="Spotify" style="width: 16px; height: 16px; object-fit: contain;">' : '🎧'; ?></a>
                                <a href="https://podcasts.apple.com/jp/podcast/%E3%82%B3%E3%83%B3%E3%83%86%E3%83%B3%E3%83%84%E3%83%95%E3%83%AA%E3%83%BC%E3%82%AF%E3%82%B9/id1692185758" class="mini-platform-link apple" target="_blank" title="Apple Podcastsで聴く"><?php echo get_theme_mod('apple_podcasts_icon') ? '<img src="' . esc_url(get_theme_mod('apple_podcasts_icon')) . '" alt="Apple Podcasts" style="width: 16px; height: 16px; object-fit: contain;">' : '🍎'; ?></a>
                                <a href="https://youtube.com/@contentfreaks" class="mini-platform-link youtube" target="_blank" title="YouTubeで聴く"><?php echo get_theme_mod('youtube_icon') ? '<img src="' . esc_url(get_theme_mod('youtube_icon')) . '" alt="YouTube" style="width: 16px; height: 16px; object-fit: contain;">' : '📺'; ?></a>
                            </div>
                        </div>
                    </div>
                </article>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="no-episodes">
                    <p>エピソードが見つかりませんでした。</p>
                    <p>RSSデータの同期を実行してください。</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($episodes_query->found_posts > 12) : ?>
        <div class="load-more-container">
            <button id="load-more-episodes" class="load-more-btn" data-offset="12" data-limit="12">
                さらに読み込む
            </button>
        </div>
        <?php endif; ?>

        <!-- オーディオプレイヤー -->
        <div id="audio-player" class="audio-player" style="display: none;">
            <div class="player-controls">
                <button id="play-pause-btn">▶</button>
                <div class="player-info">
                    <div class="player-title">タイトル</div>
                    <div class="player-progress">
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <div class="time-display">
                            <span class="current-time">0:00</span> / <span class="total-time">0:00</span>
                        </div>
                    </div>
                </div>
                <button id="close-player">✕</button>
            </div>
            <audio id="audio-element"></audio>
        </div>
    </main>
</div>

<style>
.episodes-header {
    text-align: center;
    margin: 2rem 0;
}

.page-title {
    font-size: 2.5rem;
    color: var(--accent-color, #f7ff0b);
    margin-bottom: 1rem;
}

.page-description {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 2rem;
}

.episodes-filters {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 0.8rem 1.5rem;
    background: #f5f5f5;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--accent-color, #f7ff0b);
    color: #333;
}

.episodes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.episode-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.episode-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.episode-thumbnail {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.episode-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.episode-number {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
}

.episode-duration-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
}

.episode-play-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--accent-color, #f7ff0b);
    color: #333;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.episode-card:hover .episode-play-overlay {
    opacity: 1;
}

.episode-content {
    padding: 1.5rem;
}

.episode-date {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.episode-title {
    margin: 0.5rem 0 1rem 0;
    font-size: 1.2rem;
    line-height: 1.4;
}

.episode-title a {
    color: #333;
    text-decoration: none;
}

.episode-title a:hover {
    color: var(--accent-color, #f7ff0b);
}

.episode-description {
    color: #666;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.episode-tags {
    margin-bottom: 1rem;
}

.episode-tag {
    display: inline-block;
    background: #f0f0f0;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    color: #666;
}

.episode-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.play-button, .read-more-btn, .share-button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.play-button {
    background: var(--accent-color, #f7ff0b);
    color: #333;
}

.read-more-btn {
    background: #007cba;
    color: white;
}

.share-button {
    background: #f5f5f5;
    color: #666;
    padding: 0.5rem;
    min-width: 40px;
}

.play-button:hover,
.read-more-btn:hover,
.share-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.load-more-container {
    text-align: center;
    margin: 3rem 0;
}

.load-more-btn {
    background: var(--accent-color, #f7ff0b);
    color: #333;
    border: none;
    padding: 1rem 2rem;
    border-radius: 25px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.load-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.no-episodes {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem;
    color: #666;
}

/* オーディオプレイヤー */
.audio-player {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #333;
    color: white;
    z-index: 1000;
    box-shadow: 0 -4px 15px rgba(0,0,0,0.3);
}

.player-controls {
    display: flex;
    align-items: center;
    padding: 1rem;
    gap: 1rem;
}

#play-pause-btn {
    background: var(--accent-color, #f7ff0b);
    color: #333;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 1.2rem;
    cursor: pointer;
}

.player-info {
    flex: 1;
}

.player-title {
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.player-progress {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.progress-bar {
    flex: 1;
    height: 6px;
    background: #555;
    border-radius: 3px;
    cursor: pointer;
}

.progress-fill {
    height: 100%;
    background: var(--accent-color, #f7ff0b);
    border-radius: 3px;
    width: 0%;
    transition: width 0.1s ease;
}

.time-display {
    font-size: 0.9rem;
    color: #ccc;
}

#close-player {
    background: none;
    border: none;
    color: #ccc;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.5rem;
}

/* レスポンシブ */
@media (max-width: 768px) {
    .episodes-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .episodes-filters {
        gap: 0.5rem;
    }
    
    .filter-btn {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }
    
    .episode-actions {
        flex-wrap: wrap;
    }
    
    .player-controls {
        padding: 0.8rem;
    }
    
    .player-progress {
        flex-direction: column;
        gap: 0.5rem;
        align-items: stretch;
    }
}
</style>

<?php get_footer(); ?>
