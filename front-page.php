<?php
/**
 * Template Name: ポッドキャストトップページテンプレート
 * ポッドキャスト専用のトップページレイアウト
 */

get_header(); ?>

<style>
/* ヒーローセクションの基本スタイル */
.podcast-hero {
    position: relative;
    min-height: 80vh;
    background: var(--front-hero-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 2rem 0;
}

/* モバイル最適化: ヒーロー高さを大幅削減 */
@media (max-width: 968px) {
    .podcast-hero {
        min-height: 65vh;
        padding: 1.2rem 0;
    }
}

/* フロントページ特有のモバイル調整 */
@media (max-width: 768px) {
    .podcast-hero {
        min-height: 60vh;
        padding: 0.8rem 0;
    }
}

@media (max-width: 480px) {
    .podcast-hero {
        min-height: 55vh;
        padding: 0.6rem 0;
    }
}

.podcast-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="30" cy="30" r="1.5" fill="rgba(255,255,255,0.08)"/><circle cx="70" cy="15" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="2" fill="rgba(255,255,255,0.06)"/><circle cx="50" cy="70" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="20" cy="80" r="1.5" fill="rgba(255,255,255,0.08)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    animation: grain-animation 20s infinite linear;
    pointer-events: none;
}

@keyframes grain-animation {
    0% { transform: translate(0, 0); }
    25% { transform: translate(-10px, -10px); }
    50% { transform: translate(10px, -20px); }
    75% { transform: translate(-20px, 10px); }
    100% { transform: translate(0, 0); }
}

.podcast-hero-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: var(--front-hero-particle);
    border-radius: 50%;
    animation: float 6s infinite ease-in-out;
}

.particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 8s; }
.particle:nth-child(2) { left: 20%; animation-delay: 1s; animation-duration: 6s; }
.particle:nth-child(3) { left: 30%; animation-delay: 2s; animation-duration: 7s; }
.particle:nth-child(4) { left: 40%; animation-delay: 0.5s; animation-duration: 9s; }
.particle:nth-child(5) { left: 50%; animation-delay: 1.5s; animation-duration: 5s; }
.particle:nth-child(6) { left: 60%; animation-delay: 2.5s; animation-duration: 8s; }
.particle:nth-child(7) { left: 70%; animation-delay: 0.8s; animation-duration: 6s; }
.particle:nth-child(8) { left: 80%; animation-delay: 1.8s; animation-duration: 7s; }
.particle:nth-child(9) { left: 90%; animation-delay: 0.3s; animation-duration: 9s; }

@keyframes float {
    0%, 100% { transform: translateY(100vh) rotate(0deg) scale(0); opacity: 0; }
    10% { opacity: 1; transform: translateY(90vh) rotate(45deg) scale(1); }
    90% { opacity: 1; transform: translateY(-10vh) rotate(315deg) scale(1); }
    100% { opacity: 0; transform: translateY(-20vh) rotate(360deg) scale(0); }
}

.podcast-hero-content {
    position: relative;
    z-index: 10;
    color: white;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.podcast-hero-main {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
    animation: fadeInUp 1s ease-out;
}

/* podcast-hero-content-blockの追加に対応 */
.podcast-hero-content-block {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
    width: 100%;
}

.podcast-hero-sidebar {
    display: flex;
    flex-direction: column;
    gap: 3rem;
    align-items: center;
    justify-content: center;
    text-align: center;
    height: 100%;
}

.sidebar-section-title {
    font-size: var(--card-title-large);
    font-weight: 700;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 1.5rem;
    text-align: center;
    letter-spacing: 0.5px;
}

.stats-section,
.navigation-section {
    width: 100%;
    margin-bottom: 2.5rem;
}

/* フロントページ特有のアニメーション */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-title {
    font-size: var(--hero-title);
    font-weight: 900;
    margin-bottom: 1rem;
    background: var(--front-hero-title-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 4px 20px rgba(0,0,0,0.3);
    line-height: 1.1;
    text-align: left;
}

.hero-subtitle {
    font-size: var(--hero-subtitle);
    font-weight: 800;
    margin-bottom: 1.5rem;
    color: var(--front-hero-text);
    letter-spacing: 0.5px;
    text-align: left;
}

.podcast-hero-artwork {
    margin: 1.5rem 0;
    position: relative;
    animation: gentleGlow 3s ease-in-out infinite;
    align-self: center;
    /* 幅を制限してレイアウトの崩れを防ぐ */
    max-width: 300px;
    width: 100%;
}

.podcast-artwork {
    width: 300px;
    height: 300px;
    border-radius: 25px;
    box-shadow: var(--front-hero-artwork-shadow);
    transition: transform 0.3s ease;
}

.podcast-artwork:hover {
    transform: scale(1.05) rotate(2deg);
}

@keyframes gentleGlow {
    0%, 100% {
        box-shadow: 
            0 20px 40px rgba(0,0,0,0.3),
            0 0 0 8px rgba(255,255,255,0.1),
            0 0 0 16px rgba(255,255,255,0.05),
            0 0 30px rgba(255,255,255,0.15);
    }
    50% {
        box-shadow: 
            0 22px 44px rgba(0,0,0,0.32),
            0 0 0 9px rgba(255,255,255,0.12),
            0 0 0 18px rgba(255,255,255,0.06),
            0 0 40px rgba(255,255,255,0.2);
    }
}

.podcast-hero-text {
    margin: 1.5rem 0;
    text-align: center; /* content-block内では中央揃え */
    width: 100%;
}

.podcast-hero-description {
    font-size: var(--body-text);
    line-height: 1.6;
    color: var(--front-hero-text);
    max-width: 500px;
    margin: 0 auto; /* 中央揃えのためのマージン */
}

.podcast-stats {
    display: flex;
    flex-direction: row;
    gap: 1.2rem;
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    justify-content: center;
    flex-wrap: wrap;
}

.podcast-stat {
    text-align: center;
    background: var(--front-nav-link-bg);
    padding: 1.3rem 1rem;
    border-radius: 18px;
    backdrop-filter: blur(10px);
    border: 1px solid var(--front-nav-link-border);
    transition: all 0.3s ease;
    flex: 1;
    min-width: 120px;
    max-width: 150px;
}

.podcast-stat:hover {
    transform: translateY(-5px);
    background: var(--front-nav-link-hover);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.podcast-stat-number {
    display: block;
    font-size: var(--page-title);
    font-weight: 900;
    color: var(--front-stats-number);
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    counter-reset: number;
    animation: countUp 2s ease-out forwards;
    margin-bottom: 0.3rem;
}

.podcast-stat-label {
    display: block;
    font-size: var(--meta-primary);
    color: var(--front-stats-label);
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 1.2;
}

/* ナビゲーション プリロード効果 - フロントページ特有 */
.hero-nav-menu {
    opacity: 0;
    animation: fadeInUp 0.8s ease forwards;
    animation-delay: 0.5s;
}

.hero-nav-link:nth-child(1) { animation-delay: 0.1s; }
.hero-nav-link:nth-child(2) { animation-delay: 0.2s; }
.hero-nav-link:nth-child(3) { animation-delay: 0.3s; }
.hero-nav-link:nth-child(4) { animation-delay: 0.4s; }

/* ヒーローナビゲーションセクション */
.hero-navigation {
    width: 100%;
    margin-top: 1rem;
}

.hero-nav-menu {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.8rem;
    width: 100%;
    max-width: 380px;
    margin: 0 auto;
}

.hero-nav-link {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem 0.8rem;
    background: var(--front-nav-link-bg);
    color: var(--front-nav-link-text);
    text-decoration: none;
    border-radius: 16px;
    border: 1px solid var(--front-nav-link-border);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 600;
    font-size: 0.85rem;
    text-align: center;
    min-height: 60px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    cursor: pointer;
}

/* シマーエフェクト */
.hero-nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease;
    z-index: 1;
}

.hero-nav-link:hover::before {
    left: 100%;
}

/* グラデーションボーダー効果 */
.hero-nav-link::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, #ff6b35, #f7931e, #667eea, #764ba2);
    background-size: 400% 400%;
    border-radius: 16px;
    opacity: 0;
    z-index: -1;
    animation: gradientShift 3s ease infinite;
    transition: opacity 0.3s ease;
}

.hero-nav-link:hover::after {
    opacity: 0.3;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.hero-nav-link:hover {
    background: var(--front-nav-link-hover);
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    color: var(--front-nav-link-text);
    border-color: rgba(255, 255, 255, 0.4);
}

.hero-nav-link:active {
    transform: translateY(-1px) scale(1.01);
    transition: transform 0.1s ease;
}

/* フォーカス状態（アクセシビリティ） */
.hero-nav-link:focus,
.hero-nav-link.focused {
    outline: 2px solid rgba(255, 255, 255, 0.6);
    outline-offset: 2px;
    box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
}

/* タッチデバイス対応 */
.hero-nav-link.touch-active {
    transform: scale(0.98);
    transition: transform 0.1s ease;
}

/* プレロード状態 */
.hero-nav-link.preload {
    opacity: 0;
    transform: translateY(20px);
}

/* ナビゲーションアイコンとテキストの配置 */
.hero-nav-link {
    flex-direction: column;
    gap: 0.3rem;
}

.hero-nav-link .nav-icon {
    font-size: var(--card-title);
    opacity: 0.9;
    transition: all 0.3s ease;
    z-index: 2;
    position: relative;
}

.hero-nav-link:hover .nav-icon {
    opacity: 1;
    transform: scale(1.1);
}

.hero-nav-link .nav-text {
    font-size: var(--meta-small);
    line-height: 1.2;
    font-weight: 500;
    z-index: 2;
    position: relative;
    transition: all 0.3s ease;
}

.hero-nav-link:hover .nav-text {
    font-weight: 600;
}

.podcast-hero-cta {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.podcast-cta-primary, .podcast-cta-secondary {
    display: inline-flex;
    align-items: center;
    padding: 1rem 2rem;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: var(--body-text);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    white-space: nowrap;
}

.podcast-cta-primary {
    background: var(--front-cta-primary-bg);
    color: var(--front-cta-primary-text);
    box-shadow: var(--front-cta-primary-shadow);
}

.podcast-cta-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(255, 107, 53, 0.6);
    color: var(--front-cta-primary-text);
}

.podcast-cta-secondary {
    background: var(--front-cta-secondary-bg);
    color: var(--front-cta-secondary-text);
    border: 2px solid var(--front-cta-secondary-border);
    backdrop-filter: blur(10px);
}

.podcast-cta-secondary:hover {
    background: var(--front-nav-link-hover);
    transform: translateY(-3px);
    color: var(--front-cta-secondary-text);
}

/* レスポンシブ対応 */
@media (max-width: 968px) {
    .podcast-hero-content {
        grid-template-columns: 1fr;
        gap: 2rem;
        text-align: center;
    }
    
    .podcast-hero-main {
        align-items: center;
        text-align: center;
        order: 1;
    }

    .podcast-hero-content-block {
        align-items: center;
        text-align: center;
    }
    
    .hero-title {
        text-align: center;
    }
    
    .hero-subtitle {
        text-align: center;
    }
    
    .podcast-hero-text {
        text-align: center;
    }
    
    .podcast-hero-main {
        order: 1;
    }
    
    .podcast-hero-sidebar {
        order: 2;
    }
    
    /* 統計情報は横並びで表示 */
    .podcast-stats {
        display: flex;
        flex-direction: row;
        gap: 1rem;
        max-width: 450px;
        margin: 0 auto;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .podcast-stat {
        flex: 1;
        min-width: 110px;
        max-width: 140px;
        padding: 1.2rem 0.8rem;
    }
    
    /* ナビゲーションのレスポンシブ対応 */
    .hero-nav-menu {
        grid-template-columns: 1fr 1fr;
        gap: 0.7rem;
        max-width: 320px;
    }
    
    .hero-nav-link {
        padding: 0.9rem 0.6rem;
        font-size: 0.8rem;
        min-height: 55px;
        border-radius: 14px;
    }
    
    .hero-nav-link .nav-icon {
        font-size: 1.1rem;
    }
    
    .hero-nav-link .nav-text {
        font-size: 0.7rem;
    }
    
    /* ホバー効果をモバイルでは軽量化 */
    .hero-nav-link::after {
        display: none;
    }
    
    .hero-nav-link:hover {
        transform: translateY(-2px) scale(1.01);
    }
    
    /* エピソードボタンの中央揃え */
    .episodes-cta {
        text-align: center;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 1rem;
    }
    
    .episodes-view-all-btn, .blog-view-all-btn {
        width: 280px;
        max-width: 90%;
    }
}

@media (max-width: 768px) {
    .podcast-hero {
        min-height: 65vh;
        padding: 1rem 0;
    }

    .podcast-hero-content-block {
        gap: 1rem;
    }
    
    .podcast-hero-artwork {
        margin: 1rem 0;
        max-width: 220px;
    }
    
    .podcast-artwork {
        width: 220px;
        height: 220px;
    }
    
    /* 統計情報を横並びで表示 */
    .podcast-stats {
        display: flex;
        flex-direction: row;
        gap: 0.8rem;
        max-width: 350px;
        margin: 0 auto;
        justify-content: center;
    }
    
    .podcast-stat {
        flex: 1;
        min-width: 100px;
        padding: 1rem 0.5rem;
        text-align: center;
        border-radius: 15px;
    }
    
    .podcast-stat-number {
        font-size: 1.6rem;
        margin-bottom: 0.3rem;
    }
    
    .podcast-stat-label {
        font-size: 0.7rem;
        line-height: 1.2;
    }
    }
    
    /* ナビゲーションを2x2グリッドに変更 */
    .hero-nav-menu {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.6rem;
        max-width: 300px;
        margin: 0 auto;
    }
    
    .hero-nav-link {
        width: auto;
        justify-content: center;
        padding: 0.7rem 0.4rem;
        font-size: 0.8rem;
        text-align: center;
        border-radius: 12px;
    }
    
    .podcast-hero-cta {
        flex-direction: column;
        align-items: center;
        gap: 0.8rem;
    }
    
    .podcast-cta-primary, .podcast-cta-secondary {
        width: 280px;
        justify-content: center;
    }
    
    /* フロントページ特有のセクション調整 */
    .sidebar-section-title {
        font-size: 0.9rem;
        margin-bottom: 0.8rem;
    }
    
    .podcast-hero-artwork {
        margin: 1rem 0;
    }
    
    .podcast-hero-sidebar {
        gap: 1.5rem;
    }
}

/* フロントページ特有のモバイル最適化 */
@media (max-width: 480px) {
    .podcast-hero {
        min-height: 60vh;
    }

    .podcast-hero-content-block {
        gap: 0.8rem;
    }
    
    .podcast-hero-artwork {
        margin: 0.8rem 0;
        max-width: 200px;
    }
    
    .podcast-artwork {
        width: 200px;
        height: 200px;
    }
    
    .podcast-hero-text {
        margin: 0.8rem 0;
    }
    
    /* フロントページ特有のパーティクルアニメーション無効化 */
    .podcast-hero-particles {
        display: none;
    }
    
    /* フロントページ特有の統計情報レイアウト */
    .podcast-stats {
        display: flex;
        flex-direction: row;
        gap: 0.4rem;
        max-width: 300px;
        margin: 1rem auto;
        justify-content: center;
    }
    
    .podcast-stat {
        flex: 1;
        min-width: 85px;
        padding: 0.7rem 0.2rem;
        border-radius: 10px;
    }
    
    .podcast-stat-number {
        font-size: 1.3rem;
        margin-bottom: 0.1rem;
    }
    
    .podcast-stat-label {
        font-size: 0.6rem;
        line-height: 1.0;
    }
    
    /* フロントページ特有のナビゲーション最適化 */
    .hero-nav-menu {
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        max-width: 260px;
    }
    
    .hero-nav-link {
        padding: 0.7rem 0.4rem;
        font-size: 0.7rem;
        min-height: 45px;
        border-radius: 10px;
    }
    
    .hero-nav-link .nav-icon {
        font-size: 0.9rem;
    }
    
    .hero-nav-link .nav-text {
        font-size: 0.6rem;
        line-height: 1.0;
    }
    
    /* フロントページ特有のシンプルなホバー効果 */
    .hero-nav-link::before,
    .hero-nav-link::after {
        display: none;
    }
    
    .hero-nav-link:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: none;
    }
}
}
</style>

<main id="main" class="site-main">
    <!-- ポッドキャスト専用ヒーローセクション -->
    <section class="podcast-hero">
        <!-- パーティクルアニメーション -->
        <div class="podcast-hero-particles">
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
                <h1 class="hero-title"></h1>
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
                

            </div>
        </div>
    </section>

    <script>
    // カウントアップアニメーション
    document.addEventListener('DOMContentLoaded', function() {
        const statNumbers = document.querySelectorAll('.podcast-stat-number[data-count]');
        
        const animateCount = (element) => {
            const isDecimal = element.dataset.decimal === 'true';
            const target = parseFloat(element.dataset.count);
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const updateCount = () => {
                if (current < target) {
                    current += increment;
                    if (isDecimal) {
                        element.textContent = current.toFixed(1);
                    } else {
                        element.textContent = Math.floor(current);
                    }
                    requestAnimationFrame(updateCount);
                } else {
                    if (isDecimal) {
                        element.textContent = target.toFixed(1);
                    } else {
                        // リスナー数の場合だけ「+」を付ける
                        const nextElement = element.nextElementSibling;
                        if (nextElement && nextElement.textContent === 'リスナー') {
                            element.textContent = target + '+';
                        } else {
                            element.textContent = target;
                        }
                    }
                }
            };
            
            updateCount();
        };
        
        // Intersection Observer でアニメーション開始
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    if (!element.classList.contains('animated')) {
                        element.classList.add('animated');
                        animateCount(element);
                    }
                }
            });
        });
        
        statNumbers.forEach(num => observer.observe(num));
        
        // ナビゲーション強化機能
        const navigationSection = document.querySelector('.navigation-section');
        const heroNavLinks = document.querySelectorAll('.hero-nav-link');
        
        // プリロード完了後の効果
        setTimeout(() => {
            if (navigationSection) {
                navigationSection.classList.add('navigation-loaded');
            }
        }, 100);
        
        // ナビゲーションリンクのアクセシビリティ向上
        heroNavLinks.forEach((link, index) => {
            // キーボードナビゲーション対応
            link.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
            
            // フォーカス処理
            link.addEventListener('focus', function() {
                this.classList.add('focused');
            });
            
            link.addEventListener('blur', function() {
                this.classList.remove('focused');
            });
            
            // リンク分析（分析用）
            link.addEventListener('click', function() {
                const linkText = this.querySelector('.nav-text')?.textContent || 'Unknown';
                console.log(`Navigation clicked: ${linkText}`);
                
                // Google Analytics がある場合のイベント送信
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'navigation_click', {
                        'link_text': linkText,
                        'link_position': index + 1
                    });
                }
            });
        });
        
        // タッチデバイス対応
        if ('ontouchstart' in window) {
            heroNavLinks.forEach(link => {
                link.addEventListener('touchstart', function() {
                    this.classList.add('touch-active');
                }, { passive: true });
                
                link.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.classList.remove('touch-active');
                    }, 150);
                }, { passive: true });
            });
        }
        
        // パフォーマンス監視
        const perfObserver = new PerformanceObserver((list) => {
            list.getEntries().forEach((entry) => {
                if (entry.name.includes('hero-nav')) {
                    console.log(`Navigation performance: ${entry.name} - ${entry.duration}ms`);
                }
            });
        });
        
        if ('PerformanceObserver' in window) {
            perfObserver.observe({ entryTypes: ['measure'] });
        }
    });
    </script>

    <style>
    /* プラットフォームセクション */
    .podcast-platforms-section {
        padding: 4rem 0;
        background: var(--platforms-bg);
        position: relative;
    }
    
    .podcast-platforms-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(102,126,234,0.1)"/><circle cx="80" cy="40" r="3" fill="rgba(118,75,162,0.08)"/><circle cx="40" cy="70" r="2" fill="rgba(240,147,251,0.1)"/></svg>');
        animation: float-bg 15s infinite ease-in-out;
    }
    
    @keyframes float-bg {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(-10px, -10px) rotate(1deg); }
        66% { transform: translate(10px, -5px) rotate(-1deg); }
    }
    
    .platforms-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
        position: relative;
        z-index: 2;
    }
    
    .platforms-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .platforms-header h2 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 900;
        background: var(--platforms-title);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
    }
    
    .platforms-subtitle {
        font-size: clamp(1.1rem, 2.5vw, 1.3rem);
        color: var(--platforms-subtitle);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }
    
    /* プラットフォームリンク - フロントページ用 */
    .platforms-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-top: 2.5rem;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .platforms-grid .platform-link {
        background: var(--platforms-card-bg);
        border: 2px solid transparent;
        border-radius: 16px;
        padding: 1.5rem 1rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        min-height: 120px;
        justify-content: center;
    }
    
    .platforms-grid .platform-link:hover {
        transform: translateY(-3px);
        box-shadow: var(--platforms-card-shadow);
        border-color: var(--platforms-card-border-hover);
        text-decoration: none;
    }
    
    .platforms-grid .platform-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 0.75rem auto;
        border-radius: 50%;
        background: var(--gray-100);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .platforms-grid .platform-icon img {
        width: 36px;
        height: 36px;
        object-fit: contain;
        border-radius: 50%;
    }
    
    .platforms-grid .platform-link:hover .platform-icon {
        background: var(--platforms-card-border-hover);
        transform: scale(1.1);
    }
    
    .platforms-grid .platform-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    
    .platforms-grid .platform-action {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
    }
    
    .platforms-grid .platform-link:hover .platform-name,
    .platforms-grid .platform-link:hover .platform-action {
        color: var(--text-primary);
    }
    
    /* プラットフォーム別の色 */
    .platforms-grid .platform-spotify:hover .platform-icon {
        background: #1DB954;
        color: #fff;
    }
    
    .platforms-grid .platform-apple:hover .platform-icon {
        background: #A855F7;
        color: #fff;
    }
    
    .platforms-grid .platform-youtube:hover .platform-icon {
        background: #FF0000;
        color: #fff;
    }
    
    /* コンテンツフリークスの歩みボタン */
    .history-cta {
        text-align: center;
        margin-top: 3rem;
    }
    
    .history-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        background: var(--history-btn-gradient);
        color: var(--white);
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(142, 36, 170, 0.3);
    }
    
    .history-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(142, 36, 170, 0.5);
        color: var(--white);
        text-decoration: none;
    }
    
    @media (max-width: 768px) {
        .history-btn {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
        }
        
        .history-cta {
            margin-top: 2rem;
        }
    }
    
    /* デスクトップレイアウト（1025px以上） */
    @media (min-width: 1025px) {
        .platforms-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 900px;
            margin-top: 3rem;
        }
        
        .platforms-grid .platform-link {
            padding: 2rem 1.5rem;
            min-height: 140px;
            border-radius: 20px;
            border-width: 3px;
        }
        
        .platforms-grid .platform-link:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        .platforms-grid .platform-icon {
            width: 60px;
            height: 60px;
            font-size: 1.8rem;
            margin: 0 auto 1rem auto;
        }
        
        .platforms-grid .platform-icon img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 50%;
        }
        
        .platforms-grid .platform-link:hover .platform-icon {
            transform: scale(1.15);
        }
        
        .platforms-grid .platform-name {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .platforms-grid .platform-action {
            font-size: 0.95rem;
            font-weight: 600;
        }
    }
    
    /* タブレットレイアウト（769px - 1024px） */
    @media (min-width: 769px) and (max-width: 1024px) {
        .platforms-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            max-width: 750px;
        }
        
        .platforms-grid .platform-link {
            padding: 1.5rem 1rem;
            min-height: 130px;
        }
        
        .platforms-grid .platform-icon {
            width: 55px;
            height: 55px;
            font-size: 1.6rem;
            margin: 0 auto 0.5rem auto;
        }
        
        .platforms-grid .platform-icon img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 50%;
        }
        
        .platforms-grid .platform-name {
            font-size: 1.05rem;
        }
        
        .platforms-grid .platform-action {
            font-size: 0.9rem;
        }
    }
    
    /* レスポンシブ対応（768px以下） */
    @media (max-width: 768px) {
        .platforms-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        
        .platforms-grid .platform-link {
            padding: 1rem 0.5rem;
            min-height: 100px;
        }
        
        .platforms-grid .platform-icon {
            width: 35px;
            height: 35px;
            font-size: 1.1rem;
            margin: 0 auto 0.5rem auto;
        }
        
        .platforms-grid .platform-icon img {
            width: 28px;
            height: 28px;
            object-fit: contain;
            border-radius: 50%;
        }
        
        .platforms-grid .platform-name {
            font-size: 0.8rem;
            margin-bottom: 0.2rem;
        }
        
        .platforms-grid .platform-action {
            font-size: 0.7rem;
        }
    }
    
    /* 極小レイアウト: 1カラム（480px以下） */
    @media (max-width: 480px) {
        .platforms-grid {
            grid-template-columns: 1fr;
            max-width: 300px;
        }
        
        .platforms-grid .platform-link {
            padding: 1.5rem 1rem;
            min-height: 80px;
        }
        
        .platforms-grid .platform-icon {
            width: 40px;
            height: 40px;
            font-size: 1.3rem;
            margin: 0 auto 0.5rem auto;
        }
        
        .platforms-grid .platform-icon img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 50%;
        }
        
        .platforms-grid .platform-name {
            font-size: 0.9rem;
        }
        
        .platforms-grid .platform-action {
            font-size: 0.8rem;
        }
    }
    
    /* ホストセクション - 新しい色彩フロー適用 */
    .hosts-section {
        padding: 3rem 0;
        background: var(--hosts-bg);
        color: var(--hosts-text);
    }
    
    .hosts-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .hosts-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .hosts-header h2 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 900;
        background: var(--hosts-title-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 4px 20px rgba(46,125,50,0.3);
        margin-bottom: 1rem;
    }
    
    /* プロフィールページへのボタン */
    .hosts-cta {
        text-align: center;
        margin-top: 3rem;
    }
    
    .hosts-profile-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        background: var(--hosts-title-gradient);
        color: var(--white);
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(46, 125, 50, 0.3);
    }
    
    .hosts-profile-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(46, 125, 50, 0.5);
        color: var(--white);
        text-decoration: none;
    }
    
    @media (max-width: 768px) {
        .hosts-profile-btn {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
        }
        
        .hosts-cta {
            margin-top: 2rem;
        }
    }
    
    @media (max-width: 768px) {
        .podcast-platforms-section {
            padding: 2.5rem 0;
        }
        
        .platforms-header {
            margin-bottom: 2rem;
        }
        
        .latest-episode-section {
            padding: 2.5rem 0;
        }
        
        .latest-episode-header {
            margin-bottom: 2rem;
        }
        
        .hosts-section {
            padding: 2rem 0;
        }
        
        .hosts-header {
            margin-bottom: 2rem;
        }
        
        .episodes-section {
            padding: 2.5rem 0;
        }
        
        .episodes-header {
            margin-bottom: 2rem;
        }
        
        .newsletter-section {
            padding: 2.5rem 0;
        }
        
        .newsletter-header {
            margin-bottom: 2rem;
        }
        
        .testimonials-section {
            padding: 2.5rem 0;
        }
        
        .testimonials-header {
            margin-bottom: 2rem;
        }
        
        .subscribe-section {
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .subscribe-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .rss-button /* .email-subscribe も一時的に無効化 */ {
            width: 200px;
            justify-content: center;
        }
    }
    </style>

    <style>
    /* 最新エピソードセクション */
    .latest-episode-section {
        padding: 3rem 0;
        background: var(--latest-episode-bg);
        position: relative;
    }
    
    .latest-episode-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    
    .latest-episode-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .latest-episode-header h2 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 900;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
    }
    
    .featured-episode {
        background: var(--latest-episode-card-bg);
        backdrop-filter: blur(20px);
        border-radius: 30px;
        padding: 3rem;
        box-shadow: var(--latest-episode-card-shadow);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .featured-episode::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        animation: shimmer 3s infinite ease-in-out;
    }
    
    @keyframes shimmer {
        0%, 100% { transform: translateX(-100%); }
        50% { transform: translateX(100%); }
    }
    
    .featured-episode:hover {
        transform: translateY(-5px);
        box-shadow: 
            0 30px 60px rgba(0,0,0,0.15),
            0 0 0 1px rgba(255,255,255,0.7);
    }
    
    .featured-episode-content {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 3rem;
        align-items: center;
    }
    
    /* タブレット向け調整 */
    @media (max-width: 1024px) {
        .featured-episode-content {
            grid-template-columns: 250px 1fr;
            gap: 2rem;
        }
    }
    
    /* より小さいタブレット・大きなモバイル向け調整 */
    @media (max-width: 900px) {
        .featured-episode-content {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            text-align: center;
        }
        
        .featured-episode-image {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }
        
        .featured-episode {
            padding: 2rem;
        }
        
        .featured-episode-details {
            padding: 0.8rem 0;
        }
    }
    
    .featured-episode-image {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        display: block;
        line-height: 0;
        aspect-ratio: 1 / 1; /* 正方形のアスペクト比を強制 */
    }
    
    .featured-episode-image img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* 画像をコンテナにフィットさせる */
        display: block;
        transition: transform 0.3s ease;
        vertical-align: top;
    }
    
    .featured-episode:hover .featured-episode-image img {
        transform: scale(1.05);
    }
    
    .featured-episode-default-thumbnail {
        background: var(--latest-episode-badge-bg);
        width: 100%;
        aspect-ratio: 1 / 1; /* 正方形のアスペクト比 */
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        border-radius: 20px;
        line-height: 1;
        margin: 0;
    }
    
    .featured-episode-details {
        padding: 1rem 0;
    }
    
    .episode-meta-info {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    
    .episode-date, .episode-duration {
        background: var(--latest-episode-btn-secondary);
        color: var(--latest-episode-btn-secondary-text);
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .featured-episode-title {
        font-size: clamp(1.5rem, 3vw, 2.5rem);
        font-weight: 800;
        color: var(--latest-episode-title);
        margin-bottom: 2rem;
        line-height: 1.3;
    }
    
    .featured-episode-description {
        font-size: 1.1rem;
        color: var(--latest-episode-text);
        line-height: 1.6;
        margin-bottom: 2rem;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .episode-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .episode-share-btn, .episodes-list-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 1.1rem;
        justify-content: center;
    }
    
    .episode-share-btn {
        background: var(--latest-episode-title-gradient);
        color: var(--white);
        box-shadow: 0 8px 25px var(--latest-episode-title-gradient);
    }
    
    .episode-share-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px var(--latest-episode-title-gradient);
        color: var(--white);
        text-decoration: none;
    }
    
    .episodes-list-btn {
        background: var(--latest-episode-btn-secondary);
        color: var(--latest-episode-btn-secondary-text);
        border: 2px solid var(--latest-episode-btn-secondary-text);
    }
    
    .episodes-list-btn:hover {
        background: var(--latest-episode-btn-secondary-text);
        color: var(--white);
        transform: translateY(-3px);
    }
    
    @media (max-width: 768px) {
        .latest-episode-container {
            padding: 0 1rem;
        }
        
        .featured-episode {
            padding: 1.5rem;
            margin: 0 0.5rem;
        }
        
        .featured-episode-content {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            text-align: center;
        }
        
        .featured-episode-image {
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
        }
        
        .featured-episode-details {
            padding: 0.5rem 0;
        }
        
        .featured-episode-title {
            font-size: 1.3rem;
            -webkit-line-clamp: 2;
            text-align: center;
        }
        
        .featured-episode-description {
            font-size: 0.95rem;
            -webkit-line-clamp: 3;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .episode-actions {
            justify-content: center;
        }
        
        .episode-share-btn {
            padding: 0.7rem 1.2rem;
            font-size: 0.9rem;
        }
    }
    
    /* より小さなモバイル向け */
    @media (max-width: 480px) {
        .latest-episode-section {
            padding: 2rem 0;
        }
        
        .latest-episode-container {
            padding: 0 1rem;
        }
        
        .featured-episode {
            padding: 1rem;
            margin: 0;
            border-radius: 20px;
        }
        
        .featured-episode-content {
            gap: 1rem;
        }
        
        .featured-episode-image {
            width: 100%;
            max-width: 240px;
            margin: 0 auto;
        }
        
        .featured-episode-details {
            padding: 0;
        }
        
        .featured-episode-title {
            font-size: 1.2rem;
            margin-bottom: 0.8rem;
        }
        
        .featured-episode-description {
            font-size: 0.9rem;
            margin-bottom: 1.2rem;
        }
        
        .episode-meta-info {
            justify-content: center;
        }
        
        .episode-date, .episode-duration {
            font-size: 0.8rem;
            padding: 0.3rem 0.8rem;
        }
    }
    </style>

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
                                <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
                            <?php else : 
                                // アイキャッチ画像がない場合、エピソードのメタデータから画像URLを取得を試行
                                $episode_image_url = get_post_meta(get_the_ID(), 'episode_image_url', true);
                                if ($episode_image_url) : ?>
                                    <img src="<?php echo esc_url($episode_image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" style="width: 100%; height: auto; border-radius: 20px;">
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

    <style>
    /* エピソード一覧セクション - 新しい色彩フロー適用 */
    .episodes-section {
        padding: 3.5rem 0;
        background: var(--recent-episodes-bg);
        position: relative;
        overflow: hidden;
    }
    
    .episodes-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1.5" fill="rgba(71,85,105,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
        animation: float-dots 20s infinite linear;
    }
    
    @keyframes float-dots {
        0% { transform: translate(0, 0); }
        100% { transform: translate(-20px, -20px); }
    }
    
    .episodes-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
        position: relative;
        z-index: 2;
    }
    
    .episodes-header {
        text-align: center;
        margin-bottom: 4rem;
    }
    
    .episodes-header h2 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 900;
        color: var(--recent-episodes-title);
        text-shadow: 0 4px 20px rgba(71,85,105,0.1);
        margin-bottom: 1rem;
    }
    
    .episodes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .episode-card {
        background: var(--recent-episodes-card-bg);
        backdrop-filter: blur(20px);
        border-radius: 25px;
        overflow: hidden;
        box-shadow: var(--recent-episodes-card-shadow);
        transition: all 0.3s ease;
        position: relative;
        border: 1px solid var(--recent-episodes-card-border);
    }
    
    .episode-card:hover {
        transform: translateY(-10px);
        box-shadow: 
            0 30px 60px rgba(71,85,105,0.15),
            0 0 0 1px var(--recent-episodes-card-border);
    }
    
    .episode-thumbnail {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1; /* 正方形のアスペクト比を強制 */
        overflow: hidden;
        border-radius: 25px 25px 0 0; /* カードの上部角丸に合わせる */
        flex-shrink: 0; /* サムネイルのサイズを固定 */
    }
    
    .episode-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* 正方形に収まるようにクロップ */
        object-position: center; /* 中央部分を表示 */
        transition: transform 0.3s ease;
    }
    
    .episode-card:hover .episode-thumbnail img {
        transform: scale(1.1);
    }
    
    .episode-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        height: auto;
    }
    
    .episode-default-thumbnail {
        background: var(--latest-episode-badge-bg);
        width: 100%;
        aspect-ratio: 1 / 1; /* 正方形のアスペクト比 */
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        border-radius: 25px 25px 0 0;
        flex-shrink: 0; /* サイズを固定 */
    }
    
    /* モバイル表示を確実に適用するための追加CSS */
    /* 上記の@media (max-width: 768px)と@media (max-width: 480px)で対応済み */
    
    .episode-date {
        color: var(--recent-episodes-meta);
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        background: var(--recent-episodes-card-border);
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        display: inline-block;
    }
    
    .episode-title {
        margin: 1rem 0 0.5rem;
        font-size: 1.3rem;
        font-weight: 700;
        line-height: 1.4;
    }
    
    .episode-title a {
        color: var(--recent-episodes-title);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .episode-title a:hover {
        color: var(--recent-episodes-meta);
    }
    
    /* === page-episodes.phpと同じモバイルレイアウト適用 === */
    @media screen and (max-width: 768px) {
        /* エピソードカードを確実に横型レイアウトに強制 */
        .episodes-section .episode-card,
        .episodes-grid .episode-card,
        .episode-card {
            display: flex !important;
            flex-direction: row !important;
            height: 160px !important;
            overflow: hidden !important;
            align-items: stretch !important;
            box-sizing: border-box !important;
        }
        
        .episodes-section .episode-thumbnail,
        .episodes-grid .episode-thumbnail,
        .episode-thumbnail {
            width: 160px !important;
            min-width: 160px !important;
            max-width: 160px !important;
            height: 160px !important;
            aspect-ratio: 1 / 1 !important; /* 正方形を維持 */
            flex-shrink: 0 !important;
            flex-grow: 0 !important;
            display: flex !important;
            align-items: stretch !important;
            position: relative !important;
            overflow: hidden !important;
            border-radius: 25px 0 0 25px !important; /* モバイルでは左側の角丸 */
        }
        
        .episodes-section .episode-thumbnail img,
        .episodes-grid .episode-thumbnail img,
        .episode-thumbnail img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important; /* 正方形に収まるようにクロップ */
            object-position: center !important; /* 中央部分を表示 */
        }
        
        .episodes-section .episode-default-thumbnail,
        .episodes-grid .episode-default-thumbnail,
        .episode-default-thumbnail {
            width: 100% !important;
            height: 100% !important;
            font-size: 1.5rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .episodes-section .episode-content,
        .episodes-grid .episode-content,
        .episode-content {
            flex: 1 !important;
            width: calc(100% - 160px) !important;
            min-width: 0 !important;
            padding: 0.8rem !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: flex-start !important;
            min-height: 0 !important; /* flexboxの圧縮を許可 */
        }
        
        .episodes-section .episode-title,
        .episodes-grid .episode-title,
        .episode-title {
            font-size: 1rem !important;
            line-height: 1.3 !important;
            margin-bottom: 0.3rem !important;
            font-weight: 700 !important;
            color: #333 !important;
            text-overflow: ellipsis !important;
            max-width: 100% !important;
            flex: 1 !important;
            height: auto !important;
            max-height: none !important;
        }
        
        .episodes-section .episode-date,
        .episodes-grid .episode-date,
        .episode-date {
            font-size: 0.75rem !important;
            padding: 0.2rem 0.6rem !important;
            margin-bottom: 0.5rem !important;
        }
    }
    
    @media screen and (max-width: 480px) {
        /* 480px以下ではさらにコンパクトに */
        .episodes-section .episode-thumbnail,
        .episodes-grid .episode-thumbnail,
        .episode-thumbnail {
            width: 90px !important;
            min-width: 90px !important;
            max-width: 90px !important;
            height: 90px !important;
            aspect-ratio: 1 / 1 !important; /* 正方形を維持 */
            flex-shrink: 0 !important;
            flex-grow: 0 !important;
        }
        
        .episodes-section .episode-content,
        .episodes-grid .episode-content,
        .episode-content {
            width: calc(100% - 90px) !important;
            min-width: 0 !important;
            padding: 0.7rem !important;
        }
        
        .episodes-section .episode-title,
        .episodes-grid .episode-title,
        .episode-title {
            font-size: 0.95rem !important;
            line-height: 1.2 !important;
        }
        
        .episodes-section .episode-date,
        .episodes-grid .episode-date,
        .episode-date {
            font-size: 0.7rem !important;
            padding: 0.15rem 0.5rem !important;
        }
        
        .episodes-section .episode-default-thumbnail,
        .episodes-grid .episode-default-thumbnail,
        .episode-default-thumbnail {
            font-size: 1.2rem !important;
        }
    }
    
    .episodes-cta {
        text-align: center;
        margin-top: 3rem;
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .episodes-view-all-btn, .blog-view-all-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.8rem 1.5rem;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.0rem;
        transition: all 0.3s ease;
        white-space: nowrap;
        justify-content: center;
    }
    
    .episodes-view-all-btn {
        background: var(--recent-episodes-title-gradient);
        color: var(--white);
        box-shadow: 0 8px 25px rgba(46, 125, 50, 0.3);
    }
    
    .episodes-view-all-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(46, 125, 50, 0.5);
        color: var(--white);
        text-decoration: none;
    }
    
    .blog-view-all-btn {
        background: var(--blog-hero-bg);
        color: var(--white);
        box-shadow: 0 8px 25px rgba(46, 125, 50, 0.3);
    }
    
    .blog-view-all-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(46, 125, 50, 0.5);
        color: var(--white);
        text-decoration: none;
    }
    
    .episodes-empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        border-radius: 25px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .episodes-empty-state h3 {
        color: #343a40;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .episodes-empty-state p {
        color: #6c757d;
        margin-bottom: 2rem;
    }
    
    .episodes-empty-state .button {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        padding: 1rem 2rem;
        border-radius: 50px;
        text-decoration: none;
        display: inline-block;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    .episodes-empty-state .button:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
    }
    
    /* 上記の統合モバイルCSSで対応済み */
    
    @media (max-width: 480px) {
        /* 上記の統合モバイルCSSで対応済み */
        
        .episodes-view-all-btn, .blog-view-all-btn {
            width: 250px;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
        }
        
        /* ボタンコンテナの中央揃え */
        .episodes-cta {
            text-align: center;
            justify-content: center;
            align-items: center;
            gap: 0.8rem;
        }
    }
    </style>

    <!-- エピソード一覧 -->
    <section class="episodes-section">
        <div class="episodes-container">
            <div class="episodes-header">
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
                    while ($recent_episodes_query->have_posts()) : $recent_episodes_query->the_post();
                        $audio_url = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                        $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                        $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                        $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
                ?>
                    <article class="episode-card" data-category="<?php echo esc_attr($episode_category); ?>">
                        <div class="episode-thumbnail">
                            <?php 
                            // アイキャッチ画像をまず確認
                            if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                            <?php else : 
                                // アイキャッチ画像がない場合、エピソードのメタデータから画像URLを取得を試行
                                $episode_image_url = get_post_meta(get_the_ID(), 'episode_image_url', true);
                                if ($episode_image_url) : ?>
                                    <img src="<?php echo esc_url($episode_image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" style="width: 100%; height: 200px; object-fit: cover;">
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
            <div class="hosts-header">
                <h2>ABOUT US</h2>
            </div>
            
            <?php echo do_shortcode('[podcast_hosts]'); ?>
            
            <!-- プロフィールページへのボタン -->
            <div class="hosts-cta">
                <a href="<?php echo get_permalink(get_page_by_path('profile')); ?>" class="hosts-profile-btn">
                    👥 詳しいプロフィールを見る
                </a>
            </div>
        </div>
    </section>

    <!-- 社会的証明・レビュー -->
    <section class="testimonials-section">
        <div class="testimonials-container">
            <div class="testimonials-header">
                <h2>リスナーの声</h2>
            </div>
            
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

    <!-- ポッドキャストプラットフォーム -->
    <section id="platforms" class="podcast-platforms-section">
        <div class="platforms-container">
            <div class="platforms-header">
                <h2>どこでも聴ける</h2>
                <p class="platforms-subtitle">お好みのプラットフォームでコンテンツフリークスをお楽しみください</p>
            </div>
            <?php echo do_shortcode('[podcast_platforms]'); ?>
        </div>
    </section>

    <style>
    
    /* テスティモニアルセクション - 新しい色彩フロー適用 */
    .testimonials-section {
        padding: 3.5rem 0;
        background: var(--testimonials-bg);
        color: var(--testimonials-text);
        position: relative;
        overflow: hidden;
    }
    
    .testimonials-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path d="M20,20 Q30,10 40,20 T60,20" stroke="rgba(217,119,6,0.1)" stroke-width="2" fill="none"/><path d="M10,60 Q25,50 40,60 T70,60" stroke="rgba(217,119,6,0.08)" stroke-width="1.5" fill="none"/></svg>');
        animation: float-waves 10s infinite ease-in-out;
    }
    
    @keyframes float-waves {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(20px); }
    }
    
    .testimonials-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
        position: relative;
        z-index: 2;
    }
    
    .testimonials-header {
        text-align: center;
        margin-bottom: 4rem;
    }
    
    .testimonials-header h2 {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 900;
        background: linear-gradient(135deg, var(--testimonials-title), var(--testimonials-accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 4px 20px rgba(217,119,6,0.2);
        margin-bottom: 1rem;
    }
    
    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 2rem;
    }
    
    .testimonial-card {
        background: var(--testimonials-card-bg);
        backdrop-filter: blur(20px);
        border-radius: 25px;
        padding: 2.5rem;
        border: 1px solid var(--testimonials-card-border);
        box-shadow: var(--testimonials-card-shadow);
        transition: all 0.3s ease;
        position: relative;
    }
    
    .testimonial-card::before {
        content: '"';
        position: absolute;
        top: -10px;
        left: 20px;
        font-size: 6rem;
        color: var(--testimonials-card-border);
        font-family: serif;
        line-height: 1;
    }
    
    .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 60px rgba(217,119,6,0.2);
        background: var(--white);
    }
    
    .testimonial-quote {
        font-size: 1.1rem;
        line-height: 1.6;
        margin-bottom: 2rem;
        color: var(--testimonials-quote);
        font-style: italic;
        position: relative;
        z-index: 2;
    }
    
    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .author-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(45deg, #ff6b35, #f7931e);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
    }
    
    .author-info h4 {
        color: #01579b;
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }
    
    .author-role {
        color: #424242;
        font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
        .testimonials-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .testimonial-card {
            padding: 2rem;
        }
        
        .sync-status-info {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 480px) {
        .testimonials-section {
            padding: 2.5rem 0;
        }
        
        .testimonials-container {
            padding: 0 1rem;
        }
        
        .testimonials-header {
            margin-bottom: 2.5rem;
        }
        
        .testimonials-header h2 {
            font-size: 2rem;
            margin-bottom: 0.8rem;
        }
        
        .testimonials-grid {
            gap: 1.2rem;
        }
        
        .testimonial-card {
            padding: 1.5rem;
        }
        
        .testimonial-card::before {
            font-size: 4rem;
            top: -5px;
            left: 15px;
        }
        
        .testimonial-quote {
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }
        
        .author-avatar {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .author-info h4 {
            font-size: 1rem;
        }
        
        .author-role {
            font-size: 0.8rem;
        }
    }
    </style>

    
</main>

<script>
// 最強のモバイルエピソードカード修正JavaScript
document.addEventListener('DOMContentLoaded', function() {
    function forceHorizontalLayout() {
        if (window.innerWidth <= 768) {
            console.log('Applying mobile horizontal layout...');
            
            // すべての可能なエピソードカードセレクターを対象にする
            const episodeCardSelectors = [
                '.episodes-section .episode-card',
                '.episodes-grid .episode-card',
                '.episode-card',
                'article.episode-card',
                '.modern-episode-card',
                '[class*="episode-card"]'
            ];
            
            episodeCardSelectors.forEach(selector => {
                const cards = document.querySelectorAll(selector);
                cards.forEach((card, index) => {
                    console.log(`Processing card ${index + 1} with selector: ${selector}`);
                    
                    // カードを強制的に横型レイアウトに変更
                    card.style.setProperty('display', 'flex', 'important');
                    card.style.setProperty('flex-direction', 'row', 'important');
                    card.style.setProperty('height', 'auto', 'important');
                    card.style.setProperty('min-height', 'auto', 'important');
                    card.style.setProperty('max-height', 'none', 'important');
                    card.style.setProperty('align-items', 'stretch', 'important');
                    card.style.setProperty('overflow', 'visible', 'important');
                    
                    // サムネイル要素を検索して修正
                    const thumbnailSelectors = [
                        '.episode-thumbnail',
                        '.episode-header',
                        '.episode-card-header',
                        '[class*="thumbnail"]'
                    ];
                    
                    let thumbnail = null;
                    for (const thumbSelector of thumbnailSelectors) {
                        thumbnail = card.querySelector(thumbSelector);
                        if (thumbnail) break;
                    }
                    
                    if (thumbnail) {
                        const width = window.innerWidth <= 480 ? '90px' : '110px';
                        thumbnail.style.setProperty('width', width, 'important');
                        thumbnail.style.setProperty('min-width', width, 'important');
                        thumbnail.style.setProperty('max-width', width, 'important');
                        thumbnail.style.setProperty('height', 'auto', 'important');
                        thumbnail.style.setProperty('flex-shrink', '0', 'important');
                        thumbnail.style.setProperty('display', 'flex', 'important');
                        thumbnail.style.setProperty('align-items', 'stretch', 'important');
                        thumbnail.style.setProperty('order', '1', 'important');
                        
                        // サムネイル内の画像を修正
                        const img = thumbnail.querySelector('img');
                        if (img) {
                            img.style.setProperty('width', '100%', 'important');
                            img.style.setProperty('height', '100%', 'important');
                            img.style.setProperty('object-fit', 'cover', 'important');
                            const minHeight = window.innerWidth <= 480 ? '110px' : '130px';
                            img.style.setProperty('min-height', minHeight, 'important');
                        }
                    }
                    
                    // コンテンツ要素を検索して修正
                    const contentSelectors = [
                        '.episode-content',
                        '.episode-card-content',
                        '.episode-info',
                        '[class*="content"]'
                    ];
                    
                    let content = null;
                    for (const contentSelector of contentSelectors) {
                        content = card.querySelector(contentSelector);
                        if (content) break;
                    }
                    
                    if (content) {
                        const padding = window.innerWidth <= 480 ? '0.7rem' : '0.9rem';
                        content.style.setProperty('flex', '1', 'important');
                        content.style.setProperty('padding', padding, 'important');
                        content.style.setProperty('display', 'flex', 'important');
                        content.style.setProperty('flex-direction', 'column', 'important');
                        content.style.setProperty('justify-content', 'flex-start', 'important');
                        content.style.setProperty('height', 'auto', 'important');
                        content.style.setProperty('order', '2', 'important');
                        
                        // タイトル要素を修正
                        const titleSelectors = [
                            '.episode-title',
                            '.episode-card-title',
                            'h3',
                            'h2',
                            '[class*="title"]'
                        ];
                        
                        let title = null;
                        for (const titleSelector of titleSelectors) {
                            title = content.querySelector(titleSelector);
                            if (title) break;
                        }
                        
                        if (title) {
                            const fontSize = window.innerWidth <= 480 ? '0.95rem' : '1rem';
                            const lineHeight = window.innerWidth <= 480 ? '1.2' : '1.3';
                            title.style.setProperty('font-size', fontSize, 'important');
                            title.style.setProperty('margin', '0.3rem 0', 'important');
                            title.style.setProperty('height', 'auto', 'important');
                            title.style.setProperty('max-height', 'none', 'important');
                            title.style.setProperty('line-height', lineHeight, 'important');
                            title.style.setProperty('-webkit-line-clamp', '2', 'important');
                            title.style.setProperty('line-clamp', '2', 'important');
                        }
                        
                        // 説明要素を修正
                        const descriptionSelectors = [
                            '.episode-description',
                            '.episode-excerpt',
                            '[class*="description"]',
                            '[class*="excerpt"]'
                        ];
                        
                        let description = null;
                        for (const descSelector of descriptionSelectors) {
                            description = content.querySelector(descSelector);
                            if (description) break;
                        }
                        
                        if (description) {
                            const fontSize = window.innerWidth <= 480 ? '0.75rem' : '0.8rem';
                            const lineHeight = window.innerWidth <= 480 ? '1.3' : '1.4';
                            const lineClamp = window.innerWidth <= 480 ? '2' : '3';
                            description.style.setProperty('font-size', fontSize, 'important');
                            description.style.setProperty('height', 'auto', 'important');
                            description.style.setProperty('max-height', 'none', 'important');
                            description.style.setProperty('line-height', lineHeight, 'important');
                            description.style.setProperty('-webkit-line-clamp', lineClamp, 'important');
                            description.style.setProperty('line-clamp', lineClamp, 'important');
                            description.style.setProperty('margin-bottom', '0.4rem', 'important');
                            description.style.setProperty('display', '-webkit-box', 'important');
                            description.style.setProperty('-webkit-box-orient', 'vertical', 'important');
                            description.style.setProperty('overflow', 'hidden', 'important');
                        }
                    }
                    
                    console.log(`Card ${index + 1} processed successfully`);
                });
            });
            
            console.log('Mobile horizontal layout applied successfully');
        }
    }
    
    // 初期実行
    forceHorizontalLayout();
    
    // ウィンドウリサイズ時にも実行
    window.addEventListener('resize', debounce(forceHorizontalLayout, 250));
    
    // DOMの変更を監視して、新しいカードが追加された場合にも対応
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                setTimeout(forceHorizontalLayout, 100);
            }
        });
    });
    
    // episodes-sectionを監視
    const episodesSections = document.querySelectorAll('.episodes-section, .episodes-grid');
    episodesSections.forEach(section => {
        observer.observe(section, { 
            childList: true, 
            subtree: true 
        });
    });
    
    // デバウンス関数
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>

<?php get_footer(); ?>
