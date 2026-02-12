<?php
/**
 * 構造化データ（JSON-LD）と OGP タグの出力
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * OGP (Open Graph Protocol) メタタグを出力
 */
function contentfreaks_output_ogp_tags() {
    $site_name = get_bloginfo('name');
    $default_image = get_theme_mod('podcast_artwork', '');
    
    echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
    echo '<meta property="og:locale" content="ja_JP">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    
    if (is_front_page()) {
        $description = get_theme_mod('podcast_description', '「コンテンツフリークス」は、大学時代からの友人2人で「いま気になる」注目のエンタメコンテンツを熱く語るポッドキャスト');
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($site_name) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(home_url('/')) . '">' . "\n";
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        if ($default_image) {
            echo '<meta property="og:image" content="' . esc_url($default_image) . '">' . "\n";
        }
    } elseif (is_page()) {
        // 固定ページ（エピソード一覧、ブログ、プロフィール等）
        $title = get_the_title();
        $description = has_excerpt() ? get_the_excerpt() : $site_name . ' - ' . $title;
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title . ' | ' . $site_name) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(wp_strip_all_tags($description)) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
        echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($description)) . '">' . "\n";
        if (has_post_thumbnail()) {
            $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            echo '<meta property="og:image" content="' . esc_url($thumb_url) . '">' . "\n";
        } elseif ($default_image) {
            echo '<meta property="og:image" content="' . esc_url($default_image) . '">' . "\n";
        }
    } elseif (is_singular()) {
        $post_id = get_the_ID();
        $title = get_the_title();
        $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 55);
        $permalink = get_permalink();
        $is_episode = get_post_meta($post_id, 'is_podcast_episode', true) === '1';
        
        // エピソードの場合、タイムスタンプ行を除去してからディスクリプション生成
        if ($is_episode && !has_excerpt()) {
            $raw_content = get_the_content();
            $clean_content = preg_replace('/^\s*\d{1,2}:\d{2}(:\d{2})?\s+.+$/m', '', $raw_content);
            $excerpt = wp_trim_words(wp_strip_all_tags($clean_content), 55);
        } else {
            $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 55);
        }
        
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(wp_strip_all_tags($excerpt)) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($permalink) . '">' . "\n";
        echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($excerpt)) . '">' . "\n";
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c')) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c')) . '">' . "\n";
        
        if (has_post_thumbnail()) {
            $thumb_url = get_the_post_thumbnail_url($post_id, 'large');
            echo '<meta property="og:image" content="' . esc_url($thumb_url) . '">' . "\n";
        } elseif ($default_image) {
            echo '<meta property="og:image" content="' . esc_url($default_image) . '">' . "\n";
        }
    } elseif (is_archive() || is_tag() || is_category()) {
        $title = get_the_archive_title();
        $description = get_the_archive_description() ?: $site_name . 'のアーカイブ';
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title . ' | ' . $site_name) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(wp_strip_all_tags($description)) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_pagenum_link()) . '">' . "\n";
        echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($description)) . '">' . "\n";
        if ($default_image) {
            echo '<meta property="og:image" content="' . esc_url($default_image) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'contentfreaks_output_ogp_tags', 5);

/**
 * PodcastSeries 構造化データ（トップページ用）
 */
function contentfreaks_output_structured_data() {
    if (is_front_page()) {
        $site_name = get_bloginfo('name');
        $description = get_theme_mod('podcast_description', '「コンテンツフリークス」は、大学時代からの友人2人で「いま気になる」注目のエンタメコンテンツを熱く語るポッドキャスト');
        $artwork = get_theme_mod('podcast_artwork', '');
        
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'PodcastSeries',
            'name' => $site_name,
            'description' => $description,
            'url' => home_url('/'),
            'webFeed' => CONTENTFREAKS_RSS_URL,
            'inLanguage' => 'ja',
            'author' => array(
                '@type' => 'Organization',
                'name' => 'ContentFreaks',
                'url' => home_url('/')
            )
        );
        
        if ($artwork) {
            $structured_data['image'] = $artwork;
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode($structured_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
    
    // 個別エピソードページ
    if (is_singular('post')) {
        $post_id = get_the_ID();
        $is_episode = get_post_meta($post_id, 'is_podcast_episode', true);
        
        if ($is_episode === '1') {
            $audio_url = get_post_meta($post_id, 'episode_audio_url', true);
            $duration = get_post_meta($post_id, 'episode_duration', true);
            $episode_number = get_post_meta($post_id, 'episode_number', true);
            
            $episode_data = array(
                '@context' => 'https://schema.org',
                '@type' => 'PodcastEpisode',
                'name' => get_the_title(),
                'description' => has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 55),
                'url' => get_permalink(),
                'datePublished' => get_the_date('c'),
                'partOfSeries' => array(
                    '@type' => 'PodcastSeries',
                    'name' => get_bloginfo('name'),
                    'url' => home_url('/')
                )
            );
            
            if ($episode_number) {
                $episode_data['episodeNumber'] = intval($episode_number);
            }
            
            if ($duration) {
                // "45:30" や "1:23:45" 形式をISO 8601 duration (PT##H##M##S) に変換
                $parts = array_map('intval', explode(':', $duration));
                $iso_duration = 'PT';
                if (count($parts) === 3) {
                    $iso_duration .= $parts[0] . 'H' . $parts[1] . 'M' . $parts[2] . 'S';
                } elseif (count($parts) === 2) {
                    $iso_duration .= $parts[0] . 'M' . $parts[1] . 'S';
                }
                $episode_data['timeRequired'] = $iso_duration;
            }
            
            if ($audio_url) {
                $episode_data['associatedMedia'] = array(
                    '@type' => 'MediaObject',
                    'contentUrl' => $audio_url
                );
            }
            
            if (has_post_thumbnail()) {
                $episode_data['image'] = get_the_post_thumbnail_url($post_id, 'large');
            }
            
            echo '<script type="application/ld+json">' . wp_json_encode($episode_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
        }
    }
}
add_action('wp_head', 'contentfreaks_output_structured_data', 6);
