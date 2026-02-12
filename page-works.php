<?php
/**
 * Template Name: 作品データベース
 * 語った作品をカード形式で一覧表示（ジャンルフィルター・検索付き）
 */

get_header(); ?>

<main id="main" class="site-main works-page">
    <?php contentfreaks_breadcrumb(); ?>

    <!-- ヒーローセクション -->
    <section class="works-hero">
        <div class="works-hero-bg">
            <div class="hero-pattern"></div>
        </div>
        <div class="works-hero-particles">
            <div class="works-particle"></div>
            <div class="works-particle"></div>
            <div class="works-particle"></div>
            <div class="works-particle"></div>
            <div class="works-particle"></div>
            <div class="works-particle"></div>
        </div>
        <div class="works-hero-content">
            <div class="works-hero-icon">🎬</div>
            <h1>Works Database</h1>
            <p class="works-hero-description">
                コンテンツフリークスが語った全作品を一覧で。<br>
                ドラマ・アニメ・映画・マンガ…すべてのコンテンツを検索・閲覧できます。
            </p>
            <div class="works-hero-stats">
                <div class="works-stat">
                    <span class="works-stat-number"><?php
                        $works_count = new WP_Query(array(
                            'post_type' => 'work',
                            'posts_per_page' => 1,
                            'fields' => 'ids',
                            'no_found_rows' => false
                        ));
                        echo $works_count->found_posts;
                        wp_reset_postdata();
                    ?></span>
                    <span class="works-stat-label">作品</span>
                </div>
                <div class="works-stat">
                    <span class="works-stat-number"><?php
                        $genres = get_terms(array('taxonomy' => 'work_genre', 'hide_empty' => true));
                        echo is_array($genres) ? count($genres) : 0;
                    ?></span>
                    <span class="works-stat-label">ジャンル</span>
                </div>
            </div>
        </div>
    </section>

    <!-- フィルター＆検索 -->
    <section class="works-content-section">
        <div class="works-container">
            <div class="works-controls">
                <div class="works-search-box">
                    <input type="text" id="works-search" class="works-search-input" placeholder="作品名で検索..." />
                    <button type="button" class="works-search-button">🔍</button>
                </div>

                <div class="works-genre-filters" id="genre-filters">
                    <button class="genre-filter-btn active" data-genre="">すべて</button>
                    <?php
                    $all_genres = get_terms(array('taxonomy' => 'work_genre', 'hide_empty' => true));
                    if (!is_wp_error($all_genres) && !empty($all_genres)) :
                        foreach ($all_genres as $genre) :
                    ?>
                        <button class="genre-filter-btn" data-genre="<?php echo esc_attr($genre->slug); ?>">
                            <?php echo esc_html($genre->name); ?>
                            <span class="genre-count"><?php echo $genre->count; ?></span>
                        </button>
                    <?php
                        endforeach;
                    endif;
                    ?>

                    <!-- 種類フィルター（タクソノミーがなくても動的に表示） -->
                    <?php
                    $type_icons = array(
                        'ドラマ' => '📺', 'アニメ' => '🎬', '映画' => '🎥',
                        'マンガ' => '📖', '小説' => '📚', 'バラエティ' => '🎭',
                    );
                    ?>
                </div>
            </div>

            <!-- 作品グリッド -->
            <div class="works-grid" id="works-grid">
                <?php
                $works_query = new WP_Query(array(
                    'post_type'      => 'work',
                    'posts_per_page' => 50,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ));

                if ($works_query->have_posts()) :
                    while ($works_query->have_posts()) : $works_query->the_post();
                        get_template_part('template-parts/work-card');
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                    <div class="works-empty-state">
                        <div class="works-empty-icon">🎬</div>
                        <h3>まだ作品が登録されていません</h3>
                        <p>管理画面の「作品データベース」から作品を追加してください。</p>
                        <?php if (current_user_can('edit_posts')) : ?>
                            <a href="<?php echo admin_url('post-new.php?post_type=work'); ?>" class="works-add-btn">
                                + 最初の作品を追加
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('works-search');
    const genreButtons = document.querySelectorAll('.genre-filter-btn');
    const grid = document.getElementById('works-grid');
    let currentGenre = '';
    let debounceTimer;

    function filterWorks(genre, search) {
        if (typeof contentfreaks_ajax === 'undefined') return;

        const formData = new URLSearchParams();
        formData.append('action', 'filter_works');
        formData.append('nonce', contentfreaks_ajax.nonce);
        if (genre) formData.append('genre', genre);
        if (search) formData.append('search', search);

        fetch(contentfreaks_ajax.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success && grid) {
                if (data.data.html) {
                    grid.innerHTML = data.data.html;
                } else {
                    grid.innerHTML = '<div class="works-empty-state"><div class="works-empty-icon">🔍</div><h3>見つかりませんでした</h3></div>';
                }
            }
        });
    }

    // ジャンルフィルター
    genreButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            genreButtons.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            currentGenre = this.dataset.genre;
            filterWorks(currentGenre, searchInput ? searchInput.value.trim() : '');
        });
    });

    // 検索
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const term = this.value.trim();
            debounceTimer = setTimeout(function() {
                filterWorks(currentGenre, term);
            }, 400);
        });
    }
});
</script>

<?php get_footer(); ?>
