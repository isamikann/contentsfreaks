/* ContentFreaks Child Theme JavaScript */

document.addEventListener('DOMContentLoaded', function () {
    // 外部リンクのアクセシビリティ対応（新しいタブで開く旨をスクリーンリーダーに通知）
    var externalLinks = document.querySelectorAll(
        'a[href^="http"]:not([href*="' + window.location.hostname + '"])'
    );
    externalLinks.forEach(function (link) {
        link.setAttribute('target', '_blank');
        link.setAttribute('rel', 'noopener noreferrer');
        if (!link.getAttribute('aria-label')) {
            var linkText = link.textContent.trim() || link.getAttribute('title') || '';
            if (linkText) {
                link.setAttribute('aria-label', linkText + '（新しいタブで開きます）');
            }
        }
    });
});
