<?php
/**
 * Template Name: ポッドキャストトップページテンプレート
 * ポッドキャスト専用のトップページレイアウト
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- ポッドキャスト専用ヒーローセクション -->
    <section class="podcast-hero">
        <div class="podcast-hero-content">
            <div class="podcast-hero-main">
                <!-- アートワークをタイトル直下に配置 -->
                <div class="podcast-hero-artwork">
                    <?php 
                    $podcast_artwork = get_theme_mod('podcast_artwork');
                    if ($podcast_artwork): ?>
                        <img src="<?php echo esc_url($podcast_artwork); ?>" alt="<?php echo esc_attr(get_theme_mod('podcast_name')); ?>" class="podcast-artwork">
                    <?php else: ?>
                        <!-- 一時的なプレースホルダー画像 -->
                        <div class="podcast-artwork" style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); display: flex; align-items: center; justify-content: center; font-size: 4rem; color: var(--black); border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                            🎙️
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- サブタイトルとディスクリプションを整理 -->
                <div class="podcast-hero-text">
                    <p class="podcast-hero-subtitle" style="margin-top: 1rem;">
                        好きな作品、語り尽くそう！
                    </p>
                    <div class="podcast-hero-description">
                        <?php echo esc_html(get_theme_mod('podcast_description', '「コンテンツフリークス」は、大学時代からの友人2人が、アニメやドラマを中心に「いま気になる」エンタメコンテンツを熱く語るポッドキャスト')); ?>
                    </div>
                </div>
                
                <!-- 統計情報をアートワーク下に -->
                <div class="podcast-stats">
                    <div class="podcast-stat">
                        <span class="podcast-stat-number">
                            <?php 
                            // 投稿記事からエピソード数を取得
                            $episode_count = get_posts(array(
                                'meta_key' => 'is_podcast_episode',
                                'meta_value' => '1',
                                'post_status' => 'publish',
                                'numberposts' => -1
                            ));
                            echo count($episode_count);
                            ?>
                        </span>
                        <span class="podcast-stat-label">エピソード</span>
                    </div>
                    <div class="podcast-stat">
                        <span class="podcast-stat-number">1K+</span>
                        <span class="podcast-stat-label">リスナー</span>
                    </div>
                    <div class="podcast-stat">
                        <span class="podcast-stat-number">⭐4.7</span>
                        <span class="podcast-stat-label">評価</span>
                    </div>
                </div>
                
                <!-- ナビゲーションメニューを追加 -->
                <div class="hero-navigation" style="margin: 2rem 0;">
                    <nav class="hero-nav-menu">
                        <a href="<?php echo get_permalink(get_page_by_path('blog')); ?>" class="hero-nav-link">
                            📖 ブログ記事
                        </a>
                        <a href="<?php echo get_permalink(get_page_by_path('episodes')); ?>" class="hero-nav-link">
                            🎧 エピソード一覧
                        </a>
                        <a href="<?php echo get_permalink(get_page_by_path('profile')); ?>" class="hero-nav-link">
                            👥 プロフィール
                        </a>
                        <a href="<?php echo get_permalink(get_page_by_path('history')); ?>" class="hero-nav-link">
                            📜 コンフリの歴史
                        </a>
                    </nav>
                </div>

                <!-- CTAボタンを最下部に -->
                <div class="podcast-hero-cta" style="margin-top: 2rem;">
                    <a href="#latest-episode" class="podcast-cta-primary">
                        🎧 最新エピソードを聴く
                    </a>
                    <a href="<?php echo get_permalink(get_page_by_path('blog')); ?>" class="podcast-cta-secondary">
                        📖 ブログを読む
                    </a>
                </div>
            </div>
        </div>
    </section>

    
    <!-- ポッドキャストプラットフォーム -->
    <section id="platforms" class="podcast-platforms-section">
        <div class="platforms-container">
            <div class="platforms-header">
                <h2>どこでも聴ける</h2>
                <p class="platforms-subtitle">お好みのプラットフォームでコンテンツフリークスを購読しよう</p>
            </div>
            
            <?php echo do_shortcode('[podcast_platforms]'); ?>
            
            <div class="subscribe-section">
                <h3 class="subscribe-title">🔔 更新通知を受け取る</h3>
                <p class="subscribe-description">新しいエピソードが公開されたらすぐにお知らせします</p>
                <div class="subscribe-buttons">
                    <a href="<?php echo home_url('/feed/'); ?>" class="rss-button">
                        📡 RSS購読
                    </a>
                    <a href="#newsletter" class="email-subscribe">
                        ✉️ メール通知
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ホスト紹介 -->
    <section class="hosts-section">
        <div class="hosts-container">
            <div class="hosts-header">
                <h2>ABOUT US</h2>
            </div>
            
            <?php echo do_shortcode('[podcast_hosts]'); ?>
        </div>
    </section>

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
            
            if ($latest_episode_query->have_posts()) :
                $latest_episode_query->the_post();
                $audio_url = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
            ?>
                <div class="featured-episode">
                    <div class="featured-episode-content">
                        <div class="featured-episode-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
                            <?php else : ?>
                                <div style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem;">🎙️</div>
                            <?php endif; ?>
                            
                            <?php if ($episode_number) : ?>
                                <div class="episode-number-badge">EP.<?php echo esc_html($episode_number); ?></div>
                            <?php endif; ?>
                            

                        </div>
                        
                        <div class="featured-episode-details">
                            <div class="episode-meta-info">
                                <span class="episode-date"><?php echo get_the_date('Y年n月j日'); ?></span>
                                <?php if ($duration) : ?>
                                    <span class="episode-duration"><?php echo esc_html($duration); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="featured-episode-title"><?php the_title(); ?></h3>
                            <div class="featured-episode-description"><?php echo get_the_excerpt(); ?></div>
                            
                            <div class="episode-actions">
                                <a href="<?php the_permalink(); ?>" class="episode-share-btn">詳細を見る</a>
                                <a href="<?php echo get_permalink(get_page_by_path('episodes')); ?>" class="episodes-list-btn" style="margin-left: 1rem;">エピソード一覧</a>
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
            <div class="episodes-header">
                <h2>最近のエピソード</h2>
            </div>

            
            <div class="episodes-grid">
                <?php
                // 投稿記事から最近のエピソードを取得
                $recent_episodes_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'meta_key' => 'is_podcast_episode',
                    'meta_value' => '1',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($recent_episodes_query->have_posts()) :
                    while ($recent_episodes_query->have_posts()) : $recent_episodes_query->the_post();
                        $audio_url = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                        $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                        $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
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
                            
                            <!-- <div class="episode-tags">
                                <span class="episode-tag"><?php echo esc_html($episode_category); ?></span>
                            </div> -->
                        </div>
                    </article>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #666;">
                        <h3>エピソードが見つかりませんでした</h3>
                        <p>RSSデータから投稿を作成してください。</p>
                        <?php if (current_user_can('manage_options')) : ?>
                        <a href="<?php echo admin_url('tools.php?page=contentfreaks-sync'); ?>" class="button" style="background: #007cba; color: white; padding: 1rem 2rem; border-radius: 5px; text-decoration: none; display: inline-block; margin-top: 1rem;">
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
                <a href="<?php echo get_permalink(get_page_by_path('blog')); ?>" class="blog-view-all-btn" style="margin-left: 1rem;">
                    📖 ブログ記事を見る
                </a>
            </div>
            
        </div>
    </section>


    <!-- ニュースレター登録 -->
    <section id="newsletter" class="newsletter-section">
        <div class="newsletter-container">
            <div class="newsletter-icon">✉️</div>
            <h2 class="newsletter-title">ニュースレター登録（実装予定）</h2>
            <p class="newsletter-description">
                最新エピソードの通知や、限定コンテンツをお届けします。週1回程度の配信予定です。
            </p>
            
            <form class="newsletter-form" action="#" method="post">
                <input type="email" class="newsletter-input" placeholder="メールアドレスを入力" required>
                <button type="submit" class="newsletter-submit">登録する</button>
            </form>
            
            <div class="newsletter-benefits">
                <div class="newsletter-benefit">
                    <div class="benefit-icon">🎵</div>
                    <h4 class="benefit-title">早期アクセス</h4>
                    <p class="benefit-description">新エピソードを一般公開前にお聴きいただけます</p>
                </div>
                <div class="newsletter-benefit">
                    <div class="benefit-icon">📋</div>
                    <h4 class="benefit-title">限定コンテンツ</h4>
                    <p class="benefit-description">ニュースレター限定のインサイトやリソース</p>
                </div>
                <div class="newsletter-benefit">
                    <div class="benefit-icon">💬</div>
                    <h4 class="benefit-title">コミュニティ</h4>
                    <p class="benefit-description">リスナーコミュニティでの交流機会</p>
                </div>
            </div>
        </div>
    </section>

    <!-- RSS同期状況表示（管理者のみ） -->
    <?php if (current_user_can('manage_options')): ?>
    <section class="rss-sync-status" style="background: #f8f9fa; padding: 2rem 0; border-top: 1px solid #e9ecef;">
        <div class="sync-status-container" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
            <div class="sync-status-header" style="text-align: center; margin-bottom: 1.5rem;">
                <h3 style="color: #495057; margin-bottom: 0.5rem;">🔄 ポッドキャスト管理状況</h3>
                <p style="color: #6c757d; font-size: 0.9rem;">管理者のみ表示</p>
            </div>
            
            <div class="sync-status-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <div class="sync-info-card" style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h4 style="color: #495057; margin-bottom: 1rem;">📊 投稿表示モード</h4>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>表示方式:</span>
                        <strong style="color: #28a745;">✅ WordPress投稿</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>ポッドキャスト投稿:</span>
                        <strong>
                            <?php 
                            $podcast_posts = get_posts(array(
                                'meta_key' => 'is_podcast_episode',
                                'meta_value' => '1',
                                'post_status' => 'publish',
                                'numberposts' => -1
                            ));
                            echo count($podcast_posts);
                            ?> 件
                        </strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>通常のブログ投稿:</span>
                        <strong>
                            <?php 
                            $blog_posts = get_posts(array(
                                'numberposts' => -1,
                                'post_status' => 'publish',
                                'meta_query' => array(
                                    array(
                                        'key' => 'is_podcast_episode',
                                        'compare' => 'NOT EXISTS'
                                    )
                                )
                            ));
                            echo count($blog_posts);
                            ?> 件
                        </strong>
                    </div>
                </div>
                
                <div class="sync-info-card" style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h4 style="color: #495057; margin-bottom: 1rem;">📡 RSS接続状況</h4>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>RSS URL:</span>
                        <strong style="color: #007cba; font-size: 0.8rem;">anchor.fm</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>キャッシュ有効期間:</span>
                        <strong>30分</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>最後の同期:</span>
                        <strong><?php echo get_option('contentfreaks_last_sync_time', '未実行'); ?></strong>
                    </div>
                </div>
                
                <div class="sync-info-card" style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h4 style="color: #495057; margin-bottom: 1rem;">⚙️ 管理機能</h4>
                    <a href="<?php echo admin_url('tools.php?page=contentfreaks-sync'); ?>" 
                       style="display: inline-block; background: #007cba; color: white; padding: 0.7rem 1.5rem; border-radius: 5px; text-decoration: none; margin-bottom: 0.5rem;">
                        RSS同期管理
                    </a>
                    <div style="font-size: 0.85rem; color: #6c757d;">
                        RSS接続状況・キャッシュクリア
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 社会的証明・レビュー -->
    <section class="testimonials-section">
        <div class="testimonials-container">
            <div class="testimonials-header">
                <h2>リスナーの声</h2>
            </div>
<!--             
            <div class="testimonials-stats">
                <div class="testimonial-stat">
                    <span class="stat-number">4.8</span>
                    <span class="stat-label">平均評価</span>
                </div>
                <div class="testimonial-stat">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">レビュー数</span>
                </div>
                <div class="testimonial-stat">
                    <span class="stat-number">95%</span>
                    <span class="stat-label">満足度</span>
                </div>
            </div> -->
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
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
                
                <div class="testimonial-card">
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
</main>

<?php get_footer(); ?>
