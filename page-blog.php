<?php
/**
 * Template Name: ブログ記事一覧
 * 手動投稿のブログ記事一覧を表示
 */

get_header(); ?>

<style>
/* ページ全体の上部マージン調整（モダンヘッダー対応） */
body {
    padding-top: 0 !important;
    margin-top: 0 !important;
}

/* スムーズスクロールとパフォーマンス改善 */
html {
    scroll-behavior: smooth;
}

* {
    -webkit-tap-highlight-color: rgba(255, 107, 53, 0.1);
}

/* ヒーローセクション - オレンジ系グラデーション */
.blog-hero {
    position: relative;
    background: var(--blog-hero-bg);
    padding: 1rem 0 3rem 0;
    overflow: hidden;
    min-height: 50vh;
    display: flex;
    align-items: center;
    color: var(--blog-hero-text);
    margin-bottom: 0;
}

.blog-hero-bg {
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

.blog-hero-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
}

.blog-particle {
    position: absolute;
    width: 8px;
    height: 8px;
    background: var(--blog-hero-particle);
    border-radius: 50%;
    animation: blog-float 10s infinite ease-in-out;
}

.blog-particle:nth-child(1) { left: 15%; animation-delay: 0s; }
.blog-particle:nth-child(2) { left: 30%; animation-delay: 2s; }
.blog-particle:nth-child(3) { left: 45%; animation-delay: 4s; }
.blog-particle:nth-child(4) { left: 60%; animation-delay: 1s; }
.blog-particle:nth-child(5) { left: 75%; animation-delay: 3s; }
.blog-particle:nth-child(6) { left: 90%; animation-delay: 1.5s; }

@keyframes blog-float {
    0%, 100% { transform: translateY(100vh) scale(0); opacity: 0; }
    10% { opacity: 1; transform: translateY(90vh) scale(1); }
    90% { opacity: 1; transform: translateY(-10vh) scale(1); }
    100% { opacity: 0; transform: translateY(-20vh) scale(0); }
}

.blog-hero-content {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    text-align: center;
}

.blog-hero-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    display: inline-block;
    animation: pulse 3s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.blog-hero h1 {
    font-size: var(--hero-title);
    font-weight: 800;
    color: var(--blog-hero-text);
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
    text-shadow: var(--blog-hero-title-shadow);
}

.blog-hero-description {
    font-size: var(--body-text);
    color: var(--blog-hero-text);
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.6;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.blog-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
}

.blog-stat {
    text-align: center;
    color: var(--blog-hero-text);
    background: var(--blog-hero-stats-bg);
    padding: 1.5rem 2rem;
    border-radius: 20px;
    backdrop-filter: blur(10px);
    border: 1px solid var(--blog-hero-stats-border);
    transition: all 0.3s ease;
    min-width: 120px;
}

.blog-stat:hover {
    transform: translateY(-5px);
    background: var(--blog-hero-stats-hover);
}

.blog-stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1;
    text-shadow: var(--blog-hero-stats-shadow);
}

.blog-stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}
    display: block;
    font-size: 2rem;
    font-weight: 900;
    color: white;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.blog-stat-label {
    display: block;
    font-size: 0.85rem;
    color: var(--blog-hero-stats-label);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 0.5rem;
}

/* メインコンテンツエリア */
.content-area {
    background: var(--blog-content-bg);
    min-height: 100vh;
    position: relative;
}

.main-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    position: relative;
    z-index: 2;
}

/* フィルターボタン - オレンジ系 */
.blog-filters {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin: 3rem 0;
    flex-wrap: wrap;
    padding: 2rem 0;
}

.blog-filter-btn {
    padding: 1rem 2rem;
    background: var(--blog-filters-bg);
    backdrop-filter: blur(10px);
    border: 2px solid transparent;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    font-size: 1rem;
    color: var(--blog-filters-title);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
    min-height: 44px; /* タッチ操作に適したサイズ */
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.blog-filter-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: var(--blog-content-shimmer);
    transition: left 0.6s ease;
}

/* タッチデバイス向けのアクティブ状態 */
.blog-filter-btn:active {
    transform: translateY(-1px) scale(0.98);
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.2);
}

/* ホバー効果（タッチデバイス以外） */
@media (hover: hover) and (pointer: fine) {
    .blog-filter-btn:hover::before {
        left: 100%;
    }
    
    .blog-filter-btn:hover {
        background: var(--blog-card-btn-bg);
        color: var(--blog-filters-active-text);
        border-color: rgba(255, 255, 255, 0.3);
        transform: translateY(-3px);
        box-shadow: var(--blog-card-btn-shadow);
    }
}

.blog-filter-btn.active {
    background: var(--blog-card-btn-bg);
    color: var(--blog-filters-active-text);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-3px);
    box-shadow: var(--blog-card-btn-shadow);
}

/* ブログ記事グリッド */
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
    padding: 2rem 0;
}

.blog-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 
        0 20px 40px rgba(255, 107, 53, 0.1),
        0 0 0 1px rgba(255,255,255,0.5);
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.3);
    position: relative;
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
    transform: translateY(30px);
    cursor: pointer;
}

/* タッチデバイス向けのアクティブ状態 */
.blog-card:active {
    transform: translateY(-5px) scale(0.98);
    box-shadow: 
        0 15px 30px rgba(255, 107, 53, 0.15),
        0 0 0 1px rgba(255,255,255,0.7);
}

/* ホバー効果（タッチデバイス以外） */
@media (hover: hover) and (pointer: fine) {
    .blog-card:hover {
        transform: translateY(-10px);
        box-shadow: 
            0 30px 60px rgba(255, 107, 53, 0.15),
            0 0 0 1px rgba(255,255,255,0.7);
    }
    
    .blog-card:hover .blog-thumbnail img {
        transform: scale(1.05);
    }
    
    .blog-card:hover .blog-featured-overlay {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.1);
    }
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.blog-card:nth-child(1) { animation-delay: 0.1s; }
.blog-card:nth-child(2) { animation-delay: 0.2s; }
.blog-card:nth-child(3) { animation-delay: 0.3s; }
.blog-card:nth-child(4) { animation-delay: 0.4s; }
.blog-card:nth-child(5) { animation-delay: 0.5s; }
.blog-card:nth-child(6) { animation-delay: 0.6s; }

.blog-thumbnail {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.blog-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.blog-category-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: var(--blog-card-btn-bg);
    color: var(--blog-card-btn-text);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.8rem;
    box-shadow: var(--blog-card-btn-shadow);
}

.blog-date-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
}

.blog-featured-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--blog-card-btn-bg);
    color: var(--blog-card-btn-text);
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    cursor: pointer;
    opacity: 0;
    transition: all 0.3s ease;
    box-shadow: var(--blog-card-btn-shadow);
}

.blog-content {
    padding: 2rem;
}

.blog-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.blog-author {
    color: var(--blog-filters-active);
    font-size: 0.85rem;
    font-weight: 600;
    background: rgba(255, 107, 53, 0.1);
    padding: 0.3rem 1rem;
    border-radius: 15px;
    display: inline-block;
}

.blog-read-time {
    color: var(--blog-card-meta);
    font-size: 0.8rem;
    background: rgba(102, 102, 102, 0.1);
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
}

.blog-title {
    margin: 1rem 0 1rem 0;
    font-size: 1.3rem;
    line-height: 1.4;
    font-weight: 700;
}

.blog-title a {
    color: var(--blog-card-title);
    text-decoration: none;
    transition: color 0.3s ease;
}

.blog-title a:hover {
    color: var(--blog-filters-active);
}

.blog-excerpt {
    color: var(--blog-card-excerpt);
    line-height: 1.6;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
}

.blog-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.blog-read-more {
    padding: 0.8rem 1.5rem;
    background: var(--blog-card-btn-bg);
    color: var(--blog-card-btn-text);
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    box-shadow: var(--blog-card-btn-shadow);
}

.blog-read-more:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.5);
    color: var(--blog-card-btn-text);
    background: var(--blog-card-btn-hover);
}

.blog-tags {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.blog-tag {
    background: rgba(255, 107, 53, 0.1);
    color: var(--blog-filters-active);
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.blog-tag:hover {
    background: #ff6b35;
    color: white;
}

/* ブログプレースホルダー画像 */
.blog-placeholder {
    background: var(--blog-card-btn-bg);
    width: 100%;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--blog-card-btn-text);
}

/* ロードモアボタン */
.load-more-container {
    text-align: center;
    margin: 4rem 0;
}

.load-more-btn {
    background: var(--blog-card-btn-bg);
    color: var(--blog-card-btn-text);
    border: none;
    padding: 1.2rem 3rem;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: var(--blog-card-btn-shadow);
}

.load-more-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(255, 107, 53, 0.5);
    background: var(--blog-card-btn-hover);
}

/* ブログなし状態 */
.no-blog-posts {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 25px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #6c757d;
}

.no-blog-posts p {
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

/* タブレット（768px-1023px） */
@media (min-width: 768px) and (max-width: 1023px) {
    .blog-hero {
        padding: 1.5rem 0 2.5rem 0;
    }
    
    .blog-hero-icon {
        font-size: 4.5rem;
        margin-bottom: 1.25rem;
    }
    
    .blog-hero h1 {
        font-size: 2.25rem;
        margin-bottom: 1.25rem;
    }
    
    .blog-hero-description {
        font-size: 1.3rem;
        margin-bottom: 2.25rem;
    }
    
    .blog-stats {
        gap: 2rem;
        margin-top: 2.5rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .blog-stat-number {
        font-size: 2.25rem;
    }
    
    .blog-stat-label {
        font-size: 0.85rem;
    }
}

/* プロフィールページ用モバイル調整 */
@media (max-width: 768px) {
    .blog-hero {
        padding: 0.5rem 0 2rem 0;
        min-height: 40vh;
    }
    
    .blog-hero-content {
        padding: 0 1rem;
    }
    
    .blog-hero-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
    
    .blog-hero h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
        line-height: 1.2;
    }
    
    .blog-hero-description {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        line-height: 1.4;
    }
    
    .blog-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-top: 2rem;
        max-width: 360px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .blog-stat {
        padding: 1.2rem 1rem;
        min-width: auto;
        text-align: center;
    }
    
    .blog-stat-number {
        font-size: 2rem;
    }
    
    .blog-stat-label {
        font-size: 0.8rem;
    }
    
    .main-content {
        padding: 0 1rem;
    }
    
    .blog-grid {
        grid-template-columns: 1fr;
        gap: 1.2rem;
        margin: 1.5rem 0;
        padding: 1rem 0;
    }
    
    .blog-filters {
        gap: 0.6rem;
        margin: 1.5rem 0;
        padding: 1rem 0;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .blog-filter-btn {
        padding: 0.7rem 1.2rem;
        font-size: 0.85rem;
        border-radius: 25px;
        flex: 0 0 auto;
    }
    
    .blog-card {
        border-radius: 20px;
        overflow: hidden;
    }
    
    .blog-thumbnail {
        height: 180px;
    }
    
    .blog-category-badge {
        top: 10px;
        left: 10px;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        border-radius: 15px;
    }
    
    .blog-date-badge {
        top: 10px;
        right: 10px;
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        border-radius: 15px;
    }
    
    .blog-featured-overlay {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .blog-content {
        padding: 1.2rem;
    }
    
    .blog-title {
        font-size: 1.1rem;
        margin: 0.8rem 0;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .blog-excerpt {
        font-size: 0.9rem;
        margin-bottom: 1.2rem;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .blog-meta {
        gap: 0.5rem;
        margin-bottom: 0.8rem;
        flex-wrap: wrap;
    }
    
    .blog-author,
    .blog-read-time {
        font-size: 0.8rem;
        padding: 0.25rem 0.8rem;
        border-radius: 12px;
    }
    
    .blog-actions {
        justify-content: center;
        flex-direction: column;
        align-items: stretch;
        gap: 0.8rem;
    }
    
    .blog-read-more {
        padding: 0.7rem 1.2rem;
        font-size: 0.85rem;
        text-align: center;
        border-radius: 25px;
    }
    
    .blog-tags {
        justify-content: center;
        gap: 0.4rem;
        flex-wrap: wrap;
    }
    
    .blog-tag {
        font-size: 0.7rem;
        padding: 0.25rem 0.6rem;
        border-radius: 10px;
    }
    
    .load-more-container {
        margin: 2rem 0;
    }
    
    .load-more-btn {
        padding: 1rem 2rem;
        font-size: 1rem;
        border-radius: 30px;
    }
}

@media (max-width: 480px) {
    .breadcrumb-container {
        padding: 0.75rem 1rem;
    }
    
    .breadcrumb-nav {
        margin-bottom: 0.5rem;
    }
    
    .blog-hero {
        padding: 0.25rem 0 1.5rem 0;
    }
    
    .blog-hero-content {
        padding: 0 1rem;
    }
    
    .blog-hero-icon {
        font-size: 3rem;
        margin-bottom: 0.75rem;
    }
    
    .blog-hero h1 {
        margin-bottom: 0.75rem;
    }
    
    .blog-hero-description {
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }
    
    .blog-stats {
        gap: 1rem;
        margin-top: 1.5rem;
        grid-template-columns: repeat(3, 1fr);
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .blog-stat {
        min-width: auto;
        padding: 1rem 0.5rem;
        text-align: center;
    }
    
    .blog-stat-number {
        font-size: 1.75rem;
    }
    
    .blog-stat-label {
        font-size: 0.75rem;
    }
    
    .main-content {
        padding: 0 0.8rem;
    }
    
    .blog-grid {
        gap: 1rem;
        margin: 1rem 0;
    }
    
    .blog-filters {
        gap: 0.5rem;
        margin: 1rem 0;
        padding: 0.5rem 0;
    }
    
    .blog-filter-btn {
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
        border-radius: 20px;
        min-width: 70px;
    }
    
    .blog-card {
        border-radius: 18px;
    }
    
    .blog-thumbnail {
        height: 160px;
    }
    
    .blog-category-badge {
        top: 8px;
        left: 8px;
        padding: 0.3rem 0.6rem;
        font-size: 0.7rem;
        border-radius: 12px;
    }
    
    .blog-date-badge {
        top: 8px;
        right: 8px;
        padding: 0.3rem 0.6rem;
        font-size: 0.7rem;
        border-radius: 12px;
    }
    
    .blog-featured-overlay {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }
    
    .blog-content {
        padding: 1rem;
    }
    
    .blog-title {
        font-size: 1rem;
        margin: 0.6rem 0;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .blog-excerpt {
        font-size: 0.85rem;
        margin-bottom: 1rem;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .blog-meta {
        gap: 0.4rem;
        margin-bottom: 0.6rem;
        flex-wrap: wrap;
    }
    
    .blog-author,
    .blog-read-time {
        font-size: 0.75rem;
        padding: 0.2rem 0.6rem;
        border-radius: 10px;
    }
    
    .blog-actions {
        gap: 0.6rem;
    }
    
    .blog-read-more {
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
        border-radius: 20px;
    }
    
    .blog-tags {
        gap: 0.3rem;
        flex-wrap: wrap;
    }
    
    .blog-tag {
        font-size: 0.65rem;
        padding: 0.2rem 0.5rem;
        border-radius: 8px;
    }
    
    .load-more-btn {
        padding: 0.8rem 1.5rem;
        font-size: 0.9rem;
        border-radius: 25px;
    }
    
    .no-blog-posts {
        padding: 2rem 1rem;
        border-radius: 20px;
    }
    
    .no-blog-posts p {
        font-size: 1rem;
    }
}

/* 非常に小さな画面（360px以下）への追加対応 */
@media (max-width: 360px) {
    .blog-hero-icon {
        font-size: 2rem;
    }
    
    .blog-hero h1 {
        font-size: 1.8rem;
    }
    
    .blog-hero-description {
        font-size: 0.85rem;
    }
    
    .blog-stats {
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
    
    .blog-stat {
        padding: 0.6rem 0.8rem;
        min-width: 70px;
        max-width: 90px;
    }
    
    .blog-stat-number {
        font-size: 1.2rem;
    }
    
    .blog-stat-label {
        font-size: 0.7rem;
    }
    
    .main-content {
        padding: 0 0.5rem;
    }
    
    .blog-filters {
        padding: 0.3rem 0;
    }
    
    .blog-filter-btn {
        padding: 0.5rem 0.8rem;
        font-size: 0.75rem;
        min-width: 60px;
    }
    
    .blog-thumbnail {
        height: 140px;
    }
    
    .blog-category-badge,
    .blog-date-badge {
        padding: 0.25rem 0.5rem;
        font-size: 0.65rem;
        top: 6px;
    }
    
    .blog-category-badge {
        left: 6px;
    }
    
    .blog-date-badge {
        right: 6px;
    }
    
    .blog-featured-overlay {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .blog-content {
        padding: 0.8rem;
    }
    
    .blog-title {
        font-size: 0.95rem;
        margin: 0.5rem 0;
    }
    
    .blog-excerpt {
        font-size: 0.8rem;
        margin-bottom: 0.8rem;
    }
    
    .blog-meta {
        gap: 0.3rem;
        margin-bottom: 0.5rem;
    }
    
    .blog-author,
    .blog-read-time {
        font-size: 0.7rem;
        padding: 0.15rem 0.5rem;
    }
    
    .blog-read-more {
        padding: 0.5rem 0.8rem;
        font-size: 0.75rem;
    }
    
    .blog-tag {
        font-size: 0.6rem;
        padding: 0.15rem 0.4rem;
    }
    
    .load-more-btn {
        padding: 0.7rem 1.2rem;
        font-size: 0.85rem;
    }
    
    .breadcrumb-container {
        padding: 0 0.5rem;
        font-size: 0.7rem;
    }
    
    .no-blog-posts {
        padding: 1.5rem 0.8rem;
    }
    
    .no-blog-posts p {
        font-size: 0.9rem;
    }
}
</style>

<main id="main" class="site-main contentfreaks-episodes-page">
    <div class="content-area">
        <!-- パンくずナビゲーション -->
        <nav class="breadcrumb-nav">
            <div class="breadcrumb-container">
                <a href="<?php echo home_url(); ?>" class="breadcrumb-home">🏠 ホーム</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">ブログ記事</span>
            </div>
        </nav>

        <!-- ヒーローセクション -->
        <section class="blog-hero">
            <div class="blog-hero-bg">
                <div class="hero-pattern"></div>
            </div>
            
            <!-- パーティクルアニメーション -->
            <div class="blog-hero-particles">
                <div class="blog-particle"></div>
                <div class="blog-particle"></div>
                <div class="blog-particle"></div>
                <div class="blog-particle"></div>
                <div class="blog-particle"></div>
                <div class="blog-particle"></div>
            </div>
            
            <div class="blog-hero-content">
                <div class="blog-hero-icon">📖</div>
                <h1>Blog Articles</h1>
                <p class="blog-hero-description">
                    コンテンツフリークスの手動投稿ブログ記事。ポッドキャスト分析、レビュー、コラムなど、じっくり読める記事をお届けします。
                </p>
                
                <div class="blog-stats">
                    <div class="blog-stat">
                        <span class="blog-stat-number">
                            <?php 
                            $total_blog_posts = new WP_Query(array(
                                'post_type' => 'post',
                                'posts_per_page' => -1,
                                'meta_query' => array(
                                    array(
                                        'key' => 'is_podcast_episode',
                                        'compare' => 'NOT EXISTS'
                                    )
                                ),
                                'post_status' => 'publish'
                            ));
                            echo $total_blog_posts->found_posts;
                            wp_reset_postdata();
                            ?>
                        </span>
                        <span class="blog-stat-label">記事</span>
                    </div>
                    <div class="blog-stat">
                        <span class="blog-stat-number">✍️</span>
                        <span class="blog-stat-label">執筆記事</span>
                    </div>
                    <div class="blog-stat">
                        <span class="blog-stat-number">💡</span>
                        <span class="blog-stat-label">分析</span>
                    </div>
                </div>
            </div>
        </section>

        <main class="main-content">

            <div class="blog-filters">
                <button class="blog-filter-btn active" data-filter="all">すべて</button>
                <button class="blog-filter-btn" data-filter="レビュー">レビュー</button>
                <button class="blog-filter-btn" data-filter="コラム">コラム</button>
                <button class="blog-filter-btn" data-filter="分析">分析</button>
            </div>

            <div class="blog-grid" id="blog-grid">
                <?php
                // ブログ投稿を取得（ポッドキャストエピソード以外）
                $blog_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 12,
                    'meta_query' => array(
                        array(
                            'key' => 'is_podcast_episode',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));

                if ($blog_query->have_posts()) :
                    while ($blog_query->have_posts()) : $blog_query->the_post();
                        // カテゴリーとタグを取得
                        $categories = get_the_category();
                        $tags = get_the_tags();
                        $main_category = !empty($categories) ? $categories[0]->name : 'その他';
                        $read_time = get_post_meta(get_the_ID(), 'estimated_read_time', true) ?: '3分';
                        $author_display = get_the_author_meta('display_name');
                ?>
                    <article class="blog-card" data-category="<?php echo esc_attr($main_category); ?>">
                        <div class="blog-thumbnail">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium', array('alt' => get_the_title(), 'loading' => 'lazy')); ?>
                            <?php else : ?>
                                <div class="blog-placeholder">📖</div>
                            <?php endif; ?>
                            
                            <div class="blog-category-badge"><?php echo esc_html($main_category); ?></div>
                            <div class="blog-date-badge"><?php echo get_the_date('n/j'); ?></div>
                            
                            <div class="blog-featured-overlay">📄</div>
                        </div>
                        
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="blog-author">by <?php echo esc_html($author_display); ?></span>
                                <span class="blog-read-time">読了 <?php echo esc_html($read_time); ?></span>
                            </div>
                            
                            <h3 class="blog-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="blog-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
                            </div>
                            
                            <div class="blog-actions">
                                <a href="<?php the_permalink(); ?>" class="blog-read-more">続きを読む</a>
                                <div class="blog-tags">
                                    <?php if ($tags) : ?>
                                        <?php foreach (array_slice($tags, 0, 3) as $tag) : ?>
                                            <a href="<?php echo get_tag_link($tag->term_id); ?>" class="blog-tag">#<?php echo esc_html($tag->name); ?></a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php 
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                    <div class="no-blog-posts">
                        <p>ブログ記事が見つかりませんでした。</p>
                        <p>新しい記事を投稿してください。</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($blog_query->found_posts > 12) : ?>
            <div class="load-more-container">
                <button id="load-more-blog" class="load-more-btn" data-offset="12" data-limit="12">
                    さらに読み込む
                </button>
            </div>
            <?php endif; ?>

        </main>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // フィルター機能
    const filterButtons = document.querySelectorAll('.blog-filter-btn');
    const blogCards = document.querySelectorAll('.blog-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // アクティブボタンの切り替え
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filterValue = this.dataset.filter;
            
            // カードのフィルタリング
            blogCards.forEach(card => {
                if (filterValue === 'all' || card.dataset.category === filterValue) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeInUp 0.6s ease-out forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // ブログカードのクリック処理
    blogCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // リンクが直接クリックされた場合は何もしない
            if (e.target.tagName.toLowerCase() === 'a') {
                return;
            }
            
            // カード内のリンクを探して遷移
            const link = this.querySelector('.blog-title a');
            if (link) {
                window.location.href = link.href;
            }
        });
        
        // カードにfocusableな属性を追加（アクセシビリティ向上）
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        card.setAttribute('aria-label', 'ブログ記事を読む');
        
        // キーボード操作対応
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const link = this.querySelector('.blog-title a');
                if (link) {
                    window.location.href = link.href;
                }
            }
        });
    });
    
    // ロードモア機能
    const loadMoreBtn = document.getElementById('load-more-blog');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const offset = parseInt(this.dataset.offset);
            const limit = parseInt(this.dataset.limit);
            
            // Ajax リクエストでブログ記事を追加読み込み
            fetch(`${window.location.href}?ajax=1&offset=${offset}&limit=${limit}`)
                .then(response => response.text())
                .then(html => {
                    const blogGrid = document.getElementById('blog-grid');
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    
                    // 新しいブログカードを追加
                    const newCards = tempDiv.querySelectorAll('.blog-card');
                    newCards.forEach((card, index) => {
                        card.style.animationDelay = `${(index + 1) * 0.1}s`;
                        blogGrid.appendChild(card);
                    });
                    
                    // オフセットを更新
                    this.dataset.offset = offset + limit;
                    
                    // ボタンが不要になったら非表示
                    if (newCards.length < limit) {
                        this.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('ブログ記事の読み込みに失敗しました:', error);
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
    blogCards.forEach(card => {
        observer.observe(card);
    });
});
</script>

<?php get_footer(); ?>
