<?php
/**
 * 作品データベース管理
 * カスタム投稿タイプ「work」+ メタボックスで語った作品を管理
 */

if (!defined('ABSPATH')) exit;

/**
 * カスタム投稿タイプ「作品」を登録
 */
function contentfreaks_register_works_cpt() {
    register_post_type('work', array(
        'labels' => array(
            'name'               => '作品データベース',
            'singular_name'      => '作品',
            'add_new'            => '新しい作品を追加',
            'add_new_item'       => '新しい作品を追加',
            'edit_item'          => '作品を編集',
            'view_item'          => '作品を表示',
            'all_items'          => 'すべての作品',
            'search_items'       => '作品を検索',
            'not_found'          => '作品が見つかりません',
            'not_found_in_trash' => 'ゴミ箱に作品はありません',
        ),
        'public'       => true,
        'has_archive'  => false,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-format-video',
        'menu_position' => 25,
        'supports'     => array('title', 'thumbnail', 'editor'),
        'rewrite'      => array('slug' => 'work'),
        'show_in_rest' => true,
    ));

    // ジャンル分類（タクソノミー）
    register_taxonomy('work_genre', 'work', array(
        'labels' => array(
            'name'          => 'ジャンル',
            'singular_name' => 'ジャンル',
            'add_new_item'  => '新しいジャンルを追加',
            'search_items'  => 'ジャンルを検索',
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'genre'),
    ));
}
add_action('init', 'contentfreaks_register_works_cpt');

/**
 * 作品メタボックス（管理画面用）
 */
function contentfreaks_works_meta_boxes() {
    add_meta_box(
        'work_details',
        '作品詳細',
        'contentfreaks_works_meta_box_html',
        'work',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'contentfreaks_works_meta_boxes');

function contentfreaks_works_meta_box_html($post) {
    wp_nonce_field('contentfreaks_save_work', 'work_nonce');

    $fields = array(
        'work_type'          => array('label' => '種類', 'type' => 'select', 'options' => array('ドラマ', 'アニメ', '映画', 'マンガ', '小説', 'バラエティ', 'その他')),
        'work_rating'        => array('label' => '評価（5段階）', 'type' => 'select', 'options' => array('', '1', '2', '3', '4', '5')),
        'work_year'          => array('label' => '公開年', 'type' => 'text'),
        'work_platform'      => array('label' => '視聴プラットフォーム', 'type' => 'text'),
        'work_episodes_list' => array('label' => '関連エピソード（投稿ID、カンマ区切り）', 'type' => 'text'),
        'work_one_line'      => array('label' => 'ひとこと感想', 'type' => 'textarea'),
    );

    foreach ($fields as $key => $field) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<p><label for="' . $key . '"><strong>' . esc_html($field['label']) . '</strong></label><br>';

        if ($field['type'] === 'select') {
            echo '<select name="' . $key . '" id="' . $key . '" style="width:100%">';
            foreach ($field['options'] as $opt) {
                echo '<option value="' . esc_attr($opt) . '"' . selected($value, $opt, false) . '>' . esc_html($opt ?: '—') . '</option>';
            }
            echo '</select>';
        } elseif ($field['type'] === 'textarea') {
            echo '<textarea name="' . $key . '" id="' . $key . '" rows="3" style="width:100%">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input type="text" name="' . $key . '" id="' . $key . '" value="' . esc_attr($value) . '" style="width:100%">';
        }
        echo '</p>';
    }
}

/**
 * メタデータ保存
 */
function contentfreaks_save_work_meta($post_id) {
    if (!isset($_POST['work_nonce']) || !wp_verify_nonce($_POST['work_nonce'], 'contentfreaks_save_work')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = array('work_type', 'work_rating', 'work_year', 'work_platform', 'work_episodes_list', 'work_one_line');
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_work', 'contentfreaks_save_work_meta');

/**
 * AJAX: 作品ジャンルフィルター
 */
function contentfreaks_filter_works() {
    check_ajax_referer('contentfreaks_load_more', 'nonce', true);

    $genre = isset($_POST['genre']) ? sanitize_text_field($_POST['genre']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    $args = array(
        'post_type'      => 'work',
        'posts_per_page' => 50,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if (!empty($genre)) {
        $args['tax_query'] = array(array(
            'taxonomy' => 'work_genre',
            'field'    => 'slug',
            'terms'    => $genre,
        ));
    }

    if (!empty($search)) {
        $args['s'] = $search;
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/work-card');
        }
        wp_reset_postdata();
    }
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html, 'count' => $query->found_posts));
}
add_action('wp_ajax_filter_works', 'contentfreaks_filter_works');
add_action('wp_ajax_nopriv_filter_works', 'contentfreaks_filter_works');
