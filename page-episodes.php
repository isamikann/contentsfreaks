<?php
/**
 * Template Name: エピソード一覧
 * モダンなポッドキャストエピソード一覧ページ（統合版）
 * archive-episodes.phpの機能も含む
 */

get_header(); ?>

<style>
/* ===== モダンポッドキャストエピソードページ（統合版） ===== */

/* ページ全体の上部マージン調整（モダンヘッダー対応） */
body {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

/* ヒーローセクション */
.episodes-hero {
    position: relative;
    background: var(--episodes-hero-bg);
    padding: 1rem 0 3rem 0;
    overflow: hidden;
    min-height: 50vh;
    display: flex;
    align-items: center;
    color: var(--episodes-hero-text);
    margin-bottom: 0;
}

.episodes-hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23f7ff0b1a" points="0,1000 500,800 1000,1000"/><circle fill="%23ff6b3533" cx="800" cy="200" r="100"/><circle fill="%23f7ff0b26" cx="200" cy="300" r="80"/></svg>');
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(2deg); }
}

.episodes-hero-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
}

.episodes-particle {
    position: absolute;
    width: 8px;
    height: 8px;
    background: var(--episodes-hero-particle);
    border-radius: 50%;
    animation: episodes-float 10s infinite ease-in-out;
}

.episodes-particle:nth-child(1) { left: 15%; animation-delay: 0s; }
.episodes-particle:nth-child(2) { left: 30%; animation-delay: 2s; }
.episodes-particle:nth-child(3) { left: 45%; animation-delay: 4s; }
.episodes-particle:nth-child(4) { left: 60%; animation-delay: 1s; }
.episodes-particle:nth-child(5) { left: 75%; animation-delay: 3s; }
.episodes-particle:nth-child(6) { left: 90%; animation-delay: 1.5s; }

@keyframes episodes-float {
    0%, 100% { transform: translateY(100vh) scale(0); opacity: 0; }
    10% { opacity: 1; transform: translateY(90vh) scale(1); }
    90% { opacity: 1; transform: translateY(-10vh) scale(1); }
    100% { opacity: 0; transform: translateY(-20vh) scale(0); }
}

.episodes-hero-content {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    text-align: center;
}

.episodes-hero-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: inline-block;
    animation: pulse 3s ease-in-out infinite;
    filter: drop-shadow(var(--episodes-hero-icon-shadow));
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.episodes-hero h1 {
    font-size: var(--hero-title);
    font-weight: 800;
    color: var(--episodes-hero-text);
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
    text-shadow: var(--episodes-hero-title-shadow);
}

.episodes-hero-description {
    font-size: clamp(1rem, 2vw, 1.3rem);
    color: var(--episodes-hero-text);
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.6;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.episodes-hero-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
}

.episodes-stat {
    text-align: center;
    color: var(--episodes-hero-text);
    background: var(--episodes-hero-stats-bg);
    padding: 1.5rem 2rem;
    border-radius: 20px;
    backdrop-filter: blur(10px);
    border: 1px solid var(--episodes-hero-stats-border);
    transition: all 0.3s ease;
    min-width: 120px;
}

.episodes-stat:hover {
    transform: translateY(-5px);
    background: var(--episodes-hero-stats-hover);
}

.episodes-stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1;
    text-shadow: var(--episodes-hero-stats-shadow);
}

.episodes-stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* フィルターセクション */
.episodes-filters-section {
    background: var(--episodes-filters-bg);
    padding: 3rem 0;
    position: relative;
}

.episodes-filters-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    text-align: center;
}

.filters-title {
    font-size: 2rem;
    margin-bottom: 2rem;
    color: var(--episodes-filters-title);
    font-weight: 700;
}

.search-controls {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    min-width: 300px;
}

.search-input {
    width: 100%;
    padding: 1rem 3rem 1rem 1.5rem;
    border: 2px solid var(--episodes-filters-border);
    border-radius: 50px;
    background: var(--episodes-filters-bg-alt);
    backdrop-filter: blur(10px);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--episodes-filters-active);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.search-button {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    color: var(--episodes-filters-active-text);
    background: var(--episodes-filters-active);
    cursor: pointer;
    transition: all 0.3s ease;
}

.search-button:hover {
    transform: translateY(-50%) scale(1.1);
    box-shadow: var(--episodes-card-btn-shadow);
}

/* エピソードグリッド */
.episodes-content-section {
    padding: 4rem 0;
    background: var(--episodes-grid-bg);
    min-height: 60vh;
}

.episodes-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 0.8rem;
}

.episodes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.8rem;
    margin-top: 2rem;
}

/* エピソードカード統一スタイル */
.episode-card,
.modern-episode-card {
    background: var(--episodes-card-bg);
    border-radius: 25px;
    overflow: hidden;
    box-shadow: var(--episodes-card-shadow);
    transition: all 0.3s ease;
    position: relative;
    border: 1px solid var(--episodes-card-border);
    opacity: 1;
    transform: translateY(0);
}

/* 初期カードのアニメーション */
.modern-episode-card:not(.loaded) {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease forwards;
}

.episode-card:nth-child(1),
.modern-episode-card:nth-child(1) { animation-delay: 0.1s; }
.episode-card:nth-child(2),
.modern-episode-card:nth-child(2) { animation-delay: 0.2s; }
.episode-card:nth-child(3),
.modern-episode-card:nth-child(3) { animation-delay: 0.3s; }
.episode-card:nth-child(4),
.modern-episode-card:nth-child(4) { animation-delay: 0.4s; }

.episode-card:hover,
.modern-episode-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: var(--episodes-card-hover-shadow);
    border-color: rgba(102, 126, 234, 0.2);
}

.episode-card-header,
.episode-thumbnail {
    position: relative;
    height: 180px;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    aspect-ratio: 1 / 1; /* 正方形のアスペクト比を強制 */
    flex-shrink: 0; /* サムネイルのサイズを固定 */
}

.episode-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* 正方形に収まるようにクロップ */
    object-position: center; /* 中央部分を表示 */
    transition: transform 0.3s ease;
}

.episode-card:hover .episode-thumbnail img {
    transform: scale(1.08);
}

.default-thumbnail {
    width: 100%;
    height: 100%;
    aspect-ratio: 1 / 1; /* 正方形のアスペクト比を強制 */
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-size: 2rem;
    flex-shrink: 0; /* サイズを固定 */
}

/* モバイル対応でのデフォルトサムネイル調整 */
@media (max-width: 768px) {
    .default-thumbnail {
        border-radius: 0;
    }
    
    .default-thumbnail div {
        border-radius: 0 !important;
    }
}

@media (max-width: 480px) {
    .default-thumbnail {
        border-radius: 0;
    }
    
    .default-thumbnail div {
        border-radius: 0 !important;
    }
}

.episode-duration-overlay {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(255,255,255,0.95);
    color: var(--episodes-card-title);
    padding: 0.6rem 1.2rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: var(--meta-secondary);
    backdrop-filter: blur(10px);
    z-index: 2;
}

.episode-card-content {
    padding: 1.5rem;
}

.episode-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.episode-meta-left {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    flex: 1;
    flex-wrap: wrap;
}

.episode-meta .episode-tags {
    margin-top: 0;
    flex: 1;
    justify-content: flex-end;
}

.episode-date {
    color: var(--episodes-card-meta);
    font-size: var(--meta-primary);
    font-weight: 500;
}

.episode-title {
    margin: 0 0 1rem 0;
    font-size: var(--card-title);
    font-weight: 700;
    line-height: 1.4;
    letter-spacing: -0.01em;
}

.episode-title a {
    color: var(--episodes-card-title);
    text-decoration: none;
    transition: color 0.3s ease;
    display: block;
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
}

.episode-title a:hover {
    color: #667eea;
}

/* エピソードタグスタイル - duration-metaと統一 */
.episode-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.8rem;
}

.episode-tag {
    display: inline-block;
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: var(--meta-small);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.episode-tag:hover {
    background: rgba(102, 126, 234, 0.15);
    color: #5a6fd8;
    transform: translateY(-1px);
}

/* エピソードなし状態 */
.no-episodes {
    grid-column: 1 / -1;
    text-align: center;
    padding: 5rem 2rem;
    background: var(--episodes-card-bg);
    border-radius: 25px;
    border: 2px dashed var(--episodes-card-border);
    color: var(--episodes-card-meta);
}

.no-episodes-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.no-episodes h3 {
    color: var(--episodes-card-title);
    margin-bottom: 1rem;
    font-size: 1.5rem;
    font-weight: 600;
}

.no-episodes p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.sync-episodes-btn {
    background: var(--episodes-card-btn-bg);
    color: var(--episodes-card-btn-text);
    padding: 1.2rem 2.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-block;
    font-size: 1rem;
}

.sync-episodes-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--episodes-card-btn-shadow);
    background: var(--episodes-card-btn-hover);
}

/* 無限スクロール用のローディングインジケーター */
.infinite-scroll-indicator {
    text-align: center;
    padding: 3rem 0;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s ease;
}

.infinite-scroll-indicator.visible {
    opacity: 1;
    transform: translateY(0);
}

.loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.spinner-ring {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(102, 126, 234, 0.2);
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.infinite-scroll-trigger {
    height: 1px;
    width: 100%;
    margin: 2rem 0;
}

.loading-spinner p {
    color: var(--episodes-card-meta);
    font-size: 1rem;
    margin: 0;
}

/* 読み込みボタン（削除予定） */
.load-more-section {
    text-align: center;
    margin-top: 4rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.btn-load-more {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.2rem 3rem;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
    position: relative;
    overflow: hidden;
}

.btn-load-more::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-load-more:hover::before {
    left: 100%;
}

.btn-load-more:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.btn-load-more:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* フローティングオーディオプレイヤー */
.floating-player {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    backdrop-filter: blur(20px);
    z-index: 1000;
    max-width: 400px;
    border: 1px solid rgba(0,0,0,0.1);
}

.player-content {
    padding: 1.5rem;
}

.player-info {
    margin-bottom: 1rem;
}

.current-episode-title {
    font-weight: 600;
    color: #333;
    font-size: 0.95rem;
    display: block;
    margin-bottom: 0.5rem;
}

.player-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.control-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.2rem;
}

.control-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.progress-container {
    flex: 1;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: #eee;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 0.5rem;
    cursor: pointer;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 3px;
    transition: width 0.3s ease;
    width: 0%;
}

.time-display {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #666;
}

/* タブレット向け表示調整 */
@media (max-width: 1024px) and (min-width: 769px) {
    .episodes-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }
}

/* タブレット（768px-1023px） */
@media (min-width: 768px) and (max-width: 1023px) {
    .episodes-hero {
        padding: 1.5rem 0 2.5rem 0;
    }
    
    .episodes-hero-icon {
        font-size: 4.5rem;
        margin-bottom: 1.25rem;
    }
    
    .episodes-hero h1 {
        font-size: 2.25rem;
        margin-bottom: 1.25rem;
    }
    
    .episodes-hero-description {
        font-size: 1.3rem;
        margin-bottom: 2.25rem;
    }
    
    .episodes-hero-stats {
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 2.5rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .episodes-stat-number {
        font-size: 2.25rem;
    }
    
    .episodes-stat-label {
        font-size: 0.85rem;
    }
}

/* レスポンシブ対応 */
/* プロフィールページ用モバイル調整 */
@media (max-width: 768px) {
    .episodes-hero {
        padding: 0.5rem 0 2rem 0;
        min-height: 40vh;
    }
    
    .episodes-hero-content {
        padding: 0 1rem;
    }
    
    .episodes-hero-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
    
    .episodes-hero h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
        line-height: 1.2;
    }
    
    .episodes-hero-description {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        line-height: 1.4;
    }
    
    .episodes-hero-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 2rem;
        max-width: 360px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .episodes-stat {
        padding: 1.2rem 1rem;
        min-width: auto;
        text-align: center;
    }
    
    .episodes-stat-number {
        font-size: 2rem;
    }
    
    .episodes-stat-label {
        font-size: 0.8rem;
    }
    
    .platforms-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    
    .platform-card {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
    }
    
    .platform-info {
        padding: 0.5rem;
    }
    
    .platform-name {
        font-size: 0.9rem;
    }
    
    .platform-description {
        font-size: 0.8rem;
        line-height: 1.2;
    }
    
    .search-controls {
        flex-direction: column;
        align-items: center;
    }
    
    .search-box {
        min-width: 250px;
    }
    
    .episodes-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
    }
    
    .episode-card,
    .modern-episode-card {
        display: flex;
        flex-direction: row;
        height: 160px; /* 高さを調整 */
        overflow: hidden;
    }
    
    .episode-card-header,
    .episode-thumbnail {
        width: 160px;
        height: 160px;
        aspect-ratio: 1 / 1; /* 正方形のアスペクト比を維持 */
        flex-shrink: 0;
        display: flex;
        align-items: stretch; /* 画像を親要素の高さに合わせる */
    }
    
    .episode-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* 正方形に収まるようにクロップ */
        object-position: center; /* 中央部分を表示 */
        border-radius: 0; /* モバイルでは角丸を除去 */
    }
    
    .episode-duration-overlay {
        top: 0.5rem;
        right: 0.5rem;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        border-radius: 20px;
    }
    
    .episode-card-content {
        padding: 0.8rem;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        min-height: 0; /* flexboxの圧縮を許可 */
    }
    
    .episode-title {
        font-size: 1rem;
        line-height: 1.3;
        margin-bottom: 0.3rem;
        font-weight: 700;
        color: #333;
        display: block;
        overflow: hidden;
        /* 複数行表示を許可 */
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        max-width: 100%;
        flex: 0 0 auto; /* タイトルエリアを固定 */
    }
    
    .episode-title a {
        color: inherit;
        text-decoration: none;
        display: block;
        overflow: hidden;
        /* 複数行表示を許可 */
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
    }
    
    .episode-title a:hover {
        color: #667eea;
    }
    
    .episode-tags {
        margin-top: 0.5rem;
    }
    
    .episode-tag {
        font-size: 0.65rem;
        padding: 0.15rem 0.5rem;
    }
    
    .episode-meta {
        margin-bottom: 0.5rem;
        gap: 0.3rem;
    }
    
    .episode-meta-left {
        gap: 0.5rem;
    }
    
    .episode-date {
        font-size: 0.75rem;
    }
    
    .floating-player {
        bottom: 1rem;
        right: 1rem;
        left: 1rem;
        max-width: none;
    }
}

@media (max-width: 480px) {
    .breadcrumb-container {
        padding: 0.75rem 1rem;
    }
    
    .breadcrumb-nav {
        margin-bottom: 0.5rem;
    }
    
    .episodes-hero {
        padding: 0.25rem 0 1.5rem 0;
    }
    
    .episodes-hero-content {
        padding: 0 1rem;
    }
    
    .episodes-hero-icon {
        font-size: 3rem;
        margin-bottom: 0.75rem;
    }
    
    .episodes-hero h1 {
        margin-bottom: 0.75rem;
    }
    
    .episodes-hero-description {
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }
    
    .episodes-hero-stats {
        gap: 1rem;
        margin-top: 1.5rem;
        grid-template-columns: repeat(3, 1fr);
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .episodes-stat {
        min-width: auto;
        padding: 1rem 0.5rem;
        text-align: center;
    }
    
    .episodes-stat-number {
        font-size: 1.75rem;
    }
    
    .episodes-stat-label {
        font-size: 0.75rem;
    }
    
    .platforms-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.8rem;
    }
    
    .platform-card {
        padding: 0.8rem 0.5rem;
    }
    
    .platform-name {
        font-size: 0.8rem;
        margin-bottom: 0.2rem;
    }
    
    .platform-description {
        font-size: 0.7rem;
        line-height: 1.1;
    }
    
    .episodes-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
    }
    
    .episode-card,
    .modern-episode-card {
        display: flex;
        flex-direction: row;
        height: 130px; /* 小さい画面では少し低く */
        overflow: hidden;
    }
    
    .episode-card-header,
    .episode-thumbnail {
        width: 130px;
        height: 130px;
        aspect-ratio: 1 / 1; /* 正方形のアスペクト比を維持 */
        flex-shrink: 0;
        display: flex;
        align-items: stretch;
    }
    
    .episode-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* 正方形に収まるようにクロップ */
        object-position: center; /* 中央部分を表示 */
        border-radius: 0;
    }
    
    .episode-number-badge {
        display: none; /* 480px以下では非表示 */
    }
    
    .episode-duration-overlay {
        top: 0.3rem;
        right: 0.3rem;
        padding: 0.3rem 0.6rem;
        font-size: 0.7rem;
        border-radius: 15px;
    }
    
    .episode-card-content {
        padding: 0.5rem;
        min-height: 0;
    }
    
    .episode-title {
        font-size: 0.90rem;
        margin-bottom: 0.2rem;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        max-width: 100%;
        line-height: 1.2;
        flex: 0 0 auto;
    }
    
    .episode-tags {
        margin-top: 0.3rem;
        gap: 0.3rem;
    }
    
    .episode-tag {
        font-size: 0.6rem;
        padding: 0.1rem 0.4rem;
    }
    
    .action-btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.6rem;
        min-width: auto;
        flex-shrink: 0;
        border-radius: 8px;
    }
    
    .platform-mini {
        width: 20px;
        height: 20px;
        font-size: 0.6rem;
        min-width: 20px;
        flex-shrink: 0;
    }
    
    .episode-meta {
        margin-bottom: 0.2rem;
        gap: 0.3rem;
    }
    
    .episode-meta-left {
        gap: 0.4rem;
    }
    
    .episode-date {
        font-size: 0.7rem;
    }
}
</style>


<main id="main" class="site-main contentfreaks-episodes-page">
    <!-- ブレッドクラムナビゲーション -->
    <nav class="breadcrumb-nav">
        <div class="breadcrumb-container">
            <a href="<?php echo home_url(); ?>" class="breadcrumb-home">🏠 ホーム</a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">エピソード一覧</span>
        </div>
    </nav>

    <!-- ヒーローセクション -->
    <section class="episodes-hero">
        <div class="episodes-hero-bg">
            <div class="hero-pattern"></div>
        </div>
        
        <div class="episodes-hero-particles">
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
            <div class="episodes-particle"></div>
        </div>
        
        <div class="episodes-hero-content">
            <div class="episodes-hero-icon">🎙️</div>
            <h1>Podcast Episodes</h1>
            <p class="episodes-hero-description">
                コンテンツフリークスの全エピソードを一覧でお楽しみください。
                最新のエピソードから過去の名作まで、すべてここに集約されています。
            </p>
            
            <div class="episodes-hero-stats">
                <div class="episodes-stat">
                    <span class="episodes-stat-number"><?php 
                        $total_episodes = new WP_Query(array(
                            'post_type' => 'post',
                            'posts_per_page' => -1,
                            'meta_key' => 'is_podcast_episode',
                            'meta_value' => '1',
                            'post_status' => 'publish'
                        ));
                        echo $total_episodes->found_posts ? $total_episodes->found_posts : '0';
                        wp_reset_postdata();
                    ?></span>
                    <span class="episodes-stat-label">エピソード</span>
                </div>
                <div class="episodes-stat">
                    <span class="episodes-stat-number">🔥</span>
                    <span class="episodes-stat-label">熱い語り</span>
                </div>
                <div class="episodes-stat">
                    <span class="episodes-stat-number">🔍</span>
                    <span class="episodes-stat-label">深掘り分析</span>
                </div>
            </div>
        </div>
    </section>

    <!-- エピソードコンテンツ -->
    <section class="episodes-content-section">
        <div class="episodes-container">
            <div class="search-controls">
                <div class="search-box">
                    <input type="text" id="episode-search" class="search-input" placeholder="エピソードを検索..." />
                    <button type="button" class="search-button">🔍</button>
                </div>
            </div>
            
            <div class="episodes-grid" id="episodes-grid">
            <?php
            // ポッドキャスト投稿を取得（カスタムフィールドでフィルタ）
            $episodes_query = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 18,
                'meta_key' => 'is_podcast_episode',
                'meta_value' => '1',
                'orderby' => 'date',
                'order' => 'DESC'
            ));

            if ($episodes_query->have_posts()) :
                while ($episodes_query->have_posts()) : $episodes_query->the_post();
                    // カスタムフィールドを取得
                    $audio_url_raw = get_post_meta(get_the_ID(), 'episode_audio_url', true);
                    
                    // 音声URLの修正処理
                    $audio_url = $audio_url_raw;
                    if ($audio_url_raw) {
                        // 二重エンコーディングの修正
                        if (strpos($audio_url_raw, 'https%3A%2F%2F') !== false) {
                            // パターン1: cloudfront.net/ID/https%3A%2F%2Fcloudfront.net/path
                            if (preg_match('/https:\/\/d3ctxlq1ktw2nl\.cloudfront\.net\/\d+\/https%3A%2F%2Fd3ctxlq1ktw2nl\.cloudfront\.net%2F(.+)/', $audio_url_raw, $matches)) {
                                $correct_path = urldecode($matches[1]);
                                $audio_url = 'https://d3ctxlq1ktw2nl.cloudfront.net/' . $correct_path;
                            }
                        }
                    }
                    
                    $episode_number = get_post_meta(get_the_ID(), 'episode_number', true);
                    $duration = get_post_meta(get_the_ID(), 'episode_duration', true);
                    $original_url = get_post_meta(get_the_ID(), 'episode_original_url', true);
                    $episode_category = get_post_meta(get_the_ID(), 'episode_category', true) ?: 'エピソード';
                    
                    // デバッグ情報をコンソールに出力
                    if (current_user_can('administrator')) {
                        echo '<script>console.log("Episode Debug Info:", ' . json_encode([
                            'post_id' => get_the_ID(),
                            'title' => get_the_title(),
                            'audio_url_raw' => $audio_url_raw,
                            'audio_url_fixed' => $audio_url,
                            'episode_number' => $episode_number,
                            'duration' => $duration,
                            'original_url' => $original_url,
                            'category' => $episode_category
                        ]) . ');</script>';
                    }
            ?>
                <article class="episode-card modern-episode-card" data-category="<?php echo esc_attr($episode_category); ?>">
                    <div class="episode-card-header">
                        <div class="episode-thumbnail">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium', array(
                                        'alt' => get_the_title(),
                                        'loading' => 'lazy'
                                    )); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <div class="default-thumbnail">
                                        <div style="background: linear-gradient(135deg, #f7ff0b, #ff6b35); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; border-radius: 12px;">🎙️</div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="episode-card-content">
                        <div class="episode-meta">
                            <div class="episode-meta-left">
                                <span class="episode-date"><?php echo get_the_date('Y年n月j日'); ?></span>
                                
                                <?php 
                                // タグを取得・表示（日付の横に配置）
                                $tags = get_the_tags();
                                if ($tags && !is_wp_error($tags)) : ?>
                                <div class="episode-tags">
                                    <?php foreach ($tags as $tag) : ?>
                                        <a href="<?php echo get_tag_link($tag->term_id); ?>" class="episode-tag">
                                            #<?php echo esc_html($tag->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <h3 class="episode-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                    </div>
                </article>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="no-episodes">
                    <div class="no-episodes-icon">🎙️</div>
                    <h3>エピソードが見つかりません</h3>
                    <p>まだエピソードが投稿されていないか、検索条件に一致するエピソードがありません。</p>
                    <a href="<?php echo admin_url('tools.php?page=contentfreaks-sync'); ?>" class="sync-episodes-btn">
                        RSSからエピソードを同期
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- 無限スクロール用のローディングインジケーター -->
        <?php if ($episodes_query->found_posts > 18) : ?>
        <div class="infinite-scroll-indicator" id="loading-indicator" style="display: none;">
            <div class="loading-spinner">
                <div class="spinner-ring"></div>
                <p>エピソードを読み込んでいます...</p>
            </div>
        </div>
        <div class="infinite-scroll-trigger" id="scroll-trigger" data-offset="18" data-limit="12"></div>
        <?php endif; ?>
        </div>
    </section>
</main>

<script>
// エピソードページでのjavascript.js音声機能の無効化（DOMContentLoaded前に実行）
(function() {
    console.log('Pre-disabling javascript.js audio functions');
    
    // initAudioPlayer関数を無効化
    window.initAudioPlayer = function() {
        console.log('initAudioPlayer disabled on episodes page');
        return;
    };
    
    // initPodcastPlayer関数も無効化
    window.initPodcastPlayer = function() {
        console.log('initPodcastPlayer disabled on episodes page');
        return;
    };
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 初期カードにloadedクラスを追加
    const initialCards = document.querySelectorAll('.modern-episode-card');
    initialCards.forEach(card => {
        card.addEventListener('animationend', () => {
            card.classList.add('loaded');
        });
    });
    
    // エピソード検索機能
    const searchInput = document.getElementById('episode-search');
    const episodeCards = document.querySelectorAll('.modern-episode-card, .episode-card');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            episodeCards.forEach(card => {
                const titleElement = card.querySelector('.episode-title');
                
                // 要素が存在するかチェック
                const title = titleElement ? titleElement.textContent.toLowerCase() : '';
                
                if (searchTerm === '' || title.includes(searchTerm)) {
                    // 表示
                    card.style.display = '';
                    card.style.opacity = '';
                    card.style.transform = '';
                    card.style.visibility = '';
                } else {
                    // 非表示
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // スクロールアニメーション
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
            }
        });
    }, observerOptions);
    
    // 初期状態のカードを観察
    episodeCards.forEach(card => {
        observer.observe(card);
    });
    
    // 無限スクロール機能
    const scrollTrigger = document.getElementById('scroll-trigger');
    const loadingIndicator = document.getElementById('loading-indicator');
    let isLoading = false;
    let hasMoreContent = true;
    
    if (scrollTrigger) {
        const scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoading && hasMoreContent) {
                    loadMoreEpisodes();
                }
            });
        }, {
            rootMargin: '200px'
        });
        
        scrollObserver.observe(scrollTrigger);
    }
    
    function loadMoreEpisodes() {
        if (isLoading || !hasMoreContent) return;
        
        isLoading = true;
        const offset = parseInt(scrollTrigger.dataset.offset);
        const limit = parseInt(scrollTrigger.dataset.limit);
        
        // ローディングインジケーターを表示
        loadingIndicator.style.display = 'block';
        setTimeout(() => {
            loadingIndicator.classList.add('visible');
        }, 10);
        
        // AJAXリクエストでエピソードを取得
        fetch(`${window.location.origin}/wp-admin/admin-ajax.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=load_more_episodes&offset=${offset}&limit=${limit}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.html) {
                // 新しいエピソードを追加
                const episodesGrid = document.getElementById('episodes-grid');
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.data.html;
                
                // 各カードを個別に追加してアニメーション
                const newCards = tempDiv.querySelectorAll('.modern-episode-card');
                newCards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(30px)';
                    episodesGrid.appendChild(card);
                    
                    setTimeout(() => {
                        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
                
                // オフセットを更新
                scrollTrigger.dataset.offset = offset + limit;
                
                // コンテンツがなくなったかチェック
                if (data.data.has_more === false) {
                    hasMoreContent = false;
                    scrollTrigger.style.display = 'none';
                }
            } else {
                hasMoreContent = false;
                scrollTrigger.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('エピソードの読み込みエラー:', error);
            hasMoreContent = false;
        })
        .finally(() => {
            isLoading = false;
            loadingIndicator.classList.remove('visible');
            setTimeout(() => {
                loadingIndicator.style.display = 'none';
            }, 300);
        });
    }
    
    // パフォーマンス最適化：スクロール時の処理
    let ticking = false;
    
    function updateScrollEffects() {
        const scrollY = window.scrollY;
        const heroSection = document.querySelector('.episodes-hero');
        
        if (heroSection) {
            const heroHeight = heroSection.offsetHeight;
            const scrollPercent = Math.min(scrollY / heroHeight, 1);
            
            // パララックス効果
            heroSection.style.transform = `translateY(${scrollPercent * 50}px)`;
            heroSection.style.opacity = 1 - scrollPercent * 0.3;
        }
        
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateScrollEffects);
            ticking = true;
        }
    });
});
</script>

<?php get_footer(); ?>
