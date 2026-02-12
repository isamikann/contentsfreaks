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
require_once get_stylesheet_directory() . '/inc/works_cpt.php'; // 作品データベース\nrequire_once get_stylesheet_directory() . '/inc/testimonials.php'; // リスナーの声

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
        // 既存のタグをクリア（必要に応じて）
        // wp_set_post_tags($post->ID, array());
        // タイトルからタグを再抽出
        contentfreaks_extract_and_create_tags_from_title($post->ID, $post->post_title);
        $processed_count++;
    }
    return $processed_count;
}

/**
 * 統一された管理画面
 */
function contentfreaks_unified_admin_page() {
    // 処理結果メッセージ
    $messages = array();
    
    // 手動同期処理
    if (isset($_POST['manual_sync']) && wp_verify_nonce($_POST['sync_nonce'], 'contentfreaks_sync')) {
        $result = contentfreaks_sync_rss_to_posts();
        if (!empty($result['errors'])) {
            $messages[] = array('type' => 'warning', 'message' => $result['synced'] . ' 件のエピソードを同期しました。エラー: ' . count($result['errors']) . ' 件');
        } else {
            $messages[] = array('type' => 'success', 'message' => $result['synced'] . ' 件のエピソードを同期しました！');
        }
    }
    
    // タグ再抽出処理
    if (isset($_POST['re_extract_tags']) && wp_verify_nonce($_POST['re_extract_tags_nonce'], 'contentfreaks_re_extract_tags')) {
        $processed = contentfreaks_re_extract_all_tags();
        $messages[] = array('type' => 'success', 'message' => $processed . ' 件の投稿からタグを再抽出しました！');
    }
    
    // キャッシュクリア処理
    if (isset($_POST['clear_cache']) && wp_verify_nonce($_POST['clear_cache_nonce'], 'contentfreaks_clear_cache')) {
        contentfreaks_clear_rss_cache();
        $messages[] = array('type' => 'success', 'message' => 'RSSキャッシュをクリアしました！');
    }
    
    // リライトルール更新処理
    if (isset($_POST['flush_rewrite_rules']) && wp_verify_nonce($_POST['flush_rewrite_rules_nonce'], 'contentfreaks_flush_rewrite_rules')) {
        // 強制的にリライトルールを更新
        delete_option('rewrite_rules');
        contentfreaks_episodes_rewrite_rules();
        flush_rewrite_rules();
        delete_option('contentfreaks_rewrite_rules_flushed'); // 次回の自動更新を有効化
        $messages[] = array('type' => 'success', 'message' => 'リライトルールを強制更新しました！エピソードページが正常に表示されるはずです。');
    }
    
    // リスナー数更新処理
    if (isset($_POST['update_listener_count']) && wp_verify_nonce($_POST['listener_count_nonce'], 'contentfreaks_listener_count_nonce')) {
        $listener_count = sanitize_text_field($_POST['listener_count']);
        update_option('contentfreaks_listener_count', $listener_count);
        $messages[] = array('type' => 'success', 'message' => 'リスナー数を ' . $listener_count . ' に更新しました！');
    }
    
    // 統計情報の取得
    $current_rss_count = contentfreaks_get_rss_episode_count();
    $post_count = wp_count_posts()->publish;
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
    $listener_count = get_option('contentfreaks_listener_count', 1500);
    
    ?>
    <div class="wrap">
        <h1>ポッドキャスト管理</h1>
        
        <?php
        // メッセージ表示
        foreach ($messages as $message) {
            echo '<div class="notice notice-' . $message['type'] . '"><p>' . $message['message'] . '</p></div>';
        }
        ?>
        
        <p>RSSフィードからエピソードを投稿として自動同期し、タイトルの『』内テキストを自動タグ化します。</p>
        
        <!-- 同期状況 -->
        <div class="postbox" style="margin-bottom: 20px;">
            <h2 class="hndle">📊 同期状況</h2>
            <div class="inside">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <div style="background: #f0f8ff; padding: 15px; border-radius: 8px; border-left: 4px solid #2196F3;">
                        <h4 style="margin: 0 0 10px 0; color: #2196F3;">RSSエピソード数</h4>
                        <p style="font-size: 24px; font-weight: bold; margin: 0; color: #333;"><?php echo $current_rss_count; ?> 件</p>
                    </div>
                    <div style="background: #f0fff0; padding: 15px; border-radius: 8px; border-left: 4px solid #4CAF50;">
                        <h4 style="margin: 0 0 10px 0; color: #4CAF50;">ポッドキャスト投稿数</h4>
                        <p style="font-size: 24px; font-weight: bold; margin: 0; color: #333;"><?php echo $podcast_post_count; ?> 件</p>
                    </div>
                    <div style="background: #fff8f0; padding: 15px; border-radius: 8px; border-left: 4px solid #ff9800;">
                        <h4 style="margin: 0 0 10px 0; color: #ff9800;">登録済みタグ数</h4>
                        <p style="font-size: 24px; font-weight: bold; margin: 0; color: #333;"><?php echo $total_tags; ?> 件</p>
                    </div>
                </div>
                
                <div style="background: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 8px;">
                    <h4 style="margin: 0 0 10px 0;">最新の同期情報</h4>
                    <p><strong>最後の同期:</strong> <?php echo $last_sync_time ? date('Y年n月j日 H:i:s', strtotime($last_sync_time)) : '未実行'; ?></p>
                    <p><strong>同期/更新件数:</strong> <?php echo $last_sync_count; ?>件</p>
                    <?php if (!empty($last_sync_errors)): ?>
                        <p><strong>エラー:</strong> <span style="color: #d63638;"><?php echo count($last_sync_errors); ?>件</span></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- 操作ボタン -->
        <div class="postbox" style="margin-bottom: 20px;">
            <h2 class="hndle">🔧 操作メニュー</h2>
            <div class="inside">
                <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                    <!-- 手動同期ボタン -->
                    <form method="post" style="display: inline;">
                        <?php wp_nonce_field('contentfreaks_sync', 'sync_nonce'); ?>
                        <input type="submit" name="manual_sync" class="button-primary" value="📥 手動同期実行" />
                    </form>
                    
                    <!-- タグ再抽出ボタン -->
                    <form method="post" style="display: inline;">
                        <?php wp_nonce_field('contentfreaks_re_extract_tags', 're_extract_tags_nonce'); ?>
                        <input type="submit" name="re_extract_tags" class="button-secondary" value="🏷️ タグ再抽出" />
                    </form>
                    
                    <!-- キャッシュクリアボタン -->
                    <form method="post" style="display: inline;">
                        <?php wp_nonce_field('contentfreaks_clear_cache', 'clear_cache_nonce'); ?>
                        <input type="submit" name="clear_cache" class="button-secondary" value="🗑️ キャッシュクリア" />
                    </form>
                    
                    <!-- リライトルール更新ボタン -->
                    <form method="post" style="display: inline;">
                        <?php wp_nonce_field('contentfreaks_flush_rewrite_rules', 'flush_rewrite_rules_nonce'); ?>
                        <input type="submit" name="flush_rewrite_rules" class="button-secondary" value="🔄 リライトルール更新" />
                    </form>
                    
                    <!-- RSSテストボタン -->
                    <form method="post" style="display: inline;">
                        <?php wp_nonce_field('contentfreaks_test_rss', 'test_rss_nonce'); ?>
                        <input type="submit" name="test_rss" class="button-secondary" value="🔍 RSS接続テスト" />
                    </form>
                    
                    <!-- URLテストボタン -->
                    <form method="post" style="display: inline;">
                        <?php wp_nonce_field('contentfreaks_test_url', 'test_url_nonce'); ?>
                        <input type="submit" name="test_url" class="button-secondary" value="🌐 URL構造テスト" />
                    </form>
                </div>
            </div>
        </div>
        
        <!-- リスナー数設定 -->
        <div class="postbox" style="margin-bottom: 20px;">
            <h2 class="hndle">👥 リスナー数設定</h2>
            <div class="inside">
                <form method="post">
                    <?php wp_nonce_field('contentfreaks_listener_count_nonce', 'listener_count_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="listener_count">現在のリスナー数</label></th>
                            <td>
                                <input type="number" id="listener_count" name="listener_count" 
                                       value="<?php echo esc_attr(get_option('contentfreaks_listener_count', '1500')); ?>" 
                                       min="0" step="1" style="width: 150px;" />
                                <p class="description">フロントページとプロフィールページに表示されるリスナー数を設定します。</p>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" name="update_listener_count" class="button-primary" value="リスナー数を更新" />
                    </p>
                </form>
            </div>
        </div>
        
        <?php
        // URLテスト処理
        if (isset($_POST['test_url']) && wp_verify_nonce($_POST['test_url_nonce'], 'contentfreaks_test_url')) {
            echo '<div class="postbox" style="margin-bottom: 20px;">';
            echo '<h2 class="hndle">🌐 URL構造テスト結果</h2>';
            echo '<div class="inside">';
            
            echo '<h4>現在のURL設定</h4>';
            echo '<ul>';
            echo '<li><strong>サイトURL:</strong> ' . home_url() . '</li>';
            echo '<li><strong>エピソードURL:</strong> ' . home_url('/episodes/') . '</li>';
            echo '<li><strong>パーマリンク構造:</strong> ' . (get_option('permalink_structure') ?: 'デフォルト') . '</li>';
            echo '</ul>';
            
            echo '<h4>リライトルール状態</h4>';
            $rewrite_rules = get_option('rewrite_rules', array());
            $episodes_rules = array();
            foreach ($rewrite_rules as $pattern => $rewrite) {
                if (strpos($pattern, 'episodes') !== false) {
                    $episodes_rules[$pattern] = $rewrite;
                }
            }
            
            if (!empty($episodes_rules)) {
                echo '<p style="color: green;">✅ エピソード関連のリライトルールが見つかりました:</p>';
                echo '<ul>';
                foreach ($episodes_rules as $pattern => $rewrite) {
                    echo '<li><code>' . esc_html($pattern) . '</code> → <code>' . esc_html($rewrite) . '</code></li>';
                }
                echo '</ul>';
            } else {
                echo '<p style="color: red;">❌ エピソード関連のリライトルールが見つかりません。</p>';
            }
            
            echo '<h4>ファイル・ページ存在チェック</h4>';
            echo '<ul>';
            echo '<li><strong>page-episodes.php:</strong> ' . (file_exists(get_stylesheet_directory() . '/page-episodes.php') ? '✅ 存在' : '❌ 不存在') . '</li>';
            echo '<li><strong>episodes固定ページ:</strong> ' . (get_page_by_path('episodes') ? '✅ 存在' : '❌ 不存在') . '</li>';
            echo '</ul>';
            
            echo '</div>';
            echo '</div>';
        }
        ?>
        
        <?php
        // RSSテスト処理
        if (isset($_POST['test_rss']) && wp_verify_nonce($_POST['test_rss_nonce'], 'contentfreaks_test_rss')) {
            echo '<div class="postbox" style="margin-bottom: 20px;">';
            echo '<h2 class="hndle">🔍 RSSフィードテスト結果</h2>';
            echo '<div class="inside">';
            
            // キャッシュをクリアしてから新規取得
            contentfreaks_clear_rss_cache();
            $episodes = contentfreaks_get_rss_episodes(5);
            
            if (!empty($episodes)) {
                echo '<p style="color: green;">✅ RSS取得成功！ ' . count($episodes) . ' 件のエピソードを取得</p>';
                echo '<div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">';
                
                foreach ($episodes as $episode) {
                    echo '<div style="background: white; padding: 15px; margin-bottom: 10px; border-radius: 5px; border-left: 4px solid #2196F3;">';
                    echo '<h4 style="margin: 0 0 10px 0;">' . esc_html($episode['title']) . '</h4>';
                    
                    // サムネイル情報
                    if (!empty($episode['thumbnail'])) {
                        echo '<p>🖼️ サムネイル: <a href="' . esc_url($episode['thumbnail']) . '" target="_blank">画像を確認</a></p>';
                    } else {
                        echo '<p>❌ サムネイル: 見つかりません</p>';
                    }
                    
                    // タグプレビュー
                    preg_match_all('/『([^』]+)』/', $episode['title'], $tag_matches);
                    if (!empty($tag_matches[1])) {
                        echo '<p>🏷️ タグ候補: <span style="color: #0073aa;">' . implode(', ', $tag_matches[1]) . '</span></p>';
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
            
            echo '</div>';
            echo '</div>';
        }
        ?>
        
        <?php if (!empty($last_sync_errors)): ?>
        <!-- エラー情報 -->
        <div class="postbox" style="margin-bottom: 20px;">
            <h2 class="hndle">⚠️ 同期エラー</h2>
            <div class="inside">
                <div style="background: #ffeaa7; padding: 15px; border-left: 4px solid #fdcb6e; border-radius: 4px;">
                    <h4 style="margin: 0 0 10px 0; color: #d63638;">最新の同期エラー一覧</h4>
                    <ul>
                        <?php foreach ($last_sync_errors as $error): ?>
                            <li><?php echo esc_html($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- 最近の更新記録 -->
        <div class="postbox" style="margin-bottom: 20px;">
            <h2 class="hndle">📝 最近の更新記録</h2>
            <div class="inside">
                <?php contentfreaks_display_recent_updates(); ?>
            </div>
        </div>
        
        <!-- 更新ログ -->
        <div class="postbox" style="margin-bottom: 20px;">
            <h2 class="hndle">📋 更新ログ</h2>
            <div class="inside">
                <?php contentfreaks_display_update_logs(); ?>
            </div>
        </div>
        
        <!-- 情報・ヘルプ -->
        <div class="postbox">
            <h2 class="hndle">ℹ️ 情報・ヘルプ</h2>
            <div class="inside">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                    <div style="background: #f0f8ff; padding: 15px; border-left: 4px solid #2196F3;">
                        <h4>🏷️ 自動タグ機能</h4>
                        <p><strong>機能:</strong> エピソードタイトルの『』内テキストを自動でタグとして追加</p>
                        <p><strong>例:</strong> 「第1回『YouTube』について語る」 → 「YouTube」タグを自動作成・追加</p>
                        <p><strong>複数対応:</strong> 「『YouTube』と『TikTok』の違い」 → 「YouTube」「TikTok」両方のタグを追加</p>
                    </div>
                    
                    <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;">
                        <h4>🔧 コンテンツ分類システム</h4>
                        <p><strong>方針:</strong> 手動分類のみ。自動分類は行いません</p>
                        <p><strong>RSS同期:</strong> RSSから取得した投稿のみ自動でポッドキャストエピソードに設定</p>
                        <p><strong>通常投稿:</strong> 管理画面またはクイック編集で手動分類</p>
                    </div>
                    
                    <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa;">
                        <h4>📡 RSS同期情報</h4>
                        <p><strong>RSS URL:</strong> https://anchor.fm/s/d8cfdc48/podcast/rss</p>
                        <p><strong>同期スケジュール:</strong> 1時間毎の自動同期</p>
                        <p><strong>更新検知:</strong> GUID、音声URL、ハッシュ値で既存投稿を特定・更新</p>
                        <p><strong>エピソード一覧:</strong> <a href="<?php echo home_url('/episodes/'); ?>" target="_blank">エピソード一覧ページ</a></p>
                    </div>
                    
                    <div style="background: #fffbf0; padding: 15px; border-left: 4px solid #ff9800;">
                        <h4>🔧 トラブルシューティング</h4>
                        <p><strong>エピソードページが404エラーの場合:</strong> 「🔄 リライトルール更新」ボタンをクリックしてください。</p>
                        <p><strong>その他のURL問題:</strong> WordPressの「設定」→「パーマリンク設定」で「変更を保存」を押してください。</p>
                        <p><strong>キャッシュ問題:</strong> 「🗑️ キャッシュクリア」ボタンでRSSキャッシュをクリアできます。</p>
                        <p><strong>デバッグ情報:</strong></p>
                        <ul>
                            <li>エピソードページファイル: <?php echo file_exists(get_stylesheet_directory() . '/page-episodes.php') ? '✅ 存在' : '❌ 不存在'; ?></li>
                            <li>現在のパーマリンク構造: <?php echo get_option('permalink_structure') ?: 'デフォルト'; ?></li>
                            <li>episodes固定ページ: <?php echo get_page_by_path('episodes') ? '✅ 存在' : '❌ 不存在'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
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
 * ページのURLを取得するヘルパー関数
 */
function contentfreaks_get_page_url($slug) {
    $page = get_page_by_path($slug);
    if ($page) {
        return get_permalink($page->ID);
    }
    return home_url('/' . $slug . '/');
}

/**
 * 必要なページが存在するかチェックし、なければ作成する
 */
function contentfreaks_create_pages() {
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
}
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
    
    // フォント（重要度が高いもの）
    $push_resources[] = '<https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+JP:wght@400;500;700;900&display=swap>; rel=preload; as=style';
    
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
 * ポッドキャストエピソード数を取得
 */
function contentfreaks_get_podcast_count() {
    $query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'meta_key' => 'is_podcast_episode',
        'meta_value' => '1',
        'post_status' => 'publish'
    ));
    return $query->found_posts;
}

/**
 * ブログ記事数を取得
 */
function contentfreaks_get_blog_count() {
    $query = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'is_podcast_episode',
                'compare' => 'NOT EXISTS'
            )
        ),
        'post_status' => 'publish'
    ));
    return $query->found_posts;
}

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
 * ダークモード早期適用（FOUC防止）
 */
function contentfreaks_darkmode_script() {
    echo "<script>
    (function(){
        if(localStorage.getItem('cf-theme')==='light'){
            document.documentElement.classList.add('light-mode');
        }
    })();
    </script>\n";
}
add_action('wp_head', 'contentfreaks_darkmode_script', 1);

