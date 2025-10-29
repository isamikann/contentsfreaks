<?php
/**
 * Template Name: コンテンツフリークスの歩み
 */

get_header(); ?>

<main id="main" class="site-main history-page">
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
                        <span class="stat-value"><?php 
                            $episode_count = get_posts(array(
                                'meta_key' => 'is_podcast_episode',
                                'meta_value' => '1',
                                'post_status' => 'publish',
                                'numberposts' => -1
                            ));
                            echo count($episode_count);
                        ?>+</span>
                        <span class="stat-unit">エピソード</span>
                    </div>
                    <div class="journey-stat">
                        <span class="stat-value">200+</span>
                        <span class="stat-unit">配信時間</span>
                    </div>
                    <div class="journey-stat">
                        <span class="stat-value"><?php echo esc_attr(get_option('contentfreaks_listener_count', '1500')); ?>+</span>
                        <span class="stat-unit">フォロワー</span>
                    </div>
                    <div class="journey-stat">
                        <span class="stat-value"><?php 
                            $start_date = new DateTime('2023-06-01');
                            $current_date = new DateTime();
                            $interval = $start_date->diff($current_date);
                            echo $interval->days;
                        ?>+</span>
                        <span class="stat-unit">継続日数</span>
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
                                アニメ「葬送のフリーレン」回（#20）を配信。当時No1の人気エピソードに！<br><br>
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

    <!-- アートワーク変遷ギャラリー -->
    <section class="artwork-evolution-section">
        <div class="evolution-container">
            <div class="section-header">
                <h2 class="section-title">Artwork Evolution</h2>
                <p class="section-subtitle">番組の成長とともに進化してきたアートワーク</p>
            </div>
            
            <div class="artwork-timeline">
                <!-- カラビナFM 初期 -->
                <div class="artwork-card" data-aos="fade-up">
                    <div class="artwork-image-container">
                        <img src="https://content-freaks.jp/wp-content/uploads/2024/05/1000017105.jpg" alt="カラビナFM初期アートワーク" class="evolution-artwork-image">
                        <div class="artwork-overlay">
                            <span class="artwork-year">2023.06</span>
                        </div>
                    </div>
                    <div class="artwork-info">
                        <h3 class="artwork-title">カラビナFM</h3>
                        <p class="artwork-period">2023年6月〜10月</p>
                        <p class="artwork-description">番組スタート時のオリジナルアートワーク。雑談番組として始まった原点を表現。</p>
                    </div>
                </div>
                
                <!-- コンテンツフリークス 初期 -->
                <div class="artwork-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="artwork-image-container">
                        <img src="https://content-freaks.jp/wp-content/uploads/2024/05/1000014856-1024x1024.png" alt="コンテンツフリークス初期アートワーク" class="evolution-artwork-image">
                        <div class="artwork-overlay">
                            <span class="artwork-year">2023.10</span>
                        </div>
                    </div>
                    <div class="artwork-info">
                        <h3 class="artwork-title">コンテンツフリークス 1st</h3>
                        <p class="artwork-period">2023年10月〜2024年3月</p>
                        <p class="artwork-description">番組リニューアル記念。コンテンツを語る番組へと方向性が定まった時期。</p>
                    </div>
                </div>
                
                <!-- 50回記念 -->
                <div class="artwork-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="artwork-image-container">
                        <img src="https://content-freaks.jp/wp-content/uploads/2024/05/1000015915-1024x1024.png" alt="50回記念アートワーク" class="evolution-artwork-image">
                        <div class="artwork-overlay">
                            <span class="artwork-year">2024.03</span>
                        </div>
                    </div>
                    <div class="artwork-info">
                        <h3 class="artwork-title">コンテンツフリークス 2nd</h3>
                        <p class="artwork-period">2024年3月〜2025年3月</p>
                        <p class="artwork-description">50回配信記念リニューアル。番組の成長と進化を象徴するデザイン。</p>
                    </div>
                </div>
                
                <!-- 最新 150回記念 -->
                <div class="artwork-card featured-artwork" data-aos="fade-up" data-aos-delay="300">
                    <div class="artwork-image-container">
                        <img src="https://content-freaks.jp/wp-content/uploads/2023/07/36275010-1739517733196-9955f073fd424-4.jpg" alt="最新アートワーク" class="evolution-artwork-image">
                        <div class="artwork-overlay">
                            <span class="artwork-year">2025.03</span>
                            <span class="artwork-badge">Latest</span>
                        </div>
                    </div>
                    <div class="artwork-info">
                        <h3 class="artwork-title">コンテンツフリークス 3rd</h3>
                        <p class="artwork-period">2025年3月〜現在</p>
                        <p class="artwork-description">150回＆総フォロワー1000人突破記念。現在の理想を体現した最新デザイン。</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- フォロワー成長グラフ -->
    <section class="growth-graph-section">
        <div class="growth-container">
            <div class="section-header">
                <h2 class="section-title">Growth Timeline</h2>
                <p class="section-subtitle">フォロワー数の成長推移</p>
            </div>
            
            <div class="growth-chart">
                <!-- Y軸ラベル -->
                <div class="chart-y-axis">
                    <span class="y-label">1500</span>
                    <span class="y-label">1200</span>
                    <span class="y-label">900</span>
                    <span class="y-label">600</span>
                    <span class="y-label">300</span>
                    <span class="y-label">0</span>
                </div>
                
                <!-- グラフエリア -->
                <div class="chart-area">
                    <!-- グリッド線 -->
                    <div class="chart-grid">
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                    </div>
                    
                    <!-- データポイント -->
                    <div class="chart-line">
                        <!-- 2023年6月 - 開始 -->
                        <div class="data-point" style="left: 0%; bottom: 0%;" data-aos="zoom-in" data-aos-delay="0">
                            <div class="point-marker start"></div>
                            <div class="point-label">
                                <span class="point-value">0</span>
                                <span class="point-date">2023.06</span>
                            </div>
                        </div>
                        
                        <!-- 2023年10月 - リニューアル -->
                        <div class="data-point" style="left: 14%; bottom: 6.7%;" data-aos="zoom-in" data-aos-delay="100">
                            <div class="point-marker"></div>
                            <div class="point-label">
                                <span class="point-value">100</span>
                                <span class="point-date">2023.10</span>
                            </div>
                        </div>
                        
                        <!-- 2024年3月 - 50回記念 -->
                        <div class="data-point" style="left: 30%; bottom: 13.3%;" data-aos="zoom-in" data-aos-delay="200">
                            <div class="point-marker"></div>
                            <div class="point-label">
                                <span class="point-value">200</span>
                                <span class="point-date">2024.03</span>
                            </div>
                        </div>
                        
                        <!-- 2024年7月 - Spotify 100突破 -->
                        <div class="data-point milestone" style="left: 44%; bottom: 26.7%;" data-aos="zoom-in" data-aos-delay="300">
                            <div class="point-marker milestone-marker"></div>
                            <div class="point-label">
                                <span class="point-value">400</span>
                                <span class="point-date">2024.07</span>
                                <span class="milestone-badge">Spotify 100</span>
                            </div>
                        </div>
                        
                        <!-- 2024年9月 - ショート動画開始 -->
                        <div class="data-point" style="left: 52%; bottom: 40%;" data-aos="zoom-in" data-aos-delay="400">
                            <div class="point-marker"></div>
                            <div class="point-label">
                                <span class="point-value">600</span>
                                <span class="point-date">2024.09</span>
                            </div>
                        </div>
                        
                        <!-- 2024年11月 - 急成長 -->
                        <div class="data-point" style="left: 60%; bottom: 53.3%;" data-aos="zoom-in" data-aos-delay="500">
                            <div class="point-marker"></div>
                            <div class="point-label">
                                <span class="point-value">800</span>
                                <span class="point-date">2024.11</span>
                            </div>
                        </div>
                        
                        <!-- 2025年1月 - 1000人突破 -->
                        <div class="data-point milestone" style="left: 68%; bottom: 66.7%;" data-aos="zoom-in" data-aos-delay="600">
                            <div class="point-marker milestone-marker"></div>
                            <div class="point-label">
                                <span class="point-value">1000</span>
                                <span class="point-date">2025.01</span>
                                <span class="milestone-badge">1000突破</span>
                            </div>
                        </div>
                        
                        <!-- 2025年5月 - さらに成長 -->
                        <div class="data-point" style="left: 82%; bottom: 80%;" data-aos="zoom-in" data-aos-delay="700">
                            <div class="point-marker"></div>
                            <div class="point-label">
                                <span class="point-value">1200</span>
                                <span class="point-date">2025.05</span>
                            </div>
                        </div>
                        
                        <!-- 2025年10月 - 現在 -->
                        <div class="data-point current" style="left: 100%; bottom: 100%;" data-aos="zoom-in" data-aos-delay="800">
                            <div class="point-marker current-marker"></div>
                            <div class="point-label">
                                <span class="point-value">1500+</span>
                                <span class="point-date">2025.10</span>
                                <span class="milestone-badge current-badge">現在</span>
                            </div>
                        </div>
                        
                        <!-- 成長ライン（SVG風CSS） -->
                        <svg class="growth-line" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <polyline
                                points="0,100 14,93.3 30,86.7 44,73.3 52,60 60,46.7 68,33.3 82,20 100,0"
                                fill="none"
                                stroke="url(#gradient)"
                                stroke-width="3"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:#764ba2;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#f093fb;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- 凡例 -->
            <div class="chart-legend">
                <div class="legend-item">
                    <div class="legend-marker start-legend"></div>
                    <span class="legend-text">番組開始</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker milestone-legend"></div>
                    <span class="legend-text">マイルストーン</span>
                </div>
                <div class="legend-item">
                    <div class="legend-marker current-legend"></div>
                    <span class="legend-text">現在</span>
                </div>
            </div>
        </div>
    </section>

    <!-- プラットフォーム別フォロワー数 -->
    <section class="platform-stats-section">
        <div class="platform-container">
            <div class="section-header">
                <h2 class="section-title">Platform Statistics</h2>
                <p class="section-subtitle">プラットフォーム別フォロワー数</p>
            </div>
            
            <div class="platform-stats">
                <div class="platform-bars">
                    <div class="platform-bar" data-aos="fade-right">
                        <div class="platform-info">
                            <span class="platform-name">
                                <span class="platform-icon-small spotify">
                                    <?php
                                    $spotify_icon = get_theme_mod('spotify_icon');
                                    if ($spotify_icon) {
                                        echo '<img src="' . esc_url($spotify_icon) . '" alt="Spotify" class="platform-icon-image">';
                                    } else {
                                        echo 'S';
                                    }
                                    ?>
                                </span>
                                Spotify
                            </span>
                            <span class="platform-count">300+</span>
                        </div>
                        <div class="bar-container">
                            <div class="bar-fill spotify-bar" style="width: 60%"></div>
                        </div>
                    </div>
                    
                    <div class="platform-bar" data-aos="fade-right" data-aos-delay="100">
                        <div class="platform-info">
                            <span class="platform-name">
                                <span class="platform-icon-small youtube">
                                    <?php
                                    $youtube_icon = get_theme_mod('youtube_icon');
                                    if ($youtube_icon) {
                                        echo '<img src="' . esc_url($youtube_icon) . '" alt="YouTube" class="platform-icon-image">';
                                    } else {
                                        echo '▶';
                                    }
                                    ?>
                                </span>
                                YouTube
                            </span>
                            <span class="platform-count">1,000+</span>
                        </div>
                        <div class="bar-container">
                            <div class="bar-fill youtube-bar" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="platform-bar" data-aos="fade-right" data-aos-delay="200">
                        <div class="platform-info">
                            <span class="platform-name">
                                <span class="platform-icon-small apple">
                                    <?php
                                    $apple_icon = get_theme_mod('apple_podcasts_icon');
                                    if ($apple_icon) {
                                        echo '<img src="' . esc_url($apple_icon) . '" alt="Apple Podcasts" class="platform-icon-image">';
                                    } else {
                                        echo '🍎';
                                    }
                                    ?>
                                </span>
                                Apple Podcasts
                            </span>
                            <span class="platform-count">200+</span>
                        </div>
                        <div class="bar-container">
                            <div class="bar-fill apple-bar" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- コラボレーション＆イベント履歴 -->
    <section class="collab-events-section">
        <div class="collab-container">
            <div class="section-header">
                <h2 class="section-title">Collaborations & Events</h2>
                <p class="section-subtitle">コラボレーションとイベントの軌跡</p>
            </div>
            
            <div class="content-grid">
                <!-- コラボレーション -->
                <div class="content-box" data-aos="fade-up">
                    <div class="box-header">
                        <span class="box-icon">🎙️</span>
                        <h3 class="box-title">コラボレーション</h3>
                    </div>
                    <div class="collab-list">
                        <div class="collab-item">
                            <div class="collab-date">2024.04</div>
                            <div class="collab-content">
                                <h4 class="collab-title">平成男女のイドバタラジオ</h4>
                                <p class="collab-desc">ゲスト: みな さん</p>
                                <a href="https://open.spotify.com/episode/661RG21Jp2Rs7PFggQ4nXE" class="collab-link" target="_blank">エピソードを聴く →</a>
                            </div>
                        </div>
                        <div class="collab-item">
                            <div class="collab-date">2024.05</div>
                            <div class="collab-content">
                                <h4 class="collab-title">ひよっこ研究者のさばいばる日記</h4>
                                <p class="collab-desc">ゲスト: はち さん</p>
                                <a href="https://open.spotify.com/episode/5NX4d5OYHQ7bh0VlNT42wj" class="collab-link" target="_blank">エピソードを聴く →</a>
                            </div>
                        </div>
                        <div class="collab-item">
                            <div class="collab-date">2025.09</div>
                            <div class="collab-content">
                                <h4 class="collab-title">推し活2次元LIFEラジオ</h4>
                                <p class="collab-desc">コラボ配信</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- イベント参加 -->
                <div class="content-box" data-aos="fade-up" data-aos-delay="100">
                    <div class="box-header">
                        <span class="box-icon">🎉</span>
                        <h3 class="box-title">イベント参加</h3>
                    </div>
                    <div class="event-list">
                        <div class="event-item">
                            <div class="event-badge">2023</div>
                            <div class="event-content">
                                <h4 class="event-title">科学系ポッドキャストの日</h4>
                                <p class="event-desc">初参加・映画『私は確信する』回を配信</p>
                            </div>
                        </div>
                        <div class="event-item">
                            <div class="event-badge">2024</div>
                            <div class="event-content">
                                <h4 class="event-title">ポッドキャストシンポジウム</h4>
                                <p class="event-desc">ポッドキャストコミュニティに参加</p>
                            </div>
                        </div>
                        <div class="event-item">
                            <div class="event-badge">2024</div>
                            <div class="event-content">
                                <h4 class="event-title">ポッドキャストウィークエンド</h4>
                                <p class="event-desc">リアルイベントで交流</p>
                            </div>
                        </div>
                        <div class="event-item featured">
                            <div class="event-badge featured-badge">2025</div>
                            <div class="event-content">
                                <h4 class="event-title">ポッドキャストミキサー 名古屋</h4>
                                <p class="event-desc">登壇！コンテンツクイズ企画を実施</p>
                                <a href="https://content-freaks.jp/2025-2q-growth-podcast/" class="event-link" target="_blank">詳細記事を読む →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 年間アワード一覧 -->
    <section class="awards-section">
        <div class="awards-container">
            <div class="section-header">
                <h2 class="section-title">Annual Awards</h2>
                <p class="section-subtitle">歴代コンテンツフリークス大賞</p>
            </div>
            
            <div class="awards-grid">
                <!-- 2023年 -->
                <div class="award-year-card" data-aos="fade-up">
                    <div class="award-year-header">
                        <span class="award-year-badge">2023</span>
                        <h3 class="award-year-title">第1回 コンテンツフリークス大賞</h3>
                    </div>
                    <div class="award-items">
                        <div class="award-item grand-prize">
                            <div class="award-icon">🏆</div>
                            <div class="award-info">
                                <span class="award-category">コンテンツフリークス大賞</span>
                                <span class="award-winner">PLUTO</span>
                            </div>
                        </div>
                        <div class="award-item">
                            <div class="award-icon">🎖️</div>
                            <div class="award-info">
                                <span class="award-category">みっくん賞</span>
                                <span class="award-winner">私は確信する</span>
                            </div>
                        </div>
                        <div class="award-item">
                            <div class="award-icon">🎖️</div>
                            <div class="award-info">
                                <span class="award-category">あっきー賞</span>
                                <span class="award-winner">ゴジラ-1.0</span>
                            </div>
                        </div>
                    </div>
                    <a href="https://open.spotify.com/episode/3G1nDsYBljNCbUnA496aBp" class="award-episode-link" target="_blank">
                        エピソードを聴く →
                    </a>
                </div>
                
                <!-- 2024年 -->
                <div class="award-year-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="award-year-header">
                        <span class="award-year-badge">2024</span>
                        <h3 class="award-year-title">第2回 コンテンツフリークス大賞</h3>
                    </div>
                    <div class="award-items">
                        <div class="award-item grand-prize">
                            <div class="award-icon">🏆</div>
                            <div class="award-info">
                                <span class="award-category">コンテンツフリークス大賞</span>
                                <span class="award-winner">アンメット</span>
                            </div>
                        </div>
                        <div class="award-item">
                            <div class="award-icon">📺</div>
                            <div class="award-info">
                                <span class="award-category">ドラマ賞</span>
                                <span class="award-winner">海のはじまり</span>
                            </div>
                        </div>
                        <div class="award-item">
                            <div class="award-icon">⭐</div>
                            <div class="award-info">
                                <span class="award-category">ドラマキャスト大賞</span>
                                <span class="award-winner">杉咲花</span>
                            </div>
                        </div>
                        <div class="award-item">
                            <div class="award-icon">🎬</div>
                            <div class="award-info">
                                <span class="award-category">アニメ賞</span>
                                <span class="award-winner">葬送のフリーレン</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- リスナーの声 -->
    <section class="testimonials-section">
        <div class="testimonials-container">
            <div class="section-header">
                <h2 class="section-title">Listener's Voice</h2>
                <p class="section-subtitle">リスナーからの温かいメッセージ</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card" data-aos="fade-up">
                    <div class="quote-icon">💬</div>
                    <p class="testimonial-text">「コンテンツへの深い愛情と考察が素晴らしい！いつも楽しみにしています。」</p>
                    <div class="testimonial-author">- リスナーAさん</div>
                </div>
                
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="quote-icon">💬</div>
                    <p class="testimonial-text">「2人の掛け合いが最高！コンテンツを見る視点が変わりました。」</p>
                    <div class="testimonial-author">- リスナーBさん</div>
                </div>
                
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="quote-icon">💬</div>
                    <p class="testimonial-text">「ドラマ感想回が特に好き。見終わった後すぐに聴きたくなります！」</p>
                    <div class="testimonial-author">- リスナーCさん</div>
                </div>
            </div>
        </div>
    </section>

    <!-- コンテンツ分析 -->
    <section class="content-analysis-section">
        <div class="analysis-container">
            <div class="section-header">
                <h2 class="section-title">Content Analysis</h2>
                <p class="section-subtitle">ジャンル別エピソード分析</p>
            </div>
            
            <div class="analysis-grid">
                <div class="genre-bar-chart" data-aos="fade-up">
                    <div class="genre-item">
                        <div class="genre-label">📺 ドラマ</div>
                        <div class="genre-bar-container">
                            <div class="genre-bar drama-bar" style="width: 85%">
                                <span class="genre-count">45+</span>
                            </div>
                        </div>
                    </div>
                    <div class="genre-item">
                        <div class="genre-label">🎬 アニメ</div>
                        <div class="genre-bar-container">
                            <div class="genre-bar anime-bar" style="width: 70%">
                                <span class="genre-count">35+</span>
                            </div>
                        </div>
                    </div>
                    <div class="genre-item">
                        <div class="genre-label">🎥 映画</div>
                        <div class="genre-bar-container">
                            <div class="genre-bar movie-bar" style="width: 55%">
                                <span class="genre-count">25+</span>
                            </div>
                        </div>
                    </div>
                    <div class="genre-item">
                        <div class="genre-label">💬 雑談・分析</div>
                        <div class="genre-bar-container">
                            <div class="genre-bar talk-bar" style="width: 40%">
                                <span class="genre-count">20+</span>
                            </div>
                        </div>
                    </div>
                    <div class="genre-item">
                        <div class="genre-label">📚 その他</div>
                        <div class="genre-bar-container">
                            <div class="genre-bar other-bar" style="width: 25%">
                                <span class="genre-count">10+</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- メディア掲載 -->
    <section class="media-section">
        <div class="media-container">
            <div class="section-header">
                <h2 class="section-title">Media Coverage</h2>
                <p class="section-subtitle">メディア掲載・外部露出</p>
            </div>
            
            <div class="media-grid">
                <div class="media-card" data-aos="fade-up">
                    <div class="media-icon">📰</div>
                    <div class="media-content">
                        <h4 class="media-title">ポッドキャストランキング</h4>
                        <p class="media-date">2024年8月</p>
                        <p class="media-desc">「WEEKLY PICKUP!!」に選出</p>
                    </div>
                </div>
                
                <div class="media-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="media-icon">🌐</div>
                    <div class="media-content">
                        <h4 class="media-title">公式ウェブサイト開設</h4>
                        <p class="media-date">2024年6月</p>
                        <p class="media-desc">デジタルプレゼンスを強化</p>
                    </div>
                </div>
                
                <div class="media-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="media-icon">📝</div>
                    <div class="media-content">
                        <h4 class="media-title">ブログ記事公開</h4>
                        <p class="media-date">2024年7月〜</p>
                        <p class="media-desc">ポッドキャスト運営の知見を共有</p>
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

<?php get_footer(); ?>
