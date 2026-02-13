<?php
/**
 * リスナーの声（テスティモニアル）管理
 * - カスタム投稿タイプ「testimonial」
 * - フロント投稿フォーム（AJAX）
 * - 管理者承認制（下書き→公開）
 */

if (!defined('ABSPATH')) exit;

/**
 * カスタム投稿タイプ「リスナーの声」を登録
 */
function contentfreaks_register_testimonials_cpt() {
    register_post_type('testimonial', array(
        'labels' => array(
            'name'               => 'リスナーの声',
            'singular_name'      => 'リスナーの声',
            'add_new'            => '新規追加',
            'add_new_item'       => '新しいテスティモニアルを追加',
            'edit_item'          => '編集',
            'all_items'          => 'すべてのリスナーの声',
            'search_items'       => '検索',
            'not_found'          => '見つかりません',
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-format-quote',
        'menu_position' => 26,
        'supports'      => array('title', 'editor'),
        'show_in_rest'  => false,
    ));
}
add_action('init', 'contentfreaks_register_testimonials_cpt');

/**
 * メタボックス：投稿者名・投稿元
 */
function contentfreaks_testimonial_meta_boxes() {
    add_meta_box('testimonial_info', '投稿者情報', 'contentfreaks_testimonial_meta_html', 'testimonial', 'side', 'high');
}
add_action('add_meta_boxes', 'contentfreaks_testimonial_meta_boxes');

function contentfreaks_testimonial_meta_html($post) {
    wp_nonce_field('cf_save_testimonial', 'testimonial_nonce');
    $name = get_post_meta($post->ID, 'testimonial_name', true);
    $source = get_post_meta($post->ID, 'testimonial_source', true) ?: 'Webフォーム';
    ?>
    <p>
        <label><strong>投稿者名</strong></label><br>
        <input type="text" name="testimonial_name" value="<?php echo esc_attr($name); ?>" style="width:100%">
    </p>
    <p>
        <label><strong>投稿元</strong></label><br>
        <input type="text" name="testimonial_source" value="<?php echo esc_attr($source); ?>" style="width:100%">
    </p>
    <?php
}

function contentfreaks_save_testimonial_meta($post_id) {
    if (!isset($_POST['testimonial_nonce']) || !wp_verify_nonce($_POST['testimonial_nonce'], 'cf_save_testimonial')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['testimonial_name'])) {
        update_post_meta($post_id, 'testimonial_name', sanitize_text_field($_POST['testimonial_name']));
    }
    if (isset($_POST['testimonial_source'])) {
        update_post_meta($post_id, 'testimonial_source', sanitize_text_field($_POST['testimonial_source']));
    }
}
add_action('save_post_testimonial', 'contentfreaks_save_testimonial_meta');

/**
 * クライアントIPを安全に取得（プロキシ/CDN対応）
 */
function contentfreaks_get_client_ip() {
    $headers = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR');
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = $_SERVER[$header];
            // X-Forwarded-For は複数IP含む場合がある（最初が本来のクライアント）
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return sanitize_text_field($ip);
            }
        }
    }
    return sanitize_text_field($_SERVER['REMOTE_ADDR']);
}

/**
 * AJAX: リスナーの声フォーム送信（下書きとして保存）
 */
function contentfreaks_submit_testimonial() {
    // CORS/Origin 安全検証
    header('Content-Type: application/json; charset=utf-8');

    // nonce 検証（失敗時はJSON形式でエラーを返す）
    if (!check_ajax_referer('contentfreaks_load_more', 'nonce', false)) {
        wp_send_json_error(array('message' => 'セキュリティ検証に失敗しました。ページを再読み込みしてください。'));
        wp_die();
    }

    // ハニーポットスパム対策（botが自動入力するhiddenフィールドをチェック）
    if (!empty($_POST['website_url'])) {
        // botの場合は成功レスポンスを返す（botに検知を悟らせない）
        wp_send_json_success(array('message' => 'ありがとうございます！承認後にサイトに掲載されます。'));
        wp_die();
    }

    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

    if (empty($name) || empty($message)) {
        wp_send_json_error(array('message' => 'お名前とメッセージを入力してください。'));
    }

    if (mb_strlen($message) > 500) {
        wp_send_json_error(array('message' => 'メッセージは500文字以内でお願いします。'));
    }

    // レート制限（同一IPから1時間に1回のみ）
    $ip = contentfreaks_get_client_ip();
    $rate_key = 'cf_testimonial_' . md5($ip);
    if (get_transient($rate_key)) {
        wp_send_json_error(array('message' => '送信は1時間に1回までです。しばらくお待ちください。'));
    }

    $post_id = wp_insert_post(array(
        'post_type'    => 'testimonial',
        'post_title'   => mb_substr($message, 0, 50) . '...',
        'post_content' => $message,
        'post_status'  => 'draft', // 管理者承認制
    ));

    if (is_wp_error($post_id)) {
        wp_send_json_error(array('message' => '送信に失敗しました。'));
    }

    update_post_meta($post_id, 'testimonial_name', $name);
    update_post_meta($post_id, 'testimonial_source', 'Webフォーム');

    // レート制限設定
    set_transient($rate_key, true, HOUR_IN_SECONDS);

    wp_send_json_success(array('message' => 'ありがとうございます！承認後にサイトに掲載されます。'));
}
add_action('wp_ajax_submit_testimonial', 'contentfreaks_submit_testimonial');
add_action('wp_ajax_nopriv_submit_testimonial', 'contentfreaks_submit_testimonial');

/**
 * ショートコード: リスナーの声表示
 * [listener_voices count="4"]
 */
function contentfreaks_listener_voices_shortcode($atts) {
    $atts = shortcode_atts(array('count' => 4), $atts);

    $query = new WP_Query(array(
        'post_type'      => 'testimonial',
        'posts_per_page' => intval($atts['count']),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));

    ob_start();

    if ($query->have_posts()) :
        echo '<div class="testimonials-grid">';
        while ($query->have_posts()) : $query->the_post();
            $name = get_post_meta(get_the_ID(), 'testimonial_name', true) ?: '匿名';
            $source = get_post_meta(get_the_ID(), 'testimonial_source', true) ?: '';
            $initial = mb_substr($name, 0, 1);
            ?>
            <div class="testimonial-card scale-in">
                <div class="testimonial-quote">
                    <?php echo esc_html(get_the_content()); ?>
                </div>
                <div class="testimonial-author">
                    <div class="author-avatar"><?php echo esc_html($initial); ?></div>
                    <div class="author-info">
                        <h4><?php echo esc_html($name); ?>さん</h4>
                        <?php if ($source) : ?>
                            <div class="author-role"><?php echo esc_html($source); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        endwhile;
        echo '</div>';
        wp_reset_postdata();
    endif;

    return ob_get_clean();
}
add_shortcode('listener_voices', 'contentfreaks_listener_voices_shortcode');
