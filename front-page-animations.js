/**
 * ContentFreaks フロントページアニメーション v1.0
 * カウントアップ + ヒーロー統計の Intersection Observer
 */
(function () {
    'use strict';

    function animateCount(element) {
        var target = parseFloat(element.dataset.count);
        var isDecimal = element.dataset.decimal === 'true';
        var duration = 1500;
        var step = target / (duration / 16);
        var current = 0;

        function update() {
            current = Math.min(current + step, target);

            if (isDecimal) {
                element.textContent = current.toFixed(1);
            } else {
                var suffix = element.dataset.suffix || '';
                element.textContent = Math.floor(current) + suffix;
            }

            if (current < target) requestAnimationFrame(update);
        }

        update();
    }

    function init() {
        var statNumbers = document.querySelectorAll('.podcast-stat-number[data-count]');
        if (statNumbers.length === 0) return;

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    entry.target.classList.add('animated');
                    animateCount(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(function (num) { observer.observe(num); });
    }

    /* ===== トップへ戻るボタン ===== */
    function initBackToTop() {
        var btn = document.getElementById('backToTop');
        if (!btn) return;

        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        }, { passive: true });

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    function initAll() {
        init();
        initBackToTop();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
