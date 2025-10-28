<?php
/**
 * Template Name: ポッドキャストトップページテンプレート
 * ポッドキャスト専用のトップページレイアウト
 */

get_header(); ?>


<main id="main-content" class="site-main" role="main">
    <!-- ポッドキャスト専用ヒーローセクション -->
    <section class="podcast-hero" aria-labelledby="hero-title">
        <!-- パーティクルアニメーション -->
        <div class="podcast-hero-particles" aria-hidden="true">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        
        <div class="podcast-hero-content">
            <!-- 左側：メインコンテンツ -->
            <div class="podcast-hero-main">
                <!-- タイトル -->
                <h1 id="hero-title" class="hero-title">ContentFreaks</h1>
                <p class="hero-subtitle">好きな作品、語り尽くそう！</p>
                
                <!-- アートワーク + ディスクリプションを1つのコンテナに統合 -->
                <div class="podcast-hero-content-block">
                    <!-- アートワーク -->
                    <div class="podcast-hero-artwork">
                        <?php 
                        $podcast_artwork = get_theme_mod('podcast_artwork');
                        if ($podcast_artwork): ?>
                            <img src="<?php echo esc_url($podcast_artwork); ?>" alt="<?php echo esc_attr(get_theme_mod('podcast_name')); ?>" class="podcast-artwork">
                        <?php else: ?>
                            <div class="podcast-artwork" style="background: var(--latest-episode-badge-bg); display: flex; align-items: center; justify-content: center; font-size: 4rem; color: var(--black);">
                                🎙️
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- ディスクリプション -->
                    <div class="podcast-hero-text">
                        <div class="podcast-hero-description">
                            <?php echo esc_html(get_theme_mod('podcast_description', '「コンテンツフリークス」は、大学時代からの友人2人で「いま気になる」注目のエンタメコンテンツを熱く語るポッドキャスト')); ?>
                        </div>
                        
                        <!-- コンテンツフリークスの歩みページへのリンク -->
                        <div class="history-cta">
                            <a href="<?php echo get_permalink(get_page_by_path('history')); ?>" class="history-btn">
                                📜 コンフリの歩みを見る
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 右側：統計情報とナビゲーション -->
            <div class="podcast-hero-sidebar">
                <!-- 統計情報 -->
                <div class="stats-section">
                    <h3 class="sidebar-section-title">📊 ポッドキャスト情報</h3>
                    <div class="podcast-stats">
                        <div class="podcast-stat">
                            <span class="podcast-stat-number" data-count="<?php 
                                $episode_count = get_posts(array(
                                    'meta_key' => 'is_podcast_episode',
                                    'meta_value' => '1',
                                    'post_status' => 'publish',
                                    'numberposts' => -1
                                ));
                                echo count($episode_count);
                                ?>">0
                            </span>
                            <span class="podcast-stat-label">エピソード</span>
                        </div>
                        <div class="podcast-stat">
                            <span class="podcast-stat-number" data-count="<?php echo esc_attr(get_option('contentfreaks_listener_count', '1500')); ?>"><?php echo esc_attr(get_option('contentfreaks_listener_count', '1500')); ?>+</span>
                            <span class="podcast-stat-label">リスナー</span>
                        </div>
                        <div class="podcast-stat">
                            <span class="podcast-stat-number" data-count="4.7" data-decimal="true">0</span>
                            <span class="podcast-stat-label">評価</span>
                        </div>
                    </div>
                </div>
                
                <!-- クイックナビゲーション -->
                <div class="quick-nav-section">
                    <h3 class="sidebar-section-title">🗂️ コンテンツ</h3>
                    <div class="quick-nav-links">
                        <a href="<?php echo get_permalink(get_page_by_path('episodes')); ?>" class="quick-nav-btn">
                            <span class="quick-nav-icon">🎙️</span>
                            <span class="quick-nav-label">全エピソード</span>
                        </a>
                        <a href="<?php echo get_permalink(get_page_by_path('works')); ?>" class="quick-nav-btn">
                            <span class="quick-nav-icon">📚</span>
                            <span class="quick-nav-label">作品データベース</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
    // カウントアップアニメーション - 最適化版
    document.addEventListener('DOMContentLoaded', function() {
        const statNumbers = document.querySelectorAll('.podcast-stat-number[data-count]');
        
        const animateCount = (element) => {
            const target = parseFloat(element.dataset.count);
            const isDecimal = element.dataset.decimal === 'true';
            const duration = 1500; // 2000ms → 1500ms に短縮
            const step = target / (duration / 16);
            let current = 0;
            
            const update = () => {
                current = Math.min(current + step, target);
                
                if (isDecimal) {
                    element.textContent = current.toFixed(1);
                } else {
                    const nextEl = element.nextElementSibling;
                    element.textContent = Math.floor(current) + (nextEl?.textContent === 'リスナー' ? '+' : '');
                }
                
                if (current < target) requestAnimationFrame(update);
            };
            
            update();
        };
        
        // Intersection Observer で画面に表示されたときのみアニメーション
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    entry.target.classList.add('animated');
                    animateCount(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        statNumbers.forEach(num => observer.observe(num));
    });
    </script>



    <!-- 最新エピソードセクション -->
    <section id="latest-episode" class="latest-episode-section">
        <div class="latest-episode-container">
            <div class="latest-episode-header">
                <h2>最新エピソード</h2>
            </div>
            
            <?php 
            // 投稿記事から最新エピソードを取得
            $latest_episode_query = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 1,
                'meta_key' => 'is_podcast_episode',
                'meta_value' => '1',
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            $latest_episode_id = 0; // 最新エピソードのIDを保存
            
            if ($latest_episode_query->have_posts()) :
                $latest_episode_query->the_post();
                $latest_episode_id = get_the_ID(); // 最新エピソードのIDを取得
                $audio_url = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
            ?>
                <div class="featured-episode">
                    <div class="featured-episode-content">
                        <div class="featured-episode-image">
                            <?php 
                            // アイキャッチ画像をまず確認
                            if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', array(
                                    'alt' => get_the_title(),
                                    'loading' => 'eager' // 最新エピソードは即座に読み込み
                                )); ?>
                            <?php else : 
                                // アイキャッチ画像がない場合、エピソードのメタデータから画像URLを取得を試行
                                $episode_image_url = get_post_meta(get_the_ID(), 'episode_image_url', true);
                                if ($episode_image_url) : ?>
                                    <img src="<?php echo esc_url($episode_image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="eager" style="width: 100%; height: auto; border-radius: 20px;">
                                <?php else : ?>
                                    <div class="featured-episode-default-thumbnail">🎙️</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="featured-episode-details">
                            <div class="episode-meta-info">
                                <span class="episode-date"><?php echo get_the_date('Y年n月j日'); ?></span>
                            </div>
                            
                            <h3 class="featured-episode-title"><?php the_title(); ?></h3>
                            <div class="episode-actions">
                                <a href="<?php the_permalink(); ?>" class="episode-share-btn">詳細を見る</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                wp_reset_postdata();
            else: 
            ?>
                <p>最新のエピソードが見つかりませんでした。</p>
            <?php endif; ?>
        </div>
    </section>


    <!-- エピソード一覧 -->
    <section class="episodes-section">
        <div class="episodes-container">
            <div class="episodes-header fade-in">
                <h2>最近のエピソード</h2>
            </div>

            
            <div class="episodes-grid">
                <?php
                // 投稿記事から最近のエピソードを取得（最新エピソードを除外）
                $recent_episodes_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'meta_key' => 'is_podcast_episode',
                    'meta_value' => '1',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post__not_in' => array($latest_episode_id) // 最新エピソードを除外
                ));
                
                if ($recent_episodes_query->have_posts()) :
                    $delay_index = 0;
                    while ($recent_episodes_query->have_posts()) : $recent_episodes_query->the_post();
                        $audio_url = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                        $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                        $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                        $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
                        $delay_class = 'delay-' . ($delay_index * 100 + 100);
                        $delay_index++;
                ?>
                    <article class="episode-card slide-up <?php echo esc_attr($delay_class); ?>" data-category="<?php echo esc_attr($episode_category); ?>">
                        <div class="episode-thumbnail">
                            <?php 
                            // アイキャッチ画像をまず確認
                            if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium', array(
                                    'alt' => get_the_title(),
                                    'loading' => 'lazy' // 遅延読み込み
                                )); ?>
                            <?php else : 
                                // アイキャッチ画像がない場合、エピソードのメタデータから画像URLを取得を試行
                                $episode_image_url = get_post_meta(get_the_ID(), 'episode_image_url', true);
                                if ($episode_image_url) : ?>
                                    <img src="<?php echo esc_url($episode_image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" style="width: 100%; height: 200px; object-fit: cover;">
                                <?php else : ?>
                                    <div class="episode-default-thumbnail">🎙️</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="episode-content">
                            <div class="episode-date"><?php echo get_the_date('Y年n月j日'); ?></div>
                            <h3 class="episode-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                        </div>
                    </article>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
                    <div class="episodes-empty-state">
                        <h3>エピソードが見つかりませんでした</h3>
                        <p>RSSデータから投稿を作成してください。</p>
                        <?php if (current_user_can('manage_options')) : ?>
                        <a href="<?php echo admin_url('tools.php?page=contentfreaks-sync'); ?>" class="button">
                            RSS同期管理
                        </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- エピソード一覧へのリンク -->
            <div class="episodes-cta">
                <a href="<?php echo get_permalink(get_page_by_path('episodes')); ?>" class="episodes-view-all-btn">
                    🎧 全エピソードを見る
                </a>
                <a href="<?php echo get_permalink(get_page_by_path('blog')); ?>" class="blog-view-all-btn">
                    📖 ブログ記事を見る
                </a>
            </div>
            
        </div>
    </section>

    <!-- ホスト紹介 -->
    <section class="hosts-section">
        <div class="hosts-container">
            <div class="hosts-header fade-in">
                <h2>ABOUT US</h2>
            </div>
            
            <div class="slide-up delay-100">
                <?php echo do_shortcode('[podcast_hosts]'); ?>
            </div>
            
            <!-- プロフィールページへのボタン -->
            <div class="hosts-cta fade-in delay-200">
                <a href="<?php echo get_permalink(get_page_by_path('profile')); ?>" class="hosts-profile-btn btn-primary btn-shine">
                    👥 詳しいプロフィールを見る
                </a>
            </div>
        </div>
    </section>

    <!-- 社会的証明・レビュー -->
    <section class="testimonials-section">
        <div class="testimonials-container">
            <div class="testimonials-header fade-in">
                <h2>リスナーの声</h2>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card scale-in delay-100">
                    <div class="testimonial-quote">
                        いつも配信ありがとうございます！毎度楽しく拝聴しています。お二人が番組内で紹介していたのをきっかけに検索しハマったコンテンツが多くあり、家族や友人に「コンフリの２人がオススメしてた」と話すほど好きな番組です。
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">チ</div>
                        <div class="author-info">
                            <h4>チャリさん</h4>
                            <div class="author-role">GoogleForm</div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card scale-in delay-200">
                    <div class="testimonial-quote">
                        いつも楽しく拝聴させていただいています！自分と違う視点の感想を聞くことが出来て、一緒に盛り上がれるのが嬉しいです。
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">の</div>
                        <div class="author-info">
                            <h4>のじかさん</h4>
                            <div class="author-role">GoogleForm</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ポッドキャストプラットフォーム -->
    <section id="platforms" class="podcast-platforms-section">
        <div class="platforms-container">
            <div class="platforms-header fade-in">
                <h2>どこでも聴ける</h2>
                <p class="platforms-subtitle">お好みのプラットフォームでコンテンツフリークスをお楽しみください</p>
            </div>
            <div class="slide-up delay-100">
                <?php echo do_shortcode('[podcast_platforms]'); ?>
            </div>
        </div>
    </section>


    
</main>

<script>
// 最適化されたフロントページスクリプト
document.addEventListener('DOMContentLoaded', function() {
    // モバイルレイアウトはCSSで制御されているため、
    // JavaScriptでの強制的なスタイル適用は不要
    // 必要に応じてここに軽量な機能を追加
});
</script>

<?php get_footer(); ?>
