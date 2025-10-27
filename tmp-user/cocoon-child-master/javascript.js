//ここに追加したいJavaScript、jQueryを記入してください。
//このJavaScriptファイルは、親テーマのJavaScriptファイルのあとに呼び出されます。
//JavaScriptやjQueryで親テーマのjavascript.jsに加えて関数を記入したい時に使用します。

(function ($) {
    'use strict';

    // グローバル変数
    let currentAudio = null;
    let isPlaying = false;

    $(document).ready(function () {
        initPodcastPlayer();
        initEpisodeFilters();
        initNewsletterForm();
        initSocialShare();
        initLoadMore();
        initSmoothScroll();
        initModernHeader();
        initMobileMenu();
        initCustomHeader();
    });

    /**
     * ポッドキャストプレーヤーの初期化
     */
    function initPodcastPlayer() {
        // ユーザーインタラクションの検出（自動再生ポリシー対応）
        let userInteracted = false;
        $(document).one('click touchstart keydown', function () {
            userInteracted = true;
            console.log('ユーザーインタラクションを検出しました');
        });

        // 再生ボタンのクリックイベント
        $(document).on('click', '.play-button, .episode-play-btn, .episode-play-overlay', function (e) {
            e.preventDefault();

            const audioUrl = $(this).data('audio');
            if (!audioUrl) {
                showErrorMessage('音声URLが設定されていません');
                return;
            }

            // ユーザーインタラクションチェック
            if (!userInteracted) {
                showErrorMessage('音声を再生するには、ページ上でクリックやタップを行ってください');
                return;
            }

            toggleAudio(audioUrl, $(this));
        });

        // 音声の終了イベント
        $(document).on('ended', 'audio', function () {
            resetPlayButtons();
            isPlaying = false;
        });

        // デバッグ情報をコンソールに出力
        console.log('ポッドキャストプレーヤーが初期化されました');
        console.log('サポートされている音声形式:');
        const testAudio = new Audio();
        const formats = {
            'MP3': testAudio.canPlayType('audio/mpeg'),
            'M4A': testAudio.canPlayType('audio/mp4'),
            'WAV': testAudio.canPlayType('audio/wav'),
            'OGG': testAudio.canPlayType('audio/ogg'),
            'AAC': testAudio.canPlayType('audio/aac'),
            'WebM': testAudio.canPlayType('audio/webm')
        };
        console.table(formats);
    }

    /**
     * 音声の再生/停止を切り替え
     */
    function toggleAudio(audioUrl, button) {
        try {
            // URLの検証
            if (!audioUrl || audioUrl.trim() === '') {
                showErrorMessage('音声URLが見つかりません');
                return;
            }

            // 音声形式の事前チェック
            if (!isSupportedAudioFormat(audioUrl)) {
                console.log('サポートされていない形式、代替を試行:', audioUrl);
                tryAlternativeFormats(audioUrl, button);
                return;
            }

            // 既存の音声を停止
            if (currentAudio && !currentAudio.paused) {
                currentAudio.pause();
                resetPlayButtons();
            }

            // 同じ音声の場合は停止
            if (currentAudio && currentAudio.src === audioUrl && isPlaying) {
                currentAudio.pause();
                resetPlayButtons();
                isPlaying = false;
                return;
            }

            // ローディング状態を表示
            updatePlayButton(button, 'loading');

            // 新しい音声を作成
            if (!currentAudio || currentAudio.src !== audioUrl) {
                currentAudio = new Audio();

                // 詳細なイベントリスナーを設定
                currentAudio.addEventListener('loadstart', function () {
                    console.log('音声の読み込み開始');
                });

                currentAudio.addEventListener('canplay', function () {
                    console.log('音声の再生準備完了');
                });

                currentAudio.addEventListener('error', function (e) {
                    console.error('音声エラー:', e);
                    handleAudioError(e, button, audioUrl);
                });

                currentAudio.addEventListener('ended', function () {
                    resetPlayButtons();
                    isPlaying = false;
                    $('.audio-progress').remove();
                });

                currentAudio.addEventListener('timeupdate', function () {
                    updateProgressBar(this);
                });

                // CORS設定を追加
                currentAudio.crossOrigin = 'anonymous';

                // 音声URLを設定
                currentAudio.src = audioUrl;
                currentAudio.preload = 'metadata';
            }

            // 音声を再生
            const playPromise = currentAudio.play();

            if (playPromise !== undefined) {
                playPromise.then(() => {
                    updatePlayButton(button, 'playing');
                    isPlaying = true;
                    showAudioProgress(currentAudio);
                }).catch(error => {
                    console.error('再生エラー:', error);
                    handleAudioError(error, button, audioUrl);
                });
            }

        } catch (error) {
            console.error('toggleAudio エラー:', error);
            handleAudioError(error, button, audioUrl);
        }
    }

    /**
     * 音声エラーのハンドリング
     */
    function handleAudioError(error, button, audioUrl) {
        let errorMessage = '音声の再生に失敗しました';
        let shouldTryAlternative = false;

        if (error.target && error.target.error) {
            switch (error.target.error.code) {
                case 1: // MEDIA_ERR_ABORTED
                    errorMessage = '音声の読み込みが中断されました';
                    break;
                case 2: // MEDIA_ERR_NETWORK
                    errorMessage = 'ネットワークエラーが発生しました';
                    shouldTryAlternative = true;
                    break;
                case 3: // MEDIA_ERR_DECODE
                    errorMessage = '音声ファイルが破損しています';
                    shouldTryAlternative = true;
                    break;
                case 4: // MEDIA_ERR_SRC_NOT_SUPPORTED
                    errorMessage = 'サポートされていない音声形式です';
                    shouldTryAlternative = true;
                    break;
            }
        } else if (error.name === 'NotAllowedError') {
            errorMessage = 'ブラウザが音声再生をブロックしています。ページをクリックしてから再度お試しください。';
        } else if (error.name === 'NotSupportedError') {
            errorMessage = 'この音声形式はサポートされていません';
            shouldTryAlternative = true;
        }

        // 代替形式を試す条件の場合
        if (shouldTryAlternative && audioUrl && !button.data('alternative-tried')) {
            button.data('alternative-tried', true);
            console.log('代替形式を試行中...');
            tryAlternativeFormats(audioUrl, button);
            return;
        }

        showErrorMessage(errorMessage);
        updatePlayButton(button, 'error');
        isPlaying = false;
    }

    /**
     * エラーメッセージを表示
     */
    function showErrorMessage(message) {
        // 既存のエラーメッセージを削除
        $('.audio-error-message').remove();

        const errorDiv = $(`
            <div class="audio-error-message" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: #ff4757;
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                z-index: 10001;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideInRight 0.3s ease;
                max-width: 300px;
            ">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span>${message}</span>
                    <button class="close-error" style="
                        background: none;
                        border: none;
                        color: white;
                        font-size: 1.2rem;
                        cursor: pointer;
                        margin-left: 1rem;
                    ">×</button>
                </div>
            </div>
        `);

        $('body').append(errorDiv);

        // 3秒後に自動削除
        setTimeout(() => {
            errorDiv.fadeOut(300, function () {
                $(this).remove();
            });
        }, 3000);
    }

    // エラーメッセージを手動で閉じる
    $(document).on('click', '.close-error', function () {
        $(this).closest('.audio-error-message').fadeOut(300, function () {
            $(this).remove();
        });
    });

    /**
     * 再生ボタンの表示を更新
     */
    function updatePlayButton(button, state) {
        resetPlayButtons();

        switch (state) {
            case 'loading':
                button.addClass('loading');
                button.prop('disabled', true);
                if (button.hasClass('episode-play-overlay')) {
                    button.html('⏳');
                } else {
                    button.html('⏳ 読み込み中...');
                }
                break;

            case 'playing':
                button.addClass('playing');
                button.prop('disabled', false);
                if (button.hasClass('episode-play-overlay')) {
                    button.html('⏸️');
                } else {
                    button.html('⏸️ 停止');
                }
                break;

            case 'error':
                button.addClass('error');
                button.prop('disabled', false);
                if (button.hasClass('episode-play-overlay')) {
                    button.html('❌');
                } else {
                    button.html('❌ エラー');
                }
                // 3秒後に元に戻す
                setTimeout(() => {
                    resetPlayButtons();
                }, 3000);
                break;

            default: // stopped
                button.removeClass('playing loading error');
                button.prop('disabled', false);
                if (button.hasClass('episode-play-overlay')) {
                    button.html('▶');
                } else {
                    button.html('▶ 再生');
                }
        }
    }

    /**
     * すべての再生ボタンをリセット
     */
    function resetPlayButtons() {
        $('.play-button, .episode-play-btn, .episode-play-overlay').each(function () {
            $(this).removeClass('playing loading error')
                .prop('disabled', false);

            if ($(this).hasClass('episode-play-overlay')) {
                $(this).html('▶');
            } else {
                $(this).html('▶ 再生');
            }
        });
    }

    /**
     * プログレスバーを更新
     */
    function updateProgressBar(audio) {
        if (audio.duration && audio.currentTime) {
            const progress = (audio.currentTime / audio.duration) * 100;
            $('.audio-progress-bar').css('width', progress + '%');

            // 時間表示の更新
            const currentTime = formatTime(audio.currentTime);
            const duration = formatTime(audio.duration);
            $('.audio-time-display').text(`${currentTime} / ${duration}`);
        }
    }

    /**
     * 時間を mm:ss 形式にフォーマット
     */
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }

    /**
     * 音声プログレスの表示
     */
    function showAudioProgress(audio) {
        // 既存のプログレスバーを削除
        $('.audio-progress').remove();

        // エピソードタイトルを取得
        const episodeTitle = getEpisodeTitle();

        // プログレスバーを作成
        const progressBar = $(`
            <div class="audio-progress" style="
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 60px;
                background: white;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
                z-index: 10000;
                display: flex;
                align-items: center;
                padding: 0 1rem;
                border-top: 1px solid #e9ecef;
            ">
                <button class="audio-control-btn" style="
                    background: none;
                    border: none;
                    font-size: 1.5rem;
                    cursor: pointer;
                    margin-right: 1rem;
                    padding: 0.5rem;
                    border-radius: 50%;
                    transition: background 0.3s ease;
                ">⏸️</button>
                
                <div class="audio-info" style="
                    flex: 1;
                    margin-right: 1rem;
                ">
                    <div class="audio-title" style="
                        font-weight: bold;
                        font-size: 0.9rem;
                        margin-bottom: 0.25rem;
                        color: #333;
                    ">${episodeTitle}</div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div class="audio-progress-track" style="
                            flex: 1;
                            height: 4px;
                            background: #e9ecef;
                            border-radius: 2px;
                            cursor: pointer;
                            position: relative;
                        ">
                            <div class="audio-progress-bar" style="
                                height: 100%;
                                background: #f7ff0b;
                                border-radius: 2px;
                                width: 0%;
                                transition: width 0.1s ease;
                            "></div>
                        </div>
                        <div class="audio-time-display" style="
                            font-size: 0.8rem;
                            color: #666;
                            min-width: 80px;
                        ">0:00 / 0:00</div>
                    </div>
                </div>
                
                <button class="audio-close-btn" style="
                    background: none;
                    border: none;
                    font-size: 1.2rem;
                    cursor: pointer;
                    padding: 0.5rem;
                    color: #666;
                ">✕</button>
            </div>
        `);

        $('body').append(progressBar);

        // プログレスバークリックでシーク
        progressBar.find('.audio-progress-track').on('click', function (e) {
            const rect = this.getBoundingClientRect();
            const percentage = (e.clientX - rect.left) / rect.width;
            const newTime = audio.duration * percentage;
            audio.currentTime = newTime;
        });

        // 制御ボタンのイベント
        progressBar.find('.audio-control-btn').on('click', function () {
            if (audio.paused) {
                audio.play();
                $(this).html('⏸️');
            } else {
                audio.pause();
                $(this).html('▶️');
            }
        });

        // 閉じるボタン
        progressBar.find('.audio-close-btn').on('click', function () {
            audio.pause();
            resetPlayButtons();
            isPlaying = false;
            $('.audio-progress').remove();
        });

        // 音声の状態変更に応じてボタンを更新
        audio.addEventListener('play', function () {
            progressBar.find('.audio-control-btn').html('⏸️');
        });

        audio.addEventListener('pause', function () {
            progressBar.find('.audio-control-btn').html('▶️');
        });

        // 終了時にプログレスバーを削除
        audio.addEventListener('ended', function () {
            $('.audio-progress').remove();
        });
    }

    /**
     * 音声形式がサポートされているかチェック
     */
    function isSupportedAudioFormat(url) {
        const audio = new Audio();
        const extension = url.split('.').pop().toLowerCase().split('?')[0]; // クエリパラメータを除外

        // 一般的な音声形式のチェック
        const supportedFormats = {
            'mp3': 'audio/mpeg',
            'm4a': 'audio/mp4',
            'wav': 'audio/wav',
            'ogg': 'audio/ogg',
            'aac': 'audio/aac',
            'webm': 'audio/webm'
        };

        if (supportedFormats[extension]) {
            const canPlay = audio.canPlayType(supportedFormats[extension]);
            console.log(`音声形式チェック: ${extension} -> ${canPlay}`);
            return canPlay === 'probably' || canPlay === 'maybe';
        }

        // 拡張子が不明な場合は、とりあえず試してみる
        console.log('不明な音声形式、試行します:', extension);
        return true;
    }

    /**
     * 代替音声URLを試す
     */
    function tryAlternativeFormats(originalUrl, button) {
        updatePlayButton(button, 'loading');

        // ベースURLから拡張子を削除
        const baseUrl = originalUrl.replace(/\.[^/.]+(\?.*)?$/, ""); // クエリパラメータも考慮
        const queryParams = originalUrl.includes('?') ? '?' + originalUrl.split('?')[1] : '';

        const alternatives = [
            baseUrl + '.mp3' + queryParams,
            baseUrl + '.m4a' + queryParams,
            originalUrl.replace(/\.(m4a|wav|ogg|aac|webm)(\?.*)?$/i, '.mp3$2'), // 拡張子をmp3に変更
            originalUrl // 最後に元のURLを再試行
        ];

        let attemptIndex = 0;

        function tryNext() {
            if (attemptIndex >= alternatives.length) {
                showErrorMessage('利用可能な音声形式が見つかりませんでした。MP3またはM4A形式でお試しください。');
                updatePlayButton(button, 'error');
                return;
            }

            const testUrl = alternatives[attemptIndex];
            attemptIndex++;

            // 同じURLの重複を避ける
            if (alternatives.slice(0, attemptIndex - 1).includes(testUrl)) {
                tryNext();
                return;
            }

            console.log(`代替音声を試行中 (${attemptIndex}/${alternatives.length}): ${testUrl}`);

            const testAudio = new Audio();

            testAudio.addEventListener('canplaythrough', function () {
                console.log(`音声形式が利用可能: ${testUrl}`);
                // 成功した場合、この URL で再生を開始
                toggleAudioWithUrl(testUrl, button);
            }, { once: true });

            testAudio.addEventListener('error', function (e) {
                console.log(`音声形式が利用不可: ${testUrl}`, e);
                setTimeout(tryNext, 500); // 少し待ってから次を試す
            }, { once: true });

            // タイムアウトを設定
            setTimeout(() => {
                if (testAudio.readyState < 2) { // HAVE_CURRENT_DATA未満
                    console.log(`タイムアウト: ${testUrl}`);
                    testAudio.src = ''; // リソースを解放
                    tryNext();
                }
            }, 3000);

            testAudio.src = testUrl;
            testAudio.load();
        }

        tryNext();
    }

    /**
     * 指定されたURLで音声を再生
     */
    function toggleAudioWithUrl(audioUrl, button) {
        try {
            // 前の試行フラグをクリア
            button.removeData('alternative-tried');

            currentAudio = new Audio();

            currentAudio.addEventListener('loadstart', function () {
                console.log('音声の読み込み開始');
            });

            currentAudio.addEventListener('canplay', function () {
                console.log('音声の再生準備完了');
            });

            currentAudio.addEventListener('error', function (e) {
                console.error('音声エラー:', e);
                handleAudioError(e, button, audioUrl);
            });

            currentAudio.addEventListener('ended', function () {
                resetPlayButtons();
                isPlaying = false;
                $('.audio-progress').remove();
            });

            currentAudio.addEventListener('timeupdate', function () {
                updateProgressBar(this);
            });

            currentAudio.crossOrigin = 'anonymous';
            currentAudio.src = audioUrl;
            currentAudio.preload = 'metadata';

            const playPromise = currentAudio.play();

            if (playPromise !== undefined) {
                playPromise.then(() => {
                    updatePlayButton(button, 'playing');
                    isPlaying = true;
                    showAudioProgress(currentAudio);
                }).catch(error => {
                    console.error('再生エラー:', error);
                    handleAudioError(error, button, audioUrl);
                });
            }

        } catch (error) {
            console.error('toggleAudioWithUrl エラー:', error);
            handleAudioError(error, button, audioUrl);
        }
    }

    /**
     * 現在再生中のエピソードタイトルを取得
     */
    function getEpisodeTitle() {
        // 最後にクリックされたボタンからエピソードタイトルを取得
        const episodeCard = $('.play-button.playing, .episode-play-btn.playing, .episode-play-overlay.playing').closest('.episode-card, .featured-episode');

        if (episodeCard.length > 0) {
            const title = episodeCard.find('.episode-title, .featured-episode-title').text();
            return title || '再生中...';
        }

        return '再生中...';
    }

    /**
     * エピソードフィルターの初期化
     */
    function initEpisodeFilters() {
        // 初期状態ですべてのエピソードを表示
        $('.episode-card').show();

        $('.filter-btn').on('click', function () {
            const filterValue = $(this).data('filter');
            console.log('フィルター値:', filterValue);

            // アクティブクラスの切り替え
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');

            // エピソードの表示/非表示
            if (filterValue === 'all') {
                console.log('すべて表示');
                $('.episode-card').fadeIn(300);
            } else {
                console.log('カテゴリでフィルタ:', filterValue);
                $('.episode-card').each(function () {
                    const episodeCategory = $(this).data('category');
                    console.log('エピソードカテゴリ:', episodeCategory);
                    if (episodeCategory === filterValue) {
                        $(this).fadeIn(300);
                    } else {
                        $(this).fadeOut(300);
                    }
                });
            }
        });
    }

    /**
     * ニュースレター登録フォームの処理
     */
    function initNewsletterForm() {
        $('.newsletter-form').on('submit', function (e) {
            e.preventDefault();

            const email = $(this).find('.newsletter-input').val();
            const submitBtn = $(this).find('.newsletter-submit');

            if (!isValidEmail(email)) {
                alert('有効なメールアドレスを入力してください。');
                return;
            }

            // ローディング状態
            submitBtn.prop('disabled', true).text('登録中...');

            // 実際の処理はここに実装（AJAX等）
            setTimeout(() => {
                alert('ニュースレターに登録しました！');
                $(this).find('.newsletter-input').val('');
                submitBtn.prop('disabled', false).text('登録する');
            }, 1000);
        });
    }

    /**
     * メールアドレスのバリデーション
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * ソーシャルシェア機能
     */
    function initSocialShare() {
        $('.share-button').on('click', function () {
            const url = $(this).data('url') || window.location.href;
            const title = document.title;

            // シンプルなシェアメニューを表示
            const shareMenu = `
                <div class="share-menu" style="
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: white;
                    padding: 2rem;
                    border-radius: 12px;
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                    z-index: 10001;
                    max-width: 300px;
                    width: 90%;
                ">
                    <h3 style="margin-bottom: 1rem; text-align: center;">シェアする</h3>
                    <div class="share-options" style="display: flex; gap: 1rem; justify-content: center;">
                        <a href="https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}" target="_blank" style="
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            width: 50px;
                            height: 50px;
                            background: #1DA1F2;
                            color: white;
                            border-radius: 50%;
                            text-decoration: none;
                            font-size: 1.2rem;
                        ">🐦</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}" target="_blank" style="
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            width: 50px;
                            height: 50px;
                            background: #4267B2;
                            color: white;
                            border-radius: 50%;
                            text-decoration: none;
                            font-size: 1.2rem;
                        ">📘</a>
                        <button class="copy-url" data-url="${url}" style="
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            width: 50px;
                            height: 50px;
                            background: var(--accent);
                            color: white;
                            border: none;
                            border-radius: 50%;
                            cursor: pointer;
                            font-size: 1.2rem;
                        ">📋</button>
                    </div>
                    <button class="close-share" style="
                        position: absolute;
                        top: 10px;
                        right: 10px;
                        background: none;
                        border: none;
                        font-size: 1.5rem;
                        cursor: pointer;
                        color: var(--text-secondary);
                    ">✕</button>
                </div>
                <div class="share-overlay" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 10000;
                "></div>
            `;

            $('body').append(shareMenu);
        });

        // シェアメニューを閉じる
        $(document).on('click', '.close-share, .share-overlay', function () {
            $('.share-menu, .share-overlay').remove();
        });

        // URLをコピー
        $(document).on('click', '.copy-url', function () {
            const url = $(this).data('url');
            navigator.clipboard.writeText(url).then(() => {
                $(this).html('✅');
                setTimeout(() => {
                    $('.share-menu, .share-overlay').remove();
                }, 1000);
            });
        });
    }

    /**
     * 「もっと見る」ボタンの機能（投稿ベース）
     */
    function initLoadMore() {
        // 一般的な load-more-btn クラスのボタン用
        $('.load-more-btn').on('click', function () {
            const $btn = $(this);
            const originalText = $btn.text();
            const offset = parseInt($btn.data('offset')) || 6;
            const limit = parseInt($btn.data('limit')) || 6;

            // ボタンをローディング状態に
            $btn.text('読み込み中...').prop('disabled', true);

            $.ajax({
                url: contentfreaks_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_podcast_episodes',
                    offset: offset,
                    limit: limit,
                    nonce: contentfreaks_ajax.nonce
                },
                success: function (response) {
                    if (response === 'no_more_episodes') {
                        $btn.text('すべてのエピソードを表示しました').prop('disabled', true);
                        return;
                    }

                    $('.episodes-grid, #episodes-grid').append(response);
                    $btn.data('offset', offset + limit);

                    // ボタンを元に戻す
                    $btn.text(originalText).prop('disabled', false);

                    // 新しい要素にもイベントを適用
                    initPodcastPlayer();
                },
                error: function () {
                    $btn.text('エラーが発生しました').prop('disabled', true);
                }
            });
        });

        // ID指定の load-more-episodes ボタン用
        $('#load-more-episodes').on('click', function () {
            const $button = $(this);
            const offset = parseInt($button.data('offset')) || 0;
            const limit = parseInt($button.data('limit')) || 6;

            $button.text('読み込み中...').prop('disabled', true);

            $.ajax({
                url: contentfreaks_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_podcast_episodes',
                    offset: offset,
                    limit: limit,
                    nonce: contentfreaks_ajax.nonce
                },
                success: function (response) {
                    if (response === 'no_more_episodes') {
                        $button.text('すべてのエピソードを表示しました').prop('disabled', true);
                    } else {
                        $('#episodes-grid, .episodes-grid').append(response);
                        $button.data('offset', offset + limit);
                        $button.text('さらに読み込む').prop('disabled', false);

                        // 新しい要素にもイベントを適用
                        initPodcastPlayer();
                    }
                },
                error: function () {
                    showErrorMessage('エピソードの読み込みに失敗しました');
                    $button.text('さらに読み込む').prop('disabled', false);
                }
            });
        });
    }

    /**
     * オーディオプレイヤーの詳細制御
     */
    function initAudioPlayer() {
        const $audioPlayer = $('#audio-player');
        const $audioElement = $('#audio-element')[0];
        const $playPauseBtn = $('#play-pause-btn');
        const $closePlayer = $('#close-player');
        const $progressBar = $('.progress-bar');
        const $progressFill = $('.progress-fill');
        const $currentTime = $('.current-time');
        const $totalTime = $('.total-time');
        const $playerTitle = $('.player-title');

        // 再生/一時停止ボタン
        $playPauseBtn.on('click', function () {
            if ($audioElement.paused) {
                $audioElement.play();
            } else {
                $audioElement.pause();
            }
        });

        // プレイヤーを閉じる
        $closePlayer.on('click', function () {
            $audioElement.pause();
            $audioPlayer.hide();
        });

        // プログレスバーのクリック
        $progressBar.on('click', function (e) {
            const rect = this.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            $audioElement.currentTime = percent * $audioElement.duration;
        });

        // オーディオイベント
        $audioElement.addEventListener('loadedmetadata', function () {
            $totalTime.text(formatTime($audioElement.duration));
        });

        $audioElement.addEventListener('timeupdate', function () {
            const percent = ($audioElement.currentTime / $audioElement.duration) * 100;
            $progressFill.css('width', percent + '%');
            $currentTime.text(formatTime($audioElement.currentTime));
        });

        $audioElement.addEventListener('play', function () {
            $playPauseBtn.text('⏸');
        });

        $audioElement.addEventListener('pause', function () {
            $playPauseBtn.text('▶');
        });

        // グローバルな再生関数を更新
        window.playAudio = function (audioUrl, title) {
            $audioElement.src = audioUrl;
            $playerTitle.text(title || 'ポッドキャストエピソード');
            $audioPlayer.show();
            $audioElement.play();
        };
    }

    /**
     * 時間をフォーマット
     */
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }

    // オーディオプレイヤーの初期化
    $(document).ready(function () {
        initAudioPlayer();
    });

    /**
     * スムーズスクロール
     */
    function initSmoothScroll() {
        $('a[href^="#"]').on('click', function (e) {
            e.preventDefault();

            const target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 800);
            }
        });
    }

    /**
     * いいねボタン
     */
    $(document).on('click', '.like-button', function () {
        $(this).toggleClass('liked');

        if ($(this).hasClass('liked')) {
            $(this).html('💖');
        } else {
            $(this).html('❤️');
        }
    });

    /**
     * レスポンシブ対応
     */
    function handleResize() {
        // モバイルでの調整があれば実装
    }

    $(window).on('resize', handleResize);

    /**
     * ページ読み込み時のアニメーション
     */
    function initPageAnimations() {
        // エピソードカードのフェードイン
        $('.episode-card').each(function (index) {
            $(this).css('opacity', '0').delay(index * 100).animate({
                opacity: 1
            }, 600);
        });
    }

    initPageAnimations();

    /**
     * モダンヘッダーの初期化
     */
    function initModernHeader() {
        const header = $('#contentfreaks-header');
        let lastScrollTop = 0;
        let scrollTimer = null;

        // ヘッダーが存在するかチェック
        if (header.length === 0) {
            console.log('ContentFreaksヘッダーが見つかりません');
            return;
        }

        // スクロールイベント
        $(window).scroll(function () {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function () {
                const currentScroll = $(window).scrollTop();

                // スクロール量に応じてヘッダーのスタイルを変更
                if (currentScroll > 100) {
                    header.addClass('scrolled');
                } else {
                    header.removeClass('scrolled');
                }

                // スクロール方向に応じてヘッダーの表示/非表示
                if (currentScroll > lastScrollTop && currentScroll > 200) {
                    // 下スクロール時は隠す
                    header.addClass('header-hidden');
                } else {
                    // 上スクロール時は表示
                    header.removeClass('header-hidden');
                }

                lastScrollTop = currentScroll;
            }, 10);
        });

        console.log('ContentFreaksヘッダーが初期化されました');

        // ナビゲーションアイテムのアクティブ状態管理
        updateActiveNavItem();

        // 検索機能の初期化
        initHeaderSearch();

        // ハンバーガーメニューの初期化
        initMobileMenu();
    }

    /**
     * アクティブなナビゲーションアイテムの更新
     */
    function updateActiveNavItem() {
        const currentPath = window.location.pathname;
        $('.nav-menu a, .navi-in a').each(function () {
            const linkPath = new URL(this.href).pathname;
            if (linkPath === currentPath) {
                $(this).addClass('current-menu-item');
            }
        });
    }

    /**
     * ヘッダー検索機能
     */
    function initHeaderSearch() {
        // 検索トグルボタン
        $('.search-toggle').click(function (e) {
            e.preventDefault();
            toggleSearchModal();
        });

        // ESCキーで検索を閉じる
        $(document).keyup(function (e) {
            if (e.keyCode === 27) {
                closeSearchModal();
            }
        });
    }

    /**
     * 検索モーダルの表示/非表示
     */
    function toggleSearchModal() {
        if ($('.search-modal').length === 0) {
            createSearchModal();
        }
        $('.search-modal').toggleClass('active');
        if ($('.search-modal').hasClass('active')) {
            $('.search-modal input').focus();
        }
    }

    /**
     * 検索モーダルを閉じる
     */
    function closeSearchModal() {
        $('.search-modal').removeClass('active');
    }

    /**
     * 検索モーダルの作成
     */
    function createSearchModal() {
        const searchModal = $(`
            <div class="search-modal">
                <div class="search-modal-content">
                    <form class="search-form" role="search" method="get" action="${window.location.origin}">
                        <input type="search" name="s" placeholder="エピソードや記事を検索..." class="search-input" autocomplete="off">
                        <button type="submit" class="search-submit">
                            <span>🔍</span>
                        </button>
                        <button type="button" class="search-close">
                            <span>✕</span>
                        </button>
                    </form>
                    <div class="search-suggestions">
                        <h4>人気の検索ワード</h4>
                        <div class="search-tags">
                            <a href="?s=アニメ" class="search-tag">アニメ</a>
                            <a href="?s=ドラマ" class="search-tag">ドラマ</a>
                            <a href="?s=ポッドキャスト" class="search-tag">ポッドキャスト</a>
                            <a href="?s=レビュー" class="search-tag">レビュー</a>
                        </div>
                    </div>
                </div>
            </div>
        `);

        $('body').append(searchModal);

        // 検索モーダルのイベント
        $('.search-close').click(closeSearchModal);
        $('.search-modal').click(function (e) {
            if (e.target === this) {
                closeSearchModal();
            }
        });
    }

    /**
     * モバイルハンバーガーメニューの初期化
     */
    function initMobileMenu() {
        // ハンバーガーメニューボタンが存在しない場合は作成
        if ($('.mobile-menu-toggle').length === 0) {
            createMobileMenuButton();
        }

        // モバイルナビメニューが存在しない場合は作成
        if ($('.mobile-nav-menu').length === 0) {
            createMobileNavMenu();
        }

        // ハンバーガーメニューボタンのクリックイベント
        $(document).on('click', '.mobile-menu-toggle', function (e) {
            e.preventDefault();
            toggleMobileMenu();
        });

        // モバイルメニュー外をクリックしたら閉じる
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.mobile-nav-menu, .mobile-menu-toggle').length) {
                closeMobileMenu();
            }
        });

        // ESCキーでメニューを閉じる
        $(document).keyup(function (e) {
            if (e.keyCode === 27) {
                closeMobileMenu();
            }
        });

        // ウィンドウリサイズ時の処理
        $(window).resize(function () {
            if ($(window).width() > 768) {
                closeMobileMenu();
            }
        });
    }

    /**
     * ハンバーガーメニューボタンを作成
     */
    function createMobileMenuButton() {
        const mobileButton = $(`
            <button class="mobile-menu-toggle" aria-label="メニューを開く">
                <span></span>
                <span></span>
                <span></span>
            </button>
        `);

        // ヘッダーの適切な位置に挿入
        const headerContainer = $('.header-container, .main-navigation').first();
        if (headerContainer.length) {
            headerContainer.append(mobileButton);
        }
    }

    /**
     * モバイルナビメニューを作成
     */
    function createMobileNavMenu() {
        // 既存のナビメニューからリンクを取得
        const navLinks = [];
        $('.nav-menu a, .navi-in a').each(function () {
            const href = $(this).attr('href');
            const text = $(this).text();
            if (href && text.trim()) {
                navLinks.push({ href, text });
            }
        });

        // デフォルトのナビリンクがない場合
        if (navLinks.length === 0) {
            navLinks.push(
                { href: '/', text: 'ホーム' },
                { href: '/blog/', text: 'ブログ' },
                { href: '/episodes/', text: 'エピソード' },
                { href: '/profile/', text: 'プロフィール' },
                { href: '/history/', text: '履歴' }
            );
        }

        let mobileMenuHTML = '<ul>';
        navLinks.forEach(link => {
            mobileMenuHTML += `<li><a href="${link.href}">${link.text}</a></li>`;
        });
        mobileMenuHTML += '</ul>';

        const mobileNavMenu = $(`
            <div class="mobile-nav-menu">
                ${mobileMenuHTML}
                <div class="mobile-cta">
                    <a href="#latest-episode" class="header-cta-btn">🎧 最新エピソード</a>
                    <a href="#newsletter" class="header-cta-btn secondary">📬 購読する</a>
                </div>
            </div>
        `);

        $('body').append(mobileNavMenu);
    }

    /**
     * モバイルメニューの表示/非表示を切り替え
     */
    function toggleMobileMenu() {
        const button = $('.mobile-menu-toggle');
        const menu = $('.mobile-nav-menu');

        button.toggleClass('active');
        menu.toggleClass('active');

        // ボディのスクロールを制御
        if (menu.hasClass('active')) {
            $('body').addClass('mobile-menu-open');
        } else {
            $('body').removeClass('mobile-menu-open');
        }
    }

    /**
     * モバイルメニューを閉じる
     */
    function closeMobileMenu() {
        $('.mobile-menu-toggle').removeClass('active');
        $('.mobile-nav-menu').removeClass('active');
        $('body').removeClass('mobile-menu-open');
    }

    /**
     * ContentFreaksカスタムヘッダーの初期化
     */
    function initCustomHeader() {
        const customHeader = $('#contentfreaks-header');

        if (customHeader.length === 0) {
            console.log('カスタムヘッダーが見つかりません');
            return;
        }

        // ヘッダーの表示を確実にする
        customHeader.show();
        customHeader.css({
            'display': 'block',
            'visibility': 'visible',
            'opacity': '1'
        });

        // 他のヘッダーを隠す - Cocoonのデフォルトヘッダーを完全無効化
        $('#header, .header.cf, #header-in, .header-in, .logo-header, #site-logo, .site-logo-image, .header-site-logo-image').hide();
        $('.header:not(#contentfreaks-header), #header:not(#contentfreaks-header)').hide();
        $('.navi:not(.custom-nav), .navi-in:not(.custom-nav), .global-navi:not(.custom-nav)').hide();

        console.log('カスタムヘッダーが正常に初期化されました');
    }

})(jQuery);
