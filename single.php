<?php
/**
 * 個別投稿ページ（エピソード詳細ページ）
 */

get_header(); ?>

<div class="single-episode-container site-main">
    <?php while (have_posts()) : the_post(); ?>
        
        <?php 
        // ポッドキャストエピソードかどうかをチェック
        $post_id = get_the_ID();
        $is_podcast_episode = get_post_meta($post_id, 'is_podcast_episode', true);
        $episode_number = get_post_meta($post_id, 'episode_number', true);
        $duration = get_post_meta($post_id, 'episode_duration', true);
        $original_url = get_post_meta($post_id, 'episode_original_url', true);
        $episode_category = get_post_meta($post_id, 'episode_category', true) ?: 'エピソード';
        ?>

        <article class="single-episode">
            <!-- エピソードヘッダー -->
            <header class="episode-header">
                <div class="episode-header-content">
                    <div class="episode-featured-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', array('alt' => get_the_title())); ?>
                        <?php else : ?>
                            <div class="default-episode-image">
                                <div style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; border-radius: 15px;">🎙️</div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($episode_number) : ?>
                            <div class="episode-number-large">EP.<?php echo esc_html($episode_number); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="episode-info">
                        <div class="episode-meta">
                            <span class="episode-date"><?php echo get_the_date('Y年n月j日'); ?></span>
                            <?php if ($duration) : ?>
                                <span class="episode-duration">⏱️ <?php echo esc_html($duration); ?></span>
                            <?php endif; ?>
                            
                            <?php 
                            // タグを取得・表示（メタ情報のspanと統一）
                            $tags = get_the_tags();
                            if ($tags && !is_wp_error($tags)) : ?>
                                <span class="episode-tags">
                                    <?php foreach ($tags as $tag) : ?>
                                        <a href="<?php echo get_tag_link($tag->term_id); ?>" class="episode-tag">
                                            🏷️ <?php echo esc_html($tag->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="episode-title"><?php the_title(); ?></h1>
                    </div>
                </div>
                
                <!-- ポッドキャストプラットフォームリンク -->
                <?php if ($is_podcast_episode) : ?>
                <div class="episode-platform-links">
                    <h3 class="platform-links-title">🎧 お好みのアプリで聴く</h3>
                    <?php echo do_shortcode('[podcast_platforms]'); ?>
                </div>
                <?php endif; ?>
            </header>

            <!-- エピソード本文 -->
            <div class="episode-content">
                <div class="episode-content-wrapper">
                    <div class="content-text">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <!-- 関連エピソード -->
            <?php if ($is_podcast_episode) : ?>
            <div class="related-episodes">
                <h3 class="related-episodes-title">🎵 関連エピソード</h3>
                <div class="related-episodes-grid">
                    <?php
                    // 関連エピソードを取得（同じカテゴリーから3件）
                    $related_query = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'meta_key' => 'is_podcast_episode',
                        'meta_value' => '1',
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));

                    if ($related_query->have_posts()) :
                        while ($related_query->have_posts()) : $related_query->the_post();
                            $related_episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                            $related_duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                    ?>
                        <article class="related-episode-card">
                            <div class="related-episode-thumbnail">
                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                                    </a>
                                <?php else : ?>
                                    <a href="<?php the_permalink(); ?>">
                                        <div style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); width: 100%; height: 150px; display: flex; align-items: center; justify-content: center; font-size: 2rem; border-radius: 10px;">🎙️</div>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($related_episode_number) : ?>
                                <div class="episode-number-small">EP.<?php echo esc_html($related_episode_number); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="related-episode-info">
                                <div class="related-episode-date"><?php echo get_the_date('Y年n月j日'); ?></div>
                                <h4 class="related-episode-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <?php if ($related_duration) : ?>
                                <div class="related-episode-duration">⏱️ <?php echo esc_html($related_duration); ?></div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- ナビゲーション -->
            <div class="episode-navigation">
                <div class="episode-nav-links">
                    <div class="nav-previous">
                        <?php 
                        $prev_post = get_previous_post();
                        if ($prev_post) : ?>
                            <a href="<?php echo get_permalink($prev_post->ID); ?>" class="episode-nav-link prev">
                                <span class="nav-label">← 前のエピソード</span>
                                <span class="nav-title"><?php echo esc_html($prev_post->post_title); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="nav-center">
                        <a href="/episodes/" class="episode-nav-link episodes-list">
                            🎧 エピソード一覧
                        </a>
                    </div>
                    
                    <div class="nav-next">
                        <?php 
                        $next_post = get_next_post();
                        if ($next_post) : ?>
                            <a href="<?php echo get_permalink($next_post->ID); ?>" class="episode-nav-link next">
                                <span class="nav-label">次のエピソード →</span>
                                <span class="nav-title"><?php echo esc_html($next_post->post_title); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </article>

    <?php endwhile; ?>

    <!-- コメント欄 -->
    <?php if (comments_open() || get_comments_number()) : ?>
        <div class="episode-comments">
            <h3 class="comments-title">💬 コメント</h3>
            <?php comments_template(); ?>
        </div>
    <?php endif; ?>
</div>

<style>
/* ページ全体の上部マージン調整（モダンヘッダー対応） */
body {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

.single-episode-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
    margin-top: 100px; /* ヘッダー分の余白を追加 */
}

.episode-header {
    margin-bottom: 3rem;
}

.episode-header-content {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
    align-items: start;
}

.episode-featured-image {
    position: relative;
    max-width: 300px;
}

.episode-featured-image img {
    width: 100%;
    height: auto;
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    aspect-ratio: 1;
    object-fit: cover;
}

.default-episode-image {
    width: 100%;
    height: 300px;
    border-radius: 15px;
    overflow: hidden;
    aspect-ratio: 1;
}

.episode-number-large {
    position: absolute;
    bottom: 15px;
    left: 15px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 1.1rem;
}

.episode-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: var(--meta-primary);
    color: #666;
}

.episode-title {
    font-size: var(--single-title);
    margin-bottom: 1.5rem;
    line-height: 1.3;
    color: #333;
    font-weight: 700;
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
    white-space: normal;
    /* 全文字表示を確実にする */
    text-overflow: initial;
    overflow: visible;
    display: block;
    width: 100%;
    max-width: none;
    /* 文字切り詰めを無効化 */
    -webkit-line-clamp: unset;
    -webkit-box-orient: unset;
}


/* エピソード本文 */
.episode-content {
    margin: 3rem 0;
}

.episode-content-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.content-text {
    padding: 3rem;
    line-height: 1.8;
    font-size: var(--body-text);
    color: #333;
    max-width: none;
    /* モダンな読書体験のための改善 */
    word-spacing: 0.1em;
    letter-spacing: 0.02em;
    /* フォーカス時の読みやすさ向上 */
    tab-size: 4;
}

.content-text *:focus {
    outline: 2px solid #ff6b35;
    outline-offset: 2px;
    border-radius: 4px;
}

/* 本文内の要素スタイル */
.content-text h1,
.content-text h2,
.content-text h3,
.content-text h4,
.content-text h5,
.content-text h6 {
    margin: 2.5rem 0 1.5rem 0;
    line-height: 1.4;
    color: #2c3e50;
    font-weight: 700;
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: normal;
    /* モダンな見出しスタイル */
    scroll-margin-top: 100px; /* スムーズスクロール時のオフセット */
    position: relative;
}

.content-text h2:before {
    content: '';
    position: absolute;
    left: -1rem;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 60%;
    background: linear-gradient(135deg, #f7ff0b, #ff6b35);
    border-radius: 2px;
    opacity: 0.8;
}

.content-text h2 {
    font-size: var(--single-title);
    padding-bottom: 0.8rem;
    border-bottom: 3px solid #f7ff0b;
    margin-bottom: 2rem;
}

.content-text h3 {
    font-size: var(--card-title-large);
    position: relative;
    padding-left: 1.2rem;
}

.content-text h3:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.3rem;
    width: 4px;
    height: 1.2rem;
    background: linear-gradient(135deg, #f7ff0b, #ff6b35);
    border-radius: 2px;
}

.content-text h4 {
    font-size: var(--card-title);
    color: #34495e;
}

.content-text p {
    margin-bottom: 1.5rem;
    text-align: justify;
}

.content-text ul,
.content-text ol {
    margin: 1.5rem 0;
    padding-left: 2rem;
}

.content-text li {
    margin-bottom: 0.8rem;
    line-height: 1.7;
}

.content-text blockquote {
    margin: 2rem 0;
    padding: 1.5rem 2rem;
    border-left: 4px solid #f7ff0b;
    background: #f8f9fa;
    border-radius: 0 8px 8px 0;
    font-style: italic;
    position: relative;
}

.content-text blockquote:before {
    content: '"';
    font-size: 4rem;
    color: #f7ff0b;
    position: absolute;
    top: -0.5rem;
    left: 1rem;
    font-family: serif;
    opacity: 0.3;
}

.content-text code {
    background: #f1f3f4;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 0.9em;
    color: #e91e63;
}

.content-text pre {
    background: #2d3748;
    color: #e2e8f0;
    padding: 1.5rem;
    border-radius: 8px;
    overflow-x: auto;
    margin: 2rem 0;
    line-height: 1.5;
}

.content-text pre code {
    background: none;
    color: inherit;
    padding: 0;
}

/* 画像とメディア要素の改善 */
.content-text img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin: 1.5rem 0;
    display: block;
    transition: transform 0.3s ease;
}

.content-text img:hover {
    transform: scale(1.02);
}

.content-text figure {
    margin: 2rem 0;
    text-align: center;
}

.content-text figcaption {
    margin-top: 0.5rem;
    font-size: 0.9rem;
    color: #666;
    font-style: italic;
    text-align: center;
}

/* 動画の埋め込み対応 */
.content-text iframe,
.content-text video {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1.5rem 0;
}

/* 関連エピソード */
.related-episodes {
    margin: 3rem 0;
}

.related-episodes-title {
    text-align: center;
    margin-bottom: 2rem;
    font-size: var(--page-title);
    color: #333;
}

.related-episodes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.related-episode-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.related-episode-card:hover {
    transform: translateY(-5px);
}

.related-episode-thumbnail {
    position: relative;
    height: 150px;
    overflow: hidden;
}

.related-episode-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.episode-number-small {
    position: absolute;
    bottom: 8px;
    left: 8px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: var(--meta-secondary);
    font-weight: bold;
}

.related-episode-info {
    padding: 1rem;
}

.related-episode-date {
    font-size: var(--meta-secondary);
    color: #666;
    margin-bottom: 0.5rem;
}

.related-episode-title {
    margin: 0.5rem 0;
    font-size: var(--body-text);
    line-height: 1.5;
    word-wrap: break-word;
    overflow-wrap: break-word;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: visible;
    height: auto;
}

.related-episode-title a {
    color: #333;
    text-decoration: none;
}

.related-episode-title a:hover {
    color: var(--accent-color, #f7ff0b);
}

.related-episode-duration {
    font-size: var(--meta-secondary);
    color: #666;
}

/* ナビゲーション */
.episode-navigation {
    margin: 3rem 0;
    padding: 2rem 0;
    border-top: 1px solid #e9ecef;
}

.episode-nav-links {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 2rem;
    align-items: center;
}

.episode-nav-link {
    display: block;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 10px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
}

.episode-nav-link:hover {
    background: #f7ff0b;
    text-decoration: none;
    color: #333;
    transform: translateY(-2px);
}

.episode-nav-link.prev { text-align: left; }
.episode-nav-link.next { text-align: right; }
.episode-nav-link.episodes-list { text-align: center; font-weight: bold; }

.nav-label {
    display: block;
    font-size: 0.9rem;
    font-weight: bold;
    margin-bottom: 0.3rem;
}

.nav-title {
    display: block;
    font-size: 0.9rem;
    opacity: 0.8;
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.4;
    white-space: normal;
}

/* コメント */
.episode-comments {
    margin: 3rem 0;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 15px;
}

.comments-title {
    text-align: center;
    margin-bottom: 2rem;
    color: #333;
}

/* テーブルの改善 */
.content-text table {
    width: 100%;
    border-collapse: collapse;
    margin: 2rem 0;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.content-text th {
    background: linear-gradient(135deg, #f7ff0b, #ff6b35);
    color: white;
    font-weight: 600;
    text-align: left;
    padding: 1rem;
}

.content-text td {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.content-text tr:last-child td {
    border-bottom: none;
}

.content-text tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* リストの改善 */
.content-text ul {
    list-style: none;
    padding-left: 0;
}

.content-text ul li:before {
    content: '▶';
    color: #ff6b35;
    font-weight: bold;
    margin-right: 0.8rem;
    font-size: 0.8rem;
}

.content-text ol {
    counter-reset: custom-counter;
    padding-left: 0;
    list-style: none; /* デフォルトの番号を無効化 */
}

.content-text ol li {
    counter-increment: custom-counter;
    position: relative;
    padding-left: 2.5rem;
    list-style: none; /* リストアイテムのデフォルト番号を無効化 */
}

.content-text ol li:before {
    content: counter(custom-counter);
    position: absolute;
    left: 0;
    top: 0;
    background: linear-gradient(135deg, #f7ff0b, #ff6b35);
    color: white;
    width: 1.8rem;
    height: 1.8rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
}

/* スムーズスクロールとアクセシビリティの改善 */
html {
    scroll-behavior: smooth;
}

/* プラットフォームリンク - エピソード個別ページ用（ベーススタイル） */
.episode-platform-links {
    margin: 2rem 0;
    padding: 1.5rem;
    background: #fafafa;
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.episode-platform-links .platform-links-title {
    font-size: 1.1rem;
    margin-bottom: 1.2rem;
    color: #495057;
    text-align: center;
}

.episode-platform-links .platforms-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    max-width: 600px;
    margin: 0 auto;
}

.episode-platform-links .platform-link {
    background: #fff;
    border: 2px solid #e5e5e5;
    border-radius: 12px;
    padding: 1rem 0.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    overflow: hidden;
    min-height: 100px;
    justify-content: center;
}

.episode-platform-links .platform-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    border-color: #f7ff0b;
    text-decoration: none;
}

.episode-platform-links .platform-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin: 0 auto 0.5rem auto;
    border-radius: 50%;
    background: #f8f9fa;
    transition: all 0.3s ease;
    overflow: hidden;
}

.episode-platform-links .platform-icon img {
    width: 36px;
    height: 36px;
    object-fit: contain;
    border-radius: 50%;
}

.episode-platform-links .platform-link:hover .platform-icon {
    background: #f7ff0b;
    transform: scale(1.1);
}

.episode-platform-links .platform-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 0.25rem;
}

.episode-platform-links .platform-action {
    font-size: 0.75rem;
    color: #666;
    font-weight: 500;
}

.episode-platform-links .platform-link:hover .platform-name,
.episode-platform-links .platform-link:hover .platform-action {
    color: #333;
}

/* プラットフォーム別の色 */
.episode-platform-links .platform-spotify:hover .platform-icon {
    background: #1DB954;
    color: #fff;
}

.episode-platform-links .platform-apple:hover .platform-icon {
    background: #A855F7;
    color: #fff;
}

.episode-platform-links .platform-youtube:hover .platform-icon {
    background: #FF0000;
    color: #fff;
}

/* レスポンシブ - タブレット対応 */
@media (max-width: 1024px) and (min-width: 769px) {
    .single-episode-container {
        padding: 2rem 1.5rem;
    }
    
    .episode-header-content {
        grid-template-columns: 250px 1fr;
        gap: 1.5rem;
    }
    
    .episode-featured-image {
        max-width: 250px;
    }
    
    .episode-featured-image img {
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }
    
    .episode-title {
        font-size: 1.8rem;
    }
    
    .content-text {
        padding: 2.5rem 2rem;
        font-size: 1.05rem;
        line-height: 1.75;
    }
}

/* レスポンシブ - モバイル（中） */
@media (max-width: 768px) {
    body {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }
    
    .single-episode-container {
        padding: 1rem;
        max-width: 100%;
    }
    
    .episode-header {
        padding: 1.5rem 0;
        margin-bottom: 1rem;
    }
    
    .episode-header-content {
        grid-template-columns: 1fr;
        gap: 2rem;
        text-align: center;
    }
    
    .episode-featured-image {
        max-width: 240px;
        margin: 0 auto;
    }
    
    .episode-featured-image img {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        aspect-ratio: 1;
        object-fit: cover;
    }
    
    .episode-info {
        padding: 0 1rem;
    }
    
    .episode-meta {
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .episode-meta span {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        background: rgba(247, 255, 11, 0.1);
        border-radius: 20px;
        border: 1px solid rgba(247, 255, 11, 0.3);
    }
    
    .episode-title {
        font-size: 1.75rem;
        line-height: 1.4;
        margin-bottom: 1.5rem;
        font-weight: 700;
        text-align: center;
        color: #2c3e50;
        /* シンプルで読みやすく */
        text-overflow: initial;
        overflow: visible;
        display: block;
        white-space: normal;
        -webkit-line-clamp: unset;
        -webkit-box-orient: unset;
    }
    
    .episode-content {
        margin: 2rem 0 1rem 0;
    }
    
    .episode-content-wrapper {
        border-radius: 12px;
        margin: 0;
        background: white;
        border: 1px solid #e9ecef;
    }
    
    .content-text {
        padding: 2rem 1.5rem;
        font-size: 1.05rem;
        line-height: 1.8;
        color: #2c3e50;
        /* シンプルで読みやすいスタイル */
        word-spacing: 0.02em;
        letter-spacing: 0.01em;
    }
    
    .content-text h2 {
        font-size: 1.4rem;
        margin: 2.5rem 0 1.2rem 0;
        line-height: 1.4;
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #f7ff0b;
        padding-bottom: 0.5rem;
    }
    
    .content-text h3 {
        font-size: 1.25rem;
        margin: 2rem 0 1rem 0;
        line-height: 1.4;
        color: #34495e;
        font-weight: 600;
    }
    
    .content-text h4 {
        font-size: 1.1rem;
        margin: 1.8rem 0 0.8rem 0;
        line-height: 1.4;
        color: #34495e;
        font-weight: 600;
    }
    
    .content-text p {
        margin-bottom: 1.5rem;
        text-align: left;
    }
    
    .content-text ul,
    .content-text ol {
        margin: 1.5rem 0;
        padding-left: 1.5rem;
    }
    
    .content-text li {
        margin-bottom: 0.75rem;
        line-height: 1.7;
    }
    
    .content-text blockquote {
        padding: 1.5rem;
        margin: 2rem 0;
        border-radius: 8px;
        font-size: 1rem;
        line-height: 1.7;
        background: #f8f9fa;
        border-left: 4px solid #f7ff0b;
        font-style: italic;
    }
    
    .content-text pre {
        padding: 1.2rem;
        font-size: 0.9rem;
        border-radius: 8px;
        overflow-x: auto;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
    }
    
    .content-text table {
        font-size: 0.9rem;
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
    }
    
    .content-text th,
    .content-text td {
        padding: 0.75rem 0.5rem;
        border: 1px solid #dee2e6;
        text-align: left;
    }
    
    .content-text th {
        background: #f8f9fa;
        font-weight: 600;
    }
    
    /* 画像の最適化 */
    .content-text img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1.5rem 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .episode-nav-links {
        grid-template-columns: 1fr;
        gap: 1rem;
        margin: 2rem 0;
    }
    
    .related-episodes {
        margin: 2rem 0;
        padding: 1.5rem;
        background: #fafafa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }
    
    .related-episodes-title {
        font-size: 1.3rem;
        margin-bottom: 1.5rem;
        text-align: center;
        color: #2c3e50;
    }
    
    .related-episodes-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
    }
    
    .related-episode-card {
        background: white;
        border-radius: 10px;
        border: 1px solid #dee2e6;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .related-episode-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
}

/* レスポンシブ - モバイル（小） */
@media (max-width: 480px) {
    body {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }
    
    .single-episode-container {
        padding: 0.5rem;
    }
    
    .episode-title {
        font-size: 1.4rem;
        line-height: 1.25;
        margin-bottom: 1rem;
        font-weight: 700;
        /* 小画面でも全文字表示を確実にする */
        text-overflow: initial;
        overflow: visible;
        display: block;
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: break-word;
        -webkit-line-clamp: unset;
        -webkit-box-orient: unset;
    }
    
    .episode-content {
        margin: 1.5rem 0;
    }
    
    .episode-content-wrapper {
        margin: 0 -0.25rem;
        border-radius: 6px;
    }
    
    .content-text {
        padding: 1.5rem 1rem;
        font-size: 1rem;
        line-height: 1.65;
        /* 小画面での最適化 */
        word-spacing: 0.03em;
        letter-spacing: 0.005em;
    }
    
    .content-text h2 {
        font-size: 1.35rem;
        margin: 1.8rem 0 0.8rem 0;
        line-height: 1.25;
    }
    
    .content-text h3 {
        font-size: 1.2rem;
        margin: 1.5rem 0 0.6rem 0;
        line-height: 1.25;
    }
    
    .content-text h4 {
        font-size: 1.1rem;
        margin: 1.3rem 0 0.6rem 0;
        line-height: 1.25;
    }
    
    .content-text p {
        margin-bottom: 1rem;
    }
    
    .content-text blockquote {
        padding: 1rem 0.8rem;
        margin: 1.2rem 0;
        font-size: 0.95rem;
        line-height: 1.55;
    }
    
    .content-text pre {
        padding: 1rem 0.8rem;
        font-size: 0.85rem;
        margin: 1rem 0;
    }
    
    .content-text table {
        font-size: 0.9rem;
    }
    
    .content-text th,
    .content-text td {
        padding: 0.8rem 0.5rem;
    }
    
    .platform-links-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    }
    

}

/* レスポンシブ - 極小画面 */
@media (max-width: 360px) {
    body {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }
    
    .single-episode-container {
        padding: 0.25rem;
    }
    
    .episode-title {
        font-size: 1.3rem;
        line-height: 1.2;
        margin-bottom: 0.8rem;
        font-weight: 700;
        /* 極小画面でも全文字表示を確実にする */
        text-overflow: initial;
        overflow: visible;
        display: block;
        white-space: normal;
        word-wrap: break-word;
        overflow-wrap: break-word;
        -webkit-line-clamp: unset;
        -webkit-box-orient: unset;
    }
    
    .content-text {
        padding: 1.2rem 0.8rem;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .content-text h2 {
        font-size: 1.25rem;
        margin: 1.5rem 0 0.6rem 0;
    }
    
    .content-text h3 {
        font-size: 1.15rem;
        margin: 1.3rem 0 0.5rem 0;
    }
    
    .content-text h4 {
        font-size: 1.05rem;
        margin: 1.2rem 0 0.5rem 0;
    }
    
    .content-text blockquote {
        padding: 0.8rem 0.6rem;
        margin: 1rem 0;
        font-size: 0.9rem;
    }
    
    .content-text pre {
        padding: 0.8rem 0.6rem;
        font-size: 0.8rem;
    }
    

}

/* デスクトップレイアウト（1025px以上） */
@media (min-width: 1025px) {
    .episode-platform-links .platforms-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        max-width: 700px;
        margin: 1.5rem auto 0;
    }
    
    .episode-platform-links .platform-link {
        padding: 1.5rem 1rem;
        min-height: 120px;
        border-radius: 16px;
        border-width: 3px;
    }
    
    .episode-platform-links .platform-link:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18);
    }
    
    .episode-platform-links .platform-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
        margin: 0 auto 0.75rem auto;
    }
    
    .episode-platform-links .platform-icon img {
        width: 48px;
        height: 48px;
        object-fit: contain;
        border-radius: 50%;
    }
    
    .episode-platform-links .platform-link:hover .platform-icon {
        transform: scale(1.15);
    }
    
    .episode-platform-links .platform-name {
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }
    
    .episode-platform-links .platform-action {
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .episode-platform-links .platform-links-title {
        font-size: 1.3rem;
        margin-bottom: 1.5rem;
    }
}

/* タブレットレイアウト（769px - 1024px） */
@media (min-width: 769px) and (max-width: 1024px) {
    .episode-platform-links .platforms-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        max-width: 650px;
    }
    
    .episode-platform-links .platform-link {
        padding: 1.25rem 0.75rem;
        min-height: 110px;
        border-radius: 14px;
    }
    
    .episode-platform-links .platform-icon {
        width: 55px;
        height: 55px;
        font-size: 1.35rem;
        margin: 0 auto 0.6rem auto;
    }
    
    .episode-platform-links .platform-icon img {
        width: 44px;
        height: 44px;
        object-fit: contain;
        border-radius: 50%;
    }
    
    .episode-platform-links .platform-name {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }
    
    .episode-platform-links .platform-action {
        font-size: 0.8rem;
    }
    
    .episode-platform-links .platform-links-title {
        font-size: 1.2rem;
    }
}

/* モバイル対応（768px以下） */
@media (max-width: 768px) {
    .episode-platform-links .platforms-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    
    .episode-platform-links .platform-link {
        padding: 0.75rem 0.4rem;
        min-height: 80px;
    }
    
    .episode-platform-links .platform-icon {
        width: 35px;
        height: 35px;
        font-size: 1rem;
        margin: 0 auto 0.5rem auto;
    }
    
    .episode-platform-links .platform-icon img {
        width: 28px;
        height: 28px;
        object-fit: contain;
        border-radius: 50%;
    }
    
    .episode-platform-links .platform-name {
        font-size: 0.75rem;
    }
    
    .episode-platform-links .platform-action {
        font-size: 0.65rem;
    }
}

/* 極小レイアウト: 3カラム維持（480px以下） */
@media (max-width: 480px) {
    .episode-platform-links .platforms-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        max-width: 320px;
    }
    
    .episode-platform-links .platform-link {
        padding: 0.8rem 0.3rem;
        min-height: 75px;
        border-radius: 10px;
    }
    
    .episode-platform-links .platform-icon {
        width: 30px;
        height: 30px;
        font-size: 0.9rem;
        margin: 0 auto 0.4rem auto;
    }
    
    .episode-platform-links .platform-icon img {
        width: 24px;
        height: 24px;
        object-fit: contain;
        border-radius: 50%;
    }
    
    .episode-platform-links .platform-name {
        font-size: 0.7rem;
        margin-bottom: 0.2rem;
    }
    
    .episode-platform-links .platform-action {
        font-size: 0.6rem;
    }
}

/* プリント時の最適化 */
@media print {
    .content-text {
        padding: 1rem;
        font-size: 12pt;
        line-height: 1.6;
        color: #000;
    }
    
    .content-text h2:before {
        display: none;
    }
    
    .episode-nav-links,
    .related-episodes {
        display: none;
    }
}
</style>



<?php get_footer(); ?>
