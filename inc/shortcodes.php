<?php
/**
 * ショートコード: ポッドキャストプラットフォームリンク
 */
function contentfreaks_podcast_platforms_shortcode() {
    $platforms = array(
        'spotify' => array(
            'name' => 'Spotify', 
            'icon' => get_theme_mod('spotify_icon') ? '<img src="' . esc_url(get_theme_mod('spotify_icon')) . '" alt="Spotify">' : '🎧',
            'url' => CONTENTFREAKS_SPOTIFY_URL,
            'color' => '#1DB954'
        ),
        'apple' => array(
            'name' => 'Apple Podcasts', 
            'icon' => get_theme_mod('apple_podcasts_icon') ? '<img src="' . esc_url(get_theme_mod('apple_podcasts_icon')) . '" alt="Apple Podcasts">' : '🍎',
            'url' => CONTENTFREAKS_APPLE_URL,
            'color' => '#A855F7'
        ),
        'youtube' => array(
            'name' => 'YouTube', 
            'icon' => get_theme_mod('youtube_icon') ? '<img src="' . esc_url(get_theme_mod('youtube_icon')) . '" alt="YouTube">' : '📺',
            'url' => CONTENTFREAKS_YOUTUBE_URL,
            'color' => '#FF0000'
        ),
    );
    
    ob_start();
    echo '<div class="platforms-grid">';
    
    $count = 0;
    foreach ($platforms as $key => $platform) {
        if ($count > 0) {
            echo '<span class="platform-separator">/</span>';
        }
        echo '<a href="' . esc_url($platform['url']) . '" class="platform-link platform-' . esc_attr($key) . '" target="_blank" rel="noopener">';
        echo '<span class="platform-name">' . esc_html($platform['name']) . '</span>';
        echo '</a>';
        $count++;
    }
    
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('podcast_platforms', 'contentfreaks_podcast_platforms_shortcode');

/**
 * ショートコード: ホスト紹介
 */
function contentfreaks_hosts_shortcode() {
    // カスタマイザーから2人分のホスト情報を取得
    $host1_name = get_theme_mod('host1_name', 'ホスト1');
    $host1_role = get_theme_mod('host1_role', 'メインホスト');
    $host1_bio = get_theme_mod('host1_bio', '作品の裏側を深掘り＆司会進行担当。');
    $host1_image = get_theme_mod('host1_image', '');
    $host1_twitter = get_theme_mod('host1_twitter', '');
    $host1_youtube = get_theme_mod('host1_youtube', '');
    
    $host2_name = get_theme_mod('host2_name', 'ホスト2');
    $host2_role = get_theme_mod('host2_role', 'コホスト');
    $host2_bio = get_theme_mod('host2_bio', '一般目線の感想担当、親しみやすさをプラス。');
    $host2_image = get_theme_mod('host2_image', '');
    $host2_twitter = get_theme_mod('host2_twitter', '');
    $host2_youtube = get_theme_mod('host2_youtube', '');
    
    $hosts = array();
    
    // ホスト1の情報を追加
    $host1_social = array();
    if (!empty($host1_twitter)) $host1_social['twitter'] = $host1_twitter;
    if (!empty($host1_youtube)) $host1_social['youtube'] = $host1_youtube;
    
    $hosts[] = array(
        'name' => $host1_name,
        'role' => $host1_role,
        'bio' => $host1_bio,
        'image' => $host1_image,
        'social' => $host1_social
    );
    
    // ホスト2の情報を追加
    $host2_social = array();
    if (!empty($host2_twitter)) $host2_social['twitter'] = $host2_twitter;
    if (!empty($host2_youtube)) $host2_social['youtube'] = $host2_youtube;
    
    $hosts[] = array(
        'name' => $host2_name,
        'role' => $host2_role,
        'bio' => $host2_bio,
        'image' => $host2_image,
        'social' => $host2_social
    );
    
    ob_start();
    echo '<div class="hosts-grid">';
    
    foreach ($hosts as $index => $host) {
        echo '<div class="host-card">';
        
        if ($host['image']) {
            echo '<div class="host-image"><img src="' . esc_url($host['image']) . '" alt="' . esc_attr($host['name']) . '"></div>';
        } else {
            echo '<div class="host-image" style="background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 3rem;">🎙️</div>';
        }
        
        echo '<div class="host-content">';
        echo '<h3 class="host-name">' . esc_html($host['name']) . '</h3>';
        echo '<div class="host-role">' . esc_html($host['role']) . '</div>';
        
        // bioをカスタマイザーの値を使用
        echo '<div class="host-bio">';
        echo esc_html($host['bio']);
        echo '</div>';
        
        if (!empty($host['social'])) {
            echo '<div class="host-social">';
            foreach ($host['social'] as $platform => $url) {
                if ($platform === 'twitter') {
                    $icon = '<img src="https://content-freaks.jp/wp-content/uploads/2024/05/logo-black.png" alt="Twitter" style="width: 24px; height: 24px; object-fit: contain;">';
                } else {
                    $icon = $platform === 'youtube' ? '📺' : '🔗';
                }
                echo '<a href="' . esc_url($url) . '" class="social-link" target="_blank" rel="noopener">' . $icon . '</a>';
            }
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('podcast_hosts', 'contentfreaks_hosts_shortcode');
