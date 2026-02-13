/**
 * ContentFreaks Service Worker v1.0
 * キャッシュ戦略: 静的アセットはcache-first、ページはnetwork-first
 */

var CACHE_NAME = 'contentfreaks-v1';
var STATIC_CACHE = 'contentfreaks-static-v1';
var RUNTIME_CACHE = 'contentfreaks-runtime-v1';

// プリキャッシュ対象（テーマの静的アセット）
var PRECACHE_URLS = [
    '/',
    '/episodes/',
    '/blog/',
    '/profile/'
];

// インストール: プリキャッシュ
self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(STATIC_CACHE).then(function (cache) {
            return cache.addAll(PRECACHE_URLS).catch(function () {
                // プリキャッシュ失敗は無視（オフラインでも動作可能にするためのベストエフォート）
            });
        }).then(function () {
            return self.skipWaiting();
        })
    );
});

// アクティベーション: 古いキャッシュを削除
self.addEventListener('activate', function (event) {
    var cacheWhitelist = [STATIC_CACHE, RUNTIME_CACHE];

    event.waitUntil(
        caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames.map(function (name) {
                    if (cacheWhitelist.indexOf(name) === -1) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

// Fetch: ハイブリッドキャッシュ戦略
self.addEventListener('fetch', function (event) {
    var url = new URL(event.request.url);

    // 同一オリジンのリクエストのみ処理
    if (url.origin !== location.origin) return;

    // 管理画面やAJAXリクエストはスキップ
    if (url.pathname.indexOf('/wp-admin') !== -1 ||
        url.pathname.indexOf('/wp-login') !== -1 ||
        url.pathname.indexOf('admin-ajax.php') !== -1) {
        return;
    }

    // 静的アセット（CSS, JS, フォント, 画像）: cache-first
    if (isStaticAsset(url.pathname)) {
        event.respondWith(
            caches.match(event.request).then(function (cached) {
                if (cached) return cached;

                return fetch(event.request).then(function (response) {
                    if (response && response.status === 200 && response.type === 'basic') {
                        var clone = response.clone();
                        caches.open(RUNTIME_CACHE).then(function (cache) {
                            cache.put(event.request, clone);
                        });
                    }
                    return response;
                });
            })
        );
        return;
    }

    // HTMLページ: network-first (フォールバックでキャッシュ)
    if (event.request.headers.get('accept') &&
        event.request.headers.get('accept').indexOf('text/html') !== -1) {
        event.respondWith(
            fetch(event.request).then(function (response) {
                if (response && response.status === 200) {
                    var clone = response.clone();
                    caches.open(RUNTIME_CACHE).then(function (cache) {
                        cache.put(event.request, clone);
                    });
                }
                return response;
            }).catch(function () {
                return caches.match(event.request).then(function (cached) {
                    return cached || caches.match('/');
                });
            })
        );
        return;
    }
});

/**
 * 静的アセットかどうかを判定
 */
function isStaticAsset(pathname) {
    return /\.(css|js|woff2?|ttf|eot|svg|png|jpe?g|gif|webp|ico)(\?.*)?$/i.test(pathname);
}
