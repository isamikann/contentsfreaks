<?php
/**
 * Template Name: ブログ記事一覧
 * 手動投稿のブログ記事一覧を表示
 */

get_header(); ?>


<main id="main" class="site-main contentfreaks-episodes-page">
    <div class="content-area">
        <!-- ヒーローセクション -->
        <section class="blog-hero">
            <div class="blog-hero-bg">
                <div class="hero-pattern"></div>
            </div>
            
            <div class="blog-hero-content">
                <div class="blog-hero-icon">📖</div>
                <h1>Blog Articles</h1>
                <p class="blog-hero-description">
                    コンテンツフリークスの手動投稿ブログ記事。ポッドキャスト分析、レビュー、コラムなど、じっくり読める記事をお届けします。
                </p>
                
                <div class="blog-stats">
                    <div class="blog-stat">
                        <span class="blog-stat-number">
                            <?php echo contentfreaks_get_blog_count(); ?>
                        </span>
                        <span class="blog-stat-label">記事</span>
                    </div>
                    <div class="blog-stat">
                        <span class="blog-stat-number">✍️</span>
                        <span class="blog-stat-label">執筆記事</span>
                    </div>
                    <div class="blog-stat">
                        <span class="blog-stat-number">💡</span>
                        <span class="blog-stat-label">分析</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="main-content">

            <div class="blog-filters">
                <button class="blog-filter-btn active" data-filter="all">すべて</button>
                <button class="blog-filter-btn" data-filter="レビュー">レビュー</button>
                <button class="blog-filter-btn" data-filter="コラム">コラム</button>
                <button class="blog-filter-btn" data-filter="分析">分析</button>
            </div>

            <div class="blog-grid" id="blog-grid">
                <?php
                // ブログ投稿を取得（ポッドキャストエピソード以外）
                $blog_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 12,
                    'meta_query' => array(
                        array(
                            'key' => 'is_podcast_episode',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));

                if ($blog_query->have_posts()) :
                    while ($blog_query->have_posts()) : $blog_query->the_post();
                        // カテゴリーとタグを取得
                        $categories = get_the_category();
                        $tags = get_the_tags();
                        $main_category = !empty($categories) ? $categories[0]->name : 'その他';
                        $read_time = get_post_meta(get_the_ID(), 'estimated_read_time', true) ?: '3分';
                        $author_display = get_the_author_meta('display_name');
                ?>
                    <article class="blog-card" data-category="<?php echo esc_attr($main_category); ?>">
                        <div class="blog-thumbnail">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium', array('alt' => get_the_title(), 'loading' => 'lazy')); ?>
                            <?php else : ?>
                                <div class="blog-placeholder">📖</div>
                            <?php endif; ?>
                            
                            <div class="blog-category-badge"><?php echo esc_html($main_category); ?></div>
                            <div class="blog-date-badge"><?php echo get_the_date('n/j'); ?></div>
                            
                            <div class="blog-featured-overlay">📄</div>
                        </div>
                        
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="blog-author">by <?php echo esc_html($author_display); ?></span>
                                <span class="blog-read-time">読了 <?php echo esc_html($read_time); ?></span>
                            </div>
                            
                            <h3 class="blog-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="blog-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
                            </div>
                            
                            <div class="blog-actions">
                                <a href="<?php the_permalink(); ?>" class="blog-read-more">続きを読む</a>
                                <div class="blog-tags">
                                    <?php if ($tags) : ?>
                                        <?php foreach (array_slice($tags, 0, 3) as $tag) : ?>
                                            <a href="<?php echo get_tag_link($tag->term_id); ?>" class="blog-tag">#<?php echo esc_html($tag->name); ?></a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                    <div class="no-blog-posts">
                        <p>ブログ記事が見つかりませんでした。</p>
                        <p>新しい記事を投稿してください。</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($blog_query->found_posts > 12) : ?>
            <div class="load-more-container">
                <button id="load-more-blog" class="load-more-btn" data-offset="12" data-limit="12">
                    さらに読み込む
                </button>
            </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // フィルター機能
    const filterButtons = document.querySelectorAll('.blog-filter-btn');
    const blogCards = document.querySelectorAll('.blog-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // アクティブボタンの切り替え
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filterValue = this.dataset.filter;
            
            // カードのフィルタリング
            blogCards.forEach(card => {
                if (filterValue === 'all' || card.dataset.category === filterValue) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.6s ease-out forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // ブログカードのクリック処理
    blogCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // リンクが直接クリックされた場合は何もしない
            if (e.target.tagName.toLowerCase() === 'a') {
                return;
            }
            
            // カード内のリンクを探して遷移
            const link = this.querySelector('.blog-title a');
            if (link) {
                window.location.href = link.href;
            }
        });
        
        // カードにfocusableな属性を追加（アクセシビリティ向上）
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        card.setAttribute('aria-label', 'ブログ記事を読む');
        
        // キーボード操作対応
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const link = this.querySelector('.blog-title a');
                if (link) {
                    window.location.href = link.href;
                }
            }
        });
    });
    
    // ロードモア機能（AJAX対応版）
    const loadMoreBtn = document.getElementById('load-more-blog');
    if (loadMoreBtn && typeof contentfreaks_ajax !== 'undefined') {
        loadMoreBtn.addEventListener('click', function() {
            const offset = parseInt(this.dataset.offset);
            const limit = parseInt(this.dataset.limit);
            
            this.disabled = true;
            this.textContent = '読み込み中...';
            
            const formData = new URLSearchParams();
            formData.append('action', 'load_more_blog');
            formData.append('nonce', contentfreaks_ajax.nonce);
            formData.append('offset', offset);
            formData.append('limit', limit);
            
            fetch(contentfreaks_ajax.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.html) {
                        const blogGrid = document.getElementById('blog-grid');
                        blogGrid.insertAdjacentHTML('beforeend', data.data.html);
                        
                        this.dataset.offset = offset + limit;
                        this.disabled = false;
                        this.textContent = 'さらに読み込む';
                        
                        if (!data.data.has_more) {
                            this.style.display = 'none';
                        }
                        
                        // 新しいカードにクリック処理を適用
                        const newCards = blogGrid.querySelectorAll('.blog-card:not([data-initialized])');
                        newCards.forEach(card => {
                            card.dataset.initialized = 'true';
                            card.addEventListener('click', function(e) {
                                if (e.target.tagName.toLowerCase() === 'a') return;
                                const link = this.querySelector('.blog-title a');
                                if (link) window.location.href = link.href;
                            });
                        });
                    } else {
                        this.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('ブログ記事の読み込みに失敗しました:', error);
                    this.disabled = false;
                    this.textContent = 'さらに読み込む';
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
    blogCards.forEach(card => {
        observer.observe(card);
    });
});
</script>

<?php get_footer(); ?>
