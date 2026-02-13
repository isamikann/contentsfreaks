<?php
/**
 * Template Name: Media Kit（お仕事のご依頼）
 * ビジネス向け番組情報・実績・コラボメニュー
 */

get_header(); ?>

<main id="main-content" class="site-main media-kit-page" role="main">

    <!-- ヒーローセクション -->
    <section class="mk-hero">
        <div class="mk-hero-inner">
            <div class="mk-hero-content">
                <?php
                $podcast_artwork = get_theme_mod('podcast_artwork');
                if ($podcast_artwork) : ?>
                <div class="mk-hero-artwork">
                    <img src="<?php echo esc_url($podcast_artwork); ?>" alt="<?php echo esc_attr(get_theme_mod('podcast_name', 'コンテンツフリークス')); ?>" width="120" height="120">
                </div>
                <?php endif; ?>
                <h1 class="mk-hero-title">Media Kit</h1>
                <p class="mk-hero-subtitle">コンテンツフリークスへのお仕事のご依頼・コラボレーションのご相談</p>
            </div>
        </div>
    </section>

    <!-- 番組概要 -->
    <section class="mk-section">
        <div class="mk-section-inner">
            <h2 class="mk-section-title">🎙️ 番組概要</h2>
            <div class="mk-overview-grid">
                <div class="mk-overview-text">
                    <p class="mk-lead">
                        <?php echo esc_html(get_theme_mod('podcast_description', '「コンテンツフリークス」は、大学時代からの友人2人で「いま気になる」注目のエンタメコンテンツを熱く語るポッドキャスト')); ?>
                    </p>
                    <dl class="mk-data-list">
                        <div class="mk-data-row">
                            <dt>番組名</dt>
                            <dd><?php echo esc_html(get_theme_mod('podcast_name', 'コンテンツフリークス')); ?></dd>
                        </div>
                        <div class="mk-data-row">
                            <dt>ジャンル</dt>
                            <dd>エンタメ・カルチャー（映画 / ドラマ / アニメ / マンガ）</dd>
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
                            <dd><?php echo contentfreaks_get_podcast_count(); ?> エピソード</dd>
                        </div>
                        <div class="mk-data-row">
                            <dt>プラットフォーム</dt>
                            <dd>Spotify / Apple Podcasts / YouTube</dd>
                        </div>
                    </dl>
                </div>
                <div class="mk-overview-platforms">
                    <a href="<?php echo esc_url(CONTENTFREAKS_SPOTIFY_URL); ?>" target="_blank" rel="noopener" class="mk-platform-card">
                        <span class="mk-pf-name">Spotify</span>
                        <span class="mk-pf-number"><?php echo esc_html(get_theme_mod('mk_spotify_followers', '300')); ?></span>
                        <span class="mk-pf-label">フォロワー</span>
                    </a>
                    <a href="<?php echo esc_url(CONTENTFREAKS_APPLE_URL); ?>" target="_blank" rel="noopener" class="mk-platform-card">
                        <span class="mk-pf-name">Apple Podcasts</span>
                        <span class="mk-pf-number"><?php echo esc_html(get_theme_mod('mk_apple_followers', '150')); ?></span>
                        <span class="mk-pf-label">フォロワー</span>
                    </a>
                    <a href="<?php echo esc_url(CONTENTFREAKS_YOUTUBE_URL); ?>" target="_blank" rel="noopener" class="mk-platform-card">
                        <span class="mk-pf-name">YouTube</span>
                        <span class="mk-pf-number"><?php echo esc_html(get_theme_mod('mk_youtube_subscribers', '900')); ?></span>
                        <span class="mk-pf-label">登録者</span>
                    </a>
                    <div class="mk-platform-card mk-platform-total">
                        <span class="mk-pf-name">合計</span>
                        <span class="mk-pf-number"><?php echo esc_html(get_option('contentfreaks_listener_count', '1500')); ?></span>
                        <span class="mk-pf-label">総フォロワー</span>
                    </div>
                    <?php $mk_monthly = get_theme_mod('mk_monthly_plays', ''); if ($mk_monthly) : ?>
                    <div class="mk-platform-card mk-platform-monthly">
                        <span class="mk-pf-name">月間再生数</span>
                        <span class="mk-pf-number"><?php echo esc_html($mk_monthly); ?></span>
                        <span class="mk-pf-label">回</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- リスナー属性 -->
    <section class="mk-section mk-section-alt">
        <div class="mk-section-inner">
            <h2 class="mk-section-title">👥 リスナー属性</h2>
            <div class="mk-audience-grid">
                <div class="mk-audience-card">
                    <div class="mk-audience-icon">🎯</div>
                    <h3>メインターゲット</h3>
                    <p>20〜30代のエンタメ好き。映画・ドラマ・アニメ・マンガを日常的に楽しむ層</p>
                </div>
                <div class="mk-audience-card">
                    <div class="mk-audience-icon">💬</div>
                    <h3>エンゲージメント</h3>
                    <p>作品への感想や新たな視点を求めてリピート視聴するリスナーが多い</p>
                </div>
                <div class="mk-audience-card">
                    <div class="mk-audience-icon">📱</div>
                    <h3>利用シーン</h3>
                    <p>通勤・家事・運動中の「ながら聴き」が中心。作品視聴前後に情報収集</p>
                </div>
            </div>
        </div>
    </section>

    <!-- コラボレーション実績 -->
    <section class="mk-section">
        <div class="mk-section-inner">
            <h2 class="mk-section-title">🤝 コラボレーション実績</h2>
            <div class="mk-collab-grid">
                <div class="mk-collab-card">
                    <div class="mk-collab-header">
                        <h3>平成男女のイドバタラジオ</h3>
                        <span class="mk-collab-badge">ゲスト出演</span>
                    </div>
                    <p>ポッドキャスト番組間のゲスト相互出演を実施</p>
                </div>
                <div class="mk-collab-card">
                    <div class="mk-collab-header">
                        <h3>ひよっこ研究者のさばいばる日記</h3>
                        <span class="mk-collab-badge">ゲスト出演</span>
                    </div>
                    <p>異ジャンルポッドキャストとの交流・相互出演</p>
                </div>
                <div class="mk-collab-card">
                    <div class="mk-collab-header">
                        <h3>推し活2次元LIFEラジオ</h3>
                        <span class="mk-collab-badge">コラボ配信</span>
                    </div>
                    <p>アニメ・推し活をテーマにした共同配信を実施</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 提供可能メニュー -->
    <section class="mk-section mk-section-alt">
        <div class="mk-section-inner">
            <h2 class="mk-section-title">📋 提供可能メニュー</h2>
            <div class="mk-services-grid">
                <div class="mk-service-card">
                    <div class="mk-service-icon">🎤</div>
                    <h3>ゲスト出演 / コラボ配信</h3>
                    <p>他ポッドキャスト・YouTubeチャンネルとの相互出演。エンタメ系番組との相性◎</p>
                    <ul class="mk-service-details">
                        <li>音声のみ / 映像ありいずれも対応</li>
                        <li>エンタメ全般のトークに対応</li>
                        <li>番組間の相互送客効果あり</li>
                    </ul>
                </div>
                <div class="mk-service-card">
                    <div class="mk-service-icon">📺</div>
                    <h3>作品紹介タイアップ</h3>
                    <p>映画・ドラマ・アニメ等の作品レビュー。リスナーへの自然な形での紹介が可能</p>
                    <ul class="mk-service-details">
                        <li>番組内での作品紹介・レビュー</li>
                        <li>サイト作品DBへの掲載</li>
                        <li>SNSでの告知連動</li>
                    </ul>
                </div>
                <div class="mk-service-card">
                    <div class="mk-service-icon">🎪</div>
                    <h3>イベント出演</h3>
                    <p>オフライン/オンラインイベントでのトーク出演・MC</p>
                    <ul class="mk-service-details">
                        <li>トークイベント出演・MC</li>
                        <li>オンライン配信イベント</li>
                        <li>ファンミーティング等</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- お問い合わせCTA -->
    <section class="mk-cta-section">
        <div class="mk-cta-inner">
            <div class="mk-cta-content">
                <h2>お仕事のご依頼・ご相談</h2>
                <p>コラボ出演、タイアップ、イベント等、まずはお気軽にご相談ください。<br>内容を確認の上、2〜3営業日以内にご返信いたします。</p>
                <div class="mk-cta-buttons">
                    <a href="<?php echo esc_url(contentfreaks_get_page_url('contact')); ?>?type=business" class="mk-cta-btn mk-cta-primary">
                        <span class="btn-icon">📩</span>
                        お問い合わせフォームへ
                    </a>
                    <a href="<?php echo esc_url(contentfreaks_get_page_url('profile')); ?>" class="mk-cta-btn mk-cta-secondary">
                        <span class="btn-icon">👤</span>
                        パーソナリティ紹介
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
