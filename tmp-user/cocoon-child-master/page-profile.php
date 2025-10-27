<?php
/**
 * Template Name: プロフィールページ
 */

get_header(); ?>

<main id="main" class="site-main">
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
        <div class="profile-hero-content">
            <div class="profile-hero-header">
                <h1 class="profile-hero-title">プロフィール詳細</h1>
                <p class="profile-hero-subtitle">コンテンツフリークスを支える2人のパーソナリティをご紹介</p>
            </div>
        </div>
    </section>

    <!-- ホストプロフィール詳細 -->
    <section class="profile-details-section">
        <div class="profile-details-container">
            
            <!-- みっくんプロフィール -->
            <div class="host-profile-card">
                <div class="host-profile-header">
                    <div class="host-profile-avatar">
                        <?php 
                        $host1_image = get_theme_mod('host1_image', '');
                        if ($host1_image): ?>
                            <img src="<?php echo esc_url($host1_image); ?>" alt="みっくん" class="host-avatar-image">
                        <?php else: ?>
                            <div class="avatar-placeholder" style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: var(--black); border-radius: 50%; width: 120px; height: 120px;">
                                🎙️
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="host-profile-info">
                        <h2 class="host-name">みっくん</h2>
                        <p class="host-role">コンテンツフリークス　パーソナリティ</p>
                        <div class="host-tags">
                            <span class="host-tag">コンテンツフリーク</span>
                            <span class="host-tag">司会進行担当</span>
                        </div>
                    </div>
                </div>
                
                <div class="host-profile-content">
                    <div class="host-description">
                        <p>コンテンツとポッドキャストをこよなく愛する、メーカー勤務のアプリエンジニア。マンガ・アニメ・ドラマ・映画・小説…ジャンルを問わず楽しむ雑食系。</p>
                    </div>
                    
                    <div class="host-details-grid">
                        <div class="host-detail">
                            <h4 class="detail-title">🎙 「コンテンツフリークス」では？</h4>
                            <p class="detail-content">作品の裏側を深掘り＆司会進行を担当！気になるポイントを引き出しながら、熱く語ります。</p>
                        </div>
                        
                        <div class="host-detail">
                            <h4 class="detail-title">📌 推しになりがちなキャラは？</h4>
                            <p class="detail-content">「憂いはあるが、行動はポジティブ」なキャラクターに心惹かれがち。</p>
                        </div>
                        
                        <div class="host-detail">
                            <h4 class="detail-title">💼 職業</h4>
                            <p class="detail-content">メーカー勤務のアプリエンジニア</p>
                        </div>
                        
                        <div class="host-detail">
                            <h4 class="detail-title">🎯 好きなジャンル</h4>
                            <p class="detail-content">マンガ・アニメ・ドラマ・映画・小説（雑食系）</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- あっきープロフィール -->
            <div class="host-profile-card">
                <div class="host-profile-header">
                    <div class="host-profile-avatar">
                        <?php 
                        $host2_image = get_theme_mod('host2_image', '');
                        if ($host2_image): ?>
                            <img src="<?php echo esc_url($host2_image); ?>" alt="あっきー" class="host-avatar-image">
                        <?php else: ?>
                            <div class="avatar-placeholder" style="background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; border-radius: 50%; width: 120px; height: 120px;">
                                🎧
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="host-profile-info">
                        <h2 class="host-name">あっきー</h2>
                        <p class="host-role">コンテンツフリークス　パーソナリティ</p>
                        <div class="host-tags">
                            <span class="host-tag">コンテンツ見習い</span>
                            <span class="host-tag">一般目線担当</span>
                        </div>
                    </div>
                </div>
                
                <div class="host-profile-content">
                    <div class="host-description">
                        <p>コンテンツをほどよく楽しむ、メーカー勤務のハードエンジニア。主にアニメを中心に視聴し、ドラマは「コンテンツフリークス」をきっかけにハマり中。</p>
                    </div>
                    
                    <div class="host-details-grid">
                        <div class="host-detail">
                            <h4 class="detail-title">🎙 「コンテンツフリークス」では？</h4>
                            <p class="detail-content">一般目線の感想を担当し、親しみやすさをプラス！リスナーと同じ視点で語ります。</p>
                        </div>
                        
                        <div class="host-detail">
                            <h4 class="detail-title">📌 推しになりがちなキャラは？</h4>
                            <p class="detail-content">「一周回って落ち着いた強者」なキャラクターに魅力を感じがち。</p>
                        </div>
                        
                        <div class="host-detail">
                            <h4 class="detail-title">💼 職業</h4>
                            <p class="detail-content">メーカー勤務のハードエンジニア</p>
                        </div>
                        
                        <div class="host-detail">
                            <h4 class="detail-title">🎯 好きなジャンル</h4>
                            <p class="detail-content">主にアニメ中心、ドラマにもハマり中</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 番組での役割説明 -->
    <section class="roles-section">
        <div class="roles-container">
            <div class="roles-header">
                <h2>番組での役割</h2>
                <p class="roles-subtitle">それぞれの個性を活かした番組作り</p>
            </div>
            
            <div class="roles-grid">
                <div class="role-card">
                    <div class="role-icon">🎙️</div>
                    <h3 class="role-title">みっくん</h3>
                    <div class="role-description">
                        <p><strong>司会進行＆深掘り担当</strong></p>
                        <ul>
                            <li>作品の裏側や制作背景を分析</li>
                            <li>話題の引き出しと流れの管理</li>
                            <li>熱いトークで盛り上げ役</li>
                        </ul>
                    </div>
                </div>
                
                <div class="role-card">
                    <div class="role-icon">🎧</div>
                    <h3 class="role-title">あっきー</h3>
                    <div class="role-description">
                        <p><strong>一般目線＆親しみやすさ担当</strong></p>
                        <ul>
                            <li>リスナーと同じ視点での感想</li>
                            <li>親しみやすい雰囲気作り</li>
                            <li>気軽に楽しめるトーク</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- お問い合わせセクション -->
    <section class="contact-cta-section">
        <div class="contact-cta-container">
            <div class="contact-cta-content">
                <h2 class="contact-cta-title">何かご質問やメッセージはありますか？</h2>
                <p class="contact-cta-description">
                    番組への感想、取り上げてほしいコンテンツ、ご質問など、<br>
                    お気軽にお聞かせください！
                </p>
                <a href="/contact/" class="contact-cta-button">
                    ✉️ お問い合わせ
                </a>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
