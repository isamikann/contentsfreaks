/**
 * ContentFreaks マイクロインタラクション v1.0
 * スクロールアニメーション、ツールチップ、フォームバリデーションフィードバック
 */

(function() {
    'use strict';

    // ===== 1. スクロールアニメーション =====
    
    /**
     * Intersection Observer でスクロール時のフェードインを実装
     */
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    // 一度表示したら監視を解除（パフォーマンス最適化）
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // アニメーション対象要素を監視
        const animatedElements = document.querySelectorAll(
            '.fade-in, .slide-up, .slide-in-left, .slide-in-right, .scale-in'
        );
        
        animatedElements.forEach(el => observer.observe(el));
    }

    // ===== 2. ツールチップ初期化 =====
    
    /**
     * data-tooltip 属性を持つ要素にツールチップを追加
     */
    function initTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            // すでにツールチップがある場合はスキップ
            if (element.querySelector('.tooltip')) return;
            
            const tooltipText = element.getAttribute('data-tooltip');
            const tooltipPosition = element.getAttribute('data-tooltip-position') || 'top';
            
            // ツールチップ要素を作成
            const tooltip = document.createElement('span');
            tooltip.className = `tooltip ${tooltipPosition === 'bottom' ? 'tooltip-bottom' : ''}`;
            tooltip.textContent = tooltipText;
            
            // コンテナでラップ
            if (!element.classList.contains('tooltip-container')) {
                element.classList.add('tooltip-container');
            }
            
            element.appendChild(tooltip);
        });
    }

    // ===== 3. フォームバリデーションフィードバック =====
    
    /**
     * リアルタイムフォームバリデーションとビジュアルフィードバック
     */
    function initFormValidation() {
        const formFields = document.querySelectorAll('.form-field input, .form-field textarea');
        
        formFields.forEach(field => {
            // リアルタイムバリデーション
            field.addEventListener('blur', function() {
                validateField(this);
            });
            
            // 入力時にエラーをクリア
            field.addEventListener('input', function() {
                const formField = this.closest('.form-field');
                if (formField.classList.contains('error')) {
                    formField.classList.remove('error');
                }
            });
        });
    }

    /**
     * 個別フィールドのバリデーション
     */
    function validateField(field) {
        const formField = field.closest('.form-field');
        const value = field.value.trim();
        
        // 必須チェック
        if (field.hasAttribute('required') && !value) {
            formField.classList.add('error');
            formField.classList.remove('success');
            return false;
        }
        
        // メールバリデーション
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                formField.classList.add('error');
                formField.classList.remove('success');
                return false;
            }
        }
        
        // 成功状態
        if (value) {
            formField.classList.add('success');
            formField.classList.remove('error');
            return true;
        }
        
        // 空でOKの場合
        formField.classList.remove('error', 'success');
        return true;
    }

    // ===== 4. カードインタラクション =====
    
    /**
     * カードにインタラクティブクラスを追加
     */
    function initCardInteractions() {
        // エピソードカード
        const episodeCards = document.querySelectorAll('.episode-item, .ect-post-item');
        episodeCards.forEach(card => {
            if (!card.classList.contains('episode-card')) {
                card.classList.add('episode-card');
            }
        });
        
        // 一般的なカード要素
        const cards = document.querySelectorAll('.card, .widget, .sidebar-widget');
        cards.forEach(card => {
            if (!card.classList.contains('card-interactive')) {
                card.classList.add('card-interactive');
            }
        });
    }

    // ===== 5. ボタンリップルエフェクト =====
    
    /**
     * ボタンクリック時のリップルエフェクト強化
     */
    function initButtonRipples() {
        const buttons = document.querySelectorAll('button, .btn, .button, a.wp-block-button__link');
        
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                // 既存のリップルを削除
                const existingRipple = this.querySelector('.ripple');
                if (existingRipple) {
                    existingRipple.remove();
                }
                
                // リップル要素を作成
                const ripple = document.createElement('span');
                ripple.className = 'ripple';
                
                // クリック位置を計算
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    position: absolute;
                    background: rgba(255, 255, 255, 0.5);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple-effect 0.6s ease-out;
                    pointer-events: none;
                `;
                
                this.appendChild(ripple);
                
                // アニメーション後に削除
                setTimeout(() => ripple.remove(), 600);
            });
        });
    }

    // ===== 6. スムーススクロール =====
    
    /**
     * アンカーリンクのスムーススクロール
     */
    function initSmoothScroll() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        
        anchorLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // 空のハッシュはスキップ
                if (href === '#' || href === '#!') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    
                    const offsetTop = target.getBoundingClientRect().top + window.pageYOffset;
                    const headerHeight = document.querySelector('header')?.offsetHeight || 0;
                    
                    window.scrollTo({
                        top: offsetTop - headerHeight - 20,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // ===== 7. プログレスバー =====
    
    /**
     * ページスクロール進捗バーを表示
     */
    function initScrollProgress() {
        // プログレスバー要素を作成
        const progressBar = document.createElement('div');
        progressBar.className = 'scroll-progress-bar';
        progressBar.innerHTML = '<div class="scroll-progress-fill"></div>';
        progressBar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: rgba(0, 0, 0, 0.1);
            z-index: 9999;
        `;
        
        const progressFill = progressBar.querySelector('.scroll-progress-fill');
        progressFill.style.cssText = `
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #f7ff0b, #ff6b35);
            transition: width 0.1s ease;
        `;
        
        document.body.appendChild(progressBar);
        
        // スクロール時に更新
        window.addEventListener('scroll', () => {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            const scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100;
            progressFill.style.width = `${Math.min(scrollPercent, 100)}%`;
        });
    }

    // ===== 8. ローディング状態管理 =====
    
    /**
     * ボタンのローディング状態を管理
     */
    window.setButtonLoading = function(button, loading = true) {
        if (loading) {
            button.classList.add('btn-loading');
            button.disabled = true;
            button.setAttribute('data-original-text', button.textContent);
            button.textContent = '処理中...';
        } else {
            button.classList.remove('btn-loading');
            button.disabled = false;
            const originalText = button.getAttribute('data-original-text');
            if (originalText) {
                button.textContent = originalText;
                button.removeAttribute('data-original-text');
            }
        }
    };

    // ===== 9. パララックス効果（軽量版） =====
    
    /**
     * 簡易パララックススクロール
     */
    function initParallax() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        if (parallaxElements.length === 0) return;
        
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = parseFloat(element.getAttribute('data-parallax')) || 0.5;
                const yPos = -(scrollTop * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        });
    }

    // ===== 10. リンクアニメーション自動適用 =====
    
    /**
     * 特定のリンクにアニメーションクラスを自動追加
     */
    function initLinkAnimations() {
        // ナビゲーションリンク
        const navLinks = document.querySelectorAll('nav a, .menu a');
        navLinks.forEach(link => {
            if (!link.classList.contains('link-animated')) {
                link.classList.add('link-animated');
            }
        });
    }

    // ===== 初期化 =====
    
    /**
     * DOMContentLoaded時に全機能を初期化
     */
    function init() {
        // スクロールアニメーション
        initScrollAnimations();
        
        // ツールチップ
        initTooltips();
        
        // フォームバリデーション
        initFormValidation();
        
        // カードインタラクション
        initCardInteractions();
        
        // ボタンリップル
        initButtonRipples();
        
        // スムーススクロール
        initSmoothScroll();
        
        // プログレスバー（記事ページのみ）
        if (document.body.classList.contains('single-post')) {
            initScrollProgress();
        }
        
        // パララックス
        initParallax();
        
        // リンクアニメーション
        initLinkAnimations();
        
        console.log('✨ ContentFreaks マイクロインタラクション初期化完了');
    }

    // DOMContentLoaded または即座に実行
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ===== CSS アニメーション追加 =====
    
    // リップルエフェクトのキーフレームを動的に追加
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple-effect {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        button, .btn, .button {
            position: relative;
            overflow: hidden;
        }
    `;
    document.head.appendChild(style);

})();
