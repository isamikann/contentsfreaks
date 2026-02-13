<?php
/**
 * PWA (Progressive Web App) サポート
 * - manifest.json の動的生成
 * - Service Worker のルート配信
 * - SW 登録スクリプト出力
 */

if (!defined('ABSPATH')) exit;

/**
 * PWA 用のリライトルールを追加
 */
function contentfreaks_pwa_rewrite_rules() {
    add_rewrite_rule('^sw\.js$', 'index.php?cf_sw=1', 'top');
    add_rewrite_rule('^manifest\.json$', 'index.php?cf_manifest=1', 'top');
}
add_action('init', 'contentfreaks_pwa_rewrite_rules');

/**
 * PWA 用クエリ変数を登録
 */
function contentfreaks_pwa_query_vars($vars) {
    $vars[] = 'cf_sw';
    $vars[] = 'cf_manifest';
    return $vars;
}
add_filter('query_vars', 'contentfreaks_pwa_query_vars');

/**
 * Service Worker ファイルをルートパスで配信
 */
function contentfreaks_serve_pwa_assets() {
    // Service Worker
    if (get_query_var('cf_sw')) {
        $sw_file = get_stylesheet_directory() . '/service-worker.js';
        if (file_exists($sw_file)) {
            header('Content-Type: application/javascript');
            header('Service-Worker-Allowed: /');
            header('Cache-Control: no-cache');
            readfile($sw_file);
        } else {
            status_header(404);
        }
        exit;
    }

    // Manifest
    if (get_query_var('cf_manifest')) {
        header('Content-Type: application/manifest+json');
        header('Cache-Control: public, max-age=86400');

        $site_name = get_bloginfo('name');
        $description = get_theme_mod('podcast_description', '好きな作品、語り尽くそう！エンタメコンテンツを熱く語るポッドキャスト');
        $artwork = get_theme_mod('podcast_artwork', '');

        $manifest = array(
            'name'             => $site_name . ' - ポッドキャスト',
            'short_name'       => $site_name,
            'description'      => $description,
            'start_url'        => '/',
            'display'          => 'standalone',
            'background_color' => '#1a1a1a',
            'theme_color'      => '#1a1a1a',
            'orientation'      => 'portrait-primary',
            'lang'             => 'ja',
            'categories'       => array('entertainment', 'podcasts'),
            'icons'            => array(),
        );

        // アートワークをアイコンとして利用
        if ($artwork) {
            $manifest['icons'][] = array(
                'src'   => $artwork,
                'sizes' => '512x512',
                'type'  => 'image/png',
                'purpose' => 'any maskable',
            );
        }

        // サイトアイコン（ファビコン）がある場合
        $site_icon_id = get_option('site_icon');
        if ($site_icon_id) {
            $icon_192 = wp_get_attachment_image_url($site_icon_id, array(192, 192));
            $icon_512 = wp_get_attachment_image_url($site_icon_id, array(512, 512));
            if ($icon_192) {
                $manifest['icons'][] = array('src' => $icon_192, 'sizes' => '192x192', 'type' => 'image/png');
            }
            if ($icon_512) {
                $manifest['icons'][] = array('src' => $icon_512, 'sizes' => '512x512', 'type' => 'image/png');
            }
        }

        echo wp_json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }
}
add_action('template_redirect', 'contentfreaks_serve_pwa_assets', 1);

/**
 * <head> にマニフェストリンクを出力
 */
function contentfreaks_pwa_head_tags() {
    echo '<link rel="manifest" href="' . esc_url(home_url('/manifest.json')) . '">' . "\n";
}
add_action('wp_head', 'contentfreaks_pwa_head_tags', 2);

/**
 * フッターに Service Worker 登録スクリプトを出力
 */
function contentfreaks_register_service_worker() {
    ?>
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('<?php echo esc_url(home_url('/sw.js')); ?>', {scope: '/'})
                .catch(function() { /* SW registration failed silently */ });
        });
    }
    </script>
    <?php
}
add_action('wp_footer', 'contentfreaks_register_service_worker', 99);
