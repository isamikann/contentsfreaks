<?php
/**
 * エピソードアーカイブページ
 * /episodes/ でアクセス可能
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
                // エピソードが見つからない場合、RSSからの自動同期を促す
            ?>
                <div class="no-episodes">
                    <h3>エピソードが見つかりませんでした</h3>
                    <p>RSSデータから投稿を自動生成してください。</p>
                    <a href="<?php echo admin_url('tools.php?page=contentfreaks-sync'); ?>" class="sync-episodes-btn">
                        同期を実行する
                    </a>
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

<?php get_footer(); ?>
