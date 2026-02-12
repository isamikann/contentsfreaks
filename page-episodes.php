<?php
/**
 * Template Name: エピソード一覧
 * モダンなポッドキャストエピソード一覧ページ（統合版）
 * archive-episodes.phpの機能も含む
 */

get_header(); ?>



<main id="main" class="site-main contentfreaks-episodes-page">
    <!-- ヒーローセクション -->
    <section class="episodes-hero">
        <div class="episodes-hero-bg">
            <div class="hero-pattern"></div>
        </div>
        
        <div class="episodes-hero-particles">
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
        </div>
        
        <div class="episodes-hero-content">
            <div class="episodes-hero-icon">🎙️</div>
            <h1>Podcast Episodes</h1>
            <p class="episodes-hero-description">
                コンテンツフリークスの全エピソードを一覧でお楽しみください。
                最新のエピソードから過去の名作まで、すべてここに集約されています。
            </p>
            
            <div class="episodes-hero-stats">
                <div class="episodes-stat">
                    <span class="episodes-stat-number"><?php 
                        $total_episodes = new WP_Query(array(
                            'post_type' => 'post',
                            'posts_per_page' => -1,
                            'meta_key' => 'is_podcast_episode',
                            'meta_value' => '1',
                            'post_status' => 'publish'
                        ));
                        echo $total_episodes->found_posts ? $total_episodes->found_posts : '0';
                        wp_reset_postdata();
                    ?></span>
                    <span class="episodes-stat-label">エピソード</span>
                </div>
                <div class="episodes-stat">
                    <span class="episodes-stat-number">🔥</span>
                    <span class="episodes-stat-label">熱い語り</span>
                </div>
                <div class="episodes-stat">
                    <span class="episodes-stat-number">🔍</span>
                    <span class="episodes-stat-label">深掘り分析</span>
                </div>
            </div>
        </div>
    </section>

    <!-- エピソードコンテンツ -->
    <section class="episodes-content-section">
        <div class="episodes-container">
            <div class="search-controls">
                <div class="search-box">
                    <input type="text" id="episode-search" class="search-input" placeholder="エピソードを検索..." />
                    <button type="button" class="search-button">🔍</button>
                </div>
            </div>
            
            <div class="episodes-grid" id="episodes-grid">
            <?php
            // ポッドキャスト投稿を取得（カスタムフィールドでフィルタ）
            $episodes_query = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 18,
                'meta_key' => 'is_podcast_episode',
                'meta_value' => '1',
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            if ($episodes_query->have_posts()) :
                while ($episodes_query->have_posts()) : $episodes_query->the_post();
                    // カスタムフィールドを取得
                    $audio_url_raw = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                    
                    // 音声URLの修正処理
                    $audio_url = $audio_url_raw;
                    if ($audio_url_raw) {
                        // 二重エンコーディングの修正
                        if (strpos($audio_url_raw, 'https%3A%2F%2F') !== false) {
                            // パターン1: cloudfront.net/ID/https%3A%2F%2Fcloudfront.net/path
                            if (preg_match('/https:\/\/d3ctxlq1ktw2nl\.cloudfront\.net\/\d+\/https%3A%2F%2Fd3ctxlq1ktw2nl\.cloudfront\.net%2F(.+)/', $audio_url_raw, $matches)) {
                                $correct_path = urldecode($matches[1]);
                                $audio_url = 'https://d3ctxlq1ktw2nl.cloudfront.net/' . $correct_path;
                            }
                        }
                    }
                    
                    $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                    $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                    $original_url = get_post_meta(get_the_ID(), 'episode_original_url', true);
                    $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
            ?>
                <?php get_template_part('template-parts/episode-card'); ?>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="no-episodes">
                    <div class="no-episodes-icon">🎙️</div>
                    <h3>エピソードが見つかりません</h3>
                    <p>まだエピソードが投稿されていないか、検索条件に一致するエピソードがありません。</p>
                    <a href="<?php echo admin_url('tools.php?page=contentfreaks-sync'); ?>" class="sync-episodes-btn">
                        RSSからエピソードを同期
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- 無限スクロール用のローディングインジケーター -->
        <?php if ($episodes_query->found_posts > 18) : ?>
        <div class="infinite-scroll-indicator" id="loading-indicator" style="display: none;">
            <div class="loading-spinner">
                <div class="spinner-ring"></div>
                <p>エピソードを読み込んでいます...</p>
            </div>
        </div>
        <div class="infinite-scroll-trigger" id="scroll-trigger" data-offset="18" data-limit="12"></div>
        <?php endif; ?>
        </div>
    </section>
</main>

<script>
// エピソードページでのjavascript.js音声機能の無効化（DOMContentLoaded前に実行）
(function() {
    console.log('Pre-disabling javascript.js audio functions');
    
    // initAudioPlayer関数を無効化
    window.initAudioPlayer = function() {
        console.log('initAudioPlayer disabled on episodes page');
        return;
    };
    
    // initPodcastPlayer関数も無効化
    window.initPodcastPlayer = function() {
        console.log('initPodcastPlayer disabled on episodes page');
        return;
    };
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 初期カードにloadedクラスを追加
    const initialCards = document.querySelectorAll('.modern-episode-card');
    initialCards.forEach(card => {
        card.addEventListener('animationend', () => {
            card.classList.add('loaded');
        });
    });
    
    // エピソード検索機能
    const searchInput = document.getElementById('episode-search');
    const episodeCards = document.querySelectorAll('.modern-episode-card, .episode-card');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            episodeCards.forEach(card => {
                const titleElement = card.querySelector('.episode-title');
                
                // 要素が存在するかチェック
                const title = titleElement ? titleElement.textContent.toLowerCase() : '';
                
                if (searchTerm === '' || title.includes(searchTerm)) {
                    // 表示
                    card.style.display = '';
                    card.style.opacity = '';
                    card.style.transform = '';
                    card.style.visibility = '';
                } else {
                    // 非表示
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // スクロールアニメーション
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
            }
        });
    }, observerOptions);
    
    // 初期状態のカードを観察
    episodeCards.forEach(card => {
        observer.observe(card);
    });
    
    // 無限スクロール機能
    const scrollTrigger = document.getElementById('scroll-trigger');
    const loadingIndicator = document.getElementById('loading-indicator');
    let isLoading = false;
    let hasMoreContent = true;
    
    if (scrollTrigger) {
        const scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoading && hasMoreContent) {
                    loadMoreEpisodes();
                }
            });
        }, {
            rootMargin: '200px'
        });
        
        scrollObserver.observe(scrollTrigger);
    }
    
    function loadMoreEpisodes() {
        if (isLoading || !hasMoreContent) return;
        
        isLoading = true;
        const offset = parseInt(scrollTrigger.dataset.offset);
        const limit = parseInt(scrollTrigger.dataset.limit);
        
        // ローディングインジケーターを表示
        loadingIndicator.style.display = 'block';
        setTimeout(() => {
            loadingIndicator.classList.add('visible');
        }, 10);
        
        // AJAXリクエストでエピソードを取得
        fetch(`${window.location.origin}/wp-admin/admin-ajax.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=load_more_episodes&offset=${offset}&limit=${limit}&nonce=${contentfreaks_ajax.nonce}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.html) {
                // 新しいエピソードを追加
                const episodesGrid = document.getElementById('episodes-grid');
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.data.html;
                
                // 各カードを個別に追加してアニメーション
                const newCards = tempDiv.querySelectorAll('.modern-episode-card');
                newCards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(30px)';
                    episodesGrid.appendChild(card);
                    
                    setTimeout(() => {
                        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
                
                // オフセットを更新
                scrollTrigger.dataset.offset = offset + limit;
                
                // コンテンツがなくなったかチェック
                if (data.data.has_more === false) {
                    hasMoreContent = false;
                    scrollTrigger.style.display = 'none';
                }
            } else {
                hasMoreContent = false;
                scrollTrigger.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('エピソードの読み込みエラー:', error);
            hasMoreContent = false;
        })
        .finally(() => {
            isLoading = false;
            loadingIndicator.classList.remove('visible');
            setTimeout(() => {
                loadingIndicator.style.display = 'none';
            }, 300);
        });
    }
    
    // パフォーマンス最適化：スクロール時の処理
    let ticking = false;
    
    function updateScrollEffects() {
        const scrollY = window.scrollY;
        const heroSection = document.querySelector('.episodes-hero');
        
        if (heroSection) {
            const heroHeight = heroSection.offsetHeight;
            const scrollPercent = Math.min(scrollY / heroHeight, 1);
            
            // パララックス効果
            heroSection.style.transform = `translateY(${scrollPercent * 50}px)`;
            heroSection.style.opacity = 1 - scrollPercent * 0.3;
        }
        
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateScrollEffects);
            ticking = true;
        }
    });
});
</script>

<?php get_footer(); ?>
