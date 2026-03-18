<?php
/**
 * Cocoon Child Theme Functions
 * ポッドキャストサイト専用のカスタマイズ
 */

// 直接このファイルにアクセスすることを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

// 機能ファイルを読み込む
require_once get_stylesheet_directory() . '/inc/constants.php';
require_once get_stylesheet_directory() . '/inc/disable_cocoon.php';
require_once get_stylesheet_directory() . '/inc/body_class.php';
require_once get_stylesheet_directory() . '/inc/enqueue_scripts.php';
require_once get_stylesheet_directory() . '/inc/shortcodes.php';
require_once get_stylesheet_directory() . '/inc/customizer.php';
require_once get_stylesheet_directory() . '/inc/dynamic_styles.php';
require_once get_stylesheet_directory() . '/inc/image_optimization.php'; // 画像最適化
require_once get_stylesheet_directory() . '/inc/performance_optimization.php'; // パフォーマンス最適化
require_once get_stylesheet_directory() . '/inc/structured_data.php'; // 構造化データ・OGP
require_once get_stylesheet_directory() . '/inc/works_cpt.php'; // 作品データベース
require_once get_stylesheet_directory() . '/inc/testimonials.php'; // リスナーの声
require_once get_stylesheet_directory() . '/inc/pwa.php'; // PWAサポート
require_once get_stylesheet_directory() . '/inc/youtube_stats.php'; // YouTube統計

// RSS自動投稿関連の読み込み
require_once get_stylesheet_directory() . '/rss-auto-post.php';

/**
 * 定期同期スケジュール
 */
function contentfreaks_schedule_sync() {
    if (!wp_next_scheduled('contentfreaks_hourly_sync')) {
        wp_schedule_event(time(), 'hourly', 'contentfreaks_hourly_sync');
    }
}
add_action('wp', 'contentfreaks_schedule_sync');

add_action('contentfreaks_hourly_sync', 'contentfreaks_sync_rss_to_posts');

/**
 * 管理画面メニュー（統一された管理画面）
 */
function contentfreaks_admin_menu() {
    add_management_page(
        'ポッドキャスト管理',
        'ポッドキャスト管理', 
        'manage_options',
        'contentfreaks-podcast-management',
        'contentfreaks_unified_admin_page'
    );
}
add_action('admin_menu', 'contentfreaks_admin_menu');

/**
 * RSSキャッシュクリア機能
 */
function contentfreaks_clear_rss_cache() {
    // 現在使用中のキャッシュのみクリア
    delete_transient('contentfreaks_rss_episodes_1');
    delete_transient('contentfreaks_rss_episodes_6');
    delete_transient('contentfreaks_rss_episodes_all');
    delete_transient('contentfreaks_rss_count');
    
    // 古い同期関連のオプションも削除
    delete_option('contentfreaks_last_rss_sync');
    delete_option('contentfreaks_last_sync_count');
    delete_option('contentfreaks_last_sync_error');
    
    return true;
}

/**
 * 手動でタグを再抽出する機能（管理画面用）
 */
function contentfreaks_re_extract_all_tags() {
    $podcast_posts = get_posts(array(
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'post_status' => 'publish',
        'numberposts' => -1
    ));
    $processed_count = 0;
    foreach ($podcast_posts as $post) {
        contentfreaks_extract_and_create_tags_from_title($post->ID, $post->post_title);
        $processed_count++;
    }
    return $processed_count;
}

/**
 * 統一された管理画面（タブ式）
 */
function contentfreaks_unified_admin_page() {
    $messages = array();
    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';

    // ========== POST ハンドラ: ツール ==========
    if (isset($_POST['manual_sync']) && wp_verify_nonce($_POST['sync_nonce'], 'contentfreaks_sync')) {
        $result = contentfreaks_sync_rss_to_posts();
        if (!empty($result['errors'])) {
            $messages[] = array('type' => 'warning', 'message' => $result['synced'] . ' 件のエピソードを同期しました。エラー: ' . count($result['errors']) . ' 件');
        } else {
            $messages[] = array('type' => 'success', 'message' => $result['synced'] . ' 件のエピソードを同期しました！');
        }
        $current_tab = 'tools';
    }

    if (isset($_POST['re_extract_tags']) && wp_verify_nonce($_POST['re_extract_tags_nonce'], 'contentfreaks_re_extract_tags')) {
        $processed = contentfreaks_re_extract_all_tags();
        $messages[] = array('type' => 'success', 'message' => $processed . ' 件の投稿からタグを再抽出しました！');
        $current_tab = 'tools';
    }

    if (isset($_POST['clear_cache']) && wp_verify_nonce($_POST['clear_cache_nonce'], 'contentfreaks_clear_cache')) {
        contentfreaks_clear_rss_cache();
        $messages[] = array('type' => 'success', 'message' => 'RSSキャッシュをクリアしました！');
        $current_tab = 'tools';
    }

    if (isset($_POST['flush_rewrite_rules']) && wp_verify_nonce($_POST['flush_rewrite_rules_nonce'], 'contentfreaks_flush_rewrite_rules')) {
        delete_option('rewrite_rules');
        contentfreaks_episodes_rewrite_rules();
        flush_rewrite_rules();
        delete_option('contentfreaks_rewrite_rules_flushed');
        $messages[] = array('type' => 'success', 'message' => 'リライトルールを強制更新しました！');
        $current_tab = 'tools';
    }

    // ========== POST ハンドラ: 基本設定 ==========
    if (isset($_POST['save_basic_settings']) && wp_verify_nonce($_POST['basic_settings_nonce'], 'contentfreaks_basic_settings')) {
        set_theme_mod('podcast_name', sanitize_text_field($_POST['podcast_name']));
        set_theme_mod('podcast_description', sanitize_textarea_field($_POST['podcast_description']));
        update_option('contentfreaks_pickup_episodes', sanitize_text_field($_POST['contentfreaks_pickup_episodes']));
        $messages[] = array('type' => 'success', 'message' => '基本設定を保存しました！');
        $current_tab = 'settings';
    }

    // ========== POST ハンドラ: ホスト設定 ==========
    if (isset($_POST['save_host_settings']) && wp_verify_nonce($_POST['host_settings_nonce'], 'contentfreaks_host_settings')) {
        foreach (array('host1', 'host2') as $host) {
            set_theme_mod($host . '_name', sanitize_text_field($_POST[$host . '_name']));
            set_theme_mod($host . '_role', sanitize_text_field($_POST[$host . '_role']));
            set_theme_mod($host . '_bio', sanitize_textarea_field($_POST[$host . '_bio']));
            set_theme_mod($host . '_twitter', esc_url_raw($_POST[$host . '_twitter']));
            set_theme_mod($host . '_youtube', esc_url_raw($_POST[$host . '_youtube']));
        }
        $messages[] = array('type' => 'success', 'message' => 'ホスト設定を保存しました！');
        $current_tab = 'hosts';
    }

    // ========== POST ハンドラ: メディアキット ==========
    if (isset($_POST['save_mediakit_settings']) && wp_verify_nonce($_POST['mediakit_nonce'], 'contentfreaks_mediakit')) {
        update_option('contentfreaks_listener_count', sanitize_text_field($_POST['listener_count']));
        $mk_keys = array('mk_spotify_followers', 'mk_apple_followers', 'mk_youtube_subscribers', 'mk_monthly_plays', 'mk_frequency', 'mk_since', 'mk_amazon_tag');
        foreach ($mk_keys as $key) {
            set_theme_mod($key, sanitize_text_field($_POST[$key]));
        }
        $messages[] = array('type' => 'success', 'message' => 'メディアキット設定を保存しました！');
        $current_tab = 'mediakit';
    }

    // ========== 統計情報 ==========
    $current_rss_count = contentfreaks_get_rss_episode_count();
    $podcast_posts = get_posts(array(
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'post_status' => 'publish',
        'numberposts' => -1
    ));
    $podcast_post_count = count($podcast_posts);
    $last_sync_time = get_option('contentfreaks_last_sync_time');
    $last_sync_count = get_option('contentfreaks_last_sync_count', 0);
    $last_sync_errors = get_option('contentfreaks_last_sync_errors', array());
    $total_tags = wp_count_terms('post_tag');
    $page_url = admin_url('tools.php?page=contentfreaks-podcast-management');

    $tabs = array(
        'dashboard' => '📊 ダッシュボード',
        'settings'  => '⚙️ 基本設定',
        'hosts'     => '👥 ホスト設定',
        'mediakit'  => '📈 メディアキット',
        'tools'     => '🔧 ツール',
    );
    ?>
    <div class="wrap">
        <h1>🎙️ ContentFreaks 管理</h1>

        <?php foreach ($messages as $msg): ?>
            <div class="notice notice-<?php echo esc_attr($msg['type']); ?> is-dismissible">
                <p><?php echo esc_html($msg['message']); ?></p>
            </div>
        <?php endforeach; ?>

        <nav class="nav-tab-wrapper">
            <?php foreach ($tabs as $tab_key => $tab_label): ?>
                <a href="<?php echo esc_url($page_url . '&tab=' . $tab_key); ?>"
                   class="nav-tab <?php echo $current_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html($tab_label); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div style="margin-top: 20px;">

        <?php if ($current_tab === 'dashboard'): ?>
            <!-- ===== ダッシュボード ===== -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                <div style="background: #f0f8ff; padding: 15px; border-radius: 8px; border-left: 4px solid #2196F3;">
                    <h4 style="margin: 0 0 10px 0; color: #2196F3;">RSSエピソード数</h4>
                    <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo esc_html($current_rss_count); ?> 件</p>
                </div>
                <div style="background: #f0fff0; padding: 15px; border-radius: 8px; border-left: 4px solid #4CAF50;">
                    <h4 style="margin: 0 0 10px 0; color: #4CAF50;">ポッドキャスト投稿数</h4>
                    <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo esc_html($podcast_post_count); ?> 件</p>
                </div>
                <div style="background: #fff8f0; padding: 15px; border-radius: 8px; border-left: 4px solid #ff9800;">
                    <h4 style="margin: 0 0 10px 0; color: #ff9800;">登録済みタグ数</h4>
                    <p style="font-size: 24px; font-weight: bold; margin: 0;"><?php echo esc_html($total_tags); ?> 件</p>
                </div>
            </div>

            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">最新の同期情報</h2>
                <div class="inside">
                    <p><strong>最後の同期:</strong> <?php echo $last_sync_time ? esc_html(date('Y年n月j日 H:i:s', strtotime($last_sync_time))) : '未実行'; ?></p>
                    <p><strong>同期/更新件数:</strong> <?php echo esc_html($last_sync_count); ?>件</p>
                    <?php if (!empty($last_sync_errors)): ?>
                        <div style="background: #ffeaa7; padding: 12px; border-left: 4px solid #fdcb6e; border-radius: 4px; margin-top: 10px;">
                            <h4 style="margin: 0 0 8px 0; color: #d63638;">⚠️ 同期エラー (<?php echo count($last_sync_errors); ?>件)</h4>
                            <ul style="margin: 0;">
                                <?php foreach ($last_sync_errors as $error): ?>
                                    <li><?php echo esc_html($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">📝 最近の更新記録</h2>
                <div class="inside">
                    <?php contentfreaks_display_recent_updates(); ?>
                </div>
            </div>

            <div class="postbox">
                <h2 class="hndle">📋 更新ログ</h2>
                <div class="inside">
                    <?php contentfreaks_display_update_logs(); ?>
                </div>
            </div>

        <?php elseif ($current_tab === 'settings'): ?>
            <!-- ===== 基本設定 ===== -->
            <div class="postbox">
                <h2 class="hndle">ポッドキャスト基本情報</h2>
                <div class="inside">
                    <form method="post">
                        <?php wp_nonce_field('contentfreaks_basic_settings', 'basic_settings_nonce'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="podcast_name">ポッドキャスト名</label></th>
                                <td>
                                    <input type="text" id="podcast_name" name="podcast_name" class="regular-text"
                                           value="<?php echo esc_attr(get_theme_mod('podcast_name', 'コンテンツフリークス')); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="podcast_description">ポッドキャスト説明</label></th>
                                <td>
                                    <textarea id="podcast_description" name="podcast_description" rows="4" class="large-text"><?php echo esc_textarea(get_theme_mod('podcast_description', '')); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="contentfreaks_pickup_episodes">ピックアップ投稿ID</label></th>
                                <td>
                                    <input type="text" id="contentfreaks_pickup_episodes" name="contentfreaks_pickup_episodes" class="regular-text"
                                           value="<?php echo esc_attr(get_option('contentfreaks_pickup_episodes', '')); ?>" />
                                    <p class="description">表示したい投稿IDをカンマ区切りで入力（例: 123,456,789）。空にするとセクション非表示。</p>
                                </td>
                            </tr>
                        </table>
                        <p class="description" style="margin-top: 10px;">
                            💡 アートワーク画像・プラットフォームアイコン・ヘッダーアイコンは
                            <a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=contentfreaks_podcast_settings')); ?>">外観 → カスタマイズ</a>で設定できます。
                        </p>
                        <?php submit_button('設定を保存', 'primary', 'save_basic_settings'); ?>
                    </form>
                </div>
            </div>

        <?php elseif ($current_tab === 'hosts'): ?>
            <!-- ===== ホスト設定 ===== -->
            <form method="post">
                <?php wp_nonce_field('contentfreaks_host_settings', 'host_settings_nonce'); ?>
                <?php
                $host_configs = array(
                    'host1' => array('title' => 'ホスト 1', 'default_role' => 'メインホスト'),
                    'host2' => array('title' => 'ホスト 2', 'default_role' => 'コホスト'),
                );
                foreach ($host_configs as $host_key => $host_config): ?>
                    <div class="postbox" style="margin-bottom: 20px;">
                        <h2 class="hndle"><?php echo esc_html($host_config['title']); ?></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="<?php echo esc_attr($host_key); ?>_name">名前</label></th>
                                    <td><input type="text" id="<?php echo esc_attr($host_key); ?>_name" name="<?php echo esc_attr($host_key); ?>_name" class="regular-text" value="<?php echo esc_attr(get_theme_mod($host_key . '_name', '')); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="<?php echo esc_attr($host_key); ?>_role">役職</label></th>
                                    <td><input type="text" id="<?php echo esc_attr($host_key); ?>_role" name="<?php echo esc_attr($host_key); ?>_role" class="regular-text" value="<?php echo esc_attr(get_theme_mod($host_key . '_role', $host_config['default_role'])); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="<?php echo esc_attr($host_key); ?>_bio">紹介文</label></th>
                                    <td><textarea id="<?php echo esc_attr($host_key); ?>_bio" name="<?php echo esc_attr($host_key); ?>_bio" rows="3" class="large-text"><?php echo esc_textarea(get_theme_mod($host_key . '_bio', '')); ?></textarea></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="<?php echo esc_attr($host_key); ?>_twitter">Twitter URL</label></th>
                                    <td><input type="url" id="<?php echo esc_attr($host_key); ?>_twitter" name="<?php echo esc_attr($host_key); ?>_twitter" class="regular-text" value="<?php echo esc_url(get_theme_mod($host_key . '_twitter', '')); ?>" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="<?php echo esc_attr($host_key); ?>_youtube">YouTube URL</label></th>
                                    <td><input type="url" id="<?php echo esc_attr($host_key); ?>_youtube" name="<?php echo esc_attr($host_key); ?>_youtube" class="regular-text" value="<?php echo esc_url(get_theme_mod($host_key . '_youtube', '')); ?>" /></td>
                                </tr>
                            </table>
                            <p class="description">💡 プロフィール画像は<a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=contentfreaks_podcast_settings')); ?>">外観 → カスタマイズ</a>で設定できます。</p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php submit_button('ホスト設定を保存', 'primary', 'save_host_settings'); ?>
            </form>

        <?php elseif ($current_tab === 'mediakit'): ?>
            <!-- ===== メディアキット ===== -->
            <div class="postbox">
                <h2 class="hndle">数値・実績設定</h2>
                <div class="inside">
                    <form method="post">
                        <?php wp_nonce_field('contentfreaks_mediakit', 'mediakit_nonce'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="listener_count">リスナー数</label></th>
                                <td>
                                    <input type="number" id="listener_count" name="listener_count" min="0"
                                           value="<?php echo esc_attr(get_option('contentfreaks_listener_count', '1500')); ?>" style="width: 150px;" />
                                    <p class="description">フロントページとプロフィールページに表示されます。</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mk_spotify_followers">Spotify フォロワー数</label></th>
                                <td><input type="text" id="mk_spotify_followers" name="mk_spotify_followers" class="regular-text" value="<?php echo esc_attr(get_theme_mod('mk_spotify_followers', '300')); ?>" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mk_apple_followers">Apple Podcasts フォロワー数</label></th>
                                <td><input type="text" id="mk_apple_followers" name="mk_apple_followers" class="regular-text" value="<?php echo esc_attr(get_theme_mod('mk_apple_followers', '150')); ?>" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mk_youtube_subscribers">YouTube 登録者数</label></th>
                                <td><input type="text" id="mk_youtube_subscribers" name="mk_youtube_subscribers" class="regular-text" value="<?php echo esc_attr(get_theme_mod('mk_youtube_subscribers', '900')); ?>" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mk_monthly_plays">月間再生数</label></th>
                                <td>
                                    <input type="text" id="mk_monthly_plays" name="mk_monthly_plays" class="regular-text" value="<?php echo esc_attr(get_theme_mod('mk_monthly_plays', '')); ?>" />
                                    <p class="description">空欄で非表示。</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mk_frequency">配信頻度</label></th>
                                <td><input type="text" id="mk_frequency" name="mk_frequency" class="regular-text" value="<?php echo esc_attr(get_theme_mod('mk_frequency', '毎週配信')); ?>" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mk_since">配信開始時期</label></th>
                                <td><input type="text" id="mk_since" name="mk_since" class="regular-text" value="<?php echo esc_attr(get_theme_mod('mk_since', '2023年')); ?>" /></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mk_amazon_tag">Amazon アソシエイトタグ</label></th>
                                <td>
                                    <input type="text" id="mk_amazon_tag" name="mk_amazon_tag" class="regular-text" value="<?php echo esc_attr(get_theme_mod('mk_amazon_tag', '')); ?>" />
                                    <p class="description">例: contentsfreaks-22</p>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button('メディアキット設定を保存', 'primary', 'save_mediakit_settings'); ?>
                    </form>
                </div>
            </div>

        <?php elseif ($current_tab === 'tools'): ?>
            <!-- ===== ツール ===== -->
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">操作メニュー</h2>
                <div class="inside">
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('contentfreaks_sync', 'sync_nonce'); ?>
                            <input type="submit" name="manual_sync" class="button-primary" value="📥 手動同期実行" />
                        </form>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('contentfreaks_re_extract_tags', 're_extract_tags_nonce'); ?>
                            <input type="submit" name="re_extract_tags" class="button-secondary" value="🏷️ タグ再抽出" />
                        </form>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('contentfreaks_clear_cache', 'clear_cache_nonce'); ?>
                            <input type="submit" name="clear_cache" class="button-secondary" value="🗑️ キャッシュクリア" />
                        </form>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('contentfreaks_flush_rewrite_rules', 'flush_rewrite_rules_nonce'); ?>
                            <input type="submit" name="flush_rewrite_rules" class="button-secondary" value="🔄 リライトルール更新" />
                        </form>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('contentfreaks_test_rss', 'test_rss_nonce'); ?>
                            <input type="submit" name="test_rss" class="button-secondary" value="🔍 RSS接続テスト" />
                        </form>
                        <form method="post" style="display: inline;">
                            <?php wp_nonce_field('contentfreaks_test_url', 'test_url_nonce'); ?>
                            <input type="submit" name="test_url" class="button-secondary" value="🌐 URL構造テスト" />
                        </form>
                    </div>
                </div>
            </div>

            <?php
            // URL構造テスト結果
            if (isset($_POST['test_url']) && wp_verify_nonce($_POST['test_url_nonce'], 'contentfreaks_test_url')) {
                echo '<div class="postbox" style="margin-bottom: 20px;">';
                echo '<h2 class="hndle">🌐 URL構造テスト結果</h2>';
                echo '<div class="inside">';
                echo '<h4>現在のURL設定</h4>';
                echo '<ul>';
                echo '<li><strong>サイトURL:</strong> ' . esc_html(home_url()) . '</li>';
                echo '<li><strong>エピソードURL:</strong> ' . esc_html(home_url('/episodes/')) . '</li>';
                echo '<li><strong>パーマリンク構造:</strong> ' . esc_html(get_option('permalink_structure') ?: 'デフォルト') . '</li>';
                echo '</ul>';
                echo '<h4>リライトルール状態</h4>';
                $rewrite_rules = get_option('rewrite_rules', array());
                $episodes_rules = array();
                if (is_array($rewrite_rules)) {
                    foreach ($rewrite_rules as $pattern => $rewrite) {
                        if (strpos($pattern, 'episodes') !== false) {
                            $episodes_rules[$pattern] = $rewrite;
                        }
                    }
                }
                if (!empty($episodes_rules)) {
                    echo '<p style="color: green;">✅ エピソード関連のリライトルールが見つかりました:</p><ul>';
                    foreach ($episodes_rules as $pattern => $rewrite) {
                        echo '<li><code>' . esc_html($pattern) . '</code> → <code>' . esc_html($rewrite) . '</code></li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p style="color: red;">❌ エピソード関連のリライトルールが見つかりません。</p>';
                }
                echo '<h4>ファイル・ページ存在チェック</h4><ul>';
                echo '<li><strong>page-episodes.php:</strong> ' . (file_exists(get_stylesheet_directory() . '/page-episodes.php') ? '✅ 存在' : '❌ 不存在') . '</li>';
                echo '<li><strong>episodes固定ページ:</strong> ' . (get_page_by_path('episodes') ? '✅ 存在' : '❌ 不存在') . '</li>';
                echo '</ul></div></div>';
            }

            // RSSフィードテスト結果
            if (isset($_POST['test_rss']) && wp_verify_nonce($_POST['test_rss_nonce'], 'contentfreaks_test_rss')) {
                echo '<div class="postbox" style="margin-bottom: 20px;">';
                echo '<h2 class="hndle">🔍 RSSフィードテスト結果</h2>';
                echo '<div class="inside">';
                contentfreaks_clear_rss_cache();
                $episodes = contentfreaks_get_rss_episodes(5);
                if (!empty($episodes)) {
                    echo '<p style="color: green;">✅ RSS取得成功！ ' . count($episodes) . ' 件のエピソードを取得</p>';
                    echo '<div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">';
                    foreach ($episodes as $episode) {
                        echo '<div style="background: white; padding: 15px; margin-bottom: 10px; border-radius: 5px; border-left: 4px solid #2196F3;">';
                        echo '<h4 style="margin: 0 0 10px 0;">' . esc_html($episode['title']) . '</h4>';
                        if (!empty($episode['thumbnail'])) {
                            echo '<p>🖼️ サムネイル: <a href="' . esc_url($episode['thumbnail']) . '" target="_blank">画像を確認</a></p>';
                        } else {
                            echo '<p>❌ サムネイル: 見つかりません</p>';
                        }
                        preg_match_all('/『([^』]+)』/', $episode['title'], $tag_matches);
                        if (!empty($tag_matches[1])) {
                            echo '<p>🏷️ タグ候補: <span style="color: #0073aa;">' . esc_html(implode(', ', $tag_matches[1])) . '</span></p>';
                        }
                        echo '<p>📅 日付: ' . esc_html($episode['formatted_date']) . '</p>';
                        echo '<p>🎵 音声URL: ' . ($episode['audio_url'] ? '✅ あり' : '❌ なし') . '</p>';
                        echo '<p>⏱️ 再生時間: ' . ($episode['duration'] ? esc_html($episode['duration']) : '不明') . '</p>';
                        if (!empty($episode['guid'])) {
                            echo '<p>🔗 GUID: <code>' . esc_html($episode['guid']) . '</code></p>';
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p style="color: red;">❌ エラー: エピソードを取得できませんでした</p>';
                }
                echo '</div></div>';
            }
            ?>

            <!-- ヘルプ情報 -->
            <div class="postbox">
                <h2 class="hndle">ℹ️ 情報・ヘルプ</h2>
                <div class="inside">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                        <div style="background: #f0f8ff; padding: 15px; border-left: 4px solid #2196F3;">
                            <h4>🏷️ 自動タグ機能</h4>
                            <p><strong>機能:</strong> タイトルの『』内テキストを自動でタグ追加</p>
                            <p><strong>例:</strong> 「第1回『YouTube』について語る」 → 「YouTube」タグを自動作成</p>
                        </div>
                        <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;">
                            <h4>🔧 コンテンツ分類</h4>
                            <p><strong>方針:</strong> 手動分類のみ。自動分類は行いません</p>
                            <p><strong>RSS同期:</strong> RSSから取得した投稿は自動でポッドキャストエピソードに設定</p>
                        </div>
                        <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa;">
                            <h4>📡 RSS同期情報</h4>
                            <p><strong>RSS URL:</strong> https://anchor.fm/s/d8cfdc48/podcast/rss</p>
                            <p><strong>スケジュール:</strong> 1時間毎の自動同期</p>
                            <p><a href="<?php echo esc_url(home_url('/episodes/')); ?>" target="_blank">エピソード一覧ページ →</a></p>
                        </div>
                        <div style="background: #fffbf0; padding: 15px; border-left: 4px solid #ff9800;">
                            <h4>🔧 トラブルシューティング</h4>
                            <p><strong>404エラー:</strong> 「リライトルール更新」をクリック</p>
                            <p><strong>キャッシュ:</strong> 「キャッシュクリア」でRSSキャッシュをリセット</p>
                            <p><strong>その他:</strong> 設定 → パーマリンクで「変更を保存」</p>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        </div>
    </div>
    <?php
}

/**
 * 最近の更新記録を表示
 */
function contentfreaks_display_recent_updates() {
    global $wpdb;
    
    // 最近更新されたエピソードを取得
    $recent_updates = $wpdb->get_results("
        SELECT p.ID, p.post_title, pm.meta_value as last_updated
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'episode_last_updated'
        WHERE p.post_type = 'post' 
        AND pm.meta_value IS NOT NULL
        ORDER BY pm.meta_value DESC
        LIMIT 10
    ");
    
    if (!empty($recent_updates)) {
        echo '<div style="max-height: 300px; overflow-y: auto;">';
        echo '<table class="widefat">';
        echo '<thead><tr><th>記事タイトル</th><th>最終更新</th><th>操作</th></tr></thead>';
        echo '<tbody>';
        
        foreach ($recent_updates as $update) {
            $update_time = date('Y年n月j日 H:i:s', strtotime($update->last_updated));
            $edit_link = get_edit_post_link($update->ID);
            
            echo '<tr>';
            echo '<td>' . esc_html($update->post_title) . '</td>';
            echo '<td>' . $update_time . '</td>';
            echo '<td><a href="' . $edit_link . '" class="button button-small">編集</a></td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
        echo '</div>';
    } else {
        echo '<p>最近の更新はありません。</p>';
    }
}

/**
 * 更新ログを表示
 */
function contentfreaks_display_update_logs() {
    $logs = get_option('contentfreaks_update_logs', array());
    
    if (!empty($logs)) {
        echo '<div style="max-height: 400px; overflow-y: auto;">';
        echo '<table class="widefat">';
        echo '<thead><tr><th>日時</th><th>記事タイトル</th><th>更新タイプ</th><th>詳細</th></tr></thead>';
        echo '<tbody>';
        
        foreach (array_slice($logs, 0, 30) as $log) {
            $timestamp = date('Y年n月j日 H:i:s', strtotime($log['timestamp']));
            
            echo '<tr>';
            echo '<td>' . $timestamp . '</td>';
            echo '<td>' . esc_html($log['post_title']) . '</td>';
            echo '<td>' . esc_html($log['update_type']) . '</td>';
            echo '<td>' . esc_html($log['details']) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
        echo '</div>';
        
        if (count($logs) > 30) {
            echo '<p><small>最新の30件を表示しています。（全' . count($logs) . '件）</small></p>';
        }
    } else {
        echo '<p>更新ログはありません。</p>';
    }
}

/**
 * RSSから直接エピソードデータを取得（キャッシュ機能付き）
 */
function contentfreaks_get_rss_episodes($limit = 0) {
    $spotify_rss_url = 'https://anchor.fm/s/d8cfdc48/podcast/rss';
    
    // キャッシュキー（0は全件取得を意味する）
    $cache_key = $limit > 0 ? 'contentfreaks_rss_episodes_' . $limit : 'contentfreaks_rss_episodes_all';
    $cached_data = get_transient($cache_key);
    
    if ($cached_data !== false) {
        return $cached_data;
    }
    
    $feed = fetch_feed($spotify_rss_url);
    
    if (is_wp_error($feed)) {
        error_log('RSS取得エラー: ' . $feed->get_error_message());
        return array();
    }
    
    // 0を指定すると全件取得
    $items = $limit > 0 ? $feed->get_items(0, $limit) : $feed->get_items();
    $episodes = array();
    
    if (empty($items)) {
        error_log('RSSフィードにアイテムが見つかりません');
        return array();
    }
    
    foreach ($items as $item) {
        $title = $item->get_title();
        $description = $item->get_description();
        $pub_date = $item->get_date('Y-m-d H:i:s');
        $link = $item->get_link();
        $guid = $item->get_id(); // GUIDを取得
        
        // 音声ファイルURL取得
        $audio_url = '';
        $enclosure = $item->get_enclosure();
        if ($enclosure) {
            $original_url = $enclosure->get_link();
            if ($original_url) {
                // Anchor.fm URLをCloudFront URLに変換
                if (strpos($original_url, 'anchor.fm') !== false) {
                    $audio_url = str_replace('https://anchor.fm/s/d8cfdc48/podcast/play/', 'https://d3ctxlq1ktw2nl.cloudfront.net/', $original_url);
                    $audio_url = str_replace('/play/', '/', $audio_url);
                } else {
                    $audio_url = $original_url;
                }
            }
        }
        
        // エピソード番号を抽出
        $episode_number = '';
        if (preg_match('/[#＃](\d+)/', $title, $matches)) {
            $episode_number = $matches[1];
        }
        
        // 再生時間を抽出
        $duration = '';
        if ($enclosure && method_exists($enclosure, 'get_duration')) {
            $duration_seconds = $enclosure->get_duration();
            if ($duration_seconds) {
                $minutes = floor($duration_seconds / 60);
                $seconds = $duration_seconds % 60;
                $duration = sprintf('%d:%02d', $minutes, $seconds);
            }
        }
        
        // カテゴリーを抽出（簡単な分類）
        $category = 'エピソード';
        if (strpos(strtolower($title), 'special') !== false || strpos($title, 'スペシャル') !== false) {
            $category = 'スペシャル';
        }
        
        // サムネイル画像
        $thumbnail = '';
        
        // 方法1: iTunesタグからサムネイルを取得
        if (method_exists($item, 'get_item_tags')) {
            $item_tags = $item->get_item_tags('http://www.itunes.com/dtds/podcast-1.0.dtd', 'image');
            if (!empty($item_tags[0]['attribs']['']['href'])) {
                $thumbnail = $item_tags[0]['attribs']['']['href'];
            }
        }
        
        // 方法2: フィードレベルのimage要素を確認
        if (empty($thumbnail)) {
            $feed_image = $feed->get_image_url();
            if (!empty($feed_image)) {
                $thumbnail = $feed_image;
            }
        }
        
        // 方法3: メディア要素のサムネイルを検索
        if (empty($thumbnail)) {
            $enclosure = $item->get_enclosure();
            if ($enclosure && method_exists($enclosure, 'get_thumbnail')) {
                $thumbnail = $enclosure->get_thumbnail();
            }
        }
        
        // 方法4: descriptionからimg srcを抽出
        if (empty($thumbnail)) {
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $description, $matches)) {
                $thumbnail = $matches[1];
            }
        }
        
        // 方法5: Anchor.fmの一般的なサムネイルパターンを試す
        if (empty($thumbnail)) {
            // Anchor.fmのデフォルトサムネイルパターン
            if (preg_match('/anchor\.fm\/s\/([^\/]+)/', $link, $matches)) {
                $show_id = $matches[1];
                $thumbnail = 'https://d3t3ozftmdmh3i.cloudfront.net/production/podcast_uploaded_nologo/' . $show_id . '/artwork.png';
            }
        }
        
        $episodes[] = array(
            'title' => $title,
            'description' => wp_trim_words(strip_tags($description), 30),
            'full_description' => $description,
            'pub_date' => $pub_date,
            'formatted_date' => date('Y年n月j日', strtotime($pub_date)),
            'link' => $link,
            'guid' => $guid, // GUIDを追加
            'audio_url' => $audio_url,
            'episode_number' => $episode_number,
            'duration' => $duration,
            'category' => $category,
            'thumbnail' => $thumbnail
        );
    }
    
    // キャッシュ時間を1時間に延長（RSSは頻繁に更新されないため）
    set_transient($cache_key, $episodes, HOUR_IN_SECONDS);
    
    return $episodes;
}

/**
 * RSSエピソード数を取得
 */
function contentfreaks_get_rss_episode_count() {
    $cache_key = 'contentfreaks_rss_count';
    $cached_count = get_transient($cache_key);
    
    if ($cached_count !== false) {
        return $cached_count;
    }
    
    $spotify_rss_url = 'https://anchor.fm/s/d8cfdc48/podcast/rss';
    $feed = fetch_feed($spotify_rss_url);
    
    if (is_wp_error($feed)) {
        return 0;
    }
    
    // 全エピソードを取得してカウント
    $items = $feed->get_items();
    $count = count($items);
    
    // 1時間キャッシュ
    set_transient($cache_key, $count, HOUR_IN_SECONDS);
    
    return $count;
}

/**
 * 音声URLの二重エンコーディングを修正するヘルパー関数
 * CloudFrontの二重エンコード問題に対応
 */
function contentfreaks_fix_audio_url($url) {
    if (empty($url)) return '';
    
    $fixed = $url;
    if (strpos($url, 'https%3A%2F%2F') !== false) {
        if (preg_match('/https:\/\/d3ctxlq1ktw2nl\.cloudfront\.net\/\d+\/https%3A%2F%2Fd3ctxlq1ktw2nl\.cloudfront\.net%2F(.+)/', $url, $matches)) {
            $correct_path = urldecode($matches[1]);
            $fixed = 'https://d3ctxlq1ktw2nl.cloudfront.net/' . $correct_path;
        }
    }
    // 一般的なURLデコード（念のため）
    if (strpos($fixed, '%') !== false && strpos($fixed, 'https%3A') !== false) {
        $fixed = urldecode($fixed);
    }
    return $fixed;
}

/**
 * AJAX: ランダムエピソードを取得
 */
function contentfreaks_random_episode() {
    if (!check_ajax_referer('contentfreaks_load_more', 'nonce', false)) {
        wp_send_json_error('Security check failed');
    }
    
    $random_query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 1,
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'orderby' => 'rand',
    ));
    
    if ($random_query->have_posts()) {
        $random_query->the_post();
        $url = get_permalink();
        $title = get_the_title();
        $ep_number = get_post_meta(get_the_ID(), 'episode_number', true);
        wp_reset_postdata();
        wp_send_json_success(array(
            'url' => $url,
            'title' => $title,
            'episode_number' => $ep_number
        ));
    } else {
        wp_send_json_error('No episodes found');
    }
}
add_action('wp_ajax_random_episode', 'contentfreaks_random_episode');
add_action('wp_ajax_nopriv_random_episode', 'contentfreaks_random_episode');

/**
 * AJAX: エピソードリアクションの保存
 */
function contentfreaks_save_reaction() {
    if (!check_ajax_referer('contentfreaks_load_more', 'nonce', false)) {
        wp_send_json_error('Security check failed');
    }
    
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $reaction = isset($_POST['reaction']) ? sanitize_text_field($_POST['reaction']) : '';
    
    $allowed = array('fire', 'laugh', 'idea', 'cry', 'heart');
    if (!$post_id || !in_array($reaction, $allowed, true)) {
        wp_send_json_error('Invalid parameters');
    }
    
    $meta_key = 'reaction_' . $reaction;
    $current = (int) get_post_meta($post_id, $meta_key, true);
    update_post_meta($post_id, $meta_key, $current + 1);
    
    // 全リアクション数を返す
    $counts = array();
    foreach ($allowed as $r) {
        $counts[$r] = (int) get_post_meta($post_id, 'reaction_' . $r, true);
    }
    
    wp_send_json_success(array('counts' => $counts));
}
add_action('wp_ajax_save_reaction', 'contentfreaks_save_reaction');
add_action('wp_ajax_nopriv_save_reaction', 'contentfreaks_save_reaction');

/**
 * AJAX: エピソードリアクション数を取得
 */
function contentfreaks_get_reactions() {
    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
    if (!$post_id) {
        wp_send_json_error('Invalid post ID');
    }
    
    $allowed = array('fire', 'laugh', 'idea', 'cry', 'heart');
    $counts = array();
    foreach ($allowed as $r) {
        $counts[$r] = (int) get_post_meta($post_id, 'reaction_' . $r, true);
    }
    
    wp_send_json_success(array('counts' => $counts));
}
add_action('wp_ajax_get_reactions', 'contentfreaks_get_reactions');
add_action('wp_ajax_nopriv_get_reactions', 'contentfreaks_get_reactions');

/**
 * AJAX: ブログ記事の追加読み込み
 */
function contentfreaks_load_more_blog() {
    if (!check_ajax_referer('contentfreaks_load_more', 'nonce', false)) {
        wp_send_json_error('Security check failed');
    }
    
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 12;
    
    $blog_query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => $limit,
        'offset' => $offset,
        'meta_query' => array(
            array(
                'key' => 'is_podcast_episode',
                'compare' => 'NOT EXISTS'
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    if (!$blog_query->have_posts()) {
        wp_send_json_error('No more posts');
    }
    
    ob_start();
    while ($blog_query->have_posts()) : $blog_query->the_post();
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
    
    $html = ob_get_clean();
    
    $next_query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 1,
        'offset' => $offset + $limit,
        'meta_query' => array(
            array('key' => 'is_podcast_episode', 'compare' => 'NOT EXISTS')
        ),
    ));
    
    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $next_query->have_posts()
    ));
}
add_action('wp_ajax_load_more_blog', 'contentfreaks_load_more_blog');
add_action('wp_ajax_nopriv_load_more_blog', 'contentfreaks_load_more_blog');

/**
 * AJAX: エピソードページ用の無限スクロール
 */
function contentfreaks_load_more_episodes() {
    // セキュリティチェック（nonce検証）
    if (!check_ajax_referer('contentfreaks_load_more', 'nonce', false)) {
        wp_send_json_error('Security check failed');
    }
    
    if (!isset($_POST['offset']) || !isset($_POST['limit'])) {
        wp_send_json_error('Invalid parameters');
    }
    
    $offset = intval($_POST['offset']);
    $limit = intval($_POST['limit']);
    
    // エピソードクエリを実行
    $episodes_query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => $limit,
        'offset' => $offset,
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    if (!$episodes_query->have_posts()) {
        wp_send_json_error('No more episodes');
    }
    
    ob_start();
    while ($episodes_query->have_posts()) : $episodes_query->the_post();
        get_template_part('template-parts/episode-card');
    endwhile;
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    // 次のページもあるかチェック
    $next_offset = $offset + $limit;
    $next_query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 1,
        'offset' => $next_offset,
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    $has_more = $next_query->have_posts();
    wp_reset_postdata();
    
    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more
    ));
}
add_action('wp_ajax_load_more_episodes', 'contentfreaks_load_more_episodes');
add_action('wp_ajax_nopriv_load_more_episodes', 'contentfreaks_load_more_episodes');

/**
 * テーマサポートとメニューの登録（統合版）
 */
function contentfreaks_theme_setup() {
    // カスタムメニューのサポートを追加
    add_theme_support('menus');
    
    // メニューの場所を登録
    register_nav_menus(array(
        'primary' => 'プライマリメニュー（ヘッダー）',
        'header' => 'ヘッダーメニュー',
        'footer' => 'フッターメニュー',
    ));
}
add_action('after_setup_theme', 'contentfreaks_theme_setup');

/**
 * ページのURLを取得するヘルパー関数（静的キャッシュ付き）
 */
function contentfreaks_get_page_url($slug) {
    static $cache = array();
    if (isset($cache[$slug])) return $cache[$slug];
    $page = get_page_by_path($slug);
    $url = $page ? get_permalink($page->ID) : home_url('/' . $slug . '/');
    $cache[$slug] = $url;
    return $url;
}

/**
 * 必要なページが存在するかチェックし、なければ作成する
 */
function contentfreaks_create_pages() {
    // 既に作成済みならスキップ（毎リクエストでの不要なDBクエリを削減）
    if (get_option('contentfreaks_pages_created')) return;
    
    $pages = array(
        'blog' => array(
            'title' => 'ブログ',
            'template' => 'page-blog.php'
        ),
        'episodes' => array(
            'title' => 'エピソード',
            'template' => 'page-episodes.php'
        ),
        'profile' => array(
            'title' => 'プロフィール',
            'template' => 'page-profile.php'
        ),
        'history' => array(
            'title' => '歴史',
            'template' => 'page-history.php'
        )
    );
    
    foreach ($pages as $slug => $page_data) {
        $existing_page = get_page_by_path($slug);
        if (!$existing_page) {
            $page_id = wp_insert_post(array(
                'post_title' => $page_data['title'],
                'post_name' => $slug,
                'post_status' => 'publish',
                'post_type' => 'page'
            ));
            
            if ($page_id && !is_wp_error($page_id)) {
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            }
        }
    }
    
    update_option('contentfreaks_pages_created', true);
}
add_action('after_switch_theme', 'contentfreaks_create_pages');
// initでも初回のみ実行（フラグがない場合のフォールバック）
add_action('init', 'contentfreaks_create_pages');

/**
 * エピソードページのリライトルールとテンプレート統一（修正版）
 */
function contentfreaks_episodes_rewrite_rules() {
    // カスタムリライトルールを追加
    add_rewrite_rule('^episodes/?$', 'index.php?pagename=episodes', 'top');
    add_rewrite_rule('^episodes/page/([0-9]+)/?$', 'index.php?pagename=episodes&paged=$matches[1]', 'top');
    
    // 追加のリライトルール（フォールバック）
    add_rewrite_rule('^episodes/?([^/]*)/?$', 'index.php?pagename=episodes&episodes_param=$matches[1]', 'top');
}
add_action('init', 'contentfreaks_episodes_rewrite_rules');

/**
 * クエリ変数を追加
 */
function contentfreaks_add_query_vars($vars) {
    $vars[] = 'episodes';
    $vars[] = 'episodes_param';
    return $vars;
}
add_filter('query_vars', 'contentfreaks_add_query_vars');

/**
 * テンプレート読み込み統一（page-episodes.phpに統一）- 強化版
 */
function contentfreaks_episodes_template_redirect() {
    global $wp_query;
    
    // episodes URLパターンを検出
    $request_uri = $_SERVER['REQUEST_URI'];
    $is_episodes_request = (
        get_query_var('episodes') || 
        is_page('episodes') || 
        strpos($request_uri, '/episodes') !== false ||
        get_query_var('pagename') === 'episodes'
    );
    
    if ($is_episodes_request) {
        $episodes_template = get_stylesheet_directory() . '/page-episodes.php';
        if (file_exists($episodes_template)) {
            // ページが見つからない場合のステータスを修正
            status_header(200);
            $wp_query->is_404 = false;
            $wp_query->is_page = true;
            $wp_query->is_singular = true;
            $wp_query->queried_object = get_page_by_path('episodes');
            $wp_query->queried_object_id = $wp_query->queried_object ? $wp_query->queried_object->ID : 0;
            
            // WordPressのクエリ状態をリセット
            $wp_query->init_query_flags();
            $wp_query->is_page = true;
            $wp_query->is_singular = true;
            
            include $episodes_template;
            exit;
        }
    }
}
add_action('template_redirect', 'contentfreaks_episodes_template_redirect');

/**
 * リライトルールを初期化（テーマ用の正しい方法）
 */
function contentfreaks_flush_rewrite_rules() {
    flush_rewrite_rules();
}

/**
 * テーマ有効化時とrequireされた時にリライトルールを更新
 */
function contentfreaks_theme_activation() {
    // リライトルールを追加
    contentfreaks_episodes_rewrite_rules();
    // フラッシュ実行
    flush_rewrite_rules();
}
add_action('after_setup_theme', 'contentfreaks_theme_activation');

/**
 * 404エラーを捕捉してepisodesページを表示するフォールバック
 */
function contentfreaks_404_fallback() {
    global $wp_query;
    
    if (is_404()) {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // /episodes関連のURLの場合
        if (strpos($request_uri, '/episodes') !== false) {
            $episodes_template = get_stylesheet_directory() . '/page-episodes.php';
            if (file_exists($episodes_template)) {
                // 404を解除してepisodesページを表示
                status_header(200);
                $wp_query->is_404 = false;
                $wp_query->is_page = true;
                $wp_query->is_singular = true;
                
                include $episodes_template;
                exit;
            }
        }
    }
}
add_action('template_redirect', 'contentfreaks_404_fallback', 999);

/**
 * 管理者がアクセスした時にリライトルールを自動更新
 */
function contentfreaks_auto_flush_rewrite_rules() {
    $rewrite_rules_option = 'contentfreaks_rewrite_rules_flushed';
    
    // 管理者のみかつ、まだフラッシュしていない場合
    if (current_user_can('manage_options') && !get_option($rewrite_rules_option)) {
        contentfreaks_episodes_rewrite_rules();
        flush_rewrite_rules();
        update_option($rewrite_rules_option, true);
    }
}
add_action('admin_init', 'contentfreaks_auto_flush_rewrite_rules');

/**
 * CSS読み込み状況をデバッグ（開発環境のみ）
 * 本番環境ではコメントアウト推奨
 */
/*
function contentfreaks_css_debug() {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }
    // デバッグ情報は開発時のみ有効化
}
add_action('wp_head', 'contentfreaks_css_debug');
*/

/**
 * Cocoonの競合するスタイルを無効化
 */
function contentfreaks_disable_conflicting_styles() {
    // Cocoonの一部スタイルを無効化してContentFreaks専用スタイルを優先
    wp_dequeue_style('cocoon-child-style'); // 子テーマの自動読み込みを無効化
    
    // Cocoonのヘッダー関連CSSを無効化
    add_filter('cocoon_header_style_enable', '__return_false');
    add_filter('cocoon_header_layout_enable', '__return_false');
}
add_action('wp_enqueue_scripts', 'contentfreaks_disable_conflicting_styles', 5);

/**
 * HTTP/2 Server Push ヘッダーを追加してパフォーマンスを最適化
 */
function contentfreaks_http2_server_push() {
    // クリティカルリソースをServer Pushで先行送信
    $push_resources = array();
    
    // メインスタイルシート
    $push_resources[] = '<' . get_stylesheet_directory_uri() . '/style.css>; rel=preload; as=style';
    $push_resources[] = '<' . get_stylesheet_directory_uri() . '/components.css>; rel=preload; as=style';
    
    // ページ別CSS
    if (is_front_page()) {
        $push_resources[] = '<' . get_stylesheet_directory_uri() . '/front-page.css>; rel=preload; as=style';
    } elseif (is_single()) {
        $push_resources[] = '<' . get_stylesheet_directory_uri() . '/single.css>; rel=preload; as=style';
    }
    
    // フォント（enqueue_scripts.php と同じURLを使用）
    $push_resources[] = '<https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;700&display=swap>; rel=preload; as=style';
    
    // Linkヘッダーとして送信
    if (!empty($push_resources)) {
        header('Link: ' . implode(', ', $push_resources), false);
    }
}
add_action('send_headers', 'contentfreaks_http2_server_push');

/**
 * 管理画面のカスタムスタイル
 */
function contentfreaks_admin_styles() {
    wp_enqueue_style(
        'contentfreaks-admin',
        get_stylesheet_directory_uri() . '/admin.css',
        array(),
        '1.0.0'
    );
}
add_action('admin_enqueue_scripts', 'contentfreaks_admin_styles');

/**
 * ========================================
 * コンテンツ分離システム（手動分類のみ）
 * ポッドキャストエピソードとブログ記事の分類
 * ========================================
 */

/**
 * RSS同期時のポッドキャストエピソード自動設定
 * RSS経由の投稿のみ自動でポッドキャストエピソードに設定
 */
function contentfreaks_mark_rss_posts_as_podcast($post_id) {
    // RSS同期関数から呼ばれた場合のみ自動設定
    if (defined('CONTENTFREAKS_RSS_SYNC') && CONTENTFREAKS_RSS_SYNC) {
        update_post_meta($post_id, 'is_podcast_episode', '1');
        
        // エピソード番号を自動抽出
        $post = get_post($post_id);
        if ($post && preg_match('/[#＃](\d+)/', $post->post_title, $matches)) {
            update_post_meta($post_id, 'episode_number', $matches[1]);
        }
    }
}

/**
 * ポッドキャストクエリのカスタマイズ（統合版・修正版）
 */
function contentfreaks_modify_podcast_query($query) {
    // 管理画面またはメインクエリでない場合は処理しない
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    // エピソードページ（page-episodes.php）でポッドキャストのみ表示
    if ((is_page('episodes') || get_query_var('episodes')) && !is_404()) {
        $query->set('post_type', 'post');
        $query->set('meta_key', 'is_podcast_episode');
        $query->set('meta_value', '1');
        $query->set('posts_per_page', 12);
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
        
        // 404エラーを回避
        $query->is_404 = false;
        $query->is_page = true;
    }

    // ブログページでポッドキャスト以外を表示
    if (is_page('blog')) {
        $query->set('meta_query', array(
            array(
                'key' => 'is_podcast_episode',
                'compare' => 'NOT EXISTS'
            )
        ));
    }
}
add_action('pre_get_posts', 'contentfreaks_modify_podcast_query');

/**
 * コンテンツタイプ判定ヘルパー関数
 */
function contentfreaks_is_podcast_episode($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, 'is_podcast_episode', true) === '1';
}

/**
 * ポッドキャスト専用メタボックスの追加
 */
function contentfreaks_add_podcast_meta_box() {
    add_meta_box(
        'contentfreaks_podcast_meta',
        'ポッドキャストエピソード設定',
        'contentfreaks_podcast_meta_box_callback',
        'post',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'contentfreaks_add_podcast_meta_box');

/**
 * ポッドキャストメタボックスのコールバック
 */
function contentfreaks_podcast_meta_box_callback($post) {
    wp_nonce_field('contentfreaks_podcast_meta_nonce', 'contentfreaks_podcast_meta_nonce');
    
    $is_podcast = get_post_meta($post->ID, 'is_podcast_episode', true);
    $episode_number = get_post_meta($post->ID, 'episode_number', true);
    $duration = get_post_meta($post->ID, 'episode_duration', true);
    $audio_url = get_post_meta($post->ID, 'episode_audio_url', true);
    
    echo '<table class="form-table">';
    
    echo '<tr>';
    echo '<th scope="row"><label for="is_podcast_episode">ポッドキャストエピソード</label></th>';
    echo '<td><input type="checkbox" id="is_podcast_episode" name="is_podcast_episode" value="1" ' . checked($is_podcast, '1', false) . ' /></td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<th scope="row"><label for="episode_number">エピソード番号</label></th>';
    echo '<td><input type="number" id="episode_number" name="episode_number" value="' . esc_attr($episode_number) . '" /></td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<th scope="row"><label for="episode_duration">再生時間</label></th>';
    echo '<td><input type="text" id="episode_duration" name="episode_duration" value="' . esc_attr($duration) . '" placeholder="例: 45:30" /></td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<th scope="row"><label for="episode_audio_url">音声ファイルURL</label></th>';
    echo '<td>';
    echo '<input type="url" id="episode_audio_url" name="episode_audio_url" value="' . esc_attr($audio_url) . '" style="width: 100%;" placeholder="https://example.com/audio.mp3" />';
    echo '<p class="description">音声ファイルのURLを入力すると、投稿ページに音声プレイヤーが表示されます。（ポッドキャストエピソードでなくても利用可能）<br>';
    echo '<strong>対応形式:</strong> MP3, M4A, AAC, OGG, WAV<br>';
    echo '<strong>推奨:</strong> MP3形式（最も互換性が高い）</p>';
    echo '</td>';
    echo '</tr>';
    
    echo '</table>';
}

/**
 * ポッドキャストメタデータの保存
 */
/**
 * ポッドキャストメタデータの保存（シンプル版）
 */
function contentfreaks_save_podcast_meta($post_id) {
    if (!isset($_POST['contentfreaks_podcast_meta_nonce']) || 
        !wp_verify_nonce($_POST['contentfreaks_podcast_meta_nonce'], 'contentfreaks_podcast_meta_nonce')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // ポッドキャストエピソードフラグ（シンプルに保存）
    if (isset($_POST['is_podcast_episode'])) {
        update_post_meta($post_id, 'is_podcast_episode', '1');
    } else {
        delete_post_meta($post_id, 'is_podcast_episode');
    }

    // エピソード番号
    if (isset($_POST['episode_number'])) {
        update_post_meta($post_id, 'episode_number', sanitize_text_field($_POST['episode_number']));
    }

    // 再生時間
    if (isset($_POST['episode_duration'])) {
        update_post_meta($post_id, 'episode_duration', sanitize_text_field($_POST['episode_duration']));
    }

    // 音声ファイルURL
    if (isset($_POST['episode_audio_url'])) {
        $audio_url = sanitize_url($_POST['episode_audio_url']);
        if (!empty($audio_url)) {
            update_post_meta($post_id, 'episode_audio_url', $audio_url);
        } else {
            delete_post_meta($post_id, 'episode_audio_url');
        }
    }
}
add_action('save_post', 'contentfreaks_save_podcast_meta', 10);

/**
 * 管理画面の投稿一覧にポッドキャストカラムを追加
 */
function contentfreaks_add_podcast_column($columns) {
    $columns['is_podcast'] = 'ポッドキャスト';
    return $columns;
}
add_filter('manage_posts_columns', 'contentfreaks_add_podcast_column');

/**
 * ポッドキャストカラムの内容を表示
 */
function contentfreaks_show_podcast_column($column, $post_id) {
    if ($column === 'is_podcast') {
        $is_podcast = get_post_meta($post_id, 'is_podcast_episode', true);
        echo $is_podcast === '1' ? '🎙️ エピソード' : '📝 ブログ';
    }
}
add_action('manage_posts_custom_column', 'contentfreaks_show_podcast_column', 10, 2);

/**
 * ポッドキャストカラムでソート可能にする
 */
function contentfreaks_podcast_column_sortable($columns) {
    $columns['is_podcast'] = 'is_podcast';
    return $columns;
}
add_filter('manage_edit-post_sortable_columns', 'contentfreaks_podcast_column_sortable');

/**
 * ポッドキャストカラムのソート処理
 */
function contentfreaks_podcast_column_orderby($query) {
    if (!is_admin()) {
        return;
    }

    $orderby = $query->get('orderby');
    if ($orderby === 'is_podcast') {
        $query->set('meta_key', 'is_podcast_episode');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'contentfreaks_podcast_column_orderby');

/**
 * ポッドキャストカラムをクイック編集可能にする
 */
function contentfreaks_add_podcast_quick_edit($column_name, $post_type) {
    if ($column_name === 'is_podcast' && $post_type === 'post') {
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
                    <span class="title">ポッドキャストエピソード</span>
                    <select name="is_podcast_episode" class="podcast-episode-select">
                        <option value="">選択してください</option>
                        <option value="1">🎙️ エピソード</option>
                        <option value="0">📝 ブログ</option>
                    </select>
                </label>
            </div>
        </fieldset>
        <?php
    }
}
add_action('quick_edit_custom_box', 'contentfreaks_add_podcast_quick_edit', 10, 2);

/**
 * クイック編集時の現在値を取得するJavaScript
 */
function contentfreaks_podcast_quick_edit_js() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // クイック編集ボタンがクリックされた時
        $('.editinline').on('click', function() {
            var post_id = $(this).closest('tr').attr('id').replace('post-', '');
            var $podcast_column = $('#post-' + post_id + ' .column-is_podcast');
            var is_podcast = $podcast_column.text().indexOf('🎙️') !== -1 ? '1' : '0';
            
            // クイック編集フォームに値を設定
            setTimeout(function() {
                $('.podcast-episode-select').val(is_podcast);
            }, 100);
        });
    });
    </script>
    <?php
}
add_action('admin_footer-edit.php', 'contentfreaks_podcast_quick_edit_js');

/**
 * クイック編集時の保存処理（シンプル版）
 */
function contentfreaks_save_podcast_quick_edit($post_id) {
    // クイック編集以外はスキップ
    if (!isset($_POST['action']) || $_POST['action'] !== 'inline-save') {
        return;
    }

    if (!isset($_POST['is_podcast_episode'])) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $is_podcast = sanitize_text_field($_POST['is_podcast_episode']);
    
    // シンプルに保存
    if ($is_podcast === '1') {
        update_post_meta($post_id, 'is_podcast_episode', '1');
    } else {
        delete_post_meta($post_id, 'is_podcast_episode');
    }
}
add_action('save_post', 'contentfreaks_save_podcast_quick_edit', 5);

/**
 * タイトルから『』内のテキストを抽出してタグとして自動追加
 */
function contentfreaks_extract_and_create_tags_from_title($post_id, $title) {
    // 『』内のテキストを抽出（複数対応）
    preg_match_all('/『(.*?)』/u', $title, $matches);
    if (!empty($matches[1])) {
        $tag_names = array();
        foreach ($matches[1] as $tag_text) {
            // #以降を削除
            $clean_tag = explode('#', $tag_text)[0];
            // タグ名をクリーンアップ
            $clean_tag = trim($clean_tag);
            if (!empty($clean_tag)) {
                $tag_names[] = $clean_tag;
                // タグが存在しない場合は新規作成
                if (!term_exists($clean_tag, 'post_tag')) {
                    wp_insert_term($clean_tag, 'post_tag');
                }
            }
        }
        // 投稿にタグを設定（既存タグに追加）
        if (!empty($tag_names)) {
            wp_set_post_tags($post_id, $tag_names, true);
            // ログに記録（デバッグ用）
            error_log('ContentFreaks: 投稿ID ' . $post_id . ' にタグを追加: ' . implode(', ', $tag_names));
        }
    }
}

/**
 * ========================================
 * ユーティリティ関数
 * ========================================
 */

/**
 * ポッドキャストエピソード数を取得（キャッシュ利用）
 */
function contentfreaks_get_podcast_count() {
    $count = get_transient('contentfreaks_podcast_count');
    if ($count !== false) {
        return (int) $count;
    }
    $query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'post_status' => 'publish'
    ));
    $count = $query->found_posts;
    set_transient('contentfreaks_podcast_count', $count, HOUR_IN_SECONDS);
    return $count;
}

/**
 * ブログ記事数を取得（キャッシュ利用）
 */
function contentfreaks_get_blog_count() {
    $count = get_transient('contentfreaks_blog_count');
    if ($count !== false) {
        return (int) $count;
    }
    $query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'is_podcast_episode',
                'compare' => 'NOT EXISTS'
            )
        ),
        'post_status' => 'publish'
    ));
    $count = $query->found_posts;
    set_transient('contentfreaks_blog_count', $count, HOUR_IN_SECONDS);
    return $count;
}

/**
 * 記事の保存・削除時にカウントキャッシュをクリア
 */
function contentfreaks_clear_count_cache() {
    delete_transient('contentfreaks_podcast_count');
    delete_transient('contentfreaks_blog_count');
}
add_action('save_post', 'contentfreaks_clear_count_cache');
add_action('delete_post', 'contentfreaks_clear_count_cache');

/**
 * 最新ポッドキャストエピソードを取得
 */
function contentfreaks_get_latest_podcast($limit = 5) {
    return new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => $limit,
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'orderby' => 'date',
        'order' => 'DESC'
    ));
}

/**
 * 最新ブログ記事を取得
 */
function contentfreaks_get_latest_blog($limit = 5) {
    return new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => $limit,
        'meta_query' => array(
            array(
                'key' => 'is_podcast_episode',
                'compare' => 'NOT EXISTS'
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC'
    ));
}

/**
 * 画像のLazy Loading最適化
 * WordPress 5.5以降でネイティブサポート
 */
add_filter('wp_lazy_loading_enabled', '__return_true');

// the_post_thumbnail()のデフォルト属性にloading="lazy"を追加
add_filter('wp_get_attachment_image_attributes', function($attr, $attachment, $size) {
    // 既にloading属性が設定されている場合はそのまま
    if (!isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }
    return $attr;
}, 10, 3);

/**
 * AJAX検索ハンドラー（エピソード全件検索）
 */
function contentfreaks_search_episodes() {
    check_ajax_referer('contentfreaks_load_more', 'nonce', true);

    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    if (empty($search)) {
        wp_send_json_success(array('html' => ''));
    }

    $query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 30,
        's' => $search,
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'orderby' => 'date',
        'order' => 'DESC'
    ));

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/episode-card');
        }
        wp_reset_postdata();
    }
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html, 'count' => $query->found_posts));
}
add_action('wp_ajax_search_episodes', 'contentfreaks_search_episodes');
add_action('wp_ajax_nopriv_search_episodes', 'contentfreaks_search_episodes');

/**
 * パンくずナビ出力
 */
function contentfreaks_breadcrumb() {
    if (is_front_page()) return;

    echo '<nav class="breadcrumb-nav" aria-label="パンくず">';
    echo '<ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">';

    // ホーム
    echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
    echo '<a itemprop="item" href="' . esc_url(home_url('/')) . '"><span itemprop="name">ホーム</span></a>';
    echo '<meta itemprop="position" content="1">';
    echo '</li>';

    $position = 2;

    if (is_single()) {
        // エピソード一覧 → タイトル
        $episodes_page = get_page_by_path('episodes');
        if ($episodes_page) {
            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            echo '<a itemprop="item" href="' . esc_url(get_permalink($episodes_page)) . '"><span itemprop="name">エピソード</span></a>';
            echo '<meta itemprop="position" content="' . $position . '">';
            echo '</li>';
            $position++;
        }
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span class="current" itemprop="name">' . esc_html(get_the_title()) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';
    } elseif (is_page()) {
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span class="current" itemprop="name">' . esc_html(get_the_title()) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';
    } elseif (is_tag()) {
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span class="current" itemprop="name">#' . esc_html(single_tag_title('', false)) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';
    } elseif (is_category()) {
        echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
        echo '<span class="current" itemprop="name">' . esc_html(single_cat_title('', false)) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '">';
        echo '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}

/**
 * ポッドキャストエピソードの JSON-LD 構造化データ
 * → inc/structured_data.php に統合済みのため削除
 * 以前はここに contentfreaks_episode_jsonld() があったが重複出力になっていた
 */

// =============================================
// アフィリエイト & 作品連携機能
// =============================================

/**
 * タグ名から作品DBの投稿を取得（エピソード詳細ページ用）
 * エピソードのタグ（＝作品名）と作品CPTのタイトルを照合
 */
function contentfreaks_get_works_by_tags($tag_names) {
    if (empty($tag_names)) return array();

    $works = array();
    foreach ($tag_names as $tag_name) {
        $found = get_posts(array(
            'post_type'      => 'work',
            'post_status'    => 'publish',
            'title'          => $tag_name,
            'posts_per_page' => 1,
            'fields'         => '',
        ));
        // タイトル完全一致がない場合は部分一致でフォールバック
        if (empty($found)) {
            $found = get_posts(array(
                'post_type'      => 'work',
                'post_status'    => 'publish',
                's'              => $tag_name,
                'posts_per_page' => 1,
            ));
        }
        if (!empty($found)) {
            // 重複排除
            $ids = wp_list_pluck($works, 'ID');
            if (!in_array($found[0]->ID, $ids)) {
                $works[] = $found[0];
            }
        }
    }
    return $works;
}

/**
 * 本文中の『作品名』を作品DBのリンクに自動変換（アフィリエイト対応）
 * 作品DBに登録されている作品のみ変換。未登録の『...』はそのまま。
 */
function contentfreaks_auto_link_works($content) {
    // 管理画面やRSSフィードでは変換しない
    if (is_admin() || is_feed()) return $content;
    // 個別記事ページのみ
    if (!is_singular('post')) return $content;

    // 『...』パターンを抽出
    if (!preg_match_all('/『([^』]+)』/', $content, $matches)) {
        return $content;
    }

    $amazon_tag = get_theme_mod('mk_amazon_tag', '');
    $linked = array(); // 同じ作品を2回リンクしない

    foreach ($matches[1] as $i => $work_name) {
        if (in_array($work_name, $linked)) continue;

        // 作品DBからタイトル一致を検索
        $work = get_posts(array(
            'post_type'      => 'work',
            'post_status'    => 'publish',
            'title'          => $work_name,
            'posts_per_page' => 1,
        ));

        if (empty($work)) continue;

        $work = $work[0];
        $amazon_url = get_post_meta($work->ID, 'work_amazon_url', true);
        $affiliate_url = get_post_meta($work->ID, 'work_affiliate_url', true);

        // リンク先を決定（優先: Amazon > その他アフィリエイト > 作品DB詳細ページ）
        $link_url = '';
        $rel = 'noopener';
        $target = '';

        if ($amazon_url) {
            $link_url = $amazon_url;
            if ($amazon_tag && strpos($link_url, 'tag=') === false) {
                $separator = (strpos($link_url, '?') !== false) ? '&' : '?';
                $link_url .= $separator . 'tag=' . urlencode($amazon_tag);
            }
            $rel = 'noopener sponsored';
            $target = ' target="_blank"';
        } elseif ($affiliate_url) {
            $link_url = $affiliate_url;
            $rel = 'noopener sponsored';
            $target = ' target="_blank"';
        } else {
            $link_url = get_permalink($work->ID);
        }

        $link_html = '<a href="' . esc_url($link_url) . '" rel="' . $rel . '"' . $target . ' class="work-auto-link" title="' . esc_attr($work_name . ' - 作品情報') . '">『' . esc_html($work_name) . '』</a>';

        // 最初の1つだけリンク化（同じ作品が複数回出現しても1回だけ）
        $content = preg_replace(
            '/『' . preg_quote($work_name, '/') . '』/',
            $link_html,
            $content,
            1
        );
        $linked[] = $work_name;
    }

    return $content;
}
add_filter('the_content', 'contentfreaks_auto_link_works', 20);

// =============================================
// お問い合わせフォーム AJAX 処理
// =============================================

/**
 * お問い合わせフォーム送信処理
 * リスナー / ビジネスの両タイプに対応
 */
function contentfreaks_contact_submit() {
    header('Content-Type: application/json; charset=utf-8');

    // nonce検証
    if (!check_ajax_referer('contentfreaks_load_more', 'nonce', false)) {
        wp_send_json_error(array('message' => 'セキュリティ検証に失敗しました。ページを再読み込みしてください。'));
    }

    // ハニーポット
    if (!empty($_POST['website_url'])) {
        wp_send_json_success(array('message' => 'ありがとうございます！メッセージを受け付けました。'));
    }

    $contact_type = sanitize_text_field($_POST['contact_type'] ?? 'listener');
    $name     = sanitize_text_field($_POST['name'] ?? '');
    $email    = sanitize_email($_POST['email'] ?? '');
    $message  = sanitize_textarea_field($_POST['message'] ?? '');
    $category = sanitize_text_field($_POST['category'] ?? '');
    $company  = sanitize_text_field($_POST['company'] ?? '');

    // 必須チェック
    if (empty($name) || empty($message)) {
        wp_send_json_error(array('message' => 'お名前とメッセージは必須項目です。'));
    }

    if ($contact_type === 'business' && empty($email)) {
        wp_send_json_error(array('message' => 'メールアドレスをご入力ください。'));
    }

    // メッセージ長チェック
    $max_len = ($contact_type === 'business') ? 5000 : 2000;
    if (mb_strlen($message) > $max_len) {
        wp_send_json_error(array('message' => 'メッセージが長すぎます。'));
    }

    // レート制限（同一IPから10分に1回）
    $ip = function_exists('contentfreaks_get_client_ip') ? contentfreaks_get_client_ip() : $_SERVER['REMOTE_ADDR'];
    $rate_key = 'cf_contact_' . md5($ip);
    if (get_transient($rate_key)) {
        wp_send_json_error(array('message' => '連続送信はできません。しばらくお待ちください。'));
    }

    // メール送信
    $admin_email = get_option('admin_email');
    $type_label  = ($contact_type === 'business') ? 'お仕事のご依頼' : 'リスナーからのメッセージ';
    $subject     = '[ContentFreaks] ' . $type_label . '：' . $category;

    $body = "【{$type_label}】\n\n";
    $body .= "お名前: {$name}\n";
    if (!empty($email))   $body .= "メール: {$email}\n";
    if (!empty($company))  $body .= "会社/番組名: {$company}\n";
    if (!empty($category)) $body .= "カテゴリ: {$category}\n";
    $body .= "\n---メッセージ---\n{$message}\n---\n\n";
    $body .= "送信元IP: {$ip}\n";
    $body .= "送信日時: " . wp_date('Y-m-d H:i:s') . "\n";

    $headers = array('Content-Type: text/plain; charset=UTF-8');
    if (!empty($email)) {
        $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    }

    $sent = wp_mail($admin_email, $subject, $body, $headers);

    if (!$sent) {
        wp_send_json_error(array('message' => '送信に失敗しました。時間を空けて再度お試しください。'));
    }

    // レート制限セット（10分）
    set_transient($rate_key, true, 10 * MINUTE_IN_SECONDS);

    $success_msg = ($contact_type === 'business')
        ? 'お問い合わせありがとうございます。3営業日以内にご連絡いたします。'
        : 'メッセージありがとうございます！番組内でご紹介させていただくことがあります。';

    wp_send_json_success(array('message' => $success_msg));
}
add_action('wp_ajax_contentfreaks_contact_submit', 'contentfreaks_contact_submit');
add_action('wp_ajax_nopriv_contentfreaks_contact_submit', 'contentfreaks_contact_submit');
