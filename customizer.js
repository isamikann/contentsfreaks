/**
 * ContentFreaks カスタマイザー ライブプレビュー
 * ヘッダーのホームアイコン設定のリアルタイムプレビュー
 */

(function ($) {
    'use strict';

    // ホームアイコン背景色（開始色）のライブプレビュー
    wp.customize('home_icon_bg_color', function (value) {
        value.bind(function (newval) {
            var endColor = wp.customize('home_icon_bg_color_end')();
            $('.home-link').css({
                'background': 'linear-gradient(135deg, ' + newval + ' 0%, ' + endColor + ' 100%)'
            });
            $('.home-link:hover').css({
                'box-shadow': '0 8px 25px ' + newval + '66'
            });
        });
    });

    // ホームアイコン背景色（終了色）のライブプレビュー
    wp.customize('home_icon_bg_color_end', function (value) {
        value.bind(function (newval) {
            var startColor = wp.customize('home_icon_bg_color')();
            $('.home-link').css({
                'background': 'linear-gradient(135deg, ' + startColor + ' 0%, ' + newval + ' 100%)'
            });
        });
    });

    // ホームアイコン画像のライブプレビュー
    wp.customize('home_icon_image', function (value) {
        value.bind(function (newval) {
            var iconContainer = $('.icon-container');

            if (newval) {
                // 新しい画像が設定された場合
                iconContainer.html('<img src="' + newval + '" alt="ホーム" class="home-icon-image">');
            } else {
                // 画像が削除された場合はデフォルトの絵文字に戻す
                iconContainer.html('<span class="home-icon-emoji">🏠</span>');
            }
        });
    });

    // Spotifyアイコンのライブプレビュー
    wp.customize('spotify_icon', function (value) {
        value.bind(function (newval) {
            updatePlatformIcon('spotify', newval, '🎵');
        });
    });

    // Apple Podcastsアイコンのライブプレビュー
    wp.customize('apple_podcasts_icon', function (value) {
        value.bind(function (newval) {
            updatePlatformIcon('apple', newval, '🍎');
        });
    });

    // YouTubeアイコンのライブプレビュー
    wp.customize('youtube_icon', function (value) {
        value.bind(function (newval) {
            updatePlatformIcon('youtube', newval, '📺');
        });
    });

    // プラットフォームアイコン更新関数
    function updatePlatformIcon(platform, imageUrl, defaultEmoji) {
        // ヘッダーメニューのアイコン更新
        var menuIcon = $('.slide-menu .platform-links .' + platform + ' .menu-icon');
        if (menuIcon.length) {
            if (imageUrl) {
                menuIcon.html('<img src="' + imageUrl + '" alt="' + platform + '" class="platform-icon-image">');
            } else {
                menuIcon.html(defaultEmoji);
            }
        }

        // プラットフォームカードのアイコン更新
        var platformCard = $('.platform-card.' + platform + ' .platform-icon');
        if (platformCard.length) {
            if (imageUrl) {
                platformCard.html('<img src="' + imageUrl + '" alt="' + platform + '" class="platform-card-icon-image">');
            } else {
                platformCard.html(defaultEmoji);
            }
        }

        // 小さなプラットフォームリンクのアイコン更新
        var miniLinks = $('.mini-platform-link.' + platform);
        if (miniLinks.length) {
            miniLinks.each(function () {
                if (imageUrl) {
                    $(this).html('<img src="' + imageUrl + '" alt="' + platform + '" class="mini-platform-icon-image">');
                } else {
                    $(this).html(defaultEmoji);
                }
            });
        }
    }

})(jQuery);
