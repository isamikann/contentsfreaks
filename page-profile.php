<?php
/**
 * Template Name: プロフィールページ
 */

get_header(); ?>

<main id="main" class="site-main profile-page">
    <!-- プロフィールヒーローセクション -->
    <section class="profile-hero">
        <div class="profile-hero-bg">
            <div class="hero-pattern"></div>
        </div>
        <div class="profile-hero-content">
            <div class="profile-hero-header">
                <div class="profile-hero-icon">🎙️</div>
                <h1 class="profile-hero-title">Meet the Team</h1>
                <p class="profile-hero-subtitle">コンテンツフリークスを支える2人のパーソナリティをご紹介</p>
                <div class="profile-hero-stats">
                    <div class="hero-stat">
                        <span class="stat-number">2</span>
                        <span class="stat-label">パーソナリティ</span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-number"><?php echo contentfreaks_get_podcast_count(); ?></span>
                        <span class="stat-label">エピソード</span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-number"><?php echo esc_attr(get_option('contentfreaks_listener_count', '1500')); ?>+</span>
                        <span class="stat-label">リスナー</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ホストプロフィール詳細 -->
    <section class="profile-details-section">
        <div class="profile-details-container">
            
            <!-- みっくんプロフィール -->
            <div class="host-profile-card host-card-primary">
                <div class="host-profile-header">
                    <div class="host-profile-avatar">
                        <?php 
                        $host1_image = get_theme_mod('host1_image', '');
                        if ($host1_image): ?>
                            <img src="<?php echo esc_url($host1_image); ?>" alt="みっくん" class="host-avatar-image">
                        <?php else: ?>
                            <div class="avatar-placeholder primary-gradient">
                                <span class="avatar-icon">🎙️</span>
                            </div>
                        <?php endif; ?>
                        <div class="avatar-badge">Host</div>
                    </div>
                    <div class="host-profile-info">
                        <h2 class="host-name">みっくん</h2>
                        <p class="host-role">メインパーソナリティ</p>
                        <div class="host-tags">
                            <span class="host-tag primary">コンテンツフリーク</span>
                            <span class="host-tag secondary">司会進行担当</span>
                            <span class="host-tag accent">エンジニア</span>
                        </div>
                        <div class="host-social-links">
                            <?php 
                            $host1_twitter = get_theme_mod('host1_twitter', '');
                            $host1_youtube = get_theme_mod('host1_youtube', '');
                            if ($host1_twitter): ?>
                                <a href="<?php echo esc_url($host1_twitter); ?>" class="social-link twitter" target="_blank" rel="noopener">
                                    <span class="social-icon">🐦</span>
                                </a>
                            <?php endif; ?>
                            <?php if ($host1_youtube): ?>
                                <a href="<?php echo esc_url($host1_youtube); ?>" class="social-link youtube" target="_blank" rel="noopener">
                                    <span class="social-icon">📺</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="host-profile-content">
                    <div class="host-description">
                        <p>コンテンツとポッドキャストをこよなく愛する、メーカー勤務のアプリエンジニア。マンガ・アニメ・ドラマ・映画・小説…ジャンルを問わず楽しむ雑食系クリエイターウォッチャー。</p>
                    </div>
                    
                    <div class="host-details-grid">
                        <div class="host-detail">
                            <div class="detail-icon">🎙</div>
                            <h4 class="detail-title">番組での役割</h4>
                            <p class="detail-content">作品の裏側を深掘り＆司会進行を担当！気になるポイントを引き出しながら、熱く語ります。</p>
                        </div>
                        
                        <div class="host-detail">
                            <div class="detail-icon">📌</div>
                            <h4 class="detail-title">推しキャラタイプ</h4>
                            <p class="detail-content">「憂いはあるが、行動はポジティブ」なキャラクターに心惹かれがち。</p>
                        </div>
                        
                        <div class="host-detail">
                            <div class="detail-icon">💼</div>
                            <h4 class="detail-title">職業</h4>
                            <p class="detail-content">メーカー勤務のアプリエンジニア</p>
                        </div>
                        
                        <div class="host-detail">
                            <div class="detail-icon">🎯</div>
                            <h4 class="detail-title">好きなジャンル</h4>
                            <p class="detail-content">マンガ・アニメ・ドラマ・映画・小説（雑食系）</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- あっきープロフィール -->
            <div class="host-profile-card host-card-secondary">
                <div class="host-profile-header">
                    <div class="host-profile-avatar">
                        <?php 
                        $host2_image = get_theme_mod('host2_image', '');
                        if ($host2_image): ?>
                            <img src="<?php echo esc_url($host2_image); ?>" alt="あっきー" class="host-avatar-image">
                        <?php else: ?>
                            <div class="avatar-placeholder secondary-gradient">
                                <span class="avatar-icon">🎧</span>
                            </div>
                        <?php endif; ?>
                        <div class="avatar-badge">Co-Host</div>
                    </div>
                    <div class="host-profile-info">
                        <h2 class="host-name">あっきー</h2>
                        <p class="host-role">サブパーソナリティ</p>
                        <div class="host-tags">
                            <span class="host-tag primary">コンテンツ見習い</span>
                            <span class="host-tag secondary">一般目線担当</span>
                            <span class="host-tag accent">エンジニア</span>
                        </div>
                        <div class="host-social-links">
                            <?php 
                            $host2_twitter = get_theme_mod('host2_twitter', '');
                            $host2_youtube = get_theme_mod('host2_youtube', '');
                            if ($host2_twitter): ?>
                                <a href="<?php echo esc_url($host2_twitter); ?>" class="social-link twitter" target="_blank" rel="noopener">
                                    <span class="social-icon">🐦</span>
                                </a>
                            <?php endif; ?>
                            <?php if ($host2_youtube): ?>
                                <a href="<?php echo esc_url($host2_youtube); ?>" class="social-link youtube" target="_blank" rel="noopener">
                                    <span class="social-icon">📺</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="host-profile-content">
                    <div class="host-description">
                        <p>コンテンツをほどよく楽しむ、メーカー勤務のハードエンジニア。主にアニメを中心に視聴し、ドラマは「コンテンツフリークス」をきっかけにハマり中。</p>
                    </div>
                    
                    <div class="host-details-grid">
                        <div class="host-detail">
                            <div class="detail-icon">🎙</div>
                            <h4 class="detail-title">番組での役割</h4>
                            <p class="detail-content">一般目線の感想を担当し、親しみやすさをプラス！リスナーと同じ視点で語ります。</p>
                        </div>
                        
                        <div class="host-detail">
                            <div class="detail-icon">📌</div>
                            <h4 class="detail-title">推しキャラタイプ</h4>
                            <p class="detail-content">「一周回って落ち着いた強者」なキャラクターに魅力を感じがち。</p>
                        </div>
                        
                        <div class="host-detail">
                            <div class="detail-icon">💼</div>
                            <h4 class="detail-title">職業</h4>
                            <p class="detail-content">メーカー勤務のハードエンジニア</p>
                        </div>
                        
                        <div class="host-detail">
                            <div class="detail-icon">🎯</div>
                            <h4 class="detail-title">好きなジャンル</h4>
                            <p class="detail-content">主にアニメ中心、ドラマにもハマり中</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 番組での役割説明 -->
    <section class="team-dynamics-section">
        <div class="team-dynamics-container">
            <div class="section-header">
                <h2 class="section-title">Perfect Chemistry</h2>
                <p class="section-subtitle">それぞれの個性を活かした絶妙なコンビネーション</p>
            </div>
            
            <div class="dynamics-visual">
                <div class="host-connection">
                    <div class="host-bubble host1">
                        <div class="bubble-icon">🎙️</div>
                        <div class="bubble-content">
                            <h4>みっくん</h4>
                            <p>深掘り＆分析</p>
                        </div>
                    </div>
                    
                    <div class="connection-line">
                        <div class="connection-icon">⚡</div>
                    </div>
                    
                    <div class="host-bubble host2">
                        <div class="bubble-icon">🎧</div>
                        <div class="bubble-content">
                            <h4>あっきー</h4>
                            <p>親しみやすさ</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="roles-grid">
                <div class="role-card featured">
                    <div class="role-header">
                        <div class="role-icon primary">🎙️</div>
                        <h3 class="role-title">みっくん</h3>
                        <span class="role-badge">Main Host</span>
                    </div>
                    <div class="role-description">
                        <p class="role-summary"><strong>司会進行＆深掘り担当</strong></p>
                        <ul class="role-list">
                            <li><span class="list-icon">🔍</span>作品の裏側や制作背景を分析</li>
                            <li><span class="list-icon">🎯</span>話題の引き出しと流れの管理</li>
                            <li><span class="list-icon">🔥</span>熱いトークで盛り上げ役</li>
                        </ul>
                        <div class="role-stats">
                            <div class="stat-item">
                                <span class="stat-label">分析力</span>
                                <div class="stat-bar">
                                    <div class="stat-fill" style="width: 95%"></div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">話術</span>
                                <div class="stat-bar">
                                    <div class="stat-fill" style="width: 90%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="role-card featured">
                    <div class="role-header">
                        <div class="role-icon secondary">🎧</div>
                        <h3 class="role-title">あっきー</h3>
                        <span class="role-badge">Co-Host</span>
                    </div>
                    <div class="role-description">
                        <p class="role-summary"><strong>一般目線＆親しみやすさ担当</strong></p>
                        <ul class="role-list">
                            <li><span class="list-icon">👁️</span>リスナーと同じ視点での感想</li>
                            <li><span class="list-icon">😊</span>親しみやすい雰囲気作り</li>
                            <li><span class="list-icon">💭</span>気軽に楽しめるトーク</li>
                        </ul>
                        <div class="role-stats">
                            <div class="stat-item">
                                <span class="stat-label">親しみやすさ</span>
                                <div class="stat-bar">
                                    <div class="stat-fill" style="width: 95%"></div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">共感力</span>
                                <div class="stat-bar">
                                    <div class="stat-fill" style="width: 88%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- チームワークハイライト -->
    <section class="teamwork-highlights">
        <div class="teamwork-container">
            <h2 class="section-title">What Makes Us Special</h2>
            <div class="highlights-grid">
                <div class="highlight-card">
                    <div class="highlight-icon">🎯</div>
                    <h3>絶妙なバランス</h3>
                    <p>深い分析と親しみやすさの完璧な組み合わせで、すべてのリスナーが楽しめるコンテンツを提供</p>
                </div>
                <div class="highlight-card">
                    <div class="highlight-icon">🔄</div>
                    <h3>相互補完</h3>
                    <p>お互いの強みを活かし、弱みを補い合う理想的なパートナーシップ</p>
                </div>
                <div class="highlight-card">
                    <div class="highlight-icon">🎨</div>
                    <h3>多角的視点</h3>
                    <p>異なるバックグラウンドから生まれる多様な視点で、コンテンツを多面的に解析</p>
                </div>
            </div>
        </div>
    </section>

    <!-- メディアキット / ビジネス情報セクション -->
    <section class="media-kit-section" id="media-kit">
        <div class="media-kit-container">
            <div class="media-kit-header fade-in">
                <span class="section-badge">📊 Media Kit</span>
                <h2 class="section-title">番組について</h2>
                <p class="section-subtitle">コンテンツフリークスの実績とコラボレーションメニュー</p>
            </div>

            <div class="media-kit-grid">
                <!-- 番組概要 -->
                <div class="media-kit-card fade-in">
                    <h3 class="mk-card-title">🎙️ 番組概要</h3>
                    <dl class="mk-data-list">
                        <div class="mk-data-row">
                            <dt>番組名</dt>
                            <dd><?php echo esc_html(get_theme_mod('podcast_name', 'コンテンツフリークス')); ?></dd>
                        </div>
                        <div class="mk-data-row">
                            <dt>ジャンル</dt>
                            <dd>エンタメ・カルチャー（映画/ドラマ/アニメ/マンガ）</dd>
                        </div>
                        <div class="mk-data-row">
                            <dt>配信頻度</dt>
                            <dd><?php echo esc_html(get_theme_mod('mk_frequency', '毎週配信')); ?></dd>
                        </div>
                        <div class="mk-data-row">
                            <dt>配信開始</dt>
                            <dd><?php echo esc_html(get_theme_mod('mk_since', '2023年')); ?></dd>
                        </div>
                        <div class="mk-data-row">
                            <dt>累計配信</dt>
                            <dd><?php echo contentfreaks_get_podcast_count(); ?>+ エピソード</dd>
                        </div>
                    </dl>
                </div>

                <!-- 実績数値 -->
                <div class="media-kit-card fade-in">
                    <h3 class="mk-card-title">📈 リーチ実績</h3>
                    <div class="mk-stats-grid">
                        <div class="mk-stat">
                            <span class="mk-stat-platform">Spotify</span>
                            <span class="mk-stat-number"><?php echo esc_html(get_theme_mod('mk_spotify_followers', '300')); ?>+</span>
                            <span class="mk-stat-label">フォロワー</span>
                        </div>
                        <div class="mk-stat">
                            <span class="mk-stat-platform">Apple Podcasts</span>
                            <span class="mk-stat-number"><?php echo esc_html(get_theme_mod('mk_apple_followers', '150')); ?>+</span>
                            <span class="mk-stat-label">フォロワー</span>
                        </div>
                        <div class="mk-stat">
                            <span class="mk-stat-platform">YouTube</span>
                            <span class="mk-stat-number"><?php echo esc_html(get_theme_mod('mk_youtube_subscribers', '900')); ?>+</span>
                            <span class="mk-stat-label">登録者</span>
                        </div>
                        <div class="mk-stat mk-stat-total">
                            <span class="mk-stat-platform">合計</span>
                            <span class="mk-stat-number"><?php echo esc_html(get_option('contentfreaks_listener_count', '1500')); ?>+</span>
                            <span class="mk-stat-label">総フォロワー</span>
                        </div>
                    </div>
                    <?php $mk_monthly = get_theme_mod('mk_monthly_plays', ''); if ($mk_monthly) : ?>
                    <div class="mk-monthly">
                        <span class="mk-monthly-label">月間再生数</span>
                        <span class="mk-monthly-number"><?php echo esc_html($mk_monthly); ?>+</span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- コラボ実績 -->
                <div class="media-kit-card fade-in">
                    <h3 class="mk-card-title">🤝 コラボレーション実績</h3>
                    <ul class="mk-collab-list">
                        <li>
                            <span class="collab-name">平成男女のイドバタラジオ</span>
                            <span class="collab-type">ゲスト出演</span>
                        </li>
                        <li>
                            <span class="collab-name">ひよっこ研究者のさばいばる日記</span>
                            <span class="collab-type">ゲスト出演</span>
                        </li>
                        <li>
                            <span class="collab-name">推し活2次元LIFEラジオ</span>
                            <span class="collab-type">コラボ配信</span>
                        </li>
                    </ul>
                </div>

                <!-- 提供可能メニュー -->
                <div class="media-kit-card fade-in">
                    <h3 class="mk-card-title">📋 提供可能メニュー</h3>
                    <div class="mk-menu-list">
                        <div class="mk-menu-item">
                            <div class="mk-menu-icon">🎤</div>
                            <div class="mk-menu-info">
                                <h4>ゲスト出演 / コラボ配信</h4>
                                <p>他ポッドキャスト・YouTubeチャンネルとの相互出演。エンタメ系番組との相性◎</p>
                            </div>
                        </div>
                        <div class="mk-menu-item">
                            <div class="mk-menu-icon">📺</div>
                            <div class="mk-menu-info">
                                <h4>作品紹介タイアップ</h4>
                                <p>映画・ドラマ・アニメ等の作品レビュー。リスナーへの自然な形での紹介が可能</p>
                            </div>
                        </div>
                        <div class="mk-menu-item">
                            <div class="mk-menu-icon">🎪</div>
                            <div class="mk-menu-info">
                                <h4>イベント出演</h4>
                                <p>オフライン/オンラインイベントでのトーク出演・MC</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- お問い合わせセクション（2系統CTA） -->
    <section class="contact-cta-section">
        <div class="contact-cta-bg">
            <div class="cta-pattern"></div>
        </div>
        <div class="contact-cta-container">
            <!-- リスナー向けCTA -->
            <div class="cta-dual-grid">
                <div class="cta-card cta-card-listener">
                    <div class="cta-card-icon">🎧</div>
                    <h3 class="cta-card-title">リスナーの方へ</h3>
                    <p class="cta-card-desc">
                        番組への感想、取り上げてほしいコンテンツ、<br>
                        ご質問などお気軽にどうぞ！
                    </p>
                    <div class="cta-card-buttons">
                        <a href="<?php echo esc_url(contentfreaks_get_page_url('contact')); ?>" class="contact-cta-button listener-primary">
                            <span class="btn-icon">✉️</span>
                            感想・リクエストを送る
                        </a>
                        <a href="<?php echo esc_url(contentfreaks_get_page_url('episodes')); ?>" class="contact-cta-button listener-secondary">
                            <span class="btn-icon">🎧</span>
                            エピソード一覧
                        </a>
                    </div>
                </div>

                <!-- ビジネス向けCTA -->
                <div class="cta-card cta-card-business">
                    <div class="cta-card-icon">💼</div>
                    <h3 class="cta-card-title">お仕事のご依頼</h3>
                    <p class="cta-card-desc">
                        コラボ出演・タイアップ・イベント等、<br>
                        お気軽にご相談ください。
                    </p>
                    <div class="cta-card-buttons">
                        <a href="<?php echo esc_url(contentfreaks_get_page_url('contact')); ?>?type=business" class="contact-cta-button business-primary">
                            <span class="btn-icon">📩</span>
                            お仕事のご依頼・ご相談
                        </a>
                        <a href="#media-kit" class="contact-cta-button business-secondary">
                            <span class="btn-icon">📊</span>
                            Media Kitを見る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- モダンプロフィールページ専用スタイル -->

<?php get_footer(); ?>
