<?php
/**
 * 作品カード テンプレートパーツ
 */
$work_type     = get_post_meta(get_the_ID(), 'work_type', true);
$work_rating   = get_post_meta(get_the_ID(), 'work_rating', true);
$work_year     = get_post_meta(get_the_ID(), 'work_year', true);
$work_platform = get_post_meta(get_the_ID(), 'work_platform', true);
$work_one_line = get_post_meta(get_the_ID(), 'work_one_line', true);
$work_episodes = get_post_meta(get_the_ID(), 'work_episodes_list', true);

// 種類別アイコン
$type_icons = array(
    'ドラマ'     => '📺',
    'アニメ'     => '🎬',
    '映画'       => '🎥',
    'マンガ'     => '📖',
    '小説'       => '📚',
    'バラエティ' => '🎭',
    'その他'     => '🎯',
);
$icon = isset($type_icons[$work_type]) ? $type_icons[$work_type] : '🎯';

// 評価の星表示
$stars = '';
if ($work_rating) {
    for ($i = 1; $i <= 5; $i++) {
        $stars .= ($i <= intval($work_rating)) ? '★' : '☆';
    }
}
?>

<article class="work-card" data-type="<?php echo esc_attr($work_type); ?>">
    <div class="work-card-thumbnail">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium', array('alt' => get_the_title(), 'loading' => 'lazy')); ?>
        <?php else : ?>
            <div class="work-default-thumbnail">
                <span class="work-type-icon"><?php echo $icon; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($work_type) : ?>
            <span class="work-type-badge"><?php echo $icon . ' ' . esc_html($work_type); ?></span>
        <?php endif; ?>
    </div>

    <div class="work-card-content">
        <h3 class="work-title"><?php the_title(); ?></h3>

        <div class="work-meta">
            <?php if ($work_year) : ?>
                <span class="work-year"><?php echo esc_html($work_year); ?>年</span>
            <?php endif; ?>
            <?php if ($stars) : ?>
                <span class="work-rating" title="<?php echo esc_attr($work_rating); ?>/5"><?php echo $stars; ?></span>
            <?php endif; ?>
        </div>

        <?php if ($work_one_line) : ?>
            <p class="work-one-line"><?php echo esc_html($work_one_line); ?></p>
        <?php endif; ?>

        <?php if ($work_platform) : ?>
            <span class="work-platform">📡 <?php echo esc_html($work_platform); ?></span>
        <?php endif; ?>

        <?php if ($work_episodes) : ?>
            <div class="work-related-episodes">
                <?php
                $ep_ids = array_map('trim', explode(',', $work_episodes));
                foreach ($ep_ids as $ep_id) {
                    $ep_id = intval($ep_id);
                    if ($ep_id > 0 && get_post_status($ep_id) === 'publish') {
                        echo '<a href="' . get_permalink($ep_id) . '" class="work-episode-link">🎧 ' . esc_html(get_the_title($ep_id)) . '</a>';
                    }
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</article>
