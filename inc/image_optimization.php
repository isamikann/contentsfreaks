<?php
/**
 * 画像最適化機能
 * - WebP対応
 * - レスポンシブ画像
 * - 遅延読み込み
 */

/**
 * 画像にloading="lazy"とfetchpriority属性を自動追加
 */
function contentfreaks_add_image_attributes($content) {
    // 画像タグを検索
    if (preg_match_all('/<img[^>]+>/i', $content, $images)) {
        foreach ($images[0] as $image) {
            // すでにloading属性がある場合はスキップ
            if (strpos($image, 'loading=') !== false) {
                continue;
            }
            
            // loading="lazy"を追加
            $new_image = str_replace('<img', '<img loading="lazy"', $image);
            
            // 最初の画像（LCP候補）にはfetchpriority="high"
            static $first_image = true;
            if ($first_image && strpos($image, 'class="') !== false && 
                (strpos($image, 'hero') !== false || strpos($image, 'featured') !== false)) {
                $new_image = str_replace('loading="lazy"', 'fetchpriority="high"', $new_image);
                $first_image = false;
            }
            
            $content = str_replace($image, $new_image, $content);
        }
    }
    
    return $content;
}
add_filter('the_content', 'contentfreaks_add_image_attributes');

/**
 * WebP対応: アップロードされた画像をWebPに変換
 */
function contentfreaks_generate_webp($metadata, $attachment_id) {
    // WebP拡張機能が利用可能かチェック
    if (!function_exists('imagewebp')) {
        return $metadata;
    }
    
    $file = get_attached_file($attachment_id);
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    // JPGとPNGのみ変換
    if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
        return $metadata;
    }
    
    $webp_file = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file);
    
    // すでにWebPファイルが存在する場合はスキップ
    if (file_exists($webp_file)) {
        return $metadata;
    }
    
    // 画像を読み込み
    $image = null;
    switch (strtolower($ext)) {
        case 'jpg':
        case 'jpeg':
            $image = @imagecreatefromjpeg($file);
            break;
        case 'png':
            $image = @imagecreatefrompng($file);
            // PNG透過対応
            imagealphablending($image, false);
            imagesavealpha($image, true);
            break;
    }
    
    if ($image) {
        // WebPに変換（品質90%）
        imagewebp($image, $webp_file, 90);
        imagedestroy($image);
        
        // サムネイルもWebP化
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            $upload_dir = wp_upload_dir();
            $base_dir = dirname($file);
            
            foreach ($metadata['sizes'] as $size => $size_data) {
                $size_file = $base_dir . '/' . $size_data['file'];
                $size_webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $size_file);
                
                if (file_exists($size_file) && !file_exists($size_webp)) {
                    $size_image = null;
                    switch (strtolower($ext)) {
                        case 'jpg':
                        case 'jpeg':
                            $size_image = @imagecreatefromjpeg($size_file);
                            break;
                        case 'png':
                            $size_image = @imagecreatefrompng($size_file);
                            imagealphablending($size_image, false);
                            imagesavealpha($size_image, true);
                            break;
                    }
                    
                    if ($size_image) {
                        imagewebp($size_image, $size_webp, 90);
                        imagedestroy($size_image);
                    }
                }
            }
        }
    }
    
    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'contentfreaks_generate_webp', 10, 2);

/**
 * 画像サイズの最適化: 最大幅を設定
 */
function contentfreaks_max_image_size($file) {
    if (!function_exists('wp_get_image_editor') || $file['error'] !== 0) {
        return $file;
    }
    
    $max_width = 1920;  // 最大幅
    $max_height = 1920; // 最大高さ
    
    // wp_handle_upload_prefilter は配列（name, type, tmp_name, error, size）を渡す
    $tmp_name = $file['tmp_name'];
    $editor = wp_get_image_editor($tmp_name);
    
    if (is_wp_error($editor)) {
        return $file;
    }
    
    $size = $editor->get_size();
    
    // 最大サイズを超えている場合のみリサイズ
    if ($size['width'] > $max_width || $size['height'] > $max_height) {
        $editor->resize($max_width, $max_height, false);
        $editor->save($tmp_name);
    }
    
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'contentfreaks_max_image_size');

/**
 * レスポンシブ画像のsrcset生成を強化
 */
function contentfreaks_responsive_image_sizes($sizes, $size) {
    // コンテンツ幅の画像に対してレスポンシブサイズを設定
    if ($size === 'large' || $size === 'full') {
        $sizes = '(max-width: 768px) 100vw, (max-width: 1200px) 80vw, 1200px';
    }
    
    return $sizes;
}
add_filter('wp_calculate_image_sizes', 'contentfreaks_responsive_image_sizes', 10, 2);

/**
 * 画像の圧縮品質を設定（デフォルト82% → 85%に）
 */
function contentfreaks_image_quality($quality, $mime_type) {
    // JPEG品質を85%に設定（バランス重視）
    if ($mime_type === 'image/jpeg') {
        return 85;
    }
    
    return $quality;
}
add_filter('wp_editor_set_quality', 'contentfreaks_image_quality', 10, 2);

/**
 * 不要な画像サイズの生成を停止
 */
function contentfreaks_disable_unused_image_sizes($sizes) {
    // 使用していないサイズを削除
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);
    
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'contentfreaks_disable_unused_image_sizes');

/**
 * アイキャッチ画像にdecoding="async"を追加
 */
function contentfreaks_post_thumbnail_html($html, $post_id, $post_thumbnail_id, $size, $attr) {
    // decoding="async"を追加
    if (strpos($html, 'decoding=') === false) {
        $html = str_replace('<img', '<img decoding="async"', $html);
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'contentfreaks_post_thumbnail_html', 10, 5);
