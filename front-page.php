<?php
/**
 * Template Name: ポッドキャストトップページテンプレート
 * ポッドキャスト専用のトップページレイアウト
 */

get_header(); ?>


<main id="main-content" class="site-main" role="main">
    <!-- ポッドキャスト専用ヒーローセクション -->
    <section class="podcast-hero" aria-labelledby="hero-title">
        <div class="podcast-hero-content">
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
            
            <h1 id="hero-title" class="hero-title">ContentFreaks</h1>
            <p class="hero-subtitle">好きな作品、語り尽くそう！</p>
            
            <p class="podcast-hero-description">
                <?php echo esc_html(get_theme_mod('podcast_description', '「コンテンツフリークス」は、大学時代からの友人2人で「いま気になる」注目のエンタメコンテンツを熱く語るポッドキャスト')); ?>
            </p>
        </div>
    </section>



    <!-- 最新エピソードセクション -->
    <section id="latest-episode" class="latest-episode-section">
        <div class="latest-episode-container">
            <div class="latest-episode-header">
                <h2>最新エピソード</h2>
                <p class="section-subtitle">今一番新しい配信をチェック</p>
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
                                <span class="episode-date"><?php echo get_the_date('Y.n.j'); ?></span>
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

    <!-- 今週のピックアップ -->
    <?php
    $pickup_ids_raw = get_option('contentfreaks_pickup_episodes', '');
    $pickup_ids = array_filter(array_map('intval', explode(',', $pickup_ids_raw)));
    if (!empty($pickup_ids)) :
        $pickup_query = new WP_Query(array(
            'post_type' => 'post',
            'post__in' => $pickup_ids,
            'orderby' => 'post__in',
            'posts_per_page' => count($pickup_ids),
        ));
        if ($pickup_query->have_posts()) :
    ?>
    <section class="pickup-section">
        <div class="pickup-container">
            <div class="pickup-header fade-in">
                <h2>⭐ 今週のピックアップ</h2>
                <p class="pickup-subtitle">編集部おすすめのエピソード</p>
            </div>
            <div class="episodes-grid">
                <?php
                while ($pickup_query->have_posts()) : $pickup_query->the_post();
                    get_template_part('template-parts/episode-card');
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
    <?php endif; endif; ?>


    <!-- エピソード一覧 -->
    <section class="episodes-section">
        <div class="episodes-container">
            <div class="episodes-header fade-in">
                <h2>最近のエピソード</h2>
                <p class="section-subtitle">過去の配信をさかのぼって見る</p>
            </div>

            
            <div class="episodes-grid">
                <?php
                // 投稿記事から最近のエピソードを取得（最新エピソードを除外）
                $recent_episodes_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 6,
                    'meta_key' => 'is_podcast_episode',
                    'meta_value' => '1',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post__not_in' => array($latest_episode_id) // 最新エピソードを除外
                ));
                
                if ($recent_episodes_query->have_posts()) :
                    while ($recent_episodes_query->have_posts()) : $recent_episodes_query->the_post();
                        get_template_part('template-parts/episode-card');
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
                <p class="section-subtitle">番組を応援してくれる皆さんの感想</p>
            </div>
            
            <?php
            // DB管理のリスナーの声を表示
            $testimonials_query = new WP_Query(array(
                'post_type' => 'testimonial',
                'posts_per_page' => 6,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
            ));

            if ($testimonials_query->have_posts()) :
            ?>
            <div class="testimonials-grid">
                <?php while ($testimonials_query->have_posts()) : $testimonials_query->the_post();
                    $t_name = get_post_meta(get_the_ID(), 'testimonial_name', true) ?: '匿名';
                    $t_source = get_post_meta(get_the_ID(), 'testimonial_source', true) ?: '';
                    $t_initial = mb_substr($t_name, 0, 1);
                ?>
                <div class="testimonial-card scale-in">
                    <div class="testimonial-quote">
                        <?php echo esc_html(get_the_content()); ?>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar"><?php echo esc_html($t_initial); ?></div>
                        <div class="author-info">
                            <h4><?php echo esc_html($t_name); ?>さん</h4>
                            <?php if ($t_source) : ?>
                                <div class="author-role"><?php echo esc_html($t_source); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <?php else : ?>
            <!-- DB未登録時のフォールバック（既存のハードコードデータ） -->
            <div class="testimonials-grid">
                <div class="testimonial-card scale-in">
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
                <div class="testimonial-card scale-in">
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
            <?php endif; ?>

            <!-- リスナー投稿フォーム -->
            <div class="testimonial-form-wrapper fade-in">
                <h3 class="form-title">✉️ あなたの声を聞かせてください</h3>
                <form id="testimonial-form" class="testimonial-form">
                    <div class="form-row">
                        <input type="text" name="name" placeholder="お名前（ニックネーム可）" required maxlength="50" class="form-input">
                    </div>
                    <div class="form-row">
                        <textarea name="message" placeholder="番組への感想を書いてください（500文字以内）" required maxlength="500" rows="4" class="form-input form-textarea"></textarea>
                    </div>
                    <!-- ハニーポット（スパム対策） -->
                    <div class="form-row" style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;" aria-hidden="true">
                        <label for="website_url">ウェブサイト</label>
                        <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
                    </div>
                    <button type="submit" class="form-submit-btn">送信する</button>
                    <div id="form-message" class="form-message" style="display:none;"></div>
                </form>
            </div>
        </div>
    </section>

    <!-- 実績バッジセクション -->
    <section class="achievements-badge-section">
        <div class="achievements-badge-container">
            <div class="achievements-header">
                <h2>Media Kit</h2>
            </div>
            <div class="achievements-badges">
                <div class="achievement-badge fade-in">
                    <span class="badge-icon">🎙️</span>
                    <span class="badge-value"><?php echo contentfreaks_get_podcast_count(); ?>+回</span>
                    <span class="badge-label">配信実績</span>
                </div>
                <div class="achievement-badge fade-in">
                    <span class="badge-icon">👥</span>
                    <span class="badge-value"><?php echo esc_html(get_option('contentfreaks_listener_count', '1500')); ?>+</span>
                    <span class="badge-label">総フォロワー</span>
                </div>
                <div class="achievement-badge fade-in">
                    <span class="badge-icon">🤝</span>
                    <span class="badge-value">3+組</span>
                    <span class="badge-label">コラボ実績</span>
                </div>
                <div class="achievement-badge fade-in">
                    <span class="badge-icon">⭐</span>
                    <span class="badge-value">4.7</span>
                    <span class="badge-label">平均評価</span>
                </div>
            </div>
            <div class="achievements-cta fade-in">
                <a href="<?php echo esc_url(contentfreaks_get_page_url('media-kit')); ?>" class="achievements-business-link">
                    お仕事のご依頼・Media Kit →
                </a>
            </div>
        </div>
    </section>

    <!-- 購読CTAセクション（プラットフォーム紹介を統合） -->
    <section id="platforms" class="subscribe-cta-section">
        <div class="subscribe-cta-inner">
            <h2 class="subscribe-cta-title">番組を聴いてみませんか？</h2>
            <p class="subscribe-cta-desc">お好きなプラットフォームでコンテンツフリークスを購読して、最新エピソードを見逃さずチェックしましょう。</p>
            <?php echo do_shortcode('[podcast_platforms]'); ?>
        </div>
    </section>

    
</main>

<?php get_footer(); ?>
