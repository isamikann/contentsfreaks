<?php
/**
 * Template Name: 作品データベース
 * Description: エピソードで取り上げた全作品の一覧
 */

get_header(); ?>

<main id="main-content" class="works-database-main">
    <!-- ヒーローセクション -->
    <section class="works-hero">
        <div class="works-hero-bg"></div>
        <div class="works-hero-content">
            <div class="works-hero-icon">📚</div>
            <h1 class="works-hero-title">作品データベース</h1>
            <p class="works-hero-subtitle">
                ContentFreaksで取り上げた全作品を探す
            </p>
            
            <div class="works-stats">
                <?php
                // 全投稿を取得
                $works_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                ));
                
                $all_works = array();
                
                while ($works_query->have_posts()) : $works_query->the_post();
                    $post_id = get_the_ID();
                    $post_title = get_the_title();
                    
                    // まずカスタムフィールドから作品情報を取得
                    $mentioned_works = get_post_meta($post_id, 'mentioned_works', true);
                    
                    if ($mentioned_works && is_array($mentioned_works)) {
                        foreach ($mentioned_works as $work) {
                            $work_title = isset($work['title']) ? $work['title'] : '';
                            if ($work_title && !isset($all_works[$work_title])) {
                                $all_works[$work_title] = $work;
                                $all_works[$work_title]['episodes'] = array($post_id);
                            } else if ($work_title) {
                                $all_works[$work_title]['episodes'][] = $post_id;
                            }
                        }
                    }
                    
                    // カスタムフィールドがない場合、タグから作品を抽出
                    if (empty($mentioned_works)) {
                        // タグから作品情報を取得
                        $tags = get_the_tags();
                        if ($tags && !is_wp_error($tags)) {
                            foreach ($tags as $tag) {
                                $tag_name = $tag->name;
                                
                                // タグを作品として登録
                                if (!isset($all_works[$tag_name])) {
                                    $all_works[$tag_name] = array(
                                        'title' => $tag_name,
                                        'genre' => '',
                                        'year' => get_the_date('Y', $post_id),
                                        'rating' => 0,
                                        'image' => get_the_post_thumbnail_url($post_id, 'medium') ?: '',
                                        'url' => '',
                                        'episodes' => array($post_id)
                                    );
                                } else {
                                    if (!in_array($post_id, $all_works[$tag_name]['episodes'])) {
                                        $all_works[$tag_name]['episodes'][] = $post_id;
                                    }
                                }
                            }
                        }
                    }
                endwhile;
                wp_reset_postdata();
                
                $total_works = count($all_works);
                ?>
                
                <div class="works-stat-item">
                    <span class="works-stat-number"><?php echo $total_works; ?></span>
                    <span class="works-stat-label">作品</span>
                </div>
                <div class="works-stat-item">
                    <span class="works-stat-number"><?php echo $works_query->found_posts; ?></span>
                    <span class="works-stat-label">エピソード</span>
                </div>
            </div>
        </div>
    </section>

    <!-- フィルター・検索エリア -->
    <section class="works-filters-section">
        <div class="works-container">
            <!-- 検索バー -->
            <div class="works-search-bar">
                <input type="text" 
                       id="works-search" 
                       class="works-search-input" 
                       placeholder="🔍 作品名で検索..."
                       aria-label="作品を検索">
            </div>
            
            <!-- フィルター -->
            <div class="works-filters">
                <!-- ソート -->
                <div class="filter-group">
                    <label class="filter-label">並び順</label>
                    <select id="sort-filter" class="filter-select">
                        <option value="episodes-desc">登場回数順</option>
                        <option value="title-asc">作品名順（昇順）</option>
                        <option value="title-desc">作品名順（降順）</option>
                    </select>
                </div>
                
                <!-- 表示形式 -->
                <div class="filter-group">
                    <label class="filter-label">表示</label>
                    <div class="view-toggle">
                        <button class="view-btn active" data-view="grid" aria-label="グリッド表示">
                            <span>⊞</span>
                        </button>
                        <button class="view-btn" data-view="list" aria-label="リスト表示">
                            <span>☰</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- アクティブフィルター表示 -->
            <div class="active-filters" id="active-filters" style="display: none;">
                <span class="active-filters-label">フィルター中:</span>
                <div class="active-filters-tags" id="active-filters-tags"></div>
                <button class="clear-filters-btn" id="clear-filters">すべてクリア</button>
            </div>
        </div>
    </section>

    <!-- 作品グリッド -->
    <section class="works-grid-section">
        <div class="works-container">
            <!-- 結果カウント -->
            <div class="works-results-count">
                <span id="results-count"><?php echo $total_works; ?></span> 件の作品
            </div>
            
            <!-- 作品グリッド -->
            <div class="works-grid" id="works-grid">
                <?php
                // 作品データを配列に変換してソート
                $works_array = array();
                foreach ($all_works as $title => $work) {
                    $work['title'] = $title;
                    $works_array[] = $work;
                }
                
                // デフォルトは登場回数順
                usort($works_array, function($a, $b) {
                    return count($b['episodes']) - count($a['episodes']);
                });
                
                foreach ($works_array as $work):
                    $title = $work['title'];
                    $genre = isset($work['genre']) ? $work['genre'] : 'その他';
                    $year = isset($work['year']) ? $work['year'] : '';
                    $rating = isset($work['rating']) ? $work['rating'] : 0;
                    $image = isset($work['image']) ? $work['image'] : '';
                    $url = isset($work['url']) ? $work['url'] : '';
                    $episodes = $work['episodes'];
                    $episode_count = count($episodes);
                ?>
                    <article class="work-card" 
                             data-genre="<?php echo esc_attr($genre); ?>"
                             data-year="<?php echo esc_attr($year); ?>"
                             data-episodes="<?php echo esc_attr($episode_count); ?>"
                             data-title="<?php echo esc_attr(strtolower($title)); ?>">
                        
                        <!-- 作品画像 -->
                        <div class="work-card-image">
                            <?php if ($image): ?>
                                <img src="<?php echo esc_url($image); ?>" 
                                     alt="<?php echo esc_attr($title); ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="work-card-placeholder">
                                    <span class="work-placeholder-icon">🎭</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- エピソード数バッジ -->
                            <div class="work-episodes-badge">
                                <?php echo $episode_count; ?>回登場
                            </div>
                        </div>
                        
                        <!-- 作品情報 -->
                        <div class="work-card-content">
                            <!-- タイトル -->
                            <h3 class="work-title"><?php echo esc_html($title); ?></h3>
                            
                            <!-- 評価 -->
                            <?php if ($rating > 0): ?>
                                <div class="work-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?php echo $i <= $rating ? 'filled' : ''; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- アクション -->
                            <div class="work-actions">
                                <button class="work-btn work-episodes-btn" 
                                        data-work-id="<?php echo esc_attr(sanitize_title($title)); ?>">
                                    <span>📻</span> 登場エピソード
                                </button>
                                <?php if ($url): ?>
                                    <a href="<?php echo esc_url($url); ?>" 
                                       class="work-btn work-external-btn" 
                                       target="_blank" 
                                       rel="noopener">
                                        <span>🔗</span> 詳細
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- 登場エピソード（非表示） -->
                        <div class="work-episodes-list" 
                             id="episodes-<?php echo esc_attr(sanitize_title($title)); ?>" 
                             style="display: none;">
                            <h4>この作品が登場したエピソード</h4>
                            <ul>
                                <?php foreach ($episodes as $episode_id): ?>
                                    <li>
                                        <a href="<?php echo get_permalink($episode_id); ?>">
                                            <?php echo get_the_title($episode_id); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <!-- 結果なし -->
            <div class="no-results" id="no-results" style="display: none;">
                <div class="no-results-icon">🔍</div>
                <h3>該当する作品が見つかりませんでした</h3>
                <p>検索条件を変更してお試しください</p>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
