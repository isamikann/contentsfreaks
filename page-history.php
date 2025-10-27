<?php
/**
 * Template Name: コンテンツフリークスの歩み
 */

get_header(); ?>

<main id="main" class="site-main history-page">
    <!-- ブレッドクラムナビゲーション -->
    <nav class="breadcrumb-nav">
        <div class="breadcrumb-container">
            <a href="/" class="breadcrumb-home">🏠 ホーム</a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">コンテンツフリークスの歩み</span>
        </div>
    </nav>

    <!-- 歴史ヒーローセクション -->
    <section class="history-hero">
        <div class="history-hero-bg">
            <div class="hero-particles"></div>
            <div class="hero-waves"></div>
        </div>
        <div class="history-hero-content">
            <div class="history-hero-header">
                <div class="hero-icon-container">
                    <div class="hero-icon">📖</div>
                    <div class="hero-icon-glow"></div>
                </div>
                <h1 class="history-hero-title">Our Journey</h1>
                <p class="history-hero-subtitle">
                    「カラビナFM」から「コンテンツフリークス」へ<br>
                    2人の成長と番組の進化の軌跡
                </p>
                <div class="journey-stats">
                    <div class="journey-stat">
                        <span class="stat-value">2</span>
                        <span class="stat-unit">年間</span>
                    </div>
                    <div class="journey-stat">
                        <span class="stat-value"><?php 
                            $episode_count = get_posts(array(
                                'meta_key' => 'is_podcast_episode',
                                'meta_value' => '1',
                                'post_status' => 'publish',
                                'numberposts' => -1
                            ));
                            echo count($episode_count);
                        ?></span>
                        <span class="stat-unit">エピソード</span>
                    </div>
                    <div class="journey-stat">
                        <span class="stat-value"><?php echo esc_attr(get_option('contentfreaks_listener_count', '1500')); ?>+</span>
                        <span class="stat-unit">リスナー</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- タイムライン -->
    <section class="timeline-section">
        <div class="timeline-container">
            <div class="timeline-intro">
                <h2 class="timeline-title">The Story Unfolds</h2>
                <p class="timeline-subtitle">小さな雑談番組から愛される番組への成長ストーリー</p>
            </div>
            
            <!-- 2023年 -->
            <div class="year-section" data-year="2023">
                <div class="year-header">
                    <div class="year-badge">
                        <span class="year-number">2023</span>
                        <div class="year-accent"></div>
                    </div>
                    <div class="year-info">
                        <h3 class="year-title">The Beginning</h3>
                        <p class="year-subtitle">「コンテンツを語る楽しさ」に気付いた一年</p>
                    </div>
                </div>
                
                <div class="timeline">
                    <!-- 6月 -->
                    <div class="timeline-item launch" data-aos="fade-up">
                        <div class="timeline-marker">
                            <div class="marker-icon">🎙️</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">6月</span>
                            <span class="date-year">2023</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">ポッドキャスト番組スタート！</h4>
                                <span class="timeline-badge launch-badge">Launch</span>
                            </div>
                            <p class="timeline-description">
                                みっくんが大学時代の友人・あっきーを誘い、ポッドキャスト番組「カラビナFM」をスタート！当初は「お互いが気になる話題を持ち寄る雑談番組」として始動。
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">番組の原点となる記念すべき第一歩</span>
                            </div>
							<div class="artwork-showcase">
								<img src="https://content-freaks.jp/wp-content/uploads/2024/05/1000017105.jpg" alt="カラビナFM初期アートワーク" class="artwork-image">
								<div class="artwork-caption">
									<span class="caption-label">🎨</span>
									<span class="caption-text">カラビナFM初期アートワーク</span>
								</div>
							</div>
                        </div>
                    </div>
                    
                    <!-- 7月 -->
                    <div class="timeline-item milestone" data-aos="fade-up" data-aos-delay="100">
                        <div class="timeline-marker">
                            <div class="marker-icon">🎬</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">7月</span>
                            <span class="date-year">2023</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">初のコンテンツ回を配信</h4>
                                <span class="timeline-badge milestone-badge">Milestone</span>
                            </div>
                            <p class="timeline-description">
                                初のコンテンツ回となる #4「アニメ『推しの子』は何が凄かったのか？」を配信。コンテンツについて語る楽しさに気付き、番組の方向性が少しずつ固まり始める。
                            </p>
                            <div class="timeline-actions">
                                <a href="https://open.spotify.com/episode/1Jz9gurZNUnVGoN8suwWiN?si=r1jmQN8QT--sSQR2Ox9Mdg" class="timeline-link" target="_blank">
                                    <span class="link-icon">▶</span>
                                    エピソードを聴く
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 8-9月 -->
                    <div class="timeline-item innovation" data-aos="fade-up" data-aos-delay="200">
                        <div class="timeline-marker">
                            <div class="marker-icon">📊</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">8〜9月</span>
                            <span class="date-year">2023</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">初の分析回で新たな構想が誕生</h4>
                                <span class="timeline-badge innovation-badge">Innovation</span>
                            </div>
                            <p class="timeline-description">
                                初の分析回 #10「配信をした感想とデータ分析から見る今後のカラビナFMの進む道」を配信。コンテンツ回の再生数の伸びを受け、みっくんの頭の中に「コンテンツフリークス構想」が生まれる。
                            </p>
                            <div class="timeline-actions">
                                <a href="https://open.spotify.com/episode/2KbVneYdYlnpjSwdM2koEt?si=FquwD8KQSs6zezavnpe1cg" class="timeline-link" target="_blank">
                                    <span class="link-icon">▶</span>
                                    エピソードを聴く
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 10月 -->
                    <div class="timeline-item featured breakthrough" data-aos="fade-up" data-aos-delay="300">
                        <div class="timeline-marker featured-marker">
                            <div class="marker-icon">⭐</div>
                            <div class="marker-pulse featured-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">10月</span>
                            <span class="date-year">2023</span>
                        </div>
                        <div class="timeline-content featured-content">
                            <div class="content-header">
                                <h4 class="timeline-title">人気エピソード誕生＆リニューアル発表</h4>
                                <span class="timeline-badge breakthrough-badge">Breakthrough</span>
                            </div>
                            <p class="timeline-description">
                                アニメ「葬送のフリーレン」回（#20）を配信。現在もトップ5に入る人気エピソードに！<br><br>
                                このタイミングで<strong>番組リニューアルを発表</strong>。「カラビナFM」から「コンテンツフリークス」へと改名！
                            </p>
                            <div class="timeline-actions">
                                <a href="https://open.spotify.com/episode/44KqaSVB1BSEtZm3cYMwLP?si=WeGYuKVrRZygWA9rowc8bg" class="timeline-link featured-link" target="_blank">
                                    <span class="link-icon">▶</span>
                                    エピソードを聴く
                                </a>
                            </div>
                            <div class="timeline-visual">
                                <div class="artwork-showcase">
                                    <img src="https://content-freaks.jp/wp-content/uploads/2024/05/1000014856-1024x1024.png" alt="コンテンツフリークス初期アートワーク" class="artwork-image">
                                    <div class="artwork-caption">
                                        <span class="caption-label">🎨</span>
                                        <span class="caption-text">コンテンツフリークス初期アートワーク</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 11月 -->
                    <div class="timeline-item community" data-aos="fade-up" data-aos-delay="400">
                        <div class="timeline-marker">
                            <div class="marker-icon">🔬</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">11月</span>
                            <span class="date-year">2023</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">科学系ポッドキャストの日に初参加</h4>
                                <span class="timeline-badge community-badge">Community</span>
                            </div>
                            <p class="timeline-description">
                                「科学系ポッドキャストの日」に初参加。#25 映画『私は確信する』回を配信。科学系ポッドキャスト「サイエントーク」の大ファンであるみっくん＆あっきー、大歓喜！
                            </p>
                            <div class="timeline-actions">
                                <a href="https://open.spotify.com/episode/2doICgnSs0wVdKyqK9BXaE?si=uBKftPsrRJCRkTgo69Wvsw" class="timeline-link" target="_blank">
                                    <span class="link-icon">▶</span>
                                    エピソードを聴く
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 12月 -->
                    <div class="timeline-item awards" data-aos="fade-up" data-aos-delay="500">
                        <div class="timeline-marker">
                            <div class="marker-icon">🏆</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">12月</span>
                            <span class="date-year">2023</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">2023年コンテンツフリークス大賞を発表</h4>
                                <span class="timeline-badge awards-badge">Awards</span>
                            </div>
                            <p class="timeline-description">
                                「2023年コンテンツフリークス大賞」を発表！
                            </p>
                            <div class="awards-list">
                                <div class="award-item grand">
                                    <span class="award-icon">🏆</span>
                                    <span class="award-text">コンテンツフリークス大賞：「PLUTO」</span>
                                </div>
                                <div class="award-item">
                                    <span class="award-icon">🎖</span>
                                    <span class="award-text">みっくん賞：「私は確信する」</span>
                                </div>
                                <div class="award-item">
                                    <span class="award-icon">🎖</span>
                                    <span class="award-text">あっきー賞：「ゴジラ-1.0」</span>
                                </div>
                            </div>
                            <div class="timeline-actions">
                                <a href="https://open.spotify.com/episode/3G1nDsYBljNCbUnA496aBp?si=XqUBDXOaRxeIg64cpFmVkA" class="timeline-link" target="_blank">
                                    <span class="link-icon">▶</span>
                                    エピソードを聴く
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2024年 -->
            <div class="year-section" data-year="2024">
                <div class="year-header">
                    <div class="year-badge">
                        <span class="year-number">2024</span>
                        <div class="year-accent"></div>
                    </div>
                    <div class="year-info">
                        <h3 class="year-title">Growth & Evolution</h3>
                        <p class="year-subtitle">「コンテンツを語る楽しさ」を痛感した一年</p>
                    </div>
                </div>
                
                <div class="timeline">
                    <!-- 1月 -->
                    <div class="timeline-item upgrade" data-aos="fade-up">
                        <div class="timeline-marker">
                            <div class="marker-icon">🎵</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">1月</span>
                            <span class="date-year">2024</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">番組クオリティ向上プロジェクト</h4>
                                <span class="timeline-badge upgrade-badge">Upgrade</span>
                            </div>
                            <p class="timeline-description">
                                番組のクオリティ向上を目指し、さまざまな試みをスタート！
                            </p>
                            <div class="improvement-list">
                                <div class="improvement-item">
                                    <span class="improvement-icon">🎶</span>
                                    <span class="improvement-text">BGMを追加</span>
                                </div>
                                <div class="improvement-item">
                                    <span class="improvement-icon">🔊</span>
                                    <span class="improvement-text">ジングルを2種類作成</span>
                                </div>
                                <div class="improvement-item">
                                    <span class="improvement-icon">🎼</span>
                                    <span class="improvement-text">オリジナルテーマソングを制作</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 2-3月 -->
                    <div class="timeline-item featured celebration" data-aos="fade-up" data-aos-delay="100">
                        <div class="timeline-marker featured-marker">
                            <div class="marker-icon">🎉</div>
                            <div class="marker-pulse featured-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">2〜3月</span>
                            <span class="date-year">2024</span>
                        </div>
                        <div class="timeline-content featured-content">
                            <div class="content-header">
                                <h4 class="timeline-title">50回配信達成＆アートワークリニューアル</h4>
                                <span class="timeline-badge celebration-badge">Celebration</span>
                            </div>
                            <p class="timeline-description">
                                50回配信を達成！記念としてアートワークをリニューアル！
                            </p>
                            <div class="timeline-visual">
                                <div class="artwork-showcase">
                                    <img src="https://content-freaks.jp/wp-content/uploads/2024/05/1000015915-1024x1024.png" alt="最新アートワーク" class="artwork-image">
                                    <div class="artwork-caption">
                                        <span class="caption-label">🎨</span>
                                        <span class="caption-text">50回記念アートワーク</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 4月 -->
                    <div class="timeline-item collaboration" data-aos="fade-up" data-aos-delay="200">
                        <div class="timeline-marker">
                            <div class="marker-icon">🎙</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">4月</span>
                            <span class="date-year">2024</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">初のコラボ回を配信</h4>
                                <span class="timeline-badge collaboration-badge">Collaboration</span>
                            </div>
                            <p class="timeline-description">
                                初のコラボ回を配信！ゲストに「平成男女のイドバタラジオ」の"みな"さんを迎え、熱いトークを展開！<br><br>
                                さらに、人気コンテンツの完結感想回を配信。<br>
                                #68-69「葬送のフリーレン」「るぷナナ」完結感想回
                            </p>
                            <div class="timeline-actions">
                                <a href="https://open.spotify.com/episode/661RG21Jp2Rs7PFggQ4nXE?si=1Q6tg0v4RaydL_krSec_sQ" class="timeline-link" target="_blank">
                                    <span class="link-icon">▶</span>
                                    エピソードを聴く
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 5月 -->
                    <div class="timeline-item collaboration" data-aos="fade-up" data-aos-delay="300">
                        <div class="timeline-marker">
                            <div class="marker-icon">🎙</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">5月</span>
                            <span class="date-year">2024</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">コラボ回第2弾</h4>
                                <span class="timeline-badge collaboration-badge">Collaboration</span>
                            </div>
                            <p class="timeline-description">
                                コラボ回を再び配信！ゲストに「ひよっこ研究者のさばいばる日記」の"はち"さんを迎える。<br>
                                #72「劇場版 名探偵コナン」完結感想回
                            </p>
                            <div class="timeline-actions">
                                <a href="https://open.spotify.com/episode/5NX4d5OYHQ7bh0VlNT42wj?si=BbHpDgGvTiqBl6xmkErO2Q" class="timeline-link" target="_blank">
                                    <span class="link-icon">▶</span>
                                    エピソードを聴く
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 6月 -->
                    <div class="timeline-item launch" data-aos="fade-up" data-aos-delay="400">
                        <div class="timeline-marker">
                            <div class="marker-icon">🌐</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">6月</span>
                            <span class="date-year">2024</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">公式ホームページ開設</h4>
                                <span class="timeline-badge launch-badge">Launch</span>
                            </div>
                            <p class="timeline-description">
                                コンテンツフリークスの公式ホームページを開設！初期コンテンツとして「トップページ」「プロフィール」「コンテンツフリークスの歩み」を準備。
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">デジタルプレゼンスの大幅向上</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 7月 -->
                    <div class="timeline-item milestone" data-aos="fade-up" data-aos-delay="500">
                        <div class="timeline-marker">
                            <div class="marker-icon">🎯</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">7月</span>
                            <span class="date-year">2024</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">🎉 Spotify 100フォロワー突破</h4>
                                <span class="timeline-badge milestone-badge">Milestone</span>
                            </div>
                            <p class="timeline-description">
                                Spotifyのフォロワー数が100人を突破！ひとつの大台にのった瞬間で、番組開始当初からは考えられない成長に驚きと喜びを感じました。<br><br>
                                ブログページに新たに2記事を追加し、ポッドキャスト運営の知見を共有：「ポッドキャスターを喜ばせる方法」「ポッドキャスト1年の振り返り」
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">番組の継続と成長の確信を得られた記念すべき瞬間</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 8月 -->
                    <div class="timeline-item breakthrough" data-aos="fade-up" data-aos-delay="600">
                        <div class="timeline-marker">
                            <div class="marker-icon">📺</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">8月</span>
                            <span class="date-year">2024</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">📺 YouTube 100登録者突破＆初メディア掲載</h4>
                                <span class="timeline-badge breakthrough-badge">Breakthrough</span>
                            </div>
                            <p class="timeline-description">
                                YouTubeの登録者数が100人を突破！まだ戦略なく運営していた中での予想外の成長に驚きました。<br><br>
                                「ポッドキャストランキング」様の「WEEKLY PICKUP!!」に選出！突然選ばれていてびっくりした、初めてメディアに載った記念すべき瞬間でした。
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">メディア掲載の影響かフォロワー数が大幅増加</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 9月 -->
                    <div class="timeline-item innovation" data-aos="fade-up" data-aos-delay="700">
                        <div class="timeline-marker">
                            <div class="marker-icon">🚀</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">9月</span>
                            <span class="date-year">2024</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">📈 フォロワー成長＆YouTube ショート動画革命</h4>
                                <span class="timeline-badge innovation-badge">Innovation</span>
                            </div>
                            <p class="timeline-description">
                                Spotifyのフォロワー数が150人突破！YouTubeの登録者数が300人突破！<br><br>
                                YouTube登録者が増え、ショート動画を出してみたらどうなるか試してみたくて、初のショート動画を投稿開始！5分で作成可能なショート動画のフォーマットを確立。
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">ショート動画は番組が広がるきっかけになると実感</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 10月・11月 -->
                    <div class="timeline-item viral" data-aos="fade-up" data-aos-delay="800">
                        <div class="timeline-marker">
                            <div class="marker-icon">🔥</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">10〜11月</span>
                            <span class="date-year">2024</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">📈 YouTube爆発的成長期</h4>
                                <span class="timeline-badge viral-badge">Viral</span>
                            </div>
                            <p class="timeline-description">
                                10月に400人突破、11月に600人突破！<br><br>
                                目黒蓮主演の「海のはじまり」の感想動画がバズりまくって、ドラマ感想回を出す度に登録者が増えていく現象が発生！最終回動画は1.5万回以上再生。<br><br>
                                11月にポッドキャストシンポジウム、ポッドキャストウィークエンドなどのリアルイベントに参加！
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">ドラマ感想回が番組成長の大きな要因となることを確信</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 12月 -->
                    <div class="timeline-item awards" data-aos="fade-up" data-aos-delay="900">
                        <div class="timeline-marker">
                            <div class="marker-icon">🏆</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">12月</span>
                            <span class="date-year">2024</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">🏆 2024年コンテンツフリークス大賞</h4>
                                <span class="timeline-badge awards-badge">Awards</span>
                            </div>
                            <p class="timeline-description">
                                2024年を締めくくる特別企画「2024年コンテンツフリークス大賞」を発表！
                            </p>
                            <div class="awards-list">
                                <div class="award-item grand">
                                    <span class="award-icon">🏆</span>
                                    <span class="award-text">コンテンツフリークス大賞：「アンメット」</span>
                                </div>
                                <div class="award-item">
                                    <span class="award-icon">🎖</span>
                                    <span class="award-text">ドラマ賞：「海のはじまり」</span>
                                </div>
                                <div class="award-item">
                                    <span class="award-icon">�</span>
                                    <span class="award-text">ドラマキャスト大賞：「杉咲花」</span>
                                </div>
                                <div class="award-item">
                                    <span class="award-icon">🎖</span>
                                    <span class="award-text">アニメ賞：「葬送のフリーレン」</span>
                                </div>
                            </div>
                            <div class="timeline-actions">
                                <a href="#" class="timeline-link" target="_blank">
                                    <span class="link-icon">▶</span>
                                    エピソードを聴く
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2025年 -->
            <div class="year-section" data-year="2025">
                <div class="year-header">
                    <div class="year-badge">
                        <span class="year-number">2025</span>
                        <div class="year-accent"></div>
                    </div>
                    <div class="year-info">
                        <h3 class="year-title">New Heights</h3>
                        <p class="year-subtitle">さらなる飛躍の年</p>
                    </div>
                </div>
                
                <div class="timeline">
                    <!-- 1月 -->
                    <div class="timeline-item breakthrough" data-aos="fade-up">
                        <div class="timeline-marker">
                            <div class="marker-icon">🎉</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">1月</span>
                            <span class="date-year">2025</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">🎉 総フォロワー数1000人突破！</h4>
                                <span class="timeline-badge breakthrough-badge">Breakthrough</span>
                            </div>
                            <p class="timeline-description">
                                Spotifyのフォロワー数が200人を突破！<br>
                                YouTubeの登録者数が700人を突破！<br><br>
                                そして、Spotify、ApplePodcast、YouTubeの総フォロワー数が1000人を突破！番組開始時には想像もしていなかった数字です！
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">番組開始時には想像もしていなかった数字に到達</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 2-3月 -->
                    <div class="timeline-item featured celebration" data-aos="fade-up" data-aos-delay="100">
                        <div class="timeline-marker featured-marker">
                            <div class="marker-icon">🎨</div>
                            <div class="marker-pulse featured-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">2〜3月</span>
                            <span class="date-year">2025</span>
                        </div>
                        <div class="timeline-content featured-content">
                            <div class="content-header">
                                <h4 class="timeline-title">🎨 150回配信記念アートワークリニューアル</h4>
                                <span class="timeline-badge celebration-badge">Celebration</span>
                            </div>
                            <p class="timeline-description">
                                ApplePodcastのフォロワー数が150人を突破！<br>
                                150回配信を達成！<br><br>
                                総フォロワー数が1000人＋150回配信記念としてアートワークをリニューアル！！<br>
                                半年ほどアートワークを更新したいと思っていたので現状の理想を体現したものが完成！
                            </p>
                            <div class="timeline-visual">
                                <div class="artwork-showcase">
                                    <img src="https://content-freaks.jp/wp-content/uploads/2023/07/36275010-1739517733196-9955f073fd424-4.jpg" alt="最新アートワーク" class="artwork-image">
                                    <div class="artwork-caption">
                                        <span class="caption-label">🎨</span>
                                        <span class="caption-text">最新アートワーク</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 4-5月 -->
                    <div class="timeline-item growth" data-aos="fade-up" data-aos-delay="200">
                        <div class="timeline-marker">
                            <div class="marker-icon">📈</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">4〜5月</span>
                            <span class="date-year">2025</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">📈 さらなる成長継続</h4>
                                <span class="timeline-badge growth-badge">Growth</span>
                            </div>
                            <p class="timeline-description">
                                4月：Spotifyのフォロワー数が300人を突破！<br>
                                5月：YouTubeの登録者数が800人を突破！
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">継続的な成長により、番組の安定した人気を確立</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 今後の展望 -->
    <section class="future-section">
        <div class="future-bg">
            <div class="future-pattern"></div>
        </div>
        <div class="future-container">
            <div class="future-content">
                <div class="future-icon">🚀</div>
                <h2 class="future-title">The Journey Continues</h2>
                <p class="future-subtitle">これからの「コンテンツフリークス」</p>
                <p class="future-description">
                    「カラビナFM」として始まった小さな雑談番組が、今では多くのリスナーの皆様に愛される「コンテンツフリークス」となりました。<br><br>
                    これからも、コンテンツへの愛と熱い想いを胸に、みっくん＆あっきーは語り続けます。<br>
                    新たなコンテンツとの出会い、新たなリスナーとの繋がりを大切に、番組を続けていきます。<br><br>
                    <strong>コンテンツフリークスの旅は、まだまだ始まったばかりです！</strong>
                </p>
                
                <div class="future-cta">
                    <a href="/episodes/" class="future-cta-button primary">
                        <span class="btn-icon">🎧</span>
                        最新エピソードを聴く
                    </a>
                    <a href="/" class="future-cta-button secondary">
                        <span class="btn-icon">🏠</span>
                        ホームへ戻る
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- モダンヒストリーページ専用スタイル -->
<style>
/* ===== ヒストリーページ専用スタイル - レスポンシブ対応強化版 ===== */

/* ページ全体の上部マージン調整（モダンヘッダー対応） */
body {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

.history-page {
    background: var(--history-bg);
    min-height: 100vh;
}

/* コンテナの基本設定 */
.history-page .container {
    max-width: 100%;
    padding: 0;
}

/* ヒストリーヒーローセクション */
.history-hero {
    position: relative;
    background: var(--hero-bg);
    padding: 1rem 0 3rem 0;
    overflow: hidden;
    min-height: 50vh;
    display: flex;
    align-items: center;
}

/* ヒストリーページ用モバイル調整 */
@media (max-width: 768px) {
    .history-hero {
        padding: 0.5rem 0 2rem 0;
        min-height: 40vh;
    }
}

.history-hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.hero-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><circle fill="%23f7ff0b20" cx="100" cy="100" r="2"/><circle fill="%23ff6b3520" cx="300" cy="200" r="1.5"/><circle fill="%23f7ff0b15" cx="500" cy="150" r="1"/><circle fill="%23ff6b3515" cx="700" cy="250" r="2"/><circle fill="%23f7ff0b25" cx="900" cy="100" r="1.5"/><circle fill="%23ff6b3510" cx="200" cy="350" r="1"/><circle fill="%23f7ff0b15" cx="400" cy="400" r="2"/><circle fill="%23ff6b3520" cx="600" cy="300" r="1.5"/><circle fill="%23f7ff0b10" cx="800" cy="450" r="1"/></svg>');
    animation: float-particles 30s linear infinite;
}

@keyframes float-particles {
    0% { transform: translateY(0) rotate(0deg); }
    100% { transform: translateY(-50px) rotate(360deg); }
}

.hero-waves {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100px;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100"><path fill="%23f7ff0b15" d="M0,50 Q250,20 500,50 T1000,50 L1000,100 L0,100 Z"/><path fill="%23ff6b3510" d="M0,70 Q250,40 500,70 T1000,70 L1000,100 L0,100 Z"/></svg>');
    animation: wave-flow 20s ease-in-out infinite;
}

@keyframes wave-flow {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(-50px); }
}

.history-hero-content {
    position: relative;
    z-index: 2;
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 2rem;
    text-align: center;
}

.hero-icon-container {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
}

.hero-icon {
    font-size: var(--hero-title);
    display: inline-block;
    animation: book-flip 4s ease-in-out infinite;
}

@keyframes book-flip {
    0%, 100% { transform: rotateY(0deg); }
    50% { transform: rotateY(20deg); }
}

.hero-icon-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 80px;
    height: 80px;
    background: radial-gradient(circle, var(--history-glow-primary) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    animation: glow-pulse 3s ease-in-out infinite;
}

@keyframes glow-pulse {
    0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
    50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.6; }
}

.history-hero-title {
    font-size: var(--page-title);
    font-weight: 800;
    color: var(--hero-text);
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
    background: linear-gradient(135deg, var(--hero-text), var(--hero-accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.history-hero-subtitle {
    font-size: clamp(1.1rem, 2vw, 1.5rem);
    color: var(--hero-text);
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.6;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.journey-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.journey-stat {
    text-align: center;
    color: var(--hero-text);
    min-width: 100px;
}

.stat-value {
    display: block;
    font-size: clamp(2rem, 4vw, 2.5rem);
    font-weight: 800;
    color: var(--hero-accent);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-unit {
    font-size: clamp(0.85rem, 2vw, 0.9rem);
    opacity: 0.8;
    font-weight: 600;
}

/* レスポンシブ対応 - ヒーローセクション */
@media (max-width: 768px) {
    .journey-stats {
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .hero-icon {
        font-size: clamp(2.5rem, 5vw, 3rem);
    }
    
    .hero-icon-glow {
        width: 60px;
        height: 60px;
    }
    
    .history-hero-content {
        padding: 0 1rem;
    }
    
    .history-hero-subtitle br {
        display: none;
    }
}

@media (max-width: 480px) {
    .journey-stats {
        gap: 1.5rem;
        justify-content: space-around;
    }
    
    .journey-stat {
        min-width: 80px;
    }
    
    .hero-icon {
        font-size: 2.5rem;
    }
    
    .hero-icon-container {
        margin-bottom: 0.75rem;
    }
    
    .hero-icon-glow {
        width: 50px;
        height: 50px;
    }
}

/* タイムラインセクション */
.timeline-section {
    padding: 6rem 0;
    background: var(--history-card-bg);
}

.timeline-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.timeline-intro {
    text-align: center;
    margin-bottom: 4rem;
}

.timeline-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--text-primary), var(--history-accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.timeline-subtitle {
    font-size: clamp(1.1rem, 2.5vw, 1.4rem);
    color: var(--text-secondary);
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

/* 年セクション */
.year-section {
    margin-bottom: 5rem;
}

.year-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

/* レスポンシブ対応 - タイムラインセクション */
@media (max-width: 1024px) {
    .timeline-container {
        padding: 0 1.5rem;
    }
    
    .year-header {
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .timeline-section {
        padding: 4rem 0;
    }
    
    .timeline-container {
        padding: 0 1rem;
    }
    
    .timeline-intro {
        margin-bottom: 3rem;
    }
    
    .year-section {
        margin-bottom: 3rem;
    }
    
    .year-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 2rem;
    }
}

@media (max-width: 480px) {
    .timeline-section {
        padding: 2rem 0;
    }
    
    .timeline-intro {
        margin-bottom: 2rem;
    }
    
    .year-section {
        margin-bottom: 2rem;
    }
}

.timeline-subtitle {
    font-size: 1.3rem;
    color: var(--text-secondary);
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

/* 年セクション */
.year-section {
    margin-bottom: 4rem;
    position: relative;
}

.year-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 3rem;
    padding: 2rem;
    background: linear-gradient(135deg, var(--history-bg), var(--history-card-bg));
    border-radius: 2rem;
    border: 1px solid var(--history-card-border);
    box-shadow: var(--shadow-md);
}

.year-badge {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: clamp(100px, 15vw, 120px);
    height: clamp(100px, 15vw, 120px);
    background: linear-gradient(135deg, var(--history-timeline-dot), var(--history-accent));
    border-radius: 50%;
    flex-shrink: 0;
}

.year-number {
    font-size: clamp(1.5rem, 3vw, 2rem);
    font-weight: 900;
    color: var(--black);
    line-height: 1;
}

.year-accent {
    position: absolute;
    top: -5px;
    right: -5px;
    width: clamp(25px, 4vw, 30px);
    height: clamp(25px, 4vw, 30px);
    background: var(--history-accent);
    border-radius: 50%;
    border: 3px solid var(--history-card-bg);
}

.year-info h3 {
    font-size: clamp(1.8rem, 4vw, 2.5rem);
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.year-info p {
    font-size: clamp(1rem, 2.5vw, 1.2rem);
    color: var(--text-secondary);
    line-height: 1.6;
}

/* タイムライン */
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, var(--history-timeline-dot), var(--history-accent));
    border-radius: 2px;
}

.timeline-item {
    position: relative;
    margin-bottom: 3rem;
    padding-left: 4rem;
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease forwards;
}

.timeline-item:nth-child(even) {
    animation-delay: 0.1s;
}

.timeline-item:nth-child(odd) {
    animation-delay: 0.2s;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* レスポンシブ対応 - タイムライン */
@media (max-width: 768px) {
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline::before {
        width: 3px;
        left: 0.75rem;
    }
    
    .timeline-item {
        padding-left: 3rem;
        margin-bottom: 2rem;
    }
}

@media (max-width: 480px) {
    .timeline {
        padding-left: 1rem;
    }
    
    .timeline::before {
        width: 2px;
        left: 0.5rem;
    }
    
    .timeline-item {
        padding-left: 2rem;
        margin-bottom: 1.5rem;
    }
}

.timeline-marker {
    position: absolute;
    left: -3rem;
    top: 0.5rem;
    width: 60px;
    height: 60px;
    background: var(--history-card-bg);
    border: 4px solid var(--history-timeline-dot);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    transition: var(--transition);
}

.timeline-marker.featured-marker {
    width: 80px;
    height: 80px;
    left: -4rem;
    border-width: 6px;
    border-color: var(--history-accent);
    box-shadow: 0 10px 30px var(--history-shadow-primary);
}

.marker-icon {
    font-size: 1.5rem;
}

.featured-marker .marker-icon {
    font-size: 2rem;
}

/* レスポンシブ対応 - タイムラインマーカー */
@media (hover: hover) {
    .timeline-marker:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px var(--history-shadow-strong);
    }
}

@media (max-width: 768px) {
    .timeline-marker {
        width: 50px;
        height: 50px;
        left: -2.5rem;
        border-width: 3px;
    }
    
    .timeline-marker.featured-marker {
        width: 60px;
        height: 60px;
        left: -3rem;
        border-width: 4px;
    }
    
    .marker-icon {
        font-size: 1.2rem;
    }
    
    .featured-marker .marker-icon {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .timeline-marker {
        width: 40px;
        height: 40px;
        left: -2rem;
        border-width: 2px;
        top: 0.25rem;
    }
    
    .timeline-marker.featured-marker {
        width: 50px;
        height: 50px;
        left: -2.5rem;
        border-width: 3px;
    }
    
    .marker-icon {
        font-size: 1rem;
    }
    
    .featured-marker .marker-icon {
        font-size: 1.2rem;
    }
}

.marker-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    border: 2px solid var(--history-timeline-dot);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    animation: pulse-ring 2s infinite;
}

.featured-pulse {
    border-color: var(--history-accent);
    animation: pulse-ring-featured 2s infinite;
}

@keyframes pulse-ring {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; }
}

@keyframes pulse-ring-featured {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    100% { transform: translate(-50%, -50%) scale(1.3); opacity: 0; }
}

.timeline-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-weight: 700;
    color: var(--history-accent);
    flex-wrap: wrap;
}

.date-month {
    font-size: clamp(1rem, 2.5vw, 1.2rem);
}

.date-year {
    font-size: clamp(0.8rem, 2vw, 0.9rem);
    opacity: 0.7;
}

/* レスポンシブ対応 - 日付表示 */
@media (max-width: 480px) {
    .timeline-date {
        margin-bottom: 0.75rem;
        gap: 0.25rem;
    }
}

.timeline-content {
    background: var(--white);
    padding: 2rem;
    border-radius: 1.5rem;
    box-shadow: 0 10px 30px var(--history-shadow-secondary);
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
    /* タッチデバイス対応 */
    touch-action: manipulation;
}

/* ホバー対応デバイス */
@media (hover: hover) {
    .timeline-content:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px var(--history-shadow-hover);
    }
}

/* タッチデバイス */
@media (hover: none) {
    .timeline-content:active {
        transform: scale(0.98);
    }
}

/* レスポンシブ対応 - タイムラインコンテンツ */
@media (max-width: 768px) {
    .timeline-content {
        padding: 1.5rem;
        border-radius: 1rem;
    }
}

@media (max-width: 480px) {
    .timeline-content {
        padding: 1rem;
        border-radius: 0.75rem;
    }
}

.featured-content {
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    color: var(--black);
    border: none;
}

.featured-content .timeline-title,
.featured-content .timeline-description,
.featured-content .impact-text,
.featured-content .improvement-text {
    color: var(--black);
}

.content-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.timeline-title {
    font-size: clamp(1.2rem, 3vw, 1.5rem);
    font-weight: 700;
    color: var(--text-primary);
    flex: 1;
    line-height: 1.3;
    min-width: 0; /* フレックスアイテムのオーバーフロー対応 */
}

.timeline-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: clamp(0.7rem, 1.5vw, 0.75rem);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    flex-shrink: 0;
}

/* レスポンシブ対応 - コンテンツヘッダー */
@media (max-width: 768px) {
    .content-header {
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
}

@media (max-width: 480px) {
    .content-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .timeline-title {
        flex: none;
        width: 100%;
    }
    
    .timeline-badge {
        padding: 0.2rem 0.6rem;
        font-size: 0.65rem;
    }
}

.launch-badge { background: var(--primary); color: var(--black); }
.milestone-badge { background: var(--accent); color: var(--white); }
.innovation-badge { background: #667eea; color: var(--white); }
.breakthrough-badge { background: linear-gradient(45deg, #ff6b35, #f7931e); color: var(--white); }
.community-badge { background: #48bb78; color: var(--white); }
.awards-badge { background: #ed8936; color: var(--white); }
.upgrade-badge { background: #805ad5; color: var(--white); }
.celebration-badge { background: #f56565; color: var(--white); }
.collaboration-badge { background: #38b2ac; color: var(--white); }
.viral-badge { background: linear-gradient(45deg, #ff0844, #ff6b35); color: var(--white); }
.award-badge { background: linear-gradient(45deg, #ffd700, #ffb347); color: var(--black); }
.growth-badge { background: linear-gradient(45deg, #4CAF50, #81C784); color: var(--white); }

.timeline-description {
    color: var(--text-primary);
    line-height: 1.7;
    margin-bottom: 1.5rem;
    font-size: clamp(0.95rem, 2.5vw, 1.1rem);
}

.timeline-impact {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: 0.75rem;
    margin-top: 1rem;
    flex-wrap: wrap;
}

.impact-label {
    font-weight: 700;
    color: var(--accent);
    flex-shrink: 0;
}

.impact-text {
    color: var(--text-secondary);
    font-style: italic;
    flex: 1;
    min-width: 0;
}

.timeline-actions {
    margin-top: 1.5rem;
}

.timeline-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--primary);
    color: var(--black);
    text-decoration: none;
    border-radius: 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    /* タッチデバイス対応 */
    min-height: 48px;
    touch-action: manipulation;
    cursor: pointer;
}

/* ホバー対応デバイス */
@media (hover: hover) {
    .timeline-link:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px var(--history-shadow-primary);
        text-decoration: none;
    }
}

/* タッチデバイス */
@media (hover: none) {
    .timeline-link:active {
        transform: scale(0.98);
    }
}

/* レスポンシブ対応 - タイムライン説明とアクション */
@media (max-width: 768px) {
    .timeline-description {
        margin-bottom: 1rem;
    }
    
    .timeline-impact {
        padding: 0.75rem;
        margin-top: 0.75rem;
    }
    
    .timeline-actions {
        margin-top: 1rem;
    }
    
    .timeline-link {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 480px) {
    .timeline-description {
        margin-bottom: 0.75rem;
    }
    
    .timeline-impact {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
        padding: 0.6rem;
    }
    
    .timeline-link {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        width: 100%;
        justify-content: center;
    }
}

.featured-link {
    background: var(--white);
    color: var(--black);
    border: 2px solid var(--black);
}

.featured-link:hover {
    background: var(--black);
    color: var(--white);
    border-color: var(--white);
}

.link-icon {
    font-size: 1.2rem;
}

.timeline-visual {
    margin-top: 2rem;
}

.artwork-showcase {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 2rem;
    background: var(--history-overlay-light);
    border-radius: 1rem;
    backdrop-filter: blur(10px);
}

.artwork-image {
    width: clamp(150px, 30vw, 200px);
    height: clamp(150px, 30vw, 200px);
    border-radius: 1rem;
    box-shadow: 0 10px 30px var(--history-shadow-strong);
    transition: transform 0.3s ease;
    object-fit: cover;
}

/* ホバー対応デバイス */
@media (hover: hover) {
    .artwork-image:hover {
        transform: scale(1.05);
    }
}

/* タッチデバイス */
@media (hover: none) {
    .artwork-image:active {
        transform: scale(0.95);
    }
}

/* レスポンシブ対応 - アートワーク */
@media (max-width: 768px) {
    .artwork-showcase {
        padding: 1.5rem;
        gap: 0.75rem;
    }
}

@media (max-width: 480px) {
    .artwork-showcase {
        padding: 1rem;
        gap: 0.5rem;
    }
}

.artwork-caption {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--black);
    font-weight: 600;
}

.awards-list,
.improvement-list {
    margin-top: 1rem;
}

.award-item,
.improvement-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background: var(--gray-50);
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.award-item:hover,
.improvement-item:hover {
    background: var(--primary);
    transform: translateX(5px);
}

.award-item.grand {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: var(--black);
    font-weight: 700;
}

.award-icon,
.improvement-icon {
    font-size: 1.3rem;
}

/* 未来セクション */
.future-section {
    position: relative;
    background: linear-gradient(135deg, var(--black) 0%, #1a1a1a 50%, #2d2d2d 100%);
    padding: 6rem 0;
    overflow: hidden;
}

.future-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.future-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><circle fill="%23f7ff0b15" cx="200" cy="200" r="50"/><circle fill="%23ff6b3520" cx="800" cy="300" r="80"/><polygon fill="%23f7ff0b10" points="300,700 500,500 700,700"/><circle fill="%23ff6b3515" cx="150" cy="600" r="40"/><circle fill="%23f7ff0b20" cx="850" cy="700" r="60"/></svg>');
    animation: pattern-drift 40s linear infinite;
}

@keyframes pattern-drift {
    0% { transform: translate(0, 0) rotate(0deg); }
    100% { transform: translate(-50px, -30px) rotate(5deg); }
}

.future-container {
    position: relative;
    z-index: 2;
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 2rem;
    text-align: center;
}

.future-icon {
    font-size: clamp(3rem, 6vw, 5rem);
    margin-bottom: 1.5rem;
    display: inline-block;
    animation: rocket-launch 3s ease-in-out infinite;
}

@keyframes rocket-launch {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

.future-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 900;
    color: var(--white);
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--white), var(--primary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.future-subtitle {
    font-size: clamp(1.2rem, 3vw, 1.5rem);
    color: var(--primary);
    margin-bottom: 2rem;
    font-weight: 700;
}

.future-description {
    font-size: clamp(1rem, 2.5vw, 1.2rem);
    color: var(--white);
    line-height: 1.8;
    margin-bottom: 3rem;
    opacity: 0.9;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

/* レスポンシブ対応 - 未来セクション */
@media (max-width: 768px) {
    .future-section {
        padding: 4rem 0;
    }
    
    .future-container {
        padding: 0 1rem;
    }
    
    .future-icon {
        margin-bottom: 1rem;
    }
    
    .future-subtitle {
        margin-bottom: 1.5rem;
    }
    
    .future-description {
        margin-bottom: 2rem;
    }
    
    .future-description br {
        display: none;
    }
}

@media (max-width: 480px) {
    .future-section {
        padding: 3rem 0;
    }
    
    .future-description {
        margin-bottom: 1.5rem;
    }
}

.future-cta {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-bottom: 4rem;
    flex-wrap: wrap;
}

.future-cta-button {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 3rem;
    font-weight: 700;
    font-size: 1.1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    min-width: 220px;
    justify-content: center;
    /* タッチデバイス対応 */
    min-height: 48px;
    touch-action: manipulation;
    cursor: pointer;
}

.future-cta-button.primary {
    background: var(--primary);
    color: var(--black);
    border: 2px solid var(--primary);
}

.future-cta-button.secondary {
    background: transparent;
    color: var(--white);
    border: 2px solid var(--white);
}

/* ホバー対応デバイス */
@media (hover: hover) {
    .future-cta-button.primary:hover {
        background: var(--primary-light);
        transform: translateY(-3px);
        box-shadow: 0 15px 40px var(--history-shadow-intense);
        text-decoration: none;
    }
    
    .future-cta-button.secondary:hover {
        background: var(--white);
        color: var(--black);
        transform: translateY(-3px);
        text-decoration: none;
    }
}

/* タッチデバイス */
@media (hover: none) {
    .future-cta-button:active {
        transform: scale(0.98);
    }
}

.btn-icon {
    font-size: 1.3rem;
}

/* レスポンシブ対応 - 未来CTA */
@media (max-width: 768px) {
    .future-cta {
        gap: 1rem;
        margin-bottom: 3rem;
    }
    
    .future-cta-button {
        min-width: 200px;
        padding: 0.875rem 1.75rem;
        font-size: 1rem;
    }
    
    .btn-icon {
        font-size: 1.2rem;
    }
}

@media (max-width: 480px) {
    .future-cta {
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }
    
    .future-cta-button {
        width: 100%;
        max-width: 280px;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        min-width: unset;
    }
    
    .btn-icon {
        font-size: 1.1rem;
    }
}

.future-social {
    border-top: 1px solid var(--history-border-light);
    padding-top: 3rem;
}

.social-text {
    color: var(--white);
    font-size: clamp(1rem, 2.5vw, 1.1rem);
    margin-bottom: 2rem;
    opacity: 0.8;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.social-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: var(--history-overlay-light);
    backdrop-filter: blur(10px);
    border-radius: 2rem;
    text-decoration: none;
    color: var(--white);
    transition: all 0.3s ease;
    border: 1px solid var(--history-border-light);
    /* タッチデバイス対応 */
    min-height: 48px;
    touch-action: manipulation;
    cursor: pointer;
}

/* ホバー対応デバイス */
@media (hover: hover) {
    .social-link:hover {
        background: var(--history-overlay-medium);
        transform: translateY(-3px);
        text-decoration: none;
        color: var(--white);
    }
    
    .social-link.spotify:hover {
        background: #1DB954;
        border-color: #1DB954;
    }
    
    .social-link.apple:hover {
        background: #A855F7;
        border-color: #A855F7;
    }
    
    .social-link.youtube:hover {
        background: #FF0000;
        border-color: #FF0000;
    }
}

/* タッチデバイス */
@media (hover: none) {
    .social-link:active {
        transform: scale(0.95);
        background: var(--history-overlay-medium);
    }
}

.social-icon {
    font-size: clamp(1.2rem, 3vw, 1.5rem);
}

.social-label {
    font-weight: 600;
    font-size: clamp(0.9rem, 2.5vw, 1rem);
}

/* レスポンシブ対応 - ソーシャルリンク */
@media (max-width: 768px) {
    .future-social {
        padding-top: 2rem;
    }
    
    .social-text {
        margin-bottom: 1.5rem;
    }
    
    .social-links {
        gap: 1rem;
    }
    
    .social-link {
        padding: 0.75rem 1.25rem;
        gap: 0.5rem;
    }
}

@media (max-width: 480px) {
    .future-social {
        padding-top: 1.5rem;
    }
    
    .social-text {
        margin-bottom: 1rem;
    }
    
    .social-links {
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }
    
    .social-link {
        width: 100%;
        max-width: 250px;
        justify-content: center;
        padding: 0.75rem 1rem;
    }
}

/* ===== アクセシビリティとユーザビリティの改善 ===== */

/* フォーカス状態の改善 */
.future-cta-button:focus,
.social-link:focus,
.timeline-link:focus {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

/* 読みやすさの改善 */
.timeline-description,
.future-description {
    line-height: 1.7;
}

/* モーション設定の尊重 */
@media (prefers-reduced-motion: reduce) {
    .hero-particles,
    .hero-waves,
    .future-pattern,
    .rocket-launch,
    .book-flip,
    .glow-pulse,
    .float-particles,
    .wave-flow,
    .pattern-drift {
        animation: none;
    }
    
    .timeline-marker,
    .timeline-content,
    .future-cta-button,
    .social-link {
        transition: none;
    }
}

/* 印刷スタイル */
@media print {
    .history-hero,
    .future-section {
        background: var(--white) !important;
        color: var(--black) !important;
    }
    
    .history-hero-content *,
    .future-content * {
        color: var(--black) !important;
    }
    
    .future-social {
        display: none;
    }
    
    .timeline-marker {
        box-shadow: none !important;
        border: 1px solid var(--gray-300) !important;
    }
}
    
    .history-hero-content {
        padding: 0 1rem;
    }
    
    .timeline-container {
        padding: 0 1rem;
    }
    
    .year-header {
        padding: 1.5rem;
        border-radius: 1rem;
    }
    
    .year-badge {
        width: 80px;
        height: 80px;
    }
    
    .year-number {
        font-size: 1.5rem;
    }
    
    .timeline-content {
        padding: 1.5rem;
        border-radius: 1rem;
    }
}

/* スクロールアニメーション */
@media (prefers-reduced-motion: no-preference) {
    .timeline-item {
        opacity: 0;
        transform: translateY(50px);
        transition: all 0.6s ease;
    }
    
    .timeline-item.in-view {
        opacity: 1;
        transform: translateY(0);
    }
}

/* アクセシビリティ */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* プリント対応 */
@media print {
    .history-hero-bg,
    .future-bg,
    .hero-particles,
    .hero-waves,
    .future-pattern {
        display: none !important;
    }
    
    .history-hero,
    .future-section {
        background: var(--white) !important;
        color: var(--black) !important;
    }
    
    .timeline::before {
        background: var(--black) !important;
    }
}
</style>

<?php get_footer(); ?>
