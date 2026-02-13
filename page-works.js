/**
 * Works (作品データベース) ページ - フィルター＆検索
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('works-search');
    const genreButtons = document.querySelectorAll('.genre-filter-btn');
    const grid = document.getElementById('works-grid');
    let currentGenre = '';
    let debounceTimer;

    function filterWorks(genre, search) {
        if (typeof contentfreaks_ajax === 'undefined') return;

        const formData = new URLSearchParams();
        formData.append('action', 'filter_works');
        formData.append('nonce', contentfreaks_ajax.nonce);
        if (genre) formData.append('genre', genre);
        if (search) formData.append('search', search);

        fetch(contentfreaks_ajax.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success && grid) {
                if (data.data.html) {
                    grid.innerHTML = data.data.html;
                } else {
                    grid.innerHTML = '<div class="works-empty-state"><div class="works-empty-icon">\uD83D\uDD0D</div><h3>見つかりませんでした</h3></div>';
                }
            }
        });
    }

    // ジャンルフィルター
    genreButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            genreButtons.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            currentGenre = this.dataset.genre;
            filterWorks(currentGenre, searchInput ? searchInput.value.trim() : '');
        });
    });

    // 検索（デバウンス付き）
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const term = this.value.trim();
            debounceTimer = setTimeout(function() {
                filterWorks(currentGenre, term);
            }, 400);
        });
    }
});
