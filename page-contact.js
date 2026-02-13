/**
 * ContentFreaks お問い合わせページ
 * タブ切り替え / バリデーション / AJAX送信
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initTabs();
        initForms();
        initCharCounters();
        handleUrlParams();
    });

    // ===== タブ切り替え =====
    function initTabs() {
        var tabs = document.querySelectorAll('.contact-type-tab');
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var type = this.getAttribute('data-type');
                switchTab(type);
            });
        });
    }

    function switchTab(type) {
        // タブUI
        document.querySelectorAll('.contact-type-tab').forEach(function (t) {
            var isActive = t.getAttribute('data-type') === type;
            t.classList.toggle('active', isActive);
            t.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
        // パネル
        document.querySelectorAll('.contact-panel').forEach(function (p) {
            p.classList.remove('active');
        });
        var targetPanel = document.getElementById('panel-' + type);
        if (targetPanel) {
            targetPanel.classList.add('active');
        }
    }

    // ===== URLパラメータ対応 =====
    function handleUrlParams() {
        var params = new URLSearchParams(window.location.search);
        var type = params.get('type');
        if (type === 'business') {
            switchTab('business');
        }
    }

    // ===== 文字数カウンター =====
    function initCharCounters() {
        document.querySelectorAll('.form-textarea').forEach(function (textarea) {
            var counter = textarea.parentElement.querySelector('.char-count');
            if (!counter) return;
            var currentSpan = counter.querySelector('.char-current');
            var max = parseInt(textarea.getAttribute('maxlength'), 10) || 2000;

            function update() {
                var len = textarea.value.length;
                currentSpan.textContent = len;
                counter.classList.remove('near-limit', 'at-limit');
                if (len >= max) {
                    counter.classList.add('at-limit');
                } else if (len >= max * 0.85) {
                    counter.classList.add('near-limit');
                }
            }

            textarea.addEventListener('input', update);
            update();
        });
    }

    // ===== フォーム送信 =====
    function initForms() {
        document.querySelectorAll('.contact-form').forEach(function (form) {
            form.addEventListener('submit', handleSubmit);
        });
    }

    function handleSubmit(e) {
        e.preventDefault();

        var form = e.target;
        var type = form.getAttribute('data-type');
        var submitBtn = form.querySelector('.form-submit');
        var submitText = submitBtn.querySelector('.submit-text');
        var submitLoading = submitBtn.querySelector('.submit-loading');
        var msgDiv = form.querySelector('.form-message');

        if (typeof contentfreaks_ajax === 'undefined') {
            showMessage(msgDiv, '送信設定を読み込めませんでした。ページを再読み込みしてください。', 'error');
            return;
        }

        // バリデーション
        var name = form.querySelector('input[name="name"]');
        var message = form.querySelector('textarea[name="message"]');

        if (!name.value.trim()) {
            showMessage(msgDiv, 'お名前を入力してください。', 'error');
            name.focus();
            return;
        }
        if (!message.value.trim()) {
            showMessage(msgDiv, 'メッセージを入力してください。', 'error');
            message.focus();
            return;
        }

        // ビジネス向けは追加チェック
        if (type === 'business') {
            var email = form.querySelector('input[name="email"]');
            var category = form.querySelector('select[name="category"]');
            if (!email.value.trim()) {
                showMessage(msgDiv, 'メールアドレスを入力してください。', 'error');
                email.focus();
                return;
            }
            if (!category.value) {
                showMessage(msgDiv, 'ご依頼内容を選択してください。', 'error');
                category.focus();
                return;
            }
        }

        // 送信UI
        submitBtn.disabled = true;
        submitText.style.display = 'none';
        submitLoading.style.display = 'inline';
        msgDiv.style.display = 'none';

        // データ収集
        var formData = new URLSearchParams();
        formData.append('action', 'contentfreaks_contact_submit');
        formData.append('nonce', contentfreaks_ajax.nonce);
        formData.append('contact_type', type);

        // 全フィールド収集
        form.querySelectorAll('input, textarea, select').forEach(function (el) {
            if (el.name && el.type !== 'submit') {
                formData.append(el.name, el.value);
            }
        });

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
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (data) {
                if (data.success) {
                    showMessage(msgDiv, data.data.message, 'success');
                    form.reset();
                    // 文字数カウンターリセット
                    form.querySelectorAll('.char-current').forEach(function (s) {
                        s.textContent = '0';
                    });
                    form.querySelectorAll('.char-count').forEach(function (c) {
                        c.classList.remove('near-limit', 'at-limit');
                    });
                } else {
                    showMessage(msgDiv, data.data.message || '送信に失敗しました。', 'error');
                }
            })
            .catch(function () {
                showMessage(msgDiv, '通信エラーが発生しました。しばらくしてから再度お試しください。', 'error');
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitText.style.display = 'inline';
                submitLoading.style.display = 'none';
            });
    }

    function showMessage(el, text, type) {
        el.textContent = text;
        el.className = 'form-message ' + type;
        el.style.display = 'block';
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
})();
