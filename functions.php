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
// Gemini AI 文字起こし・記事生成
require_once get_stylesheet_directory() . '/inc/proper_noun_normalizer.php'; // 固有名詞正規化
require_once get_stylesheet_directory() . '/inc/gemini_transcription.php';

/**
 * 5 分間隔のカスタム Cron インターバルを追加
 */
add_filter( 'cron_schedules', function ( $schedules ) {
    if ( ! isset( $schedules['contentfreaks_five_minutes'] ) ) {
        $schedules['contentfreaks_five_minutes'] = array(
            'interval' => 300,
            'display'  => '5分毎',
        );
    }
    return $schedules;
} );

/**
 * 定期同期スケジュール
 */
function contentfreaks_schedule_sync() {
    if (!wp_next_scheduled('contentfreaks_hourly_sync')) {
        wp_schedule_event(time(), 'hourly', 'contentfreaks_hourly_sync');
    }
    // 再生数は1日1回更新（APIクォータ節約）
    if (!wp_next_scheduled('contentfreaks_daily_youtube_sync')) {
        wp_schedule_event(time(), 'daily', 'contentfreaks_daily_youtube_sync');
    }
    // Gemini 文字起こしバッチ（5分毎・1件ずつ処理）
    if (!wp_next_scheduled('contentfreaks_gemini_transcription_batch')) {
        wp_schedule_event(time(), 'contentfreaks_five_minutes', 'contentfreaks_gemini_transcription_batch');
    }
}
// init フック: フロントエンド・管理画面どちらでもCronを登録する
add_action('init', 'contentfreaks_schedule_sync');

add_action('contentfreaks_hourly_sync', 'contentfreaks_sync_rss_to_posts');

// RSS同期後に新エピソードをYouTubeと自動紐付け
add_action('contentfreaks_hourly_sync', 'contentfreaks_auto_link_new_episodes');

// RSS同期後にアイキャッチが未設定のエピソードにRSS画像を適用
add_action('contentfreaks_hourly_sync', 'contentfreaks_apply_rss_featured_images_for_unset_posts', 20);

// 1日1回: 既存紐付け済み投稿の再生数を更新
add_action('contentfreaks_daily_youtube_sync', 'contentfreaks_refresh_youtube_views');

// Gemini 文字起こしバッチ（5分毎）
add_action('contentfreaks_gemini_transcription_batch', 'contentfreaks_process_pending_transcriptions');

// ============================================================
// RSS新規投稿ごとのAI処理即時トリガー
// ============================================================

/**
 * RSS同期で新規エピソード投稿が1件作成された際、その投稿だけを対象に
 * AI記事化をすぐにトリガーする。バッチ全体は起動せず、指定した post_id のみ処理する。
 *
 * @param int $post_id 新規作成されたエピソードの投稿ID
 */
add_action( 'contentfreaks_new_episode_created', 'contentfreaks_trigger_ai_for_new_episode' );

function contentfreaks_trigger_ai_for_new_episode( $post_id ) {
    if ( empty( contentfreaks_get_gemini_api_key() ) ) {
        error_log( 'Gemini: API Key未設定のためAI処理即時トリガーをスキップ (Post ID: ' . $post_id . ')' );
        return;
    }
    if ( contentfreaks_gemini_is_paused() ) {
        error_log( 'Gemini: 一時停止中のためAI処理即時トリガーをスキップ (Post ID: ' . $post_id . ')' );
        return;
    }

    // この投稿IDだけを引数に持つ単発Cronイベントをスケジュール
    // 過去時刻（time()-1）にすることで spawn_cron() 直後に即実行対象になる
    wp_schedule_single_event( time() - 1, 'contentfreaks_ai_process_single_episode', array( $post_id ) );

    // 非同期でWP-Cronを即時起動（ページ訪問を待たずに処理開始）
    spawn_cron();

    error_log( 'Gemini: RSS新規投稿のAI処理を即時トリガーしました (Post ID: ' . $post_id . ')' );
}

// 単発Cronイベントのフック: 指定した投稿IDのみAI記事化を実行
add_action( 'contentfreaks_ai_process_single_episode', 'contentfreaks_generate_episode_article' );

// ============================================================
// AJAX: Gemini 一時停止 / 再開トグル
// ============================================================
add_action('wp_ajax_contentfreaks_gemini_toggle_pause', function() {
    check_ajax_referer('contentfreaks_gemini_ajax', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error('権限がありません');
    }
    $paused = (bool) get_option('contentfreaks_gemini_paused', false);
    update_option('contentfreaks_gemini_paused', !$paused);
    wp_send_json_success(array('paused' => !$paused));
});

// ============================================================
// AJAX: 選択エピソードを再キュー
// ============================================================
add_action('wp_ajax_contentfreaks_requeue_episodes', function() {
    check_ajax_referer('contentfreaks_gemini_ajax', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error('権限がありません');
    }
    $ids = isset($_POST['post_ids']) ? array_map('intval', (array)$_POST['post_ids']) : array();
    $ids = array_filter($ids, function($id) { return $id > 0; });
    if (empty($ids)) {
        wp_send_json_error('エピソードが選択されていません');
    }
    $count = 0;
    foreach ($ids as $pid) {
        update_post_meta($pid, 'episode_ai_status', 'pending');
        delete_post_meta($pid, 'episode_ai_error');
        delete_post_meta($pid, 'episode_ai_debug');
        $count++;
    }
    wp_send_json_success(array('queued' => $count));
});

// ============================================================
// AJAX: key_points・transcription取得（デバッグ用）
// ============================================================
add_action('wp_ajax_contentfreaks_get_key_points', function() {
    check_ajax_referer('contentfreaks_gemini_ajax', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error('権限がありません');
    }
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if ($post_id <= 0) {
        wp_send_json_error('無効なpost_idです');
    }
    $post          = get_post($post_id);
    $key_points    = get_post_meta($post_id, 'episode_ai_key_points', true);
    $transcription = get_post_meta($post_id, 'episode_ai_transcription', true);

    if (empty($key_points) && empty($transcription)) {
        wp_send_json_error('key_points・transcriptionが見つかりません（未処理の可能性があります）');
    }

    wp_send_json_success(array(
        'post_title'    => $post ? $post->post_title : '',
        'key_points'    => $key_points    ?: '（key_pointsなし）',
        'transcription' => $transcription ?: '（transcriptionなし）',
    ));
});

// ============================================================
// AJAX: Gemini 1件処理（WP-Cron非依存・直接実行）
// post_id を指定した場合はその投稿のみ処理。省略時は pending から1件取得。
// ============================================================
add_action('wp_ajax_contentfreaks_run_gemini', function() {
    check_ajax_referer('contentfreaks_gemini_ajax', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error('権限がありません');
    }

    @set_time_limit(600);

    $target_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

    if ( $target_id > 0 ) {
        // 指定された投稿IDのみ処理
        $post = get_post( $target_id );
        if ( ! $post ) {
            wp_send_json_error( '投稿が見つかりません (ID: ' . $target_id . ')' );
        }
        $post_id    = $post->ID;
        $post_title = $post->post_title;
        // pending に設定してから処理（ステータスが何であれ上書き実行）
        update_post_meta( $post_id, 'episode_ai_status', 'pending' );
    } else {
        // 従来通り: pending から日付降順で1件取得
        $posts = get_posts(array(
            'post_type'   => 'post',
            'post_status' => array('publish', 'draft'),
            'meta_key'    => 'episode_ai_status',
            'meta_value'  => 'pending',
            'orderby'     => 'date',
            'order'       => 'DESC',
            'numberposts' => 1,
        ));

        if (empty($posts)) {
            wp_send_json_success(array('status' => 'no_pending', 'message' => 'pending なエピソードがありません'));
        }

        $post_id    = $posts[0]->ID;
        $post_title = $posts[0]->post_title;
    }

    // PHP出力バッファでWarning/Noticeも捕捉
    ob_start();
    $result = contentfreaks_generate_episode_article($post_id);
    $php_output = ob_get_clean();

    $status = get_post_meta($post_id, 'episode_ai_status', true);
    $error  = get_post_meta($post_id, 'episode_ai_error', true);

    // エラー詳細が空の場合、PHPの出力やresultから補完
    if ($status === 'error' && empty($error)) {
        $fallback = '';
        if (!empty($php_output)) {
            $fallback = 'PHP出力: ' . substr(strip_tags($php_output), 0, 500);
        } elseif ($result === false) {
            $fallback = 'generate_episode_article() が false を返しましたが、エラー詳細が未記録です';
        }
        if ($fallback) {
            update_post_meta($post_id, 'episode_ai_error', $fallback);
            $error = $fallback;
        }
    }

    $debug = get_post_meta($post_id, 'episode_ai_debug', true);

    wp_send_json_success(array(
        'post_id'    => $post_id,
        'post_title' => $post_title,
        'status'     => $status,
        'error'      => $error ?: null,
        'php_output' => !empty($php_output) ? substr($php_output, 0, 500) : null,
        'debug_step' => $debug ?: null,
    ));
});

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

// 管理画面に Gemini AJAX 用インライン JS を追加
add_action('admin_footer', function() {
    $screen = get_current_screen();
    if (!$screen || strpos($screen->id, 'contentfreaks-podcast-management') === false) return;
    ?>
    <script>
    (function($){
        var nonce = <?php echo wp_json_encode(wp_create_nonce('contentfreaks_gemini_ajax')); ?>;
        var stopRequested = false;
        var countdownTimer = null;

        function resetRunBtn() {
            stopRequested = false;
            $('#gemini-run-btn').prop('disabled', false).text('▶ AI記事化：今すぐ 1 件処理');
            $('#gemini-stop-btn').hide();
        }

        function runGemini(retryCount) {
            retryCount = retryCount || 0;
            if (stopRequested) {
                $('#gemini-run-status').css('color','#888').text('⏹ 停止しました。');
                resetRunBtn();
                return;
            }
            var $btn = $('#gemini-run-btn');
            var $stopBtn = $('#gemini-stop-btn');
            var $status = $('#gemini-run-status');
            $btn.prop('disabled', true);
            $stopBtn.show();
            if (retryCount === 0) {
                $btn.text('⏳ 処理中...');
                $status.css('color','#888').text('音声ダウンロード中（最大2〜3分かかります）');
            }
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                timeout: 360000,
                data: { action: 'contentfreaks_run_gemini', nonce: nonce },
                success: function(res) {
                    if (res.success) {
                        var d = res.data;
                        if (d.status === 'no_pending') {
                            $status.css('color','#888').text('pendingがありません。先に「全件キュー登録」を押してください。');
                            resetRunBtn();
                        } else if (d.status === 'done') {
                            $status.css('color','#15803d').text('✅ 完了: ' + d.post_title);
                            resetRunBtn();
                            location.reload();
                        } else if (d.status === 'error' && d.error && (d.error.indexOf('429') !== -1 || d.error.indexOf('503') !== -1)) {
                            if (stopRequested) {
                                $status.css('color','#888').text('⏹ 停止しました。');
                                resetRunBtn();
                                return;
                            }
                            // 429: レート制限（最低65秒）/ 503: 一時過負荷（最低30秒）
                            var is503 = d.error.indexOf('503') !== -1;
                            var sec = is503 ? 30 : 65;
                            var m = d.error.match(/(\d+)秒後/);
                            if (m) sec = Math.max(parseInt(m[1], 10) + (is503 ? 5 : 10), sec);
                            var waitLabel = is503 ? 'モデル過負荷のため' : 'レート制限のため';
                            var debugInfo = d.debug_step ? ' [' + d.debug_step + ']' : '';
                            $status.css('color','#b45309').text(
                                '⏳ ' + waitLabel + ' ' + sec + ' 秒後に自動リトライします... (' + d.post_title + ')' + debugInfo
                            );
                            $btn.text('⏳ ' + sec + '秒後にリトライ...');
                            var countdown = sec;
                            countdownTimer = setInterval(function(){
                                if (stopRequested) {
                                    clearInterval(countdownTimer);
                                    $status.css('color','#888').text('⏹ 停止しました。');
                                    resetRunBtn();
                                    return;
                                }
                                countdown--;
                                $btn.text('⏳ ' + countdown + '秒後にリトライ...');
                                if (countdown <= 0) {
                                    clearInterval(countdownTimer);
                                    $status.css('color','#888').text('リトライ中...');
                                    runGemini(retryCount + 1);
                                }
                            }, 1000);
                        } else if (d.status === 'error') {
                            var errDetail = d.error || d.php_output || '詳細不明';
                            var debugInfo = d.debug_step ? ' [debug: ' + d.debug_step + ']' : '';
                            $status.css('color','#b91c1c').text('❌ エラー: ' + d.post_title + ' — ' + errDetail + debugInfo);
                            resetRunBtn();
                        } else if (d.status === 'done') {
                            var debugInfo = d.debug_step ? ' [' + d.debug_step + ']' : '';
                            $status.css('color','#059669').text('✅ 成功: ' + d.post_title + debugInfo);
                            resetRunBtn();
                        } else {
                            $status.css('color','#b45309').text('⚠️ ' + d.status + ': ' + d.post_title + (d.error ? ' — '+d.error : ''));
                            resetRunBtn();
                        }
                    } else {
                        $status.css('color','red').text('❌ AJAXエラー: ' + (res.data || '不明'));
                        resetRunBtn();
                    }
                },
                error: function(xhr, status) {
                    $status.css('color','red').text('❌ 通信エラー: ' + status);
                    resetRunBtn();
                }
            });
        }

        $('#gemini-run-btn').on('click', function(){ runGemini(0); });

        $('#gemini-stop-btn').on('click', function(){
            stopRequested = true;
            if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
            $(this).prop('disabled', true).text('停止中...');
            $('#gemini-run-status').css('color','#888').text('⏹ 停止しました。次のリクエストはキャンセルされます。');
        });

        $('#gemini-pause-btn').on('click', function(){
            var $btn = $(this);
            $btn.prop('disabled', true);
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: { action: 'contentfreaks_gemini_toggle_pause', nonce: nonce },
                success: function(res) {
                    if (res.success) {
                        var paused = res.data.paused;
                        if (paused) {
                            $btn.text('▶ Cron再開').css({'background':'#15803d','color':'#fff','border-color':'#166534'});
                            $('#gemini-run-status').css('color','#b45309').text('⏸ Cron一時停止中：5分毎の自動処理を停止しました。');
                        } else {
                            $btn.text('⏸ Cron一時停止').css({'background':'#b45309','color':'#fff','border-color':'#92400e'});
                            $('#gemini-run-status').css('color','#15803d').text('▶ Cron再開：5分毎の自動処理を再開しました。');
                        }
                    }
                    $btn.prop('disabled', false);
                },
                error: function() {
                    $('#gemini-run-status').css('color','red').text('❌ 通信エラー');
                    $btn.prop('disabled', false);
                }
            });
        });

        // エピソード一覧: 全選択/解除
        $('#ep-select-all').on('change', function(){
            $('.ep-checkbox').prop('checked', this.checked);
            updateSelectionBtns();
        });
        $(document).on('change', '.ep-checkbox', function(){ updateSelectionBtns(); });
        function updateSelectionBtns(){
            var cnt = $('.ep-checkbox:checked').length;
            var dis = cnt === 0;
            var label = cnt > 0 ? '（' + cnt + '件）' : '';
            $('#run-selected-btn').prop('disabled', dis).text('▶ 選択してAI記事化' + label);
            $('#requeue-selected-btn').prop('disabled', dis).text('🔄 再キューのみ' + label);
        }

        // ── 選択してAI記事化（直接実行） ──────────────────────
        var selQueue = [];
        var selIndex = 0;
        var selStop  = false;
        var selCdTimer = null;

        function runGeminiForId(postId, progress, onDone, retryCount) {
            retryCount = retryCount || 0;
            var $status = $('#requeue-status');
            var $runBtn = $('#run-selected-btn');
            var $stopBtn = $('#run-selected-stop-btn');
            $runBtn.prop('disabled', true).text('⏳ 処理中 [' + progress + ']...');
            $stopBtn.show();
            $status.css('color','#888').text('[' + progress + '] 音声ダウンロード〜AI処理中（最大2〜3分）...');
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                timeout: 360000,
                data: { action: 'contentfreaks_run_gemini', nonce: nonce, post_id: postId },
                success: function(res) {
                    if (!res.success) {
                        $status.css('color','red').text('[' + progress + '] ❌ AJAXエラー: ' + (res.data || '不明'));
                        onDone('ajax_error'); return;
                    }
                    var d = res.data;
                    if (d.status === 'done') {
                        $status.css('color','#059669').text('[' + progress + '] ✅ 完了: ' + d.post_title);
                        onDone('done');
                    } else if (d.status === 'error' && d.error && (d.error.indexOf('429') !== -1 || d.error.indexOf('503') !== -1)) {
                        if (selStop) { onDone('stopped'); return; }
                        // 429: レート制限（最低65秒）/ 503: 一時過負荷（最低30秒）
                        var is503 = d.error.indexOf('503') !== -1;
                        var sec = is503 ? 30 : 65;
                        var m = d.error.match(/(\d+)秒後/);
                        if (m) sec = Math.max(parseInt(m[1], 10) + (is503 ? 5 : 10), sec);
                        var countdown = sec;
                        var waitLabel = is503 ? '⏳ 過負荷' : '⏳ レート制限';
                        var debugInfo = d.debug_step ? ' [' + d.debug_step + ']' : '';
                        $status.css('color','#b45309').text('[' + progress + '] ' + waitLabel + ' → ' + sec + '秒後リトライ: ' + d.post_title + debugInfo);
                        selCdTimer = setInterval(function(){
                            if (selStop) { clearInterval(selCdTimer); onDone('stopped'); return; }
                            countdown--;
                            $runBtn.text('⏳ ' + countdown + '秒後リトライ [' + progress + ']...');
                            if (countdown <= 0) {
                                clearInterval(selCdTimer);
                                runGeminiForId(postId, progress, onDone, retryCount + 1);
                            }
                        }, 1000);
                    } else if (d.status === 'error') {
                        var errMsg = d.error || d.php_output || '詳細不明';
                        $status.css('color','#b91c1c').text('[' + progress + '] ❌ エラー: ' + d.post_title + ' — ' + errMsg);
                        onDone('error');
                    } else {
                        $status.css('color','#888').text('[' + progress + '] ⚠️ ' + d.status + ': ' + (d.post_title || ''));
                        onDone(d.status);
                    }
                },
                error: function(xhr, st) {
                    $('#requeue-status').css('color','red').text('[' + progress + '] ❌ 通信エラー: ' + st);
                    onDone('network_error');
                }
            });
        }

        function runNextSelected() {
            if (selStop || selIndex >= selQueue.length) {
                var total = selIndex;
                $('#run-selected-btn').prop('disabled', false).text('▶ 選択してAI記事化');
                $('#run-selected-stop-btn').hide().prop('disabled', false).text('⏹ 停止');
                selStop = false;
                if (selIndex > 0 && !selStop) {
                    $('#requeue-status').css('color','#059669').text('✅ ' + total + '件の処理が完了しました。2秒後にリロードします。');
                    setTimeout(function(){ location.reload(); }, 2000);
                } else {
                    $('#requeue-status').css('color','#888').text('⏹ 停止しました。');
                }
                return;
            }
            var postId   = selQueue[selIndex];
            var progress = (selIndex + 1) + '/' + selQueue.length;
            selIndex++;
            runGeminiForId(postId, progress, function(result) {
                if (result === 'stopped') {
                    selStop = true;
                }
                runNextSelected();
            });
        }

        $('#run-selected-btn').on('click', function(){
            selQueue = [];
            $('.ep-checkbox:checked').each(function(){ selQueue.push(parseInt($(this).val(), 10)); });
            if (selQueue.length === 0) return;
            selIndex = 0;
            selStop  = false;
            runNextSelected();
        });

        $('#run-selected-stop-btn').on('click', function(){
            selStop = true;
            if (selCdTimer) { clearInterval(selCdTimer); selCdTimer = null; }
            $(this).prop('disabled', true).text('停止中...');
            $('#requeue-status').css('color','#888').text('⏹ 停止リクエスト済み。現在の処理後に停止します。');
        });

        // ── key_points確認モーダル ──────────────────────────────
        $(document).on('click', '.ep-keypoints-btn', function(){
            var postId = $(this).data('post-id');
            var $modal = $('#ep-keypoints-modal');
            var $body  = $('#ep-keypoints-body');
            var $title = $('#ep-keypoints-title');
            var $search = $('#ep-keypoints-search');

            $title.text('読み込み中...');
            $body.text('');
            $search.val('');
            $modal.show();

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: { action: 'contentfreaks_get_key_points', nonce: nonce, post_id: postId },
                success: function(res) {
                    if (res.success) {
                        $title.text('📝 key_points: ' + res.data.post_title);
                        $body.data('original', res.data.key_points);
                        $body.text(res.data.key_points);
                    } else {
                        $title.text('エラー');
                        $body.text(res.data || '不明なエラー');
                    }
                },
                error: function() {
                    $title.text('通信エラー');
                    $body.text('AJAXリクエストに失敗しました');
                }
            });
        });

        // ── key_pointsモーダル: タブ切り替え ─────────────────
        var kpCurrentTab = 'keypoints';
        var kpData = {};

        function kpHighlight(selector, keyword) {
            var original = $(selector).data('original') || '';
            if (!keyword) {
                $(selector).text(original);
                return;
            }
            var escaped = original.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            var re = new RegExp(keyword.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'), 'g');
            $(selector).html(escaped.replace(re, '<mark style="background:#fef08a;padding:0 2px;">'+keyword+'</mark>'));
            var $first = $(selector+' mark').first();
            if ($first.length) $(selector).scrollTop($first.position().top - 20);
        }

        function kpSwitchTab(tab) {
            kpCurrentTab = tab;
            if (tab === 'keypoints') {
                $('#kp-tab-keypoints').css({'font-weight':'bold','border-bottom':'2px solid #2563eb','color':'#2563eb'});
                $('#kp-tab-transcription').css({'font-weight':'normal','border-bottom':'2px solid transparent','color':'#666'});
                $('#ep-keypoints-body').data('original', kpData.key_points || '').text(kpData.key_points || '');
            } else {
                $('#kp-tab-transcription').css({'font-weight':'bold','border-bottom':'2px solid #2563eb','color':'#2563eb'});
                $('#kp-tab-keypoints').css({'font-weight':'normal','border-bottom':'2px solid transparent','color':'#666'});
                $('#ep-keypoints-body').data('original', kpData.transcription || '').text(kpData.transcription || '');
            }
            var keyword = $.trim($('#ep-keypoints-search').val());
            if (keyword) kpHighlight('#ep-keypoints-body', keyword);
        }

        $(document).on('click', '.ep-keypoints-btn', function(){
            var postId = $(this).data('post-id');
            kpData = {};
            kpCurrentTab = 'keypoints';
            $('#ep-keypoints-title').text('読み込み中...');
            $('#ep-keypoints-body').text('');
            $('#ep-keypoints-search').val('');
            $('#kp-tab-keypoints').css({'font-weight':'bold','border-bottom':'2px solid #2563eb','color':'#2563eb'});
            $('#kp-tab-transcription').css({'font-weight':'normal','border-bottom':'2px solid transparent','color':'#666'});
            $('#ep-keypoints-modal').show();

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: { action: 'contentfreaks_get_key_points', nonce: nonce, post_id: postId },
                success: function(res) {
                    if (res.success) {
                        kpData = res.data;
                        $('#ep-keypoints-title').text(res.data.post_title);
                        kpSwitchTab('keypoints');
                    } else {
                        $('#ep-keypoints-title').text('エラー');
                        $('#ep-keypoints-body').text(res.data || '不明なエラー');
                    }
                },
                error: function() {
                    $('#ep-keypoints-title').text('通信エラー');
                    $('#ep-keypoints-body').text('AJAXリクエストに失敗しました');
                }
            });
        });

        $('#kp-tab-keypoints').on('click', function(){ kpSwitchTab('keypoints'); });
        $('#kp-tab-transcription').on('click', function(){ kpSwitchTab('transcription'); });

        // モーダルを閉じる
        $('#ep-keypoints-close').on('click', function(){ $('#ep-keypoints-modal').hide(); });
        $('#ep-keypoints-modal').on('click', function(e){
            if ($(e.target).is('#ep-keypoints-modal')) $(this).hide();
        });

        // 検索・ハイライト
        $('#ep-keypoints-search').on('input', function(){
            kpHighlight('#ep-keypoints-body', $.trim($(this).val()));
        });

        // ── 再キューのみ ──────────────────────────────────────
        $('#requeue-selected-btn').on('click', function(){
            var ids = [];
            $('.ep-checkbox:checked').each(function(){ ids.push($(this).val()); });
            if (ids.length === 0) return;
            if (!confirm(ids.length + '件のエピソードを再キュー（pending）に戻します。よろしいですか？')) return;
            var $btn = $(this);
            $btn.prop('disabled', true);
            $('#requeue-status').css('color','#888').text('処理中...');
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: { action: 'contentfreaks_requeue_episodes', nonce: nonce, post_ids: ids },
                success: function(res) {
                    if (res.success) {
                        $('#requeue-status').css('color','#059669').text('✅ ' + res.data.queued + '件を待機中に戻しました。2秒後にリロードします。');
                        setTimeout(function(){ location.reload(); }, 2000);
                    } else {
                        $('#requeue-status').css('color','red').text('❌ ' + (res.data || 'エラー'));
                        $btn.prop('disabled', false);
                    }
                },
                error: function() {
                    $('#requeue-status').css('color','red').text('❌ 通信エラー');
                    $btn.prop('disabled', false);
                }
            });
        });
    })(jQuery);
    </script>
    <?php
});

/**
 * 管理画面のPOST処理を admin_init フックで処理（推奨パターン）
 */
function contentfreaks_handle_admin_posts() {
    // 管理画面でなければ実行しない
    if (!is_admin()) {
        return;
    }

    // 権限チェック
    if (!current_user_can('manage_options')) {
        return;
    }

    // 手動同期実行
    if (isset($_POST['manual_sync']) && isset($_POST['sync_nonce']) && wp_verify_nonce($_POST['sync_nonce'], 'contentfreaks_sync')) {
        $result = contentfreaks_sync_rss_to_posts();

        // 結果をTransientに保存（30秒）
        if (!empty($result['errors'])) {
            set_transient('contentfreaks_admin_message', array(
                'type' => 'warning',
                'message' => $result['synced'] . ' 件のエピソードを同期しました。エラー: ' . count($result['errors']) . ' 件'
            ), 30);
        } else {
            set_transient('contentfreaks_admin_message', array(
                'type' => 'success',
                'message' => $result['synced'] . ' 件のエピソードを同期しました！'
            ), 30);
        }

        // リダイレクト
        wp_safe_remote_get(add_query_arg('tab', 'tools', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // タグ再抽出
    if (isset($_POST['re_extract_tags']) && isset($_POST['re_extract_tags_nonce']) && wp_verify_nonce($_POST['re_extract_tags_nonce'], 'contentfreaks_re_extract_tags')) {
        $processed = contentfreaks_re_extract_all_tags();
        set_transient('contentfreaks_admin_message', array(
            'type' => 'success',
            'message' => $processed . ' 件の投稿からタグを再抽出しました！'
        ), 30);
        wp_safe_remote_get(add_query_arg('tab', 'tools', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // キャッシュクリア
    if (isset($_POST['clear_cache']) && isset($_POST['clear_cache_nonce']) && wp_verify_nonce($_POST['clear_cache_nonce'], 'contentfreaks_clear_cache')) {
        contentfreaks_clear_rss_cache();
        set_transient('contentfreaks_admin_message', array(
            'type' => 'success',
            'message' => 'RSSキャッシュをクリアしました！'
        ), 30);
        wp_safe_remote_get(add_query_arg('tab', 'tools', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // リライトルール更新
    if (isset($_POST['flush_rewrite_rules']) && isset($_POST['flush_rewrite_rules_nonce']) && wp_verify_nonce($_POST['flush_rewrite_rules_nonce'], 'contentfreaks_flush_rewrite_rules')) {
        delete_option('rewrite_rules');
        contentfreaks_episodes_rewrite_rules();
        flush_rewrite_rules();
        delete_option('contentfreaks_rewrite_rules_flushed');
        set_transient('contentfreaks_admin_message', array(
            'type' => 'success',
            'message' => 'リライトルールを強制更新しました！'
        ), 30);
        wp_safe_remote_get(add_query_arg('tab', 'tools', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // YouTube動画紐付け
    if (isset($_POST['sync_youtube_videos']) && isset($_POST['sync_youtube_videos_nonce']) && wp_verify_nonce($_POST['sync_youtube_videos_nonce'], 'contentfreaks_sync_youtube_videos')) {
        if (function_exists('contentfreaks_queue_youtube_sync_job') && contentfreaks_queue_youtube_sync_job('manual')) {
            set_transient('contentfreaks_admin_message', array(
                'type' => 'success',
                'message' => 'YouTube動画紐付けをバックグラウンドに投入しました。完了まで少し待ってから再読み込みしてください。'
            ), 30);
        }
        wp_safe_remote_get(add_query_arg('tab', 'tools', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // Gemini API設定保存
    if (isset($_POST['save_gemini_settings']) && isset($_POST['gemini_settings_nonce']) && wp_verify_nonce($_POST['gemini_settings_nonce'], 'contentfreaks_gemini_settings')) {
        $gemini_key = sanitize_text_field($_POST['gemini_api_key'] ?? '');
        update_option('contentfreaks_gemini_api_key', $gemini_key);
        set_transient('contentfreaks_admin_message', array(
            'type'    => 'success',
            'message' => 'Gemini API 設定を保存しました！',
        ), 30);
        wp_safe_remote_get(add_query_arg('tab', 'settings', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // Gemini: 全エピソードをキューに追加
    if (isset($_POST['gemini_queue_all']) && isset($_POST['gemini_queue_all_nonce']) && wp_verify_nonce($_POST['gemini_queue_all_nonce'], 'contentfreaks_gemini_queue_all')) {
        $queued = contentfreaks_queue_all_episodes_for_transcription();
        set_transient('contentfreaks_admin_message', array(
            'type'    => 'success',
            'message' => $queued . ' 件のエピソードを AI 処理キューに追加しました。',
        ), 30);
        wp_safe_remote_get(add_query_arg('tab', 'ai', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // Gemini: エラー投稿をすべて pending に戻す（リトライ）
    if (isset($_POST['gemini_retry_errors']) && isset($_POST['gemini_retry_errors_nonce']) && wp_verify_nonce($_POST['gemini_retry_errors_nonce'], 'contentfreaks_gemini_retry_errors')) {
        global $wpdb;
        $error_ids = $wpdb->get_col(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'episode_ai_status' AND meta_value = 'error'"
        );
        foreach ($error_ids as $eid) {
            update_post_meta((int)$eid, 'episode_ai_status', 'pending');
            delete_post_meta((int)$eid, 'episode_ai_error');
        }
        set_transient('contentfreaks_admin_message', array(
            'type'    => 'success',
            'message' => count($error_ids) . ' 件のエラーをリトライキューに追加しました。',
        ), 30);
        wp_safe_remote_get(add_query_arg('tab', 'ai', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // YouTube API設定保存
    if (isset($_POST['save_youtube_settings']) && isset($_POST['youtube_settings_nonce']) && wp_verify_nonce($_POST['youtube_settings_nonce'], 'contentfreaks_youtube_settings')) {
        $api_key    = sanitize_text_field($_POST['youtube_api_key']);
        $channel_id = sanitize_text_field($_POST['youtube_channel_id']);
        update_option('contentfreaks_youtube_api_key',    $api_key);
        update_option('contentfreaks_youtube_channel_id', $channel_id);
        contentfreaks_clear_youtube_stats_cache();
        set_transient('contentfreaks_admin_message', array(
            'type' => 'success',
            'message' => 'YouTube API設定を保存しました！'
        ), 30);
        wp_safe_remote_get(add_query_arg('tab', 'settings', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // 基本設定保存
    if (isset($_POST['save_basic_settings']) && isset($_POST['basic_settings_nonce']) && wp_verify_nonce($_POST['basic_settings_nonce'], 'contentfreaks_basic_settings')) {
        set_theme_mod('podcast_name', sanitize_text_field($_POST['podcast_name']));
        set_theme_mod('podcast_description', sanitize_textarea_field($_POST['podcast_description']));
        update_option('contentfreaks_pickup_episodes', sanitize_text_field($_POST['contentfreaks_pickup_episodes']));
        set_transient('contentfreaks_admin_message', array(
            'type' => 'success',
            'message' => '基本設定を保存しました！'
        ), 30);
        wp_safe_remote_get(add_query_arg('tab', 'settings', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // ホスト設定保存
    if (isset($_POST['save_host_settings']) && isset($_POST['host_settings_nonce']) && wp_verify_nonce($_POST['host_settings_nonce'], 'contentfreaks_host_settings')) {
        foreach (array('host1', 'host2') as $host) {
            set_theme_mod($host . '_name', sanitize_text_field($_POST[$host . '_name']));
            set_theme_mod($host . '_role', sanitize_text_field($_POST[$host . '_role']));
            set_theme_mod($host . '_bio', sanitize_textarea_field($_POST[$host . '_bio']));
            set_theme_mod($host . '_twitter', esc_url_raw($_POST[$host . '_twitter']));
            set_theme_mod($host . '_youtube', esc_url_raw($_POST[$host . '_youtube']));
        }
        set_transient('contentfreaks_admin_message', array(
            'type' => 'success',
            'message' => 'ホスト設定を保存しました！'
        ), 30);
        wp_safe_remote_get(add_query_arg('tab', 'hosts', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }

    // メディアキット設定保存
    if (isset($_POST['save_mediakit_settings']) && isset($_POST['mediakit_nonce']) && wp_verify_nonce($_POST['mediakit_nonce'], 'contentfreaks_mediakit')) {
        $mk_keys = array('mk_spotify_followers', 'mk_apple_followers', 'mk_youtube_subscribers', 'mk_monthly_plays', 'mk_frequency', 'mk_since', 'mk_amazon_tag');
        foreach ($mk_keys as $key) {
            set_theme_mod($key, sanitize_text_field($_POST[$key]));
        }
        set_transient('contentfreaks_admin_message', array(
            'type' => 'success',
            'message' => 'メディアキット設定を保存しました！'
        ), 30);
        wp_safe_remote_get(add_query_arg('tab', 'mediakit', admin_url('tools.php?page=contentfreaks-podcast-management')));
        return;
    }
}
add_action('admin_init', 'contentfreaks_handle_admin_posts', 5);

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
    // Transientからメッセージを取得
    $messages = array();
    $transient_message = get_transient('contentfreaks_admin_message');
    if ($transient_message) {
        $messages[] = $transient_message;
        delete_transient('contentfreaks_admin_message');
    }

    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';

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
        'ai'        => '🤖 AI記事化',
        'settings'  => '⚙️ 設定',
        'hosts'     => '👥 ホスト',
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

        <?php elseif ($current_tab === 'ai'): ?>
            <!-- ===== AI記事化 ===== -->
            <?php
            $ai_stats = contentfreaks_get_ai_stats();
            $ai_key_set = !empty(contentfreaks_get_gemini_api_key());
            $gemini_paused = (bool) get_option('contentfreaks_gemini_paused', false);
            ?>

            <?php if (!$ai_key_set): ?>
                <div class="notice notice-warning" style="margin-bottom:16px;">
                    <p>⚠️ Gemini API Key が未設定です。<a href="<?php echo esc_url(add_query_arg('tab', 'settings', $page_url)); ?>">設定タブ</a>から入力してください。</p>
                </div>
            <?php endif; ?>

            <!-- ステータスカード -->
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">処理状況</h2>
                <div class="inside">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; margin-bottom: 16px;">
                        <div style="background:#f0f8ff;padding:12px;border-radius:6px;border-left:3px solid #2196F3;text-align:center;">
                            <div style="font-size:11px;color:#666;margin-bottom:4px;">待機中</div>
                            <div style="font-size:22px;font-weight:bold;color:#2196F3;"><?php echo esc_html($ai_stats['pending']); ?></div>
                        </div>
                        <div style="background:#fffbf0;padding:12px;border-radius:6px;border-left:3px solid #f59e0b;text-align:center;">
                            <div style="font-size:11px;color:#666;margin-bottom:4px;">処理中</div>
                            <div style="font-size:22px;font-weight:bold;color:#f59e0b;"><?php echo esc_html($ai_stats['processing']); ?></div>
                        </div>
                        <div style="background:#f0fff0;padding:12px;border-radius:6px;border-left:3px solid #4CAF50;text-align:center;">
                            <div style="font-size:11px;color:#666;margin-bottom:4px;">完了</div>
                            <div style="font-size:22px;font-weight:bold;color:#4CAF50;"><?php echo esc_html($ai_stats['done']); ?></div>
                        </div>
                        <div style="background:#fff0f0;padding:12px;border-radius:6px;border-left:3px solid #ef4444;text-align:center;">
                            <div style="font-size:11px;color:#666;margin-bottom:4px;">エラー</div>
                            <div style="font-size:22px;font-weight:bold;color:#ef4444;"><?php echo esc_html($ai_stats['error']); ?></div>
                        </div>
                        <div style="background:#f9f9f9;padding:12px;border-radius:6px;border-left:3px solid #aaa;text-align:center;">
                            <div style="font-size:11px;color:#666;margin-bottom:4px;">未処理</div>
                            <div style="font-size:22px;font-weight:bold;color:#888;"><?php echo esc_html($ai_stats['unprocessed']); ?></div>
                        </div>
                    </div>
                    <?php if ($gemini_paused): ?>
                        <div style="background:#fffbf0;padding:10px 15px;border-left:4px solid #f59e0b;border-radius:4px;">
                            ⏸ Cron一時停止中 — 5分毎の自動処理は停止しています。
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 操作パネル -->
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">操作</h2>
                <div class="inside">
                    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:12px;">
                        <button type="button" id="gemini-run-btn" class="button-primary" style="cursor:pointer;">
                            ▶ 今すぐ 1 件処理
                        </button>
                        <button type="button" id="gemini-stop-btn" class="button-secondary" style="cursor:pointer;display:none;background:#b91c1c;color:#fff;border-color:#991b1b;">
                            ⏹ 停止
                        </button>
                        <button type="button" id="gemini-pause-btn" class="button-secondary" style="cursor:pointer;<?php echo $gemini_paused ? 'background:#15803d;color:#fff;border-color:#166534;' : 'background:#b45309;color:#fff;border-color:#92400e;'; ?>">
                            <?php echo $gemini_paused ? '▶ Cron再開' : '⏸ Cron一時停止'; ?>
                        </button>
                        <span id="gemini-run-status" style="font-size:13px;"></span>
                    </div>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_gemini_queue_all', 'gemini_queue_all_nonce'); ?>
                            <input type="submit" name="gemini_queue_all" class="button-secondary" value="🤖 全件キュー登録" />
                        </form>
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_gemini_retry_errors', 'gemini_retry_errors_nonce'); ?>
                            <input type="submit" name="gemini_retry_errors" class="button-secondary" value="🔄 エラーを全件リトライ" <?php echo $ai_stats['error'] === 0 ? 'disabled' : ''; ?> />
                        </form>
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_gemini_diagnose', 'gemini_diagnose_nonce'); ?>
                            <input type="submit" name="gemini_diagnose" class="button-secondary" value="🔍 診断" />
                        </form>
                    </div>
                    <p style="margin-top:10px;color:#666;font-size:13px;">
                        Cron が有効な場合、5分毎に待機中のエピソードを1件ずつ自動処理します。<br>
                        「今すぐ 1 件処理」は手動でAJAX実行します。429エラー時は自動で65秒待機後にリトライします。
                    </p>
                </div>
            </div>

            <?php
            // エラー詳細テーブル
            if ($ai_stats['error'] > 0):
                global $wpdb;
                $error_posts = $wpdb->get_results(
                    "SELECT p.ID, p.post_title, pm2.meta_value AS ai_error
                       FROM {$wpdb->posts} p
                       INNER JOIN {$wpdb->postmeta} pm  ON p.ID = pm.post_id  AND pm.meta_key  = 'episode_ai_status' AND pm.meta_value = 'error'
                       LEFT  JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'episode_ai_error'
                      WHERE p.post_type = 'post'
                      ORDER BY p.post_date DESC"
                );
            ?>
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">❌ エラー詳細</h2>
                <div class="inside">
                    <table class="widefat" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th>エピソード</th>
                                <th>エラー内容</th>
                                <th style="width:60px;">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($error_posts as $ep): ?>
                            <tr>
                                <td><?php echo esc_html($ep->post_title); ?></td>
                                <td style="color:#b91c1c;"><?php echo esc_html($ep->ai_error ?: '詳細不明'); ?></td>
                                <td><a href="<?php echo esc_url(get_edit_post_link($ep->ID)); ?>" class="button button-small">編集</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- エピソード一覧（個別再処理） -->
            <?php
            global $wpdb;
            $all_episodes = $wpdb->get_results(
                "SELECT p.ID, p.post_title, p.post_date,
                        COALESCE(pm_s.meta_value, '') AS ai_status,
                        COALESCE(pm_e.meta_value, '') AS ai_error,
                        COALESCE(pm_d.meta_value, '') AS ai_debug
                   FROM {$wpdb->posts} p
                   INNER JOIN {$wpdb->postmeta} pm_ep ON p.ID = pm_ep.post_id AND pm_ep.meta_key = 'is_podcast_episode' AND pm_ep.meta_value = '1'
                   LEFT JOIN {$wpdb->postmeta} pm_s  ON p.ID = pm_s.post_id  AND pm_s.meta_key  = 'episode_ai_status'
                   LEFT JOIN {$wpdb->postmeta} pm_e  ON p.ID = pm_e.post_id  AND pm_e.meta_key  = 'episode_ai_error'
                   LEFT JOIN {$wpdb->postmeta} pm_d  ON p.ID = pm_d.post_id  AND pm_d.meta_key  = 'episode_ai_debug'
                  WHERE p.post_type = 'post' AND p.post_status IN ('publish','draft')
                  ORDER BY p.post_date DESC"
            );
            if (!empty($all_episodes)):
            ?>
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">📋 エピソード一覧</h2>
                <div class="inside">
                    <div style="margin-bottom:10px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                        <button type="button" id="run-selected-btn" class="button-primary" disabled style="cursor:pointer;">▶ 選択してAI記事化</button>
                        <button type="button" id="run-selected-stop-btn" class="button-secondary" style="display:none;cursor:pointer;background:#b91c1c;color:#fff;border-color:#991b1b;">⏹ 停止</button>
                        <button type="button" id="requeue-selected-btn" class="button-secondary" disabled style="cursor:pointer;">🔄 再キューのみ</button>
                        <span id="requeue-status" style="font-size:13px;"></span>
                    </div>
                    <table class="widefat striped" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th style="width:30px;"><input type="checkbox" id="ep-select-all" /></th>
                                <th>エピソード</th>
                                <th style="width:70px;">ステータス</th>
                                <th style="width:100px;">日付</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_episodes as $ep):
                                $status = $ep->ai_status ?: '未処理';
                                $badge_color = match($status) {
                                    'done'       => '#059669',
                                    'pending'    => '#2563eb',
                                    'processing' => '#d97706',
                                    'error'      => '#dc2626',
                                    default      => '#888',
                                };
                                $badge_label = match($status) {
                                    'done'       => '✅ 完了',
                                    'pending'    => '⏳ 待機',
                                    'processing' => '⚙️ 処理中',
                                    'error'      => '❌ エラー',
                                    default      => '— 未処理',
                                };
                            ?>
                            <tr>
                                <td><input type="checkbox" class="ep-checkbox" value="<?php echo (int)$ep->ID; ?>" /></td>
                                <td>
                                    <a href="<?php echo esc_url(get_edit_post_link($ep->ID)); ?>"><?php echo esc_html($ep->post_title); ?></a>
                                    <?php if ($status === 'error' && $ep->ai_error): ?>
                                        <br><small style="color:#dc2626;"><?php echo esc_html(mb_strimwidth($ep->ai_error, 0, 80, '…')); ?></small>
                                    <?php endif; ?>
                                    <?php if ($status === 'done'): ?>
                                        <br><button type="button" class="button button-small ep-keypoints-btn" data-post-id="<?php echo (int)$ep->ID; ?>" style="margin-top:4px;font-size:11px;">📝 key_points確認</button>
                                    <?php endif; ?>
                                </td>
                                <td><span style="color:<?php echo $badge_color; ?>;font-weight:bold;font-size:12px;"><?php echo $badge_label; ?></span></td>
                                <td style="font-size:12px;color:#666;"><?php echo esc_html(date_i18n('Y/m/d', strtotime($ep->post_date))); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p style="margin-top:8px;color:#666;font-size:12px;">
                        ✅完了済みを含め、チェックを入れたエピソードを「待機中(pending)」に戻して再処理できます。
                    </p>
                </div>
            </div>

            <!-- key_points確認モーダル -->
            <div id="ep-keypoints-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:99999;overflow-y:auto;">
                <div style="background:#fff;margin:40px auto;max-width:860px;border-radius:8px;padding:24px;position:relative;">
                    <button type="button" id="ep-keypoints-close" style="position:absolute;top:12px;right:16px;font-size:20px;background:none;border:none;cursor:pointer;color:#666;">✕</button>
                    <h2 id="ep-keypoints-title" style="margin:0 0 12px 0;font-size:15px;padding-right:32px;color:#333;"></h2>

                    <!-- タブ -->
                    <div style="display:flex;gap:0;border-bottom:1px solid #e5e5e5;margin-bottom:12px;">
                        <button type="button" id="kp-tab-keypoints" style="padding:8px 18px;background:none;border:none;cursor:pointer;font-size:13px;font-weight:bold;border-bottom:2px solid #2563eb;color:#2563eb;">📝 key_points（要点）</button>
                        <button type="button" id="kp-tab-transcription" style="padding:8px 18px;background:none;border:none;cursor:pointer;font-size:13px;font-weight:normal;border-bottom:2px solid transparent;color:#666;">🎙 全文字起こし</button>
                    </div>

                    <div style="margin-bottom:10px;">
                        <input type="text" id="ep-keypoints-search" placeholder="名前で検索してハイライト（例: 及川光博）" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-size:13px;" />
                    </div>
                    <div id="ep-keypoints-body" style="white-space:pre-wrap;font-size:13px;line-height:1.7;max-height:60vh;overflow-y:auto;background:#f9f9f9;padding:16px;border-radius:4px;border:1px solid #e5e5e5;"></div>
                    <p style="margin-top:8px;color:#888;font-size:12px;">
                        💡 key_points（要点）は音声から抽出したもので、Geminiへの記事生成インプットになります。<br>
                        全文字起こしにある名前 → 音声で実際に言及された可能性あり。key_pointsのみにある名前 → 要点抽出時に混入した可能性あり。
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <?php
            // AI 診断結果
            if (isset($_POST['gemini_diagnose']) && wp_verify_nonce($_POST['gemini_diagnose_nonce'] ?? '', 'contentfreaks_gemini_diagnose')) {
                global $wpdb;
                echo '<div class="postbox" style="margin-bottom:20px;"><h2 class="hndle">🔍 AI 診断結果</h2><div class="inside">';

                $total_posts = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='post' AND post_status IN ('publish','draft')");
                echo "<p>📄 投稿(post)総数: <strong>{$total_posts}</strong></p>";

                $podcast_count = (int) $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key='is_podcast_episode' AND meta_value='1'");
                echo "<p>🎙️ is_podcast_episode=1: <strong>{$podcast_count}</strong></p>";

                $audio_count = (int) $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key='episode_audio_url' AND meta_value != ''");
                echo "<p>🎵 episode_audio_url 保有: <strong>{$audio_count}</strong></p>";

                $status_rows = $wpdb->get_results("SELECT meta_value, COUNT(*) AS cnt FROM {$wpdb->postmeta} WHERE meta_key='episode_ai_status' GROUP BY meta_value");
                echo "<p>🤖 episode_ai_status:</p><ul>";
                if (empty($status_rows)) {
                    echo "<li style='color:red;'>未登録（全件キュー登録がまだ）</li>";
                } else {
                    foreach ($status_rows as $r) {
                        echo '<li><code>' . esc_html($r->meta_value) . '</code>: ' . (int)$r->cnt . '件</li>';
                    }
                }
                echo '</ul>';

                $cron_next = wp_next_scheduled('contentfreaks_gemini_transcription_batch');
                echo '<p>⏱️ 次回Cron: ' . ($cron_next ? esc_html(date_i18n('Y-m-d H:i:s', $cron_next)) . '（' . human_time_diff($cron_next) . '後）' : '<strong style="color:red;">未登録</strong>') . '</p>';
                echo '<p>⏸ 一時停止: ' . ($gemini_paused ? '<strong style="color:#b45309;">はい</strong>' : 'いいえ') . '</p>';

                // Gemini モデル使用状況
                if ( function_exists( 'contentfreaks_get_model_status' ) ) {
                    echo '<p>🤖 <strong>Geminiモデル状況:</strong></p><ul>';
                    foreach ( contentfreaks_get_model_status() as $ms ) {
                        $icon  = $ms['available'] ? '✅' : '🔴';
                        $label = $ms['available'] ? '利用可能' : 'レート制限中（最大1時間）';
                        $color = $ms['available'] ? 'green' : '#b45309';
                        echo '<li>' . $icon . ' <code>' . esc_html( $ms['model'] ) . '</code>: <span style="color:' . $color . ';">' . $label . '</span></li>';
                    }
                    echo '</ul>';
                }

                $sample = $wpdb->get_row("SELECT p.ID, p.post_title, pm.meta_value AS audio_url FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON p.ID=pm.post_id AND pm.meta_key='episode_audio_url' WHERE p.post_type='post' ORDER BY p.post_date DESC LIMIT 1");
                if ($sample) {
                    echo '<p>🔗 最新音声URL:<br><code style="word-break:break-all;font-size:11px;">' . esc_html($sample->audio_url) . '</code></p>';
                }

                if (!$cron_next) {
                    wp_schedule_event(time(), 'contentfreaks_five_minutes', 'contentfreaks_gemini_transcription_batch');
                    echo '<p style="color:green;">✅ Cron を登録しました。</p>';
                }

                echo '</div></div>';
            }
            ?>

        <?php elseif ($current_tab === 'settings'): ?>
            <!-- ===== 設定 ===== -->
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

            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">🤖 Gemini AI 設定（文字起こし・記事生成）</h2>
                <div class="inside">
                    <form method="post">
                        <?php wp_nonce_field('contentfreaks_gemini_settings', 'gemini_settings_nonce'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="gemini_api_key">Gemini API Key</label></th>
                                <td>
                                    <input type="password" id="gemini_api_key" name="gemini_api_key" class="regular-text"
                                           value="<?php echo esc_attr(get_option('contentfreaks_gemini_api_key', '')); ?>" autocomplete="off" />
                                    <p class="description">
                                        <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener">Google AI Studio</a> で無料発行できます。<br>
                                        セキュリティ強化のため、wp-config.php に<code>define('CONTENTFREAKS_GEMINI_API_KEY', 'AIzaSy...');</code>と記載する方法も推奨します（その場合はここへの入力不要）。
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <?php
                        $gemini_key_check = contentfreaks_get_gemini_api_key();
                        if (!empty($gemini_key_check)): ?>
                        <div style="background:#f0fff0;padding:10px 15px;border-left:4px solid #4CAF50;border-radius:4px;margin-bottom:15px;">
                            ✅ API Key が設定されています。新規エピソードは RSS 同期後に自動でキューに追加され、5分毎に記事化されます。
                        </div>
                        <?php else: ?>
                        <div style="background:#f0f8ff;padding:10px 15px;border-left:4px solid #2196F3;border-radius:4px;margin-bottom:15px;">
                            ℹ️ API Key を入力すると、ポッドキャスト音声から自動でブログ記事が生成されます（Gemini 1.5 Flash 無料枠）。
                        </div>
                        <?php endif; ?>
                        <?php submit_button('Gemini 設定を保存', 'primary', 'save_gemini_settings'); ?>
                    </form>
                </div>
            </div>

            <div class="postbox">
                <h2 class="hndle">🎬 YouTube API 設定</h2>
                <div class="inside">
                    <form method="post">
                        <?php wp_nonce_field('contentfreaks_youtube_settings', 'youtube_settings_nonce'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="youtube_api_key">API キー</label></th>
                                <td>
                                    <input type="password" id="youtube_api_key" name="youtube_api_key" class="regular-text"
                                           value="<?php echo esc_attr(get_option('contentfreaks_youtube_api_key', '')); ?>" autocomplete="off" />
                                    <p class="description">Google Cloud Console で発行した YouTube Data API v3 のキー</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="youtube_channel_id">チャンネル ID</label></th>
                                <td>
                                    <input type="text" id="youtube_channel_id" name="youtube_channel_id" class="regular-text"
                                           value="<?php echo esc_attr(get_option('contentfreaks_youtube_channel_id', '')); ?>" />
                                    <p class="description">UCxxxxxxxx 形式。YouTube Studio → 設定 → チャンネル → 高度な設定 で確認できます。</p>
                                </td>
                            </tr>
                        </table>
                        <?php
                        $yt_stats = contentfreaks_get_youtube_channel_stats();
                        if ($yt_stats): ?>
                        <div style="background:#f0fff0;padding:10px 15px;border-left:4px solid #4CAF50;border-radius:4px;margin-bottom:15px;">
                            ✅ API接続OK &nbsp;|&nbsp;
                            登録者数: <strong><?php echo contentfreaks_format_yt_number($yt_stats['subscriber_count']); ?></strong> &nbsp;|&nbsp;
                            総再生数: <strong><?php echo contentfreaks_format_yt_number($yt_stats['view_count']); ?></strong>
                        </div>
                        <?php elseif (get_option('contentfreaks_youtube_api_key')): ?>
                        <div style="background:#fff8f0;padding:10px 15px;border-left:4px solid #ff9800;border-radius:4px;margin-bottom:15px;">
                            ⚠️ API接続に失敗しました。キーまたはチャンネルIDを確認してください。
                        </div>
                        <?php else: ?>
                        <div style="background:#f0f8ff;padding:10px 15px;border-left:4px solid #2196F3;border-radius:4px;margin-bottom:15px;">
                            ℹ️ APIキーを入力するとエピソードページのヒーローに登録者数・再生数が表示されます。
                        </div>
                        <?php endif; ?>
                        <?php submit_button('YouTube設定を保存', 'primary', 'save_youtube_settings'); ?>
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
                                <th scope="row">総フォロワー数</th>
                                <td>
                                    <strong><?php echo esc_html(contentfreaks_get_total_followers()); ?></strong>
                                    <p class="description">Spotify / Apple Podcasts / YouTube の各数値から自動計算されます。</p>
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

            <!-- RSS・コンテンツ管理 -->
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">📡 RSS・コンテンツ管理</h2>
                <div class="inside">
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_sync', 'sync_nonce'); ?>
                            <input type="submit" name="manual_sync" class="button-primary" value="📥 手動同期実行" />
                        </form>
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_re_extract_tags', 're_extract_tags_nonce'); ?>
                            <input type="submit" name="re_extract_tags" class="button-secondary" value="🏷️ タグ再抽出" />
                        </form>
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_clear_cache', 'clear_cache_nonce'); ?>
                            <input type="submit" name="clear_cache" class="button-secondary" value="🗑️ キャッシュクリア" />
                        </form>
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_flush_rewrite_rules', 'flush_rewrite_rules_nonce'); ?>
                            <input type="submit" name="flush_rewrite_rules" class="button-secondary" value="🔄 リライトルール更新" />
                        </form>
                    </div>
                </div>
            </div>

            <!-- YouTube -->
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">🎬 YouTube</h2>
                <div class="inside">
                    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_sync_youtube_videos', 'sync_youtube_videos_nonce'); ?>
                            <input type="submit" name="sync_youtube_videos" class="button-secondary" value="🎬 YouTube動画紐付け" />
                        </form>
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_debug_youtube_match', 'debug_youtube_match_nonce'); ?>
                            <input type="submit" name="debug_youtube_match" class="button-secondary" value="🔎 紐付け診断" />
                        </form>
                    </div>
                    <?php if (function_exists('contentfreaks_get_youtube_sync_job_status')) : ?>
                        <?php $youtube_job_status = contentfreaks_get_youtube_sync_job_status(); ?>
                        <div style="padding:10px 14px;border:1px solid #dcdcde;border-radius:6px;background:#fafafa;">
                            <strong>紐付け状態:</strong>
                            <?php if ($youtube_job_status['status'] === 'queued') : ?>
                                <span style="color:#b45309;">実行待ち</span>
                                <?php if (!empty($youtube_job_status['next_run'])) : ?>
                                    <span style="margin-left:8px;color:#666;">次回: <?php echo esc_html(date_i18n('H:i:s', $youtube_job_status['next_run'])); ?></span>
                                <?php endif; ?>
                            <?php elseif ($youtube_job_status['status'] === 'done') : ?>
                                <span style="color:#15803d;">完了</span>
                                <?php if (!empty($youtube_job_status['last']['result'])) : ?>
                                    <?php $last_result = $youtube_job_status['last']['result']; ?>
                                    <span style="margin-left:8px;color:#666;">紐付け: <?php echo esc_html((string) ($last_result['synced'] ?? 0)); ?>件 / 未マッチ: <?php echo esc_html((string) ($last_result['skipped'] ?? 0)); ?>件</span>
                                <?php endif; ?>
                            <?php else : ?>
                                <span style="color:#4b5563;">待機中ではありません</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 診断 -->
            <div class="postbox" style="margin-bottom: 20px;">
                <h2 class="hndle">🔍 診断テスト</h2>
                <div class="inside">
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_test_rss', 'test_rss_nonce'); ?>
                            <input type="submit" name="test_rss" class="button-secondary" value="🔍 RSS接続テスト" />
                        </form>
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field('contentfreaks_test_url', 'test_url_nonce'); ?>
                            <input type="submit" name="test_url" class="button-secondary" value="🌐 URL構造テスト" />
                        </form>
                    </div>
                </div>
            </div>

            <?php
            // YouTube紐付け診断結果
            if (isset($_POST['debug_youtube_match']) && wp_verify_nonce($_POST['debug_youtube_match_nonce'], 'contentfreaks_debug_youtube_match')) {
                $api_key    = (defined('CONTENTFREAKS_YOUTUBE_API_KEY')    && CONTENTFREAKS_YOUTUBE_API_KEY    !== '')
                                ? CONTENTFREAKS_YOUTUBE_API_KEY
                                : get_option('contentfreaks_youtube_api_key', '');
                $channel_id = (defined('CONTENTFREAKS_YOUTUBE_CHANNEL_ID') && CONTENTFREAKS_YOUTUBE_CHANNEL_ID !== '')
                                ? CONTENTFREAKS_YOUTUBE_CHANNEL_ID
                                : get_option('contentfreaks_youtube_channel_id', '');

                echo '<div class="postbox" style="margin-bottom:20px;"><h2 class="hndle">🔎 YouTube紐付け診断</h2><div class="inside">';

                $wp_episodes = get_posts(array('post_type'=>'post','posts_per_page'=>-1,'meta_key'=>'is_podcast_episode','meta_value'=>'1','fields'=>'ids'));
                $wp_key_stats   = array('number' => 0, 'part1' => 0, 'part2' => 0, 'final' => 0, 'title' => 0);
                $wp_key_samples = array('number' => array(), 'part1' => array(), 'part2' => array(), 'final' => array(), 'title' => array());
                $wp_no_key      = array();
                foreach ($wp_episodes as $pid) {
                    $title = get_the_title($pid);
                    $key = contentfreaks_make_title_episode_key($title);
                    if (!$key) { $wp_no_key[] = $title; continue; }
                    $key_parts = explode('::', $key);
                    $key_kind = $key_parts[2] ?? ($key_parts[1] ?? 'unknown');
                    if (isset($wp_key_stats[$key_kind])) {
                        $wp_key_stats[$key_kind]++;
                        if (count($wp_key_samples[$key_kind]) < 5) $wp_key_samples[$key_kind][] = $title;
                    }
                }
                echo '<h4>WP投稿側 (マッチキー)</h4>';
                echo '<p>話数あり: <strong>' . $wp_key_stats['number'] . '</strong> / 前編: <strong>' . $wp_key_stats['part1'] . '</strong> / 後編: <strong>' . $wp_key_stats['part2'] . '</strong> / 最終回: <strong>' . $wp_key_stats['final'] . '</strong> / 作品名のみ: <strong>' . $wp_key_stats['title'] . '</strong> / 抽出不可: <strong>' . count($wp_no_key) . '</strong></p>';
                foreach (array('number'=>'話数あり','part1'=>'前編','part2'=>'後編','final'=>'最終回','title'=>'作品名のみ') as $sk => $sl) {
                    if (!empty($wp_key_samples[$sk])) {
                        echo '<details><summary>' . esc_html($sl) . 'サンプル</summary><ul>';
                        foreach ($wp_key_samples[$sk] as $t) echo '<li>' . esc_html($t) . '</li>';
                        echo '</ul></details>';
                    }
                }
                if (!empty($wp_no_key)) {
                    echo '<details><summary>抽出不可タイトル（先頭5件）</summary><ul>';
                    foreach (array_slice($wp_no_key, 0, 5) as $t) echo '<li>' . esc_html($t) . '</li>';
                    echo '</ul></details>';
                }

                if (!empty($api_key) && !empty($channel_id)) {
                    $playlist_id = 'UU' . substr($channel_id, 2);
                    $resp = wp_remote_get(add_query_arg(array('part'=>'snippet','playlistId'=>$playlist_id,'maxResults'=>20,'key'=>$api_key), 'https://www.googleapis.com/youtube/v3/playlistItems'), array('timeout'=>15));
                    echo '<h4>YouTube動画（最新20件）</h4>';
                    if (!is_wp_error($resp) && wp_remote_retrieve_response_code($resp) === 200) {
                        $data = json_decode(wp_remote_retrieve_body($resp), true);
                        $wp_keys = array();
                        foreach ($wp_episodes as $pid) { $k = contentfreaks_make_title_episode_key(get_the_title($pid)); if ($k) $wp_keys[] = $k; }
                        echo '<table class="widefat" style="font-size:13px;"><thead><tr><th>タイトル</th><th style="width:140px;">キー</th><th style="width:100px;">種別</th><th style="width:60px;">一致</th></tr></thead><tbody>';
                        foreach ($data['items'] as $item) {
                            $yt_title = $item['snippet']['title'] ?? '';
                            $yt_key   = contentfreaks_make_title_episode_key($yt_title);
                            $yt_kind = '';
                            if ($yt_key) {
                                $yt_key_parts = explode('::', $yt_key);
                                $yt_kind = (!empty($yt_key_parts[2]) ? ($yt_key_parts[2] === 'part1' ? '前編' : ($yt_key_parts[2] === 'part2' ? '後編' : $yt_key_parts[2])) : ($yt_key_parts[1] ?? ''));
                            }
                            $matched = ($yt_key && in_array($yt_key, $wp_keys));
                            echo '<tr><td>' . esc_html($yt_title) . '</td><td style="font-family:monospace;">' . ($yt_key ? esc_html($yt_key) : '—') . '</td><td style="text-align:center;">' . ($yt_kind ?: '—') . '</td><td style="text-align:center;">' . ($matched ? '✅' : ($yt_key ? '❌' : '—')) . '</td></tr>';
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<p style="color:red;">YouTube API接続失敗</p>';
                    }
                } else {
                    echo '<p style="color:orange;">⚠️ APIキーまたはチャンネルIDが未設定</p>';
                }
                echo '</div></div>';
            }

            // URL構造テスト結果
            if (isset($_POST['test_url']) && wp_verify_nonce($_POST['test_url_nonce'], 'contentfreaks_test_url')) {
                echo '<div class="postbox" style="margin-bottom:20px;"><h2 class="hndle">🌐 URL構造テスト</h2><div class="inside">';
                echo '<ul>';
                echo '<li><strong>サイトURL:</strong> ' . esc_html(home_url()) . '</li>';
                echo '<li><strong>パーマリンク:</strong> ' . esc_html(get_option('permalink_structure') ?: 'デフォルト') . '</li>';
                echo '</ul>';
                $rewrite_rules = get_option('rewrite_rules', array());
                $episodes_rules = array();
                if (is_array($rewrite_rules)) { foreach ($rewrite_rules as $p => $r) { if (strpos($p, 'episodes') !== false) $episodes_rules[$p] = $r; } }
                if (!empty($episodes_rules)) { echo '<p style="color:green;">✅ エピソード用ルールあり</p>'; }
                else { echo '<p style="color:red;">❌ エピソード用ルールなし</p>'; }
                echo '<p>page-episodes.php: ' . (file_exists(get_stylesheet_directory() . '/page-episodes.php') ? '✅' : '❌') . ' / episodes固定ページ: ' . (get_page_by_path('episodes') ? '✅' : '❌') . '</p>';
                echo '</div></div>';
            }

            // RSSフィードテスト結果
            if (isset($_POST['test_rss']) && wp_verify_nonce($_POST['test_rss_nonce'], 'contentfreaks_test_rss')) {
                echo '<div class="postbox" style="margin-bottom:20px;"><h2 class="hndle">🔍 RSSテスト</h2><div class="inside">';
                contentfreaks_clear_rss_cache();
                $episodes = contentfreaks_get_rss_episodes(5);
                if (!empty($episodes)) {
                    echo '<p style="color:green;">✅ 取得成功: ' . count($episodes) . ' 件</p>';
                    echo '<div style="max-height:300px;overflow-y:auto;border:1px solid #ddd;padding:10px;background:#f9f9f9;">';
                    foreach ($episodes as $episode) {
                        echo '<div style="background:#fff;padding:12px;margin-bottom:8px;border-radius:4px;border-left:3px solid #2196F3;">';
                        echo '<strong>' . esc_html($episode['title']) . '</strong>';
                        echo '<br><small>📅 ' . esc_html($episode['formatted_date']) . ' | 🎵 ' . ($episode['audio_url'] ? 'あり' : 'なし') . ' | ⏱️ ' . ($episode['duration'] ?: '不明') . '</small>';
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p style="color:red;">❌ 取得失敗</p>';
                }
                echo '</div></div>';
            }
            ?>

            <!-- ヘルプ -->
            <div class="postbox">
                <h2 class="hndle">ℹ️ ヘルプ</h2>
                <div class="inside">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;">
                        <div style="background:#f0f8ff;padding:12px;border-left:3px solid #2196F3;">
                            <h4 style="margin:0 0 6px;">🏷️ 自動タグ</h4>
                            <p style="margin:0;font-size:13px;">タイトルの『』内を自動タグ化</p>
                        </div>
                        <div style="background:#f9f9f9;padding:12px;border-left:3px solid #0073aa;">
                            <h4 style="margin:0 0 6px;">📡 RSS同期</h4>
                            <p style="margin:0;font-size:13px;">1時間毎に自動実行</p>
                        </div>
                        <div style="background:#fffbf0;padding:12px;border-left:3px solid #ff9800;">
                            <h4 style="margin:0 0 6px;">🔧 トラブル</h4>
                            <p style="margin:0;font-size:13px;">404 → リライトルール更新 / キャッシュ → クリア</p>
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
 * 数値っぽい文字列を整数に正規化する
 */
function contentfreaks_normalize_stat_number($value) {
    if (is_numeric($value)) {
        return (int) $value;
    }

    $digits = preg_replace('/[^0-9]/', '', (string) $value);
    return $digits === '' ? 0 : (int) $digits;
}

/**
 * 総フォロワー数を取得
 */
function contentfreaks_get_total_followers($youtube_followers = null) {
    $spotify_followers = contentfreaks_normalize_stat_number(get_theme_mod('mk_spotify_followers', '300'));
    $apple_followers   = contentfreaks_normalize_stat_number(get_theme_mod('mk_apple_followers', '150'));

    if ($youtube_followers === null) {
        $yt_stats = contentfreaks_get_youtube_channel_stats();
        $youtube_followers = $yt_stats
            ? $yt_stats['subscriber_count']
            : get_theme_mod('mk_youtube_subscribers', '900');
    }

    $youtube_followers = contentfreaks_normalize_stat_number($youtube_followers);

    return $spotify_followers + $apple_followers + $youtube_followers;
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
