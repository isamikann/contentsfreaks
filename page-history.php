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
                    
                    <!-- 6月 -->
                    <div class="timeline-item milestone" data-aos="fade-up" data-aos-delay="300">
                        <div class="timeline-marker">
                            <div class="marker-icon">🎯</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">6月</span>
                            <span class="date-year">2025</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">🎯 Apple Podcast 200フォロワー突破</h4>
                                <span class="timeline-badge milestone-badge">Milestone</span>
                            </div>
                            <p class="timeline-description">
                                Apple Podcastのフォロワー数が200人を突破！主要プラットフォームでの着実な成長を実現。
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">マルチプラットフォームでの認知度向上</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 7月 -->
                    <div class="timeline-item featured community" data-aos="fade-up" data-aos-delay="400">
                        <div class="timeline-marker featured-marker">
                            <div class="marker-icon">🎤</div>
                            <div class="marker-pulse featured-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">7月</span>
                            <span class="date-year">2025</span>
                        </div>
                        <div class="timeline-content featured-content">
                            <div class="content-header">
                                <h4 class="timeline-title">🎤 名古屋「ポッドキャストミキサー」に登壇！</h4>
                                <span class="timeline-badge community-badge">Community</span>
                            </div>
                            <p class="timeline-description">
                                名古屋で開催された「ポッドキャストミキサー」に登壇！「ドタバタグッドボタン」のけーちゃんと一緒に、対談形式で名古屋にまつわるコンテンツクイズを実施。<br><br>
                                会場は満席でワイワイ賑やかな雰囲気！クイズ中は真剣に考えたり、珍回答に大笑いしたり、メリハリがあって楽しい空間に。<br><br>
                                さらに、イベントを機にポッドキャスト用のオリジナル名刺も制作！コンフリブランドカラーで統一し、新規のコンフリキャラクターもデザインに採用。
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">初の本格的なイベント登壇でリスナーとの交流を実現</span>
                            </div>
                            <div class="timeline-actions">
                                <a href="https://content-freaks.jp/2025-2q-growth-podcast/" class="timeline-link featured-link" target="_blank">
                                    <span class="link-icon">📝</span>
                                    詳細記事を読む
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 8月 -->
                    <div class="timeline-item innovation" data-aos="fade-up" data-aos-delay="500">
                        <div class="timeline-marker">
                            <div class="marker-icon">🎨</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">8月</span>
                            <span class="date-year">2025</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">🎨 サムネイルデザイン刷新</h4>
                                <span class="timeline-badge innovation-badge">Innovation</span>
                            </div>
                            <p class="timeline-description">
                                ポッドキャスト用とYouTube用の2種類のサムネイルフォーマットを新たに作成！<br><br>
                                改善ポイント：<br>
                                ▶ コンフリカラーで統一感を実現<br>
                                ▶ 誰が見ても一目でコンフリだと分かるデザイン<br>
                                ▶ サムネイル作成がスムーズに<br>
                                ▶ ポッドキャストではコンフリマーク、YouTubeではPodcastマークを追加
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">YouTubeのクリック率が2〜4％から7〜10％に大幅アップ！</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 9月 -->
                    <div class="timeline-item breakthrough" data-aos="fade-up" data-aos-delay="600">
                        <div class="timeline-marker">
                            <div class="marker-icon">🎉</div>
                            <div class="marker-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">9月</span>
                            <span class="date-year">2025</span>
                        </div>
                        <div class="timeline-content">
                            <div class="content-header">
                                <h4 class="timeline-title">🎉 YouTube 900人突破＆コラボ配信</h4>
                                <span class="timeline-badge breakthrough-badge">Breakthrough</span>
                            </div>
                            <p class="timeline-description">
                                YouTubeの登録者数が900人を突破！サムネイル改善の効果が着実に数字に表れる。<br><br>
                                さらに、「推し活2次元LIFEラジオ」とコラボ配信を実施！番組間の交流がさらに活発に。
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">YouTube 1000人突破まであと少し！</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 10月 -->
                    <div class="timeline-item featured celebration" data-aos="fade-up" data-aos-delay="700">
                        <div class="timeline-marker featured-marker">
                            <div class="marker-icon">🏆</div>
                            <div class="marker-pulse featured-pulse"></div>
                        </div>
                        <div class="timeline-date">
                            <span class="date-month">10月</span>
                            <span class="date-year">2025</span>
                        </div>
                        <div class="timeline-content featured-content">
                            <div class="content-header">
                                <h4 class="timeline-title">🏆 YouTube登録者1000人突破！！！</h4>
                                <span class="timeline-badge celebration-badge">Celebration</span>
                            </div>
                            <p class="timeline-description">
                                ついに目標であったYouTube登録者数1000人を突破！！！<br><br>
                                番組開始から約2年、サムネイル改善やコンテンツの充実により、ついに大台達成。これまで応援してくださったすべてのリスナーの皆様に心から感謝！
                            </p>
                            <div class="timeline-impact">
                                <span class="impact-label">Impact:</span>
                                <span class="impact-text">番組史上最大のマイルストーン達成！</span>
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
/* ==========================================================================
   History Page - Modern Timeline Design
   ========================================================================== */

:root {
    --history-primary: #667eea;
    --history-secondary: #764ba2;
    --history-accent: #f093fb;
    --history-yellow: #f7ff0b;
    --history-orange: #ff6b35;
    --history-success: #10b981;
    --history-dark: #1a1a1a;
    --history-light: #ffffff;
    --history-gray-100: #f7fafc;
    --history-gray-200: #edf2f7;
    --history-gray-300: #e2e8f0;
    --history-gray-700: #4a5568;
    --history-gray-900: #1a202c;
}

/* ========== Base Styles ========== */
.history-page {
    background: var(--history-light);
    margin-top: 0;
}

/* ========== Breadcrumb Navigation ========== */
.breadcrumb-nav {
    background: linear-gradient(135deg, var(--history-primary) 0%, var(--history-secondary) 100%);
    padding: 1rem 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.breadcrumb-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--history-light);
}

.breadcrumb-home {
    color: var(--history-light);
    text-decoration: none;
    font-weight: 600;
    transition: opacity 0.3s ease;
}

.breadcrumb-home:hover {
    opacity: 0.8;
}

.breadcrumb-separator {
    opacity: 0.6;
}

.breadcrumb-current {
    font-weight: 500;
    opacity: 0.9;
}

/* ========== Hero Section ========== */
.history-hero {
    position: relative;
    background: linear-gradient(135deg, var(--history-primary) 0%, var(--history-secondary) 50%, var(--history-accent) 100%);
    padding: 6rem 2rem;
    overflow: hidden;
    margin-bottom: 4rem;
}

.history-hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.1;
}

.hero-particles {
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 50px 50px;
    animation: particleFloat 20s linear infinite;
}

@keyframes particleFloat {
    0% { transform: translateY(0); }
    100% { transform: translateY(-50px); }
}

.hero-waves {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100px;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120'%3E%3Cpath d='M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z' fill='%23ffffff' opacity='0.1'/%3E%3C/svg%3E") repeat-x;
    background-size: cover;
}

.history-hero-content {
    position: relative;
    z-index: 1;
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
}

.hero-icon-container {
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
}

.hero-icon {
    font-size: 4rem;
    filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));
    animation: heroIconFloat 3s ease-in-out infinite;
}

@keyframes heroIconFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.hero-icon-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 120px;
    height: 120px;
    background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%);
    border-radius: 50%;
    animation: glowPulse 2s ease-in-out infinite;
}

@keyframes glowPulse {
    0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
    50% { opacity: 1; transform: translate(-50%, -50%) scale(1.2); }
}

.history-hero-title {
    font-size: 4rem;
    font-weight: 900;
    color: var(--history-light);
    margin: 0 0 1rem 0;
    letter-spacing: -0.02em;
    text-shadow: 0 4px 20px rgba(0,0,0,0.2);
    line-height: 1.1;
}

.history-hero-subtitle {
    font-size: 1.25rem;
    color: rgba(255,255,255,0.95);
    margin: 0 0 3rem 0;
    line-height: 1.6;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.journey-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    flex-wrap: wrap;
}

.journey-stat {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 3rem;
    font-weight: 900;
    color: var(--history-yellow);
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    line-height: 1;
}

.stat-unit {
    display: block;
    font-size: 1rem;
    color: rgba(255,255,255,0.9);
    margin-top: 0.5rem;
    font-weight: 600;
}

/* ========== Timeline Section ========== */
.timeline-section {
    padding: 4rem 2rem;
    background: var(--history-gray-100);
}

.timeline-container {
    max-width: 1200px;
    margin: 0 auto;
}

.timeline-intro {
    text-align: center;
    margin-bottom: 4rem;
}

.timeline-title {
    font-size: 3rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--history-primary), var(--history-secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 1rem 0;
}

.timeline-subtitle {
    font-size: 1.25rem;
    color: var(--history-gray-700);
    margin: 0;
}

/* ========== Year Section ========== */
.year-section {
    margin-bottom: 5rem;
}

.year-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 3rem;
    padding-bottom: 1.5rem;
    border-bottom: 3px solid var(--history-primary);
    position: relative;
}

.year-header::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 100px;
    height: 3px;
    background: var(--history-yellow);
}

.year-badge {
    position: relative;
    flex-shrink: 0;
}

.year-number {
    display: block;
    font-size: 3.5rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--history-primary), var(--history-accent));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
}

.year-accent {
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    background: linear-gradient(135deg, var(--history-primary), var(--history-accent));
    opacity: 0.1;
    border-radius: 12px;
    z-index: -1;
}

.year-info {
    flex: 1;
}

.year-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--history-dark);
    margin: 0 0 0.5rem 0;
}

.year-subtitle {
    font-size: 1.125rem;
    color: var(--history-gray-700);
    margin: 0;
}

/* ========== Timeline ========== */
.timeline {
    position: relative;
    padding-left: 3rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, var(--history-primary), var(--history-accent));
    border-radius: 2px;
}

.timeline-item {
    position: relative;
    margin-bottom: 3rem;
    background: var(--history-light);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.timeline-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
}

.timeline-item.featured {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(240, 147, 251, 0.05) 100%);
    border: 2px solid var(--history-primary);
}

.timeline-marker {
    position: absolute;
    left: -4.5rem;
    top: 2rem;
    width: 50px;
    height: 50px;
    background: var(--history-light);
    border: 4px solid var(--history-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}

.timeline-item:hover .timeline-marker {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.timeline-marker.featured-marker {
    background: linear-gradient(135deg, var(--history-primary), var(--history-accent));
    border-color: var(--history-yellow);
    animation: markerPulse 2s ease-in-out infinite;
}

@keyframes markerPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(247, 255, 11, 0.7); }
    50% { box-shadow: 0 0 0 10px rgba(247, 255, 11, 0); }
}

.marker-icon {
    font-size: 1.5rem;
    line-height: 1;
}

.marker-pulse {
    position: absolute;
    top: -4px;
    left: -4px;
    right: -4px;
    bottom: -4px;
    border: 2px solid var(--history-primary);
    border-radius: 50%;
    animation: pulseRing 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulseRing {
    0% { transform: scale(1); opacity: 1; }
    100% { transform: scale(1.3); opacity: 0; }
}

.timeline-date {
    display: inline-flex;
    align-items: baseline;
    gap: 0.5rem;
    background: var(--history-gray-100);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    margin-bottom: 1rem;
    font-weight: 600;
}

.date-month {
    color: var(--history-primary);
    font-size: 1rem;
}

.date-year {
    color: var(--history-gray-700);
    font-size: 0.875rem;
}

.timeline-content {
    /* Content styles handled by child elements */
}

.content-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.timeline-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--history-dark);
    margin: 0;
    flex: 1;
    min-width: 200px;
}

.timeline-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}

.launch-badge {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.milestone-badge {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.innovation-badge {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

.breakthrough-badge {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.community-badge {
    background: linear-gradient(135deg, #ec4899, #db2777);
    color: white;
}

.awards-badge {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: var(--history-dark);
}

.collaboration-badge {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
    color: white;
}

.upgrade-badge {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: white;
}

.celebration-badge {
    background: linear-gradient(135deg, #f7ff0b, #ffd700);
    color: var(--history-dark);
    animation: badgeShine 2s ease-in-out infinite;
}

@keyframes badgeShine {
    0%, 100% { box-shadow: 0 0 10px rgba(247, 255, 11, 0.5); }
    50% { box-shadow: 0 0 20px rgba(247, 255, 11, 0.8); }
}

.growth-badge {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.viral-badge {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.timeline-description {
    font-size: 1rem;
    line-height: 1.7;
    color: var(--history-gray-700);
    margin: 0 0 1rem 0;
}

.timeline-impact {
    background: rgba(102, 126, 234, 0.1);
    border-left: 4px solid var(--history-primary);
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1.5rem;
}

.impact-label {
    font-weight: 700;
    color: var(--history-primary);
    margin-right: 0.5rem;
}

.impact-text {
    color: var(--history-gray-700);
}

.timeline-actions {
    margin-top: 1.5rem;
}

.timeline-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--history-primary), var(--history-secondary));
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.timeline-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.timeline-link.featured-link {
    background: linear-gradient(135deg, var(--history-yellow), var(--history-orange));
    color: var(--history-dark);
}

.link-icon {
    font-size: 1rem;
}

/* Lists */
.awards-list,
.improvement-list {
    margin-top: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.award-item,
.improvement-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: rgba(255,255,255,0.8);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.award-item:hover,
.improvement-item:hover {
    background: rgba(255,255,255,1);
    transform: translateX(4px);
}

.award-item.grand {
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(245, 158, 11, 0.2));
    border: 2px solid #fbbf24;
}

.award-icon,
.improvement-icon {
    font-size: 1.25rem;
    flex-shrink: 0;
}

.award-text,
.improvement-text {
    font-size: 1rem;
    color: var(--history-gray-700);
    font-weight: 500;
}

/* Artwork Showcase */
.artwork-showcase {
    margin-top: 1.5rem;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

.artwork-showcase:hover {
    transform: scale(1.02);
    box-shadow: 0 12px 32px rgba(0,0,0,0.2);
}

.artwork-image {
    width: 100%;
    height: auto;
    display: block;
}

.artwork-caption {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: var(--history-gray-100);
}

.caption-label {
    font-size: 1.25rem;
}

.caption-text {
    font-size: 0.875rem;
    color: var(--history-gray-700);
    font-weight: 600;
}

/* ========== Future Section ========== */
.future-section {
    position: relative;
    background: linear-gradient(135deg, var(--history-dark) 0%, #2d3748 100%);
    padding: 6rem 2rem;
    overflow: hidden;
}

.future-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.05;
}

.future-pattern {
    width: 100%;
    height: 100%;
    background-image: 
        repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.05) 35px, rgba(255,255,255,.05) 70px);
}

.future-container {
    position: relative;
    z-index: 1;
    max-width: 900px;
    margin: 0 auto;
}

.future-content {
    text-align: center;
}

.future-icon {
    font-size: 4rem;
    margin-bottom: 2rem;
    animation: heroIconFloat 3s ease-in-out infinite;
}

.future-title {
    font-size: 3rem;
    font-weight: 900;
    color: var(--history-light);
    margin: 0 0 1rem 0;
}

.future-subtitle {
    font-size: 1.5rem;
    color: var(--history-yellow);
    margin: 0 0 2rem 0;
    font-weight: 700;
}

.future-description {
    font-size: 1.125rem;
    line-height: 1.8;
    color: rgba(255,255,255,0.9);
    margin: 0 0 3rem 0;
}

.future-cta {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.future-cta-button {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1.125rem;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.future-cta-button.primary {
    background: linear-gradient(135deg, var(--history-yellow), var(--history-orange));
    color: var(--history-dark);
    box-shadow: 0 4px 20px rgba(247, 255, 11, 0.4);
}

.future-cta-button.primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(247, 255, 11, 0.6);
}

.future-cta-button.secondary {
    background: rgba(255,255,255,0.1);
    color: var(--history-light);
    border: 2px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(10px);
}

.future-cta-button.secondary:hover {
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.5);
    transform: translateY(-3px);
}

.btn-icon {
    font-size: 1.25rem;
}

/* ========== Responsive Design ========== */
@media (max-width: 768px) {
    .history-hero {
        padding: 4rem 1.5rem;
    }
    
    .history-hero-title {
        font-size: 2.5rem;
    }
    
    .history-hero-subtitle {
        font-size: 1rem;
    }
    
    .journey-stats {
        gap: 2rem;
    }
    
    .stat-value {
        font-size: 2rem;
    }
    
    .timeline-title {
        font-size: 2rem;
    }
    
    .year-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .year-number {
        font-size: 2.5rem;
    }
    
    .year-title {
        font-size: 1.5rem;
    }
    
    .timeline {
        padding-left: 2rem;
    }
    
    .timeline::before {
        width: 3px;
    }
    
    .timeline-marker {
        left: -3.25rem;
        width: 40px;
        height: 40px;
    }
    
    .marker-icon {
        font-size: 1.25rem;
    }
    
    .timeline-item {
        padding: 1.5rem;
    }
    
    .timeline-title {
        font-size: 1.25rem;
    }
    
    .content-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .future-title {
        font-size: 2rem;
    }
    
    .future-subtitle {
        font-size: 1.25rem;
    }
    
    .future-description {
        font-size: 1rem;
    }
    
    .future-cta {
        flex-direction: column;
        align-items: stretch;
    }
    
    .future-cta-button {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .breadcrumb-container {
        padding: 0 1rem;
        font-size: 0.875rem;
    }
    
    .history-hero {
        padding: 3rem 1rem;
    }
    
    .history-hero-title {
        font-size: 2rem;
    }
    
    .hero-icon {
        font-size: 3rem;
    }
    
    .journey-stats {
        gap: 1.5rem;
    }
    
    .stat-value {
        font-size: 1.75rem;
    }
    
    .stat-unit {
        font-size: 0.875rem;
    }
    
    .timeline-section {
        padding: 3rem 1rem;
    }
    
    .timeline-intro {
        margin-bottom: 3rem;
    }
    
    .timeline-title {
        font-size: 1.75rem;
    }
    
    .timeline-subtitle {
        font-size: 1rem;
    }
    
    .year-number {
        font-size: 2rem;
    }
    
    .year-title {
        font-size: 1.25rem;
    }
    
    .year-subtitle {
        font-size: 1rem;
    }
    
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline-marker {
        left: -2.75rem;
        width: 36px;
        height: 36px;
        border-width: 3px;
    }
    
    .marker-icon {
        font-size: 1rem;
    }
    
    .timeline-item {
        padding: 1.25rem;
        margin-bottom: 2rem;
    }
    
    .timeline-title {
        font-size: 1.125rem;
    }
    
    .timeline-description {
        font-size: 0.9375rem;
    }
    
    .future-section {
        padding: 4rem 1rem;
    }
    
    .future-icon {
        font-size: 3rem;
    }
    
    .future-title {
        font-size: 1.75rem;
    }
    
    .future-subtitle {
        font-size: 1.125rem;
    }
    
    .future-cta-button {
        padding: 0.875rem 1.5rem;
        font-size: 1rem;
    }
}

/* ========== Print Styles ========== */
@media print {
    .breadcrumb-nav,
    .future-cta {
        display: none;
    }
    
    .timeline-item {
        page-break-inside: avoid;
    }
}
</style>

<?php get_footer(); ?>
