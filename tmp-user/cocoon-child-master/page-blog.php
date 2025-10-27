<?php
/**
 * Template Name: ブログページ
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- ブログヒーローセクション -->
    <section class="blog-hero">
        <div class="blog-hero-content">
            <div class="blog-hero-header">
                <h1 class="blog-hero-title">ブログ</h1>
                <p class="blog-hero-subtitle">コンテンツフリークスの最新記事とエピソード</p>
            </div>
        </div>
    </section>

    <!-- 新着記事セクション -->
    <section class="blog-featured-section">
        <div class="blog-featured-container">
            <div class="blog-section-header">
                <h2>New Post<span class="section-subtitle">新着記事</span></h2>
            </div>
            
            <div class="featured-posts-grid">
                <?php
                // 最新記事を3件取得
                $featured_posts_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($featured_posts_query->have_posts()) :
                    while ($featured_posts_query->have_posts()) : $featured_posts_query->the_post();
                        $is_podcast_episode = get_post_meta(get_the_ID(), 'is_podcast_episode', true);
                        $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                ?>
                    <article class="featured-post-card">
                        <div class="featured-post-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <div class="featured-post-placeholder" style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); width: 100%; height: 250px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--black);">
                                        <?php echo $is_podcast_episode ? '🎙️' : '📝'; ?>
                                    </div>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($is_podcast_episode && $episode_number) : ?>
                                <div class="episode-badge">EP.<?php echo esc_html($episode_number); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="featured-post-content">
                            <div class="featured-post-meta">
                                <span class="post-date"><?php echo get_the_date('Y年n月j日'); ?></span>
                                <?php if ($is_podcast_episode) : ?>
                                    <span class="post-type-badge podcast">ポッドキャスト</span>
                                <?php else : ?>
                                    <span class="post-type-badge blog">ブログ記事</span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="featured-post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="featured-post-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
                            </div>
                            
                            <div class="featured-post-footer">
                                <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                    <?php echo $is_podcast_episode ? '🎧 エピソードを聴く' : '📖 記事を読む'; ?>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                endif; 
                ?>
            </div>
        </div>
    </section>

    <!-- カテゴリー別記事セクション -->
    <section class="blog-category-section">
        <div class="blog-category-container">
            <div class="blog-section-header">
                <h2>Category<span class="section-subtitle">カテゴリーごとの記事</span></h2>
            </div>
            
            <!-- カテゴリーフィルター -->
            <div class="category-filter">
                <button class="filter-btn active" data-filter="all">すべて</button>
                <button class="filter-btn" data-filter="podcast">ポッドキャスト</button>
                <button class="filter-btn" data-filter="blog">ブログ記事</button>
                <button class="filter-btn" data-filter="review">レビュー</button>
                <button class="filter-btn" data-filter="news">ニュース</button>
            </div>
            
            <div class="category-posts-grid">
                <?php
                // カテゴリー別の記事を6件取得
                $category_posts_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 6,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($category_posts_query->have_posts()) :
                    while ($category_posts_query->have_posts()) : $category_posts_query->the_post();
                        $is_podcast_episode = get_post_meta(get_the_ID(), 'is_podcast_episode', true);
                        $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                        $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                        
                        // カテゴリーの判定ロジックを改善
                        $post_category = $is_podcast_episode ? 'podcast' : 'blog';
                        $categories = get_the_category();
                        if (!empty($categories)) {
                            foreach ($categories as $category) {
                                // アニメ、ドラマカテゴリーをレビューとして扱う
                                if (in_array($category->slug, ['anime', 'drama', 'review', 'アニメ', 'ドラマ'])) {
                                    $post_category = 'review';
                                    break;
                                } elseif (in_array($category->slug, ['news', 'ニュース'])) {
                                    $post_category = 'news';
                                    break;
                                }
                            }
                        }
                ?>
                    <article class="category-post-card" data-category="<?php echo esc_attr($post_category); ?>">
                        <div class="category-post-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <div class="category-post-placeholder" style="background: linear-gradient(135deg, #667eea, #764ba2); width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white;">
                                        <?php echo $is_podcast_episode ? '🎙️' : '📝'; ?>
                                    </div>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($is_podcast_episode && $episode_number) : ?>
                                <div class="episode-badge">EP.<?php echo esc_html($episode_number); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($duration) : ?>
                                <div class="duration-badge"><?php echo esc_html($duration); ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="category-post-content">
                            <div class="category-post-meta">
                                <span class="post-date"><?php echo get_the_date('Y年n月j日'); ?></span>
                                <span class="post-type-badge <?php echo esc_attr($post_category); ?>">
                                    <?php 
                                    switch($post_category) {
                                        case 'podcast': echo 'ポッドキャスト'; break;
                                        case 'review': echo 'レビュー'; break;
                                        case 'news': echo 'ニュース'; break;
                                        default: echo 'ブログ記事'; break;
                                    }
                                    ?>
                                </span>
                            </div>
                            
                            <h3 class="category-post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="category-post-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </div>
                        </div>
                    </article>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                endif; 
                ?>
            </div>
            
            <!-- 記事一覧へのリンク -->
            <div class="view-all-posts">
                <a href="<?php echo get_permalink(get_option('page_for_posts')) ?: home_url('/blog/'); ?>" class="view-all-btn">
                    📚 すべての記事を見る
                </a>
            </div>
        </div>
    </section>

    <!-- 人気記事セクション -->
    <section class="blog-popular-section">
        <div class="blog-popular-container">
            <div class="blog-section-header">
                <h2>Popular<span class="section-subtitle">人気記事</span></h2>
            </div>
            
            <div class="popular-posts-grid">
                <?php
                // 人気記事を取得（ここではコメント数やビュー数での並び替えを想定）
                $popular_posts_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 4,
                    'meta_query' => array(
                        'relation' => 'OR',
                        array(
                            'key' => 'is_podcast_episode',
                            'value' => '1',
                            'compare' => '='
                        ),
                        array(
                            'key' => 'is_podcast_episode',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                    'orderby' => 'comment_count',
                    'order' => 'DESC'
                ));
                
                if ($popular_posts_query->have_posts()) :
                    $counter = 1;
                    while ($popular_posts_query->have_posts()) : $popular_posts_query->the_post();
                        $is_podcast_episode = get_post_meta(get_the_ID(), 'is_podcast_episode', true);
                        $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                ?>
                    <article class="popular-post-card">
                        <div class="popular-post-rank"><?php echo $counter; ?></div>
                        
                        <div class="popular-post-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('thumbnail', array('alt' => get_the_title())); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <div class="popular-post-placeholder" style="background: linear-gradient(135deg, #f093fb, #f5576c); width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; border-radius: 10px;">
                                        <?php echo $is_podcast_episode ? '🎙️' : '📝'; ?>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="popular-post-content">
                            <div class="popular-post-meta">
                                <span class="post-date"><?php echo get_the_date('Y年n月j日'); ?></span>
                                <?php if ($is_podcast_episode && $episode_number) : ?>
                                    <span class="episode-number">EP.<?php echo esc_html($episode_number); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="popular-post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="popular-post-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                            </div>
                        </div>
                    </article>
                <?php 
                        $counter++;
                    endwhile;
                    wp_reset_postdata();
                else:
                ?>
                    <div class="no-posts-message">
                        <p>人気記事がまだありません。記事やエピソードをお楽しみください！</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ニュースレター登録セクション -->
    <section class="blog-newsletter-section">
        <div class="blog-newsletter-container">
            <div class="newsletter-card">
                <div class="newsletter-icon">📬</div>
                <h2 class="newsletter-title">新着記事をお見逃しなく</h2>
                <p class="newsletter-description">
                    最新のエピソードやブログ記事の更新通知を受け取りませんか？<br>
                    月数回、厳選されたコンテンツをお届けします。
                </p>
                
                <form class="newsletter-form" action="#" method="post">
                    <div class="newsletter-input-group">
                        <input type="email" class="newsletter-input" placeholder="メールアドレスを入力" required>
                        <button type="submit" class="newsletter-submit">登録する</button>
                    </div>
                </form>
                
                <p class="newsletter-note">
                    ※ 現在実装準備中です。RSS購読もご利用ください。
                </p>
            </div>
        </div>
    </section>
</main>

<!-- JavaScriptでカテゴリーフィルターを実装 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const postCards = document.querySelectorAll('.category-post-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // アクティブクラスの切り替え
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // 記事の表示・非表示
            postCards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php get_footer(); ?>
