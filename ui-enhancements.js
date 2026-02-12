/**
 * ContentFreaks UI Enhancements v1.0
 * - トップに戻るボタン
 * - SNSシェア
 * - お気に入りエピソード
 * - パンくずナビ（PHP側で出力）
 * - AJAX検索
 */

(function () {
    'use strict';

    // ===== 1. トップに戻るボタン =====

    function initScrollToTop() {
        const btn = document.createElement('button');
        btn.className = 'scroll-to-top';
        btn.setAttribute('aria-label', 'ページの先頭に戻る');
        btn.innerHTML = '↑';
        document.body.appendChild(btn);

        let ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () {
                    if (window.scrollY > 400) {
                        btn.classList.add('visible');
                    } else {
                        btn.classList.remove('visible');
                    }
                    ticking = false;
                });
                ticking = true;
            }
        });

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ===== 2. SNSシェアボタン（エピソード詳細ページ） =====

    function initShareButtons() {
        const episodeHeader = document.querySelector('.episode-platform-links');
        if (!episodeHeader) return;

        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);
        const rawUrl = window.location.href;
        const rawTitle = document.title;

        const container = document.createElement('div');
        container.className = 'share-buttons';

        // Web Share API 対応チェック
        if (navigator.share) {
            container.innerHTML = `
                <span class="share-buttons-title">📤 シェア</span>
                <button class="share-btn share-btn-native">📤 シェアする</button>
                <a href="https://twitter.com/intent/tweet?url=${url}&text=${title}" 
                   target="_blank" rel="noopener" class="share-btn share-btn-x">
                    𝕏 ポスト
                </a>
            `;
            const nativeBtn = container.querySelector('.share-btn-native');
            nativeBtn.addEventListener('click', function () {
                navigator.share({ title: rawTitle, url: rawUrl }).catch(function () {});
            });
        } else {
            container.innerHTML = `
                <span class="share-buttons-title">📤 シェア</span>
                <a href="https://twitter.com/intent/tweet?url=${url}&text=${title}" 
                   target="_blank" rel="noopener" class="share-btn share-btn-x">
                    𝕏 ポスト
                </a>
                <a href="https://social-plugins.line.me/lineit/share?url=${url}" 
                   target="_blank" rel="noopener" class="share-btn share-btn-line">
                    LINE 送る
                </a>
                <button class="share-btn share-btn-copy" data-url="${rawUrl}">
                    🔗 コピー
                </button>
            `;
        }

        episodeHeader.parentNode.insertBefore(container, episodeHeader.nextSibling);

        // コピーボタン（navigator.share 対応時は存在しない）
        const copyBtn = container.querySelector('.share-btn-copy');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                navigator.clipboard.writeText(this.dataset.url).then(function () {
                    copyBtn.classList.add('copied');
                    copyBtn.textContent = '✓ コピーしました';
                    setTimeout(function () {
                        copyBtn.classList.remove('copied');
                        copyBtn.innerHTML = '🔗 コピー';
                    }, 2000);
                });
            });
        }
    }

    // ===== 4. お気に入りエピソード =====

    function getFavorites() {
        try {
            return JSON.parse(localStorage.getItem('cf-favorites') || '[]');
        } catch {
            return [];
        }
    }

    function saveFavorites(ids) {
        localStorage.setItem('cf-favorites', JSON.stringify(ids));
    }

    function toggleFavorite(postId) {
        const favs = getFavorites();
        const idx = favs.indexOf(postId);
        if (idx > -1) {
            favs.splice(idx, 1);
        } else {
            favs.push(postId);
        }
        saveFavorites(favs);
        return favs.includes(postId);
    }

    function initFavorites() {
        const cards = document.querySelectorAll('.episode-card');
        if (cards.length === 0) return;

        const favs = getFavorites();

        cards.forEach(function (card) {
            const link = card.querySelector('.episode-title a');
            if (!link) return;

            // hrefからpost識別子を取得（URLをそのまま使用）
            const href = link.getAttribute('href');
            if (!href) return;

            const header = card.querySelector('.episode-card-header') || card.querySelector('.episode-thumbnail');
            if (!header) return;

            // 既にボタンがある場合はスキップ
            if (header.querySelector('.favorite-btn')) return;

            const btn = document.createElement('button');
            btn.className = 'favorite-btn' + (favs.includes(href) ? ' favorited' : '');
            btn.setAttribute('aria-label', 'お気に入りに追加');
            btn.innerHTML = '<span class="heart-icon"></span>';

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const isFav = toggleFavorite(href);
                btn.classList.toggle('favorited', isFav);
                btn.setAttribute('aria-label', isFav ? 'お気に入りから削除' : 'お気に入りに追加');
                updateFavoritesCount();
            });

            header.style.position = 'relative';
            header.appendChild(btn);
        });

        // お気に入りフィルターボタン（エピソード一覧ページ）
        initFavoritesFilter();
    }

    function initFavoritesFilter() {
        const searchControls = document.querySelector('.search-controls');
        if (!searchControls) return;

        const favs = getFavorites();
        const filterBtn = document.createElement('button');
        filterBtn.className = 'favorites-filter';
        filterBtn.innerHTML = `♥ お気に入り <span class="favorites-count">${favs.length}</span>`;

        let filterActive = false;

        filterBtn.addEventListener('click', function () {
            filterActive = !filterActive;
            filterBtn.classList.toggle('active', filterActive);
            const cards = document.querySelectorAll('.episode-card');

            if (filterActive) {
                const currentFavs = getFavorites();
                cards.forEach(function (card) {
                    const link = card.querySelector('.episode-title a');
                    const href = link ? link.getAttribute('href') : '';
                    card.style.display = currentFavs.includes(href) ? '' : 'none';
                });
            } else {
                cards.forEach(function (card) {
                    card.style.display = '';
                });
            }
        });

        searchControls.appendChild(filterBtn);
    }

    function updateFavoritesCount() {
        const badge = document.querySelector('.favorites-count');
        if (badge) {
            badge.textContent = getFavorites().length;
        }
    }

    // ===== 5. AJAX検索（エピソード検索拡張） =====

    function initAjaxSearch() {
        const searchInput = document.getElementById('episode-search');
        if (!searchInput) return;
        if (typeof contentfreaks_ajax === 'undefined') return;

        let debounceTimer;
        const grid = document.getElementById('episodes-grid');
        const originalHTML = grid ? grid.innerHTML : '';
        const loadMoreWrapper = document.getElementById('load-more-wrapper');

        // 検索クリアボタンを追加
        var clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.className = 'search-clear-btn';
        clearBtn.setAttribute('aria-label', '検索をクリア');
        clearBtn.innerHTML = '×';
        clearBtn.style.display = 'none';
        searchInput.parentNode.style.position = 'relative';
        searchInput.parentNode.appendChild(clearBtn);

        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            clearBtn.style.display = 'none';
            if (grid) grid.innerHTML = originalHTML;
            var countEl = document.getElementById('search-result-count');
            if (countEl) countEl.style.display = 'none';
            if (loadMoreWrapper) loadMoreWrapper.style.display = '';
            initFavorites();
            searchInput.focus();
        });

        searchInput.addEventListener('input', function () {
            const term = this.value.trim();

            clearTimeout(debounceTimer);
            clearBtn.style.display = term.length > 0 ? '' : 'none';

            if (term.length === 0) {
                // 検索語が空なら元に戻す
                if (grid) grid.innerHTML = originalHTML;
                var countEl = document.getElementById('search-result-count');
                if (countEl) countEl.style.display = 'none';
                if (loadMoreWrapper) loadMoreWrapper.style.display = '';
                initFavorites(); // お気に入りボタン再初期化
                return;
            }

            // 検索中はLoad Moreを非表示
            if (loadMoreWrapper) loadMoreWrapper.style.display = 'none';

            if (term.length < 2) return;

            debounceTimer = setTimeout(function () {
                // ローディング表示
                if (grid) {
                    grid.classList.add('is-searching');
                    grid.insertAdjacentHTML('afterbegin', '<div class="search-loading-indicator" id="search-loading"><div class="search-spinner"></div></div>');
                }

                const formData = new URLSearchParams();
                formData.append('action', 'search_episodes');
                formData.append('nonce', contentfreaks_ajax.nonce);
                formData.append('search', term);

                fetch(contentfreaks_ajax.ajax_url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        // ローディング解除
                        grid.classList.remove('is-searching');
                        var loadingEl = document.getElementById('search-loading');
                        if (loadingEl) loadingEl.remove();

                        if (data.success && grid) {
                            if (data.data.html) {
                                grid.innerHTML = data.data.html;
                                // 検索結果件数を表示
                                var countEl = document.getElementById('search-result-count');
                                if (!countEl) {
                                    countEl = document.createElement('div');
                                    countEl.id = 'search-result-count';
                                    countEl.className = 'search-result-count';
                                    grid.parentNode.insertBefore(countEl, grid);
                                }
                                countEl.textContent = '\u300c' + term + '\u300d\u306e\u691c\u7d22\u7d50\u679c: ' + data.data.count + '\u4ef6';
                                countEl.style.display = '';
                            } else {
                                grid.innerHTML = '<div class="no-episodes"><div class="no-episodes-icon">\ud83d\udd0d</div><h3>\u898b\u3064\u304b\u308a\u307e\u305b\u3093\u3067\u3057\u305f</h3><p>\u300c' + term + '\u300d\u306b\u4e00\u81f4\u3059\u308b\u30a8\u30d4\u30bd\u30fc\u30c9\u306f\u3042\u308a\u307e\u305b\u3093\u3002</p></div>';
                                var countEl2 = document.getElementById('search-result-count');
                                if (countEl2) countEl2.style.display = 'none';
                            }
                            initFavorites(); // お気に入りボタン再初期化
                        }
                    });
            }, 400);
        });
    }

    // ===== 6. リスナー投稿フォーム =====

    function initTestimonialForm() {
        var form = document.getElementById('testimonial-form');
        if (!form) return;
        if (typeof contentfreaks_ajax === 'undefined') return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var nameInput = form.querySelector('input[name="name"]');
            var msgInput = form.querySelector('textarea[name="message"]');
            var msgDiv = document.getElementById('form-message');
            var submitBtn = form.querySelector('.form-submit-btn');

            if (!nameInput.value.trim() || !msgInput.value.trim()) {
                showFormMessage(msgDiv, 'お名前とメッセージを入力してください。', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = '送信中...';

            var formData = new URLSearchParams();
            formData.append('action', 'submit_testimonial');
            formData.append('nonce', contentfreaks_ajax.nonce);
            formData.append('name', nameInput.value.trim());
            formData.append('message', msgInput.value.trim());

            fetch(contentfreaks_ajax.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        showFormMessage(msgDiv, data.data.message, 'success');
                        form.reset();
                    } else {
                        showFormMessage(msgDiv, data.data.message || '送信に失敗しました。', 'error');
                    }
                })
                .catch(function () {
                    showFormMessage(msgDiv, '通信エラーが発生しました。', 'error');
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = '送信する';
                });
        });
    }

    function showFormMessage(el, text, type) {
        el.textContent = text;
        el.style.display = 'block';
        el.className = 'form-message form-message-' + type;
        setTimeout(function () { el.style.display = 'none'; }, 5000);
    }

    // ===== 初期化 =====

    // ===== 7. 読了プログレスバー（エピソード詳細） =====

    function initReadingProgress() {
        if (!document.querySelector('.single-episode-container')) return;

        var bar = document.createElement('div');
        bar.className = 'reading-progress-bar';
        bar.innerHTML = '<div class="reading-progress-fill"></div>';
        document.body.appendChild(bar);

        var fill = bar.querySelector('.reading-progress-fill');
        var ticking = false;

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () {
                    var docHeight = document.documentElement.scrollHeight - window.innerHeight;
                    var progress = docHeight > 0 ? (window.scrollY / docHeight) * 100 : 0;
                    fill.style.width = Math.min(progress, 100) + '%';
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    // ===== 8. チャプタータイムスタンプ → 音声シーク =====

    function initChapterSeek() {
        var seekBtns = document.querySelectorAll('.chapter-seek');
        if (seekBtns.length === 0) return;

        var audio = document.querySelector('.episode-audio-player');
        if (!audio) return;

        seekBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var timeStr = this.dataset.time;
                var parts = timeStr.split(':').map(Number);
                var seconds = 0;
                if (parts.length === 3) {
                    seconds = parts[0] * 3600 + parts[1] * 60 + parts[2];
                } else if (parts.length === 2) {
                    seconds = parts[0] * 60 + parts[1];
                }

                audio.currentTime = seconds;
                if (audio.paused) audio.play();

                // プレーヤーまでスクロール
                audio.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // アクティブ表示
                seekBtns.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');
            });
        });
    }

    /* --- フッター接近時にモバイルCTAバーを隠す --- */
    function initMobileListenBar() {
        var bar = document.getElementById('mobile-listen-bar');
        if (!bar) return;
        var footer = document.querySelector('.footer-section');
        if (!footer || !('IntersectionObserver' in window)) return;

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                bar.classList.toggle('is-hidden', entry.isIntersecting);
            });
        }, { threshold: 0.1 });

        observer.observe(footer);
    }

    function init() {
        initScrollToTop();
        initShareButtons();
        initFavorites();
        initAjaxSearch();
        initTestimonialForm();
        initMobileListenBar();
        initReadingProgress();
        initChapterSeek();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
