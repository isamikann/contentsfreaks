/**
 * ContentFreaks - エピソード一覧ページ専用JS
 * page-episodes.php から外部化
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // 初期カードにloadedクラスを追加
        var initialCards = document.querySelectorAll('.episode-card');
        initialCards.forEach(function (card) {
            card.addEventListener('animationend', function () {
                card.classList.add('loaded');
            });
        });

        // スクロールアニメーション
        var observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
                }
            });
        }, observerOptions);

        // 初期状態のカードを観察
        initialCards.forEach(function (card) {
            observer.observe(card);
        });

        // Load More ボタン機能
        var loadMoreBtn = document.getElementById('load-more-btn');
        var loadingIndicator = document.getElementById('loading-indicator');
        var isLoading = false;

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function () {
                loadMoreEpisodes();
            });
        }

        function loadMoreEpisodes() {
            if (isLoading) return;
            if (typeof contentfreaks_ajax === 'undefined') return;

            isLoading = true;
            var offset = parseInt(loadMoreBtn.dataset.offset, 10);
            var limit = parseInt(loadMoreBtn.dataset.limit, 10);

            // ボタンを一時的に無効化し、ローディング表示
            loadMoreBtn.disabled = true;
            loadMoreBtn.textContent = '読み込み中…';
            loadingIndicator.style.display = 'block';

            // AJAXリクエストでエピソードを取得
            fetch(contentfreaks_ajax.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=load_more_episodes&offset=' + offset + '&limit=' + limit + '&nonce=' + contentfreaks_ajax.nonce
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.success && data.data && data.data.html) {
                        // 新しいエピソードを追加
                        var episodesGrid = document.getElementById('episodes-grid');
                        var tempDiv = document.createElement('div');
                        tempDiv.innerHTML = data.data.html;

                        // 各カードを個別に追加してアニメーション
                        var newCards = tempDiv.querySelectorAll('.episode-card');
                        newCards.forEach(function (card, index) {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(30px)';
                            episodesGrid.appendChild(card);

                            setTimeout(function () {
                                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, index * 100);
                        });

                        // オフセットを更新
                        loadMoreBtn.dataset.offset = offset + limit;

                        // コンテンツがなくなったかチェック
                        if (data.data.has_more === false) {
                            document.getElementById('load-more-wrapper').style.display = 'none';
                        }
                    } else {
                        document.getElementById('load-more-wrapper').style.display = 'none';
                    }
                })
                .catch(function (error) {
                    console.error('エピソードの読み込みエラー:', error);
                })
                .finally(function () {
                    isLoading = false;
                    loadMoreBtn.disabled = false;
                    loadMoreBtn.textContent = 'もっと見る';
                    loadingIndicator.style.display = 'none';
                });
        }

        // パフォーマンス最適化：スクロール時の処理
        var ticking = false;

        function updateScrollEffects() {
            var scrollY = window.scrollY;
            var heroSection = document.querySelector('.episodes-hero');

            if (heroSection) {
                var heroHeight = heroSection.offsetHeight;
                var scrollPercent = Math.min(scrollY / heroHeight, 1);

                // パララックス効果
                heroSection.style.transform = 'translateY(' + (scrollPercent * 50) + 'px)';
                heroSection.style.opacity = 1 - scrollPercent * 0.3;
            }

            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(updateScrollEffects);
                ticking = true;
            }
        });
    });
})();
