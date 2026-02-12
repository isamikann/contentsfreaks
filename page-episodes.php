<?php
/**
 * Template Name: エピソード一覧
 * モダンなポッドキャストエピソード一覧ページ（統合版）
 * archive-episodes.phpの機能も含む
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
            <div class="episodes-hero-icon">🎙️</div>
            <h1>Podcast Episodes</h1>
            <p class="episodes-hero-description">
                コンテンツフリークスの全エピソードを一覧でお楽しみください。
                最新のエピソードから過去の名作まで、すべてここに集約されています。
            </p>
            
            <div class="episodes-hero-stats">
                <div class="episodes-stat">
                    <span class="episodes-stat-number"><?php echo contentfreaks_get_podcast_count(); ?></span>
                    <span class="episodes-stat-label">エピソード</span>
                </div>
                <div class="episodes-stat">
                    <span class="episodes-stat-number">🔥</span>
                    <span class="episodes-stat-label">熱い語り</span>
                </div>
                <div class="episodes-stat">
                    <span class="episodes-stat-number">🔍</span>
                    <span class="episodes-stat-label">深掘り分析</span>
                </div>
            </div>
        </div>
    </section>

    <!-- エピソードコンテンツ -->
    <section class="episodes-content-section">
        <div class="episodes-container">
            <div class="search-controls">
                <div class="search-box">
                    <input type="text" id="episode-search" class="search-input" placeholder="エピソードを検索..." />
                    <button type="button" class="search-button">🔍</button>
                </div>
                <button type="button" class="random-episode-btn" id="random-episode-btn" title="ランダムなエピソードを開く">
                    🎲 今日の1本
                </button>
            </div>
            
            <div class="episodes-grid" id="episodes-grid">
            <?php
            // ポッドキャスト投稿を取得（カスタムフィールドでフィルタ）
            $episodes_query = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 18,
                'meta_key' => 'is_podcast_episode',
                'meta_value' => '1',
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            if ($episodes_query->have_posts()) :
                while ($episodes_query->have_posts()) : $episodes_query->the_post();
                    // カスタムフィールドを取得
                    $audio_url_raw = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                    
                    // 音声URLの修正処理（ヘルパー関数で統一）
                    $audio_url = contentfreaks_fix_audio_url($audio_url_raw);
                    
                    $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                    $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                    $original_url = get_post_meta(get_the_ID(), 'episode_original_url', true);
                    $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
            ?>
                <?php get_template_part('template-parts/episode-card'); ?>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="no-episodes">
                    <div class="no-episodes-icon">🎙️</div>
                    <h3>エピソードが見つかりません</h3>
                    <p>まだエピソードが投稿されていないか、検索条件に一致するエピソードがありません。</p>
                    <a href="<?php echo admin_url('tools.php?page=contentfreaks-sync'); ?>" class="sync-episodes-btn">
                        RSSからエピソードを同期
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Load Moreパターン -->
        <?php if ($episodes_query->found_posts > 18) : ?>
        <div class="load-more-wrapper" id="load-more-wrapper">
            <button class="load-more-btn" id="load-more-btn" data-offset="18" data-limit="12">
                もっと見る
            </button>
        </div>
        <div class="load-more-spinner" id="loading-indicator" style="display: none;">
            <div class="loading-spinner">
                <div class="spinner-ring"></div>
                <p>エピソードを読み込んでいます...</p>
            </div>
        </div>
        <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
