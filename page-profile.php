<?php
/**
 * Template Name: プロフィールページ
 */

get_header(); ?>

<main id="main" class="site-main profile-page">
    <!-- ブレッドクラムナビゲーション -->
    <nav class="breadcrumb-nav">
        <div class="breadcrumb-container">
            <a href="/" class="breadcrumb-home">🏠 ホーム</a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">プロフィール</span>
        </div>
    </nav>

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
                        <span class="stat-number"><?php 
                            $episode_count = get_posts(array(
                                'meta_key' => 'is_podcast_episode',
                                'meta_value' => '1',
                                'post_status' => 'publish',
                                'numberposts' => -1
                            ));
                            echo count($episode_count);
                        ?></span>
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

            <!-- ========== デバッグ開始：あっきープロフィール ========== -->
            <?php echo '<!-- DEBUG: あっきープロフィールセクション開始 -->'; ?>
            
            <!-- あっきープロフィール -->
            <div class="host-profile-card host-card-secondary" style="background: #ffcccc !important; border: 5px solid red !important; display: block !important; visibility: visible !important;">
                <?php echo '<!-- DEBUG: あっきープロフィールカード内部 -->'; ?>
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
            <!-- ========== デバッグ終了：あっきープロフィール ========== -->
            <?php echo '<!-- DEBUG: あっきープロフィールセクション終了 -->'; ?>
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

    <!-- お問い合わせセクション -->
    <section class="contact-cta-section">
        <div class="contact-cta-bg">
            <div class="cta-pattern"></div>
        </div>
        <div class="contact-cta-container">
            <div class="contact-cta-content">
                <div class="cta-icon">💌</div>
                <h2 class="contact-cta-title">Let's Connect!</h2>
                <p class="contact-cta-description">
                    番組への感想、取り上げてほしいコンテンツ、ご質問など、<br>
                    どんなメッセージもお待ちしています！
                </p>
                <div class="cta-buttons">
                    <a href="/contact/" class="contact-cta-button primary">
                        <span class="btn-icon">✉️</span>
                        お問い合わせ
                    </a>
                    <a href="/episodes/" class="contact-cta-button secondary">
                        <span class="btn-icon">🎧</span>
                        エピソード一覧
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- モダンプロフィールページ専用スタイル -->
<style>
/* ===== プロフィールページ専用スタイル - レスポンシブ対応強化版 ===== */

/* ページ全体の上部マージン調整（モダンヘッダー対応） */
body {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

.profile-page {
    background: var(--profile-bg);
    min-height: 100vh;
}

/* コンテナの基本設定 */
.profile-page .container {
    max-width: 100%;
    padding: 0;
}

/* プロフィールヒーローセクション */
.profile-hero {
    position: relative;
    background: var(--hero-bg);
    padding: 1rem 0 3rem 0;
    overflow: hidden;
    min-height: 50vh;
    display: flex;
    align-items: center;
}

/* プロフィールページ用モバイル調整 */
@media (max-width: 768px) {
    .profile-hero {
        padding: 0.5rem 0 2rem 0;
        min-height: 40vh;
    }
}

.profile-hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23f7ff0b10" points="0,1000 500,800 1000,1000"/><circle fill="%23ff6b3520" cx="800" cy="200" r="100"/><circle fill="%23f7ff0b15" cx="200" cy="300" r="80"/></svg>');
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(2deg); }
}

.profile-hero-content {
    position: relative;
    z-index: 2;
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 2rem;
    text-align: center;
}

.profile-hero-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: inline-block;
    animation: pulse 3s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.profile-hero-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    color: var(--hero-text);
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
}

.profile-hero-subtitle {
    font-size: clamp(1.1rem, 2vw, 1.5rem);
    color: var(--hero-text);
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.6;
}

.profile-hero-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
}

.hero-stat {
    text-align: center;
    color: var(--hero-text);
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--hero-accent);
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 0.5rem;
}

/* プロフィール詳細セクション */
.profile-details-section {
    padding: 4rem 0;
    background: var(--hosts-bg);
}

.profile-details-container {
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
}

/* グリッドレイアウトの改善 */
@media (max-width: 1200px) {
    .profile-details-container {
        gap: 2rem;
    }
}

@media (max-width: 1024px) {
    .profile-details-container {
        grid-template-columns: 1fr;
        max-width: 800px;
    }
}

.host-profile-card {
    background: var(--profile-card-bg);
    border-radius: 2rem;
    padding: 2.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--hosts-card-border);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    /* タッチデバイス対応 */
    touch-action: manipulation;
}

.host-profile-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--hosts-title-gradient);
}

/* ホバー対応デバイス */
@media (hover: hover) {
    .host-profile-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hosts-card-shadow-hover);
    }
    
    .host-detail:hover {
        background: var(--profile-card-border);
        transform: translateY(-5px);
    }
    
    .host-detail:hover .detail-icon,
    .host-detail:hover .detail-title,
    .host-detail:hover .detail-content {
        color: var(--black);
    }
    
    .highlight-card:hover {
        background: var(--profile-card-border);
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(247, 255, 11, 0.3);
    }
    
    .highlight-card:hover .highlight-icon,
    .highlight-card:hover h3,
    .highlight-card:hover p {
        color: var(--black);
    }
    
    .role-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--hosts-card-shadow-hover);
    }
}

/* タッチデバイスでの代替効果 */
@media (hover: none) {
    .host-detail:active {
        background: var(--profile-card-border);
        transform: scale(0.98);
    }
    
    .host-detail:active .detail-icon,
    .host-detail:active .detail-title,
    .host-detail:active .detail-content {
        color: var(--black);
    }
}

.host-card-primary::before {
    background: var(--hosts-title-gradient);
}

.host-card-secondary::before {
    background: linear-gradient(90deg, var(--profile-accent), #667eea);
}

.host-profile-header {
    text-align: center;
    margin-bottom: 2rem;
}

.host-profile-avatar {
    position: relative;
    margin-bottom: 1.5rem;
}

.host-avatar-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--profile-card-border);
    box-shadow: var(--shadow-md);
}

.avatar-placeholder {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: var(--shadow-md);
}

.primary-gradient {
    background: var(--hosts-title-gradient);
}

.secondary-gradient {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.avatar-icon {
    font-size: 4rem;
    color: var(--hero-text);
}

.avatar-badge {
    position: absolute;
    bottom: 0;
    right: 10px;
    background: var(--profile-accent);
    color: var(--hero-text);
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.host-name {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.host-role {
    font-size: 1.1rem;
    color: var(--profile-accent);
    font-weight: 600;
    margin-bottom: 1rem;
}

.host-tags {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.host-tag {
    padding: 0.375rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.host-tag.primary {
    background: var(--profile-card-border);
    color: var(--black);
}

.host-tag.secondary {
    background: var(--gray-200);
    color: var(--text-primary);
}

.host-tag.accent {
    background: var(--profile-accent);
    color: var(--hero-text);
}

.host-social-links {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.social-link {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--profile-social);
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: var(--transition);
    /* タッチデバイス対応 */
    min-width: 48px;
    min-height: 48px;
    touch-action: manipulation;
}

/* タッチデバイスでのホバー効果を調整 */
@media (hover: hover) {
    .social-link:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
    
    .social-link.twitter:hover {
        background: #1DA1F2;
    }
    
    .social-link.youtube:hover {
        background: #FF0000;
    }
}

/* タッチデバイスでのタップ対応 */
@media (hover: none) {
    .social-link:active {
        transform: scale(0.95);
    }
}

.social-icon {
    font-size: 1.2rem;
}

.host-description {
    text-align: center;
    margin-bottom: 2rem;
}

.host-description p {
    color: var(--text-secondary);
    line-height: 1.7;
    font-size: 1.1rem;
}

.host-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.host-detail {
    padding: 1.5rem;
    background: var(--gray-50);
    border-radius: 1rem;
    text-align: center;
    transition: var(--transition);
    /* タッチデバイス対応 */
    touch-action: manipulation;
}

.detail-icon {
    font-size: 2rem;
    margin-bottom: 0.75rem;
    display: block;
}

.detail-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.detail-content {
    font-size: 0.9rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

/* チームダイナミクスセクション */
.team-dynamics-section {
    padding: 4rem 0;
    background: var(--episodes-bg);
}

.team-dynamics-container {
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 2rem;
}

.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    font-size: clamp(2.5rem, 4vw, 3.5rem);
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.section-subtitle {
    font-size: 1.2rem;
    color: var(--text-secondary);
    line-height: 1.6;
}

.dynamics-visual {
    display: flex;
    justify-content: center;
    margin-bottom: 3rem;
}

.host-connection {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.host-bubble {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem 2rem;
    background: var(--profile-card-bg);
    border-radius: 2rem;
    box-shadow: var(--shadow-md);
    border: 2px solid var(--profile-card-border);
}

.host-bubble.host2 {
    border-color: var(--profile-accent);
}

.bubble-icon {
    font-size: 2.5rem;
}

.bubble-content h4 {
    margin: 0 0 0.25rem 0;
    font-weight: 700;
    color: var(--text-primary);
}

.bubble-content p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.connection-line {
    display: flex;
    align-items: center;
    position: relative;
}

.connection-line::before {
    content: '';
    width: 80px;
    height: 3px;
    background: var(--hosts-title-gradient);
    border-radius: 2px;
}

.connection-icon {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    background: var(--profile-card-bg);
    color: var(--profile-accent);
    font-size: 1.5rem;
    padding: 0.5rem;
    border-radius: 50%;
    animation: spark 2s ease-in-out infinite;
}

@keyframes spark {
    0%, 100% { transform: translateX(-50%) scale(1); }
    50% { transform: translateX(-50%) scale(1.2); }
}

.roles-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.role-card {
    background: var(--profile-card-bg);
    border-radius: 1.5rem;
    padding: 2rem;
    border: 1px solid var(--hosts-card-border);
    transition: var(--transition);
    /* タッチデバイス対応 */
    touch-action: manipulation;
}

.role-card.featured {
    border: 2px solid var(--profile-card-border);
    box-shadow: var(--shadow-lg);
}

.role-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.role-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1rem;
}

.role-icon.primary {
    background: var(--hosts-title-gradient);
    color: var(--black);
}

.role-icon.secondary {
    background: linear-gradient(135deg, var(--profile-accent), #667eea);
    color: var(--hero-text);
}

.role-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.role-badge {
    display: inline-block;
    background: var(--gray-200);
    color: var(--text-primary);
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.role-summary {
    text-align: center;
    font-size: 1.1rem;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

.role-list {
    list-style: none;
    padding: 0;
    margin: 0 0 1.5rem 0;
}

.role-list li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    color: var(--text-secondary);
}

.list-icon {
    font-size: 1.2rem;
}

.role-stats {
    margin-top: 1.5rem;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.stat-label {
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-weight: 600;
}

.stat-bar {
    flex: 1;
    height: 6px;
    background: var(--gray-200);
    border-radius: 3px;
    margin-left: 1rem;
    overflow: hidden;
}

.stat-fill {
    height: 100%;
    background: var(--hosts-title-gradient);
    border-radius: 3px;
    transition: width 1s ease;
}

/* チームワークハイライト */
.teamwork-highlights {
    padding: 4rem 0;
    background: var(--hosts-bg);
}

.teamwork-container {
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 2rem;
    text-align: center;
}

.highlights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.highlight-card {
    padding: 2rem;
    background: var(--episodes-bg);
    border-radius: 1.5rem;
    transition: var(--transition);
    /* タッチデバイス対応 */
    touch-action: manipulation;
}

.highlight-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.highlight-card h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.highlight-card p {
    color: var(--text-secondary);
    line-height: 1.6;
}

/* お問い合わせCTAセクション */
.contact-cta-section {
    position: relative;
    background: var(--hero-bg);
    padding: 4rem 0;
    overflow: hidden;
}

.contact-cta-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.cta-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><circle fill="%23f7ff0b15" cx="100" cy="100" r="50"/><circle fill="%23ff6b3520" cx="900" cy="200" r="80"/><polygon fill="%23f7ff0b10" points="200,800 400,600 600,800"/></svg>');
}

.contact-cta-container {
    position: relative;
    z-index: 2;
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 2rem;
    text-align: center;
}

.cta-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: inline-block;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.contact-cta-title {
    font-size: clamp(2.5rem, 4vw, 3.5rem);
    font-weight: 800;
    color: var(--hero-text);
    margin-bottom: 1rem;
}

.contact-cta-description {
    font-size: 1.2rem;
    color: var(--hero-text);
    opacity: 0.9;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.cta-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.contact-cta-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 16px 32px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1.1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    min-width: 200px;
    justify-content: center;
    /* タッチデバイス対応 */
    min-height: 48px;
    touch-action: manipulation;
    cursor: pointer;
}

.contact-cta-button.primary {
    background: var(--profile-card-border);
    color: var(--black);
    border: 2px solid var(--profile-card-border);
}

.contact-cta-button.secondary {
    background: transparent;
    color: var(--hero-text);
    border: 2px solid var(--hero-text);
}

/* ホバー対応デバイス */
@media (hover: hover) {
    .contact-cta-button.primary:hover {
        background: var(--primary-light);
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(247, 255, 11, 0.4);
    }
    
    .contact-cta-button.secondary:hover {
        background: var(--hero-text);
        color: var(--black);
        transform: translateY(-3px);
    }
}

/* タッチデバイス */
@media (hover: none) {
    .contact-cta-button:active {
        transform: scale(0.98);
    }
}

.btn-icon {
    font-size: 1.2rem;
}

/* レスポンシブ対応 - 改善版 */

/* タブレット（1024px以下） */
@media (max-width: 1024px) {
    .profile-details-container {
        grid-template-columns: 1fr;
        gap: 2rem;
        padding: 0 1.5rem;
    }
    
    .roles-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .host-connection {
        flex-direction: column;
        gap: 1rem;
    }
    
    .connection-line::before {
        width: 3px;
        height: 50px;
        transform: rotate(90deg);
    }
    
    .connection-icon {
        transform: translateX(-50%) rotate(90deg);
    }
    
    .highlights-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }
}

/* ===== レスポンシブデザイン - ホストセクション完全対応 ===== */

/* 大型タブレット（1024px以下） */
@media (max-width: 1024px) {
    .profile-details-container {
        padding: 0 1.5rem;
    }
    
    .host-profile-card {
        padding: 2rem 1.75rem;
        margin-bottom: 2rem;
    }
    
    .host-details-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
    }
    
    .host-detail {
        padding: 1.25rem;
    }
}

/* タブレット（768px-1023px） */
@media (min-width: 768px) and (max-width: 1023px) {
    .profile-hero {
        padding: 1.5rem 0 2.5rem 0;
    }
    
    .profile-hero-icon {
        font-size: 4.5rem;
        margin-bottom: 1.25rem;
    }
    
    .profile-hero-title {
        font-size: 2.25rem;
        margin-bottom: 1.25rem;
    }
    
    .profile-hero-subtitle {
        font-size: 1.3rem;
        margin-bottom: 2.25rem;
    }
    
    .profile-hero-stats {
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 2.5rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .stat-number {
        font-size: 2.25rem;
    }
    
    .stat-label {
        font-size: 0.85rem;
    }
    
    .host-avatar-image,
    .avatar-placeholder {
        width: 130px;
        height: 130px;
    }
    
    .avatar-icon {
        font-size: 3.25rem;
    }
    
    .host-name {
        font-size: 1.75rem;
    }
    
    .host-role {
        font-size: 1.15rem;
    }
    
    .host-details-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

/* タブレット小（768px以下） */
@media (max-width: 768px) {
    .profile-hero {
        padding: 0.5rem 0 2rem 0;
    }
    
    .profile-hero-content {
        padding: 0 1rem;
    }
    
    .profile-hero-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
    
    .profile-hero-title {
        font-size: 2rem;
        margin-bottom: 1rem;
        line-height: 1.2;
    }
    
    .profile-hero-subtitle {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        line-height: 1.4;
    }
    
    .profile-hero-stats {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 2rem;
    }
    
    .hero-stat {
        min-width: 80px;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
    }
    
    .profile-details-section {
        padding: 2.5rem 0;
    }
    
    .profile-details-container {
        padding: 0 1rem;
    }
    
    .host-profile-card {
        padding: 2rem 1.5rem;
        border-radius: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .host-avatar-image,
    .avatar-placeholder {
        width: 140px;
        height: 140px;
    }
    
    .avatar-icon {
        font-size: 3.5rem;
    }
    
    .avatar-badge {
        font-size: 0.8rem;
        padding: 0.25rem 0.75rem;
    }
    
    .host-name {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }
    
    .host-role {
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }
    
    .host-tags {
        gap: 0.375rem;
        justify-content: center;
    }
    
    .host-tag {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .host-social-links {
        gap: 0.75rem;
    }
    
    .social-link {
        width: 42px;
        height: 42px;
        min-width: 44px;
        min-height: 44px;
    }
    
    .host-details-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .host-detail {
        padding: 1.25rem;
    }
    
    .detail-icon {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }
    
    .detail-title {
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }
    
    .detail-content {
        font-size: 0.85rem;
        line-height: 1.6;
    }
    
    .section-title {
        font-size: clamp(2rem, 6vw, 3rem);
    }
    
    .section-subtitle {
        font-size: 1.1rem;
        line-height: 1.5;
    }
    
    .highlights-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .highlight-card {
        padding: 1.5rem;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }
    
    .contact-cta-button {
        width: 100%;
        max-width: 280px;
        padding: 14px 24px;
        font-size: 1rem;
    }
    
    .team-dynamics-container,
    .teamwork-container,
    .contact-cta-container {
        padding: 0 1rem;
    }
}
    }
    
    .highlight-card {
        padding: 1.5rem;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }
    
    .contact-cta-button {
        width: 100%;
        max-width: 280px;
        padding: 14px 24px;
        font-size: 1rem;
    }
    
    .team-dynamics-container,
    .profile-details-container,
    .teamwork-container,
    .contact-cta-container {
        padding: 0 1rem;
    }
}

/* スマートフォン（480px以下） */
@media (max-width: 480px) {
    .breadcrumb-container {
        padding: 0.75rem 1rem;
    }
    
    .breadcrumb-nav {
        margin-bottom: 0.5rem;
    }
    
    .profile-hero {
        padding: 0.25rem 0 1.5rem 0;
    }
    
    .profile-hero-icon {
        font-size: 3rem;
        margin-bottom: 0.75rem;
    }
    
    .profile-hero-title {
        margin-bottom: 0.75rem;
    }
    
    .profile-hero-subtitle {
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }
    
    .profile-hero-stats {
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .stat-number {
        font-size: 1.75rem;
    }
    
    .stat-label {
        font-size: 0.75rem;
    }
    
    .profile-details-section {
        padding: 2rem 0;
    }
    
    .host-profile-card {
        padding: 1.5rem 1rem;
        border-radius: 1rem;
        margin-bottom: 1rem;
    }
    
    .host-avatar-image,
    .avatar-placeholder {
        width: 120px;
        height: 120px;
    }
    
    .avatar-icon {
        font-size: 3rem;
    }
    
    .avatar-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.6rem;
    }
    
    .host-name {
        font-size: 1.5rem;
        margin-bottom: 0.375rem;
    }
    
    .host-role {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .host-description p {
        font-size: 1rem;
        line-height: 1.6;
    }
    
    .host-detail {
        padding: 1rem;
    }
    
    .detail-icon {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }
    
    .detail-title {
        font-size: 0.9rem;
        margin-bottom: 0.375rem;
    }
    
    .detail-content {
        font-size: 0.8rem;
    }
    
    .team-dynamics-section {
        padding: 2rem 0;
    }
    
    .section-header {
        margin-bottom: 2rem;
    }
    
    .dynamics-visual {
        margin-bottom: 2rem;
    }
    
    .host-bubble {
        padding: 1rem 1.25rem;
        border-radius: 1.5rem;
        flex-direction: column;
        text-align: center;
    }
    
    .bubble-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .role-card {
        padding: 1.25rem;
        border-radius: 1rem;
    }
    
    .role-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
    }
    
    .role-title {
        font-size: 1.25rem;
        margin-bottom: 0.375rem;
    }
    
    .role-summary {
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    
    .role-list li {
        gap: 0.5rem;
        padding: 0.375rem 0;
        font-size: 0.85rem;
        line-height: 1.4;
    }
    
    .list-icon {
        font-size: 1rem;
        flex-shrink: 0;
    }
    
    .stat-item {
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
    }
    
    .stat-bar {
        margin-left: 0.75rem;
        height: 5px;
    }
    
    .teamwork-highlights {
        padding: 2rem 0;
    }
    
    .highlight-card {
        padding: 1.25rem;
        border-radius: 1rem;
    }
    
    .highlight-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }
    
    .highlight-card h3 {
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
    }
    
    .highlight-card p {
        font-size: 0.9rem;
    }
    
    .contact-cta-section {
        padding: 2rem 0;
    }
    
    .cta-icon {
        font-size: 3rem;
        margin-bottom: 0.75rem;
    }
    
    .contact-cta-title {
        margin-bottom: 0.75rem;
    }
    
    .contact-cta-description {
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .contact-cta-description br {
        display: none;
    }
    
    .cta-buttons {
        margin-bottom: 2rem;
    }
}

/* 超小型デバイス（360px以下） */
@media (max-width: 360px) {
    .profile-hero-content {
        padding: 0 0.75rem;
    }
    
    .host-profile-card {
        padding: 1.25rem 0.75rem;
    }
    
    .host-avatar-image,
    .avatar-placeholder {
        width: 100px;
        height: 100px;
    }
    
    .avatar-icon {
        font-size: 2.5rem;
    }
    
    .host-name {
        font-size: 1.25rem;
    }
    
    .host-detail {
        padding: 0.75rem;
    }
    
    .role-card {
        padding: 1rem;
    }
    
    .highlight-card {
        padding: 1rem;
    }
    
    .contact-cta-button {
        padding: 12px 20px;
        font-size: 0.95rem;
    }
}

/* ===== アクセシビリティとユーザビリティの改善 ===== */

/* フォーカス状態の改善 */
.social-link:focus,
.contact-cta-button:focus,
.host-detail:focus {
    outline: 2px solid var(--profile-card-border);
    outline-offset: 2px;
}

/* 読みやすさの改善 */
.host-description p,
.contact-cta-description,
.highlight-card p {
    line-height: 1.7;
    font-size: clamp(0.9rem, 2.5vw, 1.1rem);
}

/* モーション設定の尊重 */
@media (prefers-reduced-motion: reduce) {
    .profile-hero-icon,
    .connection-icon,
    .cta-icon,
    .hero-pattern {
        animation: none;
    }
    
    .host-profile-card,
    .host-detail,
    .role-card,
    .highlight-card,
    .contact-cta-button,
    .social-link {
        transition: none;
    }
}

/* ダークモード対応の準備 */
@media (prefers-color-scheme: dark) {
    .profile-page {
        /* 将来のダークモード対応 */
    }
}

/* 印刷スタイル */
@media print {
    .profile-hero {
        background: var(--hosts-bg) !important;
        color: var(--text-primary) !important;
        box-shadow: none !important;
    }
    
    .profile-hero-content * {
        color: var(--text-primary) !important;
    }
    
    .contact-cta-section {
        display: none;
    }
    
    .host-profile-card,
    .role-card,
    .highlight-card {
        box-shadow: none !important;
        border: 1px solid var(--hosts-card-border) !important;
    }
}
</style>

<?php get_footer(); ?>
