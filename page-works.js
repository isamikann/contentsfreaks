/**
 * 作品データベース - JavaScript機能
 */

(function() {
    'use strict';
    
    // DOM要素
    const searchInput = document.getElementById('works-search');
    const genreFilter = document.getElementById('genre-filter');
    const sortFilter = document.getElementById('sort-filter');
    const viewBtns = document.querySelectorAll('.view-btn');
    const worksGrid = document.getElementById('works-grid');
    const resultsCount = document.getElementById('results-count');
    const noResults = document.getElementById('no-results');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const activeFiltersEl = document.getElementById('active-filters');
    const activeFiltersTags = document.getElementById('active-filters-tags');
    const workCards = document.querySelectorAll('.work-card');
    
    // 現在のフィルター状態
    let currentFilters = {
        search: '',
        genre: 'all',
        sort: 'episodes-desc'
    };
    
    // 初期化
    function init() {
        if (!worksGrid) return;
        
        // イベントリスナー
        if (searchInput) {
            searchInput.addEventListener('input', handleSearch);
        }
        
        if (genreFilter) {
            genreFilter.addEventListener('change', handleGenreFilter);
        }
        
        if (sortFilter) {
            sortFilter.addEventListener('change', handleSort);
        }
        
        viewBtns.forEach(btn => {
            btn.addEventListener('click', handleViewToggle);
        });
        
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', clearFilters);
        }
        
        // エピソードボタン
        document.querySelectorAll('.work-episodes-btn').forEach(btn => {
            btn.addEventListener('click', toggleEpisodesList);
        });
        
        // 初期表示
        applyFilters();
    }
    
    // 検索処理
    function handleSearch(e) {
        currentFilters.search = e.target.value.toLowerCase().trim();
        applyFilters();
        updateActiveFilters();
    }
    
    // ジャンルフィルター処理
    function handleGenreFilter(e) {
        currentFilters.genre = e.target.value;
        applyFilters();
        updateActiveFilters();
    }
    
    // ソート処理
    function handleSort(e) {
        currentFilters.sort = e.target.value;
        applySorting();
    }
    
    // フィルター適用
    function applyFilters() {
        let visibleCount = 0;
        
        workCards.forEach(card => {
            const title = card.dataset.title || '';
            const genre = card.dataset.genre || '';
            
            // 検索条件チェック
            const matchesSearch = !currentFilters.search || 
                                 title.includes(currentFilters.search);
            
            // ジャンル条件チェック
            const matchesGenre = currentFilters.genre === 'all' || 
                                genre === currentFilters.genre;
            
            // 表示/非表示
            if (matchesSearch && matchesGenre) {
                card.style.display = '';
                card.style.animation = 'fade-in 0.3s ease';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // 結果カウント更新
        if (resultsCount) {
            resultsCount.textContent = visibleCount;
        }
        
        // 結果なし表示
        if (noResults) {
            noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
        
        // ソートも適用
        applySorting();
    }
    
    // ソート適用
    function applySorting() {
        const cards = Array.from(workCards).filter(card => card.style.display !== 'none');
        
        cards.sort((a, b) => {
            switch (currentFilters.sort) {
                case 'episodes-desc':
                    return parseInt(b.dataset.episodes) - parseInt(a.dataset.episodes);
                    
                case 'title-asc':
                    return (a.dataset.title || '').localeCompare(b.dataset.title || '', 'ja');
                    
                case 'title-desc':
                    return (b.dataset.title || '').localeCompare(a.dataset.title || '', 'ja');
                    
                case 'year-desc':
                    const yearB = parseInt(b.dataset.year) || 0;
                    const yearA = parseInt(a.dataset.year) || 0;
                    return yearB - yearA;
                    
                case 'year-asc':
                    const yearA2 = parseInt(a.dataset.year) || 0;
                    const yearB2 = parseInt(b.dataset.year) || 0;
                    return yearA2 - yearB2;
                    
                default:
                    return 0;
            }
        });
        
        // DOM再配置
        cards.forEach(card => {
            worksGrid.appendChild(card);
        });
    }
    
    // 表示切替
    function handleViewToggle(e) {
        const btn = e.currentTarget;
        const view = btn.dataset.view;
        
        // ボタンのアクティブ状態
        viewBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        // グリッド表示切替
        if (view === 'list') {
            worksGrid.classList.add('list-view');
        } else {
            worksGrid.classList.remove('list-view');
        }
    }
    
    // アクティブフィルター表示更新
    function updateActiveFilters() {
        const tags = [];
        
        // 検索タグ
        if (currentFilters.search) {
            tags.push({
                type: 'search',
                label: `検索: ${currentFilters.search}`,
                value: currentFilters.search
            });
        }
        
        // ジャンルタグ
        if (currentFilters.genre !== 'all') {
            tags.push({
                type: 'genre',
                label: `ジャンル: ${currentFilters.genre}`,
                value: currentFilters.genre
            });
        }
        
        // タグ表示
        if (tags.length > 0) {
            activeFiltersEl.style.display = 'flex';
            activeFiltersTags.innerHTML = tags.map(tag => `
                <span class="filter-tag">
                    ${tag.label}
                    <button class="filter-tag-remove" 
                            data-type="${tag.type}" 
                            aria-label="削除">×</button>
                </span>
            `).join('');
            
            // タグ削除イベント
            activeFiltersTags.querySelectorAll('.filter-tag-remove').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = this.dataset.type;
                    removeFilter(type);
                });
            });
        } else {
            activeFiltersEl.style.display = 'none';
        }
    }
    
    // フィルター削除
    function removeFilter(type) {
        if (type === 'search') {
            currentFilters.search = '';
            if (searchInput) searchInput.value = '';
        } else if (type === 'genre') {
            currentFilters.genre = 'all';
            if (genreFilter) genreFilter.value = 'all';
        }
        
        applyFilters();
        updateActiveFilters();
    }
    
    // 全フィルタークリア
    function clearFilters() {
        currentFilters = {
            search: '',
            genre: 'all',
            sort: currentFilters.sort
        };
        
        if (searchInput) searchInput.value = '';
        if (genreFilter) genreFilter.value = 'all';
        
        applyFilters();
        updateActiveFilters();
    }
    
    // エピソードリスト表示切替
    function toggleEpisodesList(e) {
        const btn = e.currentTarget;
        const workId = btn.dataset.workId;
        const episodesList = document.getElementById(`episodes-${workId}`);
        
        if (episodesList) {
            const isVisible = episodesList.style.display !== 'none';
            
            if (isVisible) {
                episodesList.style.display = 'none';
                btn.innerHTML = '<span>📻</span> 登場エピソード';
            } else {
                episodesList.style.display = 'block';
                btn.innerHTML = '<span>✕</span> 閉じる';
            }
        }
    }
    
    // スクロールアニメーション
    function addScrollAnimation() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, index * 50);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        workCards.forEach(card => {
            observer.observe(card);
        });
    }
    
    // ページ読み込み時に初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // スクロールアニメーション開始
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addScrollAnimation);
    } else {
        addScrollAnimation();
    }
    
})();
