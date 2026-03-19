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

    function copyTextToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            return navigator.clipboard.writeText(text);
        }

        return new Promise(function (resolve, reject) {
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.setAttribute('readonly', 'readonly');
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();

            try {
                var copied = document.execCommand('copy');
                document.body.removeChild(textarea);
                if (copied) {
                    resolve();
                } else {
                    reject(new Error('copy failed'));
                }
            } catch (error) {
                document.body.removeChild(textarea);
                reject(error);
            }
        });
    }

    function initFooterRssCopy() {
        const copyBtn = document.querySelector('.footer-rss-copy');
        if (!copyBtn) return;

        copyBtn.addEventListener('click', function () {
            const url = this.dataset.url;
            const originalText = this.textContent;

            copyTextToClipboard(url).then(function () {
                copyBtn.classList.add('is-copied');
                copyBtn.textContent = 'コピーしました';
                setTimeout(function () {
                    copyBtn.classList.remove('is-copied');
                    copyBtn.textContent = originalText;
                }, 2000);
            }).catch(function () {
                window.prompt('RSSフィードURLをコピーしてください', url);
            });
        });
    }

    // ===== 4. お気に入りエピソード（無効化済み） =====

    function initFavorites() {
        // お気に入り機能は無効化されました
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
            var honeypot = form.querySelector('input[name="website_url"]');
            formData.append('action', 'submit_testimonial');
            formData.append('nonce', contentfreaks_ajax.nonce);
            formData.append('name', nameInput.value.trim());
            formData.append('message', msgInput.value.trim());
            formData.append('website_url', honeypot ? honeypot.value : '');

            fetch(contentfreaks_ajax.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData.toString()
            })
                .then(function (r) {
                    if (!r.ok) {
                        throw new Error('HTTP ' + r.status);
                    }
                    return r.text();
                })
                .then(function (text) {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('レスポンスの解析に失敗しました');
                    }
                })
                .then(function (data) {
                    if (data.success) {
                        showFormMessage(msgDiv, data.data.message, 'success');
                        form.reset();
                    } else {
                        var msg = (data.data && data.data.message) ? data.data.message : '送信に失敗しました。';
                        showFormMessage(msgDiv, msg, 'error');
                    }
                })
                .catch(function (err) {
                    showFormMessage(msgDiv, '通信エラーが発生しました。しばらく経ってから再度お試しください。', 'error');
                    if (typeof console !== 'undefined') {
                        console.error('Testimonial submit error:', err);
                    }
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

    // ===== 9. ランダムエピソードボタン =====

    function initRandomEpisode() {
        var btn = document.getElementById('random-episode-btn');
        if (!btn) return;
        if (typeof contentfreaks_ajax === 'undefined') return;

        btn.addEventListener('click', function () {
            btn.disabled = true;
            btn.textContent = '🎲 選んでいます...';

            var formData = new URLSearchParams();
            formData.append('action', 'random_episode');
            formData.append('nonce', contentfreaks_ajax.nonce);

            fetch(contentfreaks_ajax.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success && data.data.url) {
                        window.location.href = data.data.url;
                    } else {
                        btn.textContent = '🎲 今日の1本';
                        btn.disabled = false;
                    }
                })
                .catch(function () {
                    btn.textContent = '🎲 今日の1本';
                    btn.disabled = false;
                });
        });
    }

    // ===== 10. 再生速度コントロール =====

    function initPlaybackSpeed() {
        var buttons = document.querySelectorAll('.speed-btn');
        if (buttons.length === 0) return;

        var audio = document.querySelector('.episode-audio-player');
        if (!audio) return;

        // localStorageから前回の設定を復元
        var saved = localStorage.getItem('cf-playback-speed');
        if (saved) {
            var rate = parseFloat(saved);
            audio.playbackRate = rate;
            buttons.forEach(function (b) {
                b.classList.toggle('active', parseFloat(b.dataset.speed) === rate);
            });
        }

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var speed = parseFloat(this.dataset.speed);
                audio.playbackRate = speed;
                localStorage.setItem('cf-playback-speed', speed);
                buttons.forEach(function (b) { b.classList.remove('active'); });
                this.classList.add('active');
            });
        });
    }

    // ===== 11. エピソードリアクション =====

    // ===== 10.5 音声再生位置の保持 =====

    function initAudioResume() {
        var audio = document.querySelector('.episode-audio-player');
        if (!audio) return;

        var postId = '';
        if (typeof contentfreaks_ajax !== 'undefined' && contentfreaks_ajax.post_id) {
            postId = String(contentfreaks_ajax.post_id);
        }
        if (!postId) {
            var reactionsEl = document.getElementById('episode-reactions');
            if (reactionsEl) postId = reactionsEl.dataset.postId;
        }
        if (!postId) return;

        var storageKey = 'cf-audio-pos-' + postId;
        var savedPos = parseFloat(localStorage.getItem(storageKey) || '0');

        // 保存位置があれば復元UIを表示
        if (savedPos > 10) {
            var resumeBar = document.createElement('div');
            resumeBar.className = 'audio-resume-bar';
            var mins = Math.floor(savedPos / 60);
            var secs = Math.floor(savedPos % 60);
            var timeStr = mins + ':' + (secs < 10 ? '0' : '') + secs;
            resumeBar.innerHTML = '<button type="button" class="resume-btn">▶ 前回の続き（' + timeStr + '）から再生</button>' +
                '<button type="button" class="resume-dismiss">\u00d7</button>';

            var player = audio.closest('.episode-inline-player');
            if (player) {
                player.insertBefore(resumeBar, audio);
            }

            resumeBar.querySelector('.resume-btn').addEventListener('click', function () {
                audio.currentTime = savedPos;
                audio.play();
                resumeBar.remove();
            });

            resumeBar.querySelector('.resume-dismiss').addEventListener('click', function () {
                localStorage.removeItem(storageKey);
                resumeBar.remove();
            });
        }

        // 再生中は10秒ごとに位置を保存
        var saveInterval = null;
        audio.addEventListener('play', function () {
            if (saveInterval) clearInterval(saveInterval);
            saveInterval = setInterval(function () {
                if (audio.currentTime > 5) {
                    localStorage.setItem(storageKey, audio.currentTime);
                }
            }, 10000);
        });

        audio.addEventListener('pause', function () {
            if (saveInterval) clearInterval(saveInterval);
            if (audio.currentTime > 5) {
                localStorage.setItem(storageKey, audio.currentTime);
            }
        });

        // 再生完了時は位置をクリア
        audio.addEventListener('ended', function () {
            if (saveInterval) clearInterval(saveInterval);
            localStorage.removeItem(storageKey);
        });
    }

    // ===== 11. エピソードリアクション（続き） =====

    function initReactions() {
        var container = document.getElementById('episode-reactions');
        if (!container) return;
        if (typeof contentfreaks_ajax === 'undefined') return;

        var postId = container.dataset.postId;

        // リアクション数を取得
        fetch(contentfreaks_ajax.ajax_url + '?action=get_reactions&post_id=' + postId)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    updateReactionCounts(data.data.counts);
                }
            });

        // 送信済みリアクションをlocalStorageで管理
        var reacted = getReactedMap();

        var buttons = container.querySelectorAll('.reaction-btn');
        buttons.forEach(function (btn) {
            var reaction = btn.dataset.reaction;
            if (reacted[postId] && reacted[postId].indexOf(reaction) > -1) {
                btn.classList.add('reacted');
                btn.setAttribute('aria-pressed', 'true');
            }

            btn.addEventListener('click', function () {
                if (btn.classList.contains('reacted')) return; // 既に押下済み

                btn.classList.add('reacted');
                btn.setAttribute('aria-pressed', 'true');
                btn.disabled = true;

                // アニメーション
                var emoji = btn.querySelector('.reaction-emoji');
                emoji.style.transform = 'scale(1.4)';
                setTimeout(function () { emoji.style.transform = ''; }, 300);

                var formData = new URLSearchParams();
                formData.append('action', 'save_reaction');
                formData.append('nonce', contentfreaks_ajax.nonce);
                formData.append('post_id', postId);
                formData.append('reaction', reaction);

                fetch(contentfreaks_ajax.ajax_url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.success) {
                            updateReactionCounts(data.data.counts);
                            saveReacted(postId, reaction);
                        }
                        btn.disabled = false;
                    })
                    .catch(function () {
                        btn.classList.remove('reacted');
                        btn.disabled = false;
                    });
            });
        });

        function updateReactionCounts(counts) {
            Object.keys(counts).forEach(function (key) {
                var el = container.querySelector('[data-count="' + key + '"]');
                if (el) el.textContent = counts[key] > 0 ? counts[key] : '';
            });
        }

        function getReactedMap() {
            try {
                return JSON.parse(localStorage.getItem('cf-reactions') || '{}');
            } catch (e) {
                return {};
            }
        }

        function saveReacted(pid, reaction) {
            var map = getReactedMap();
            if (!map[pid]) map[pid] = [];
            if (map[pid].indexOf(reaction) === -1) {
                map[pid].push(reaction);
            }
            localStorage.setItem('cf-reactions', JSON.stringify(map));
        }
    }

    // ===== 12. リスニング統計（localStorage） =====

    function initListeningStats() {
        if (!document.querySelector('.single-episode-container')) return;

        // 訪問エピソードを記録
        var postId = '';
        var reactionsEl = document.getElementById('episode-reactions');
        if (reactionsEl) {
            postId = reactionsEl.dataset.postId;
        } else if (typeof contentfreaks_ajax !== 'undefined' && contentfreaks_ajax.post_id) {
            postId = String(contentfreaks_ajax.post_id);
        }

        if (postId) {
            try {
                var visited = JSON.parse(localStorage.getItem('cf-visited') || '[]');
                if (visited.indexOf(postId) === -1) {
                    visited.push(postId);
                    localStorage.setItem('cf-visited', JSON.stringify(visited));
                }
                // 最終視聴日を記録
                localStorage.setItem('cf-last-listen', new Date().toISOString().split('T')[0]);
            } catch (e) {}
        }
    }

    function init() {
        initScrollToTop();
        initShareButtons();
        initFooterRssCopy();
        initFavorites();
        initAjaxSearch();
        initTestimonialForm();
        initReadingProgress();
        initChapterSeek();
        initRandomEpisode();
        initPlaybackSpeed();
        initAudioResume();
        initReactions();
        initListeningStats();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
