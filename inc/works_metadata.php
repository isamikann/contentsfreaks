<?php
/**
 * 作品メタデータ管理
 * エピソード投稿に作品情報を追加するためのカスタムフィールド
 */

// 直接このファイルにアクセスすることを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 投稿編集画面に作品メタボックスを追加
 */
function contentfreaks_add_works_meta_box() {
    add_meta_box(
        'contentfreaks_works_meta',
        '📚 紹介した作品',
        'contentfreaks_works_meta_box_callback',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'contentfreaks_add_works_meta_box');

/**
 * メタボックスのHTML出力
 */
function contentfreaks_works_meta_box_callback($post) {
    // nonceフィールドを追加
    wp_nonce_field('contentfreaks_save_works_meta', 'contentfreaks_works_meta_nonce');
    
    // 既存のデータを取得
    $mentioned_works = get_post_meta($post->ID, 'mentioned_works', true);
    if (!is_array($mentioned_works)) {
        $mentioned_works = array();
    }
    
    ?>
    <div class="contentfreaks-works-meta-container">
        <style>
            .contentfreaks-works-meta-container {
                padding: 15px 0;
            }
            .work-item {
                background: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 15px;
                position: relative;
            }
            .work-item-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 2px solid #ddd;
            }
            .work-item-title {
                font-size: 16px;
                font-weight: 600;
                color: #333;
            }
            .remove-work-btn {
                background: #dc3545;
                color: white;
                border: none;
                padding: 5px 15px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 12px;
            }
            .remove-work-btn:hover {
                background: #c82333;
            }
            .work-field-group {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
                margin-bottom: 10px;
            }
            .work-field-group.full-width {
                grid-template-columns: 1fr;
            }
            .work-field {
                display: flex;
                flex-direction: column;
            }
            .work-field label {
                font-weight: 600;
                margin-bottom: 5px;
                color: #555;
                font-size: 13px;
            }
            .work-field input,
            .work-field select {
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 14px;
            }
            .work-field input:focus,
            .work-field select:focus {
                border-color: #007cba;
                outline: none;
                box-shadow: 0 0 0 2px rgba(0, 124, 186, 0.1);
            }
            .add-work-btn {
                background: #007cba;
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-top: 10px;
            }
            .add-work-btn:hover {
                background: #005a87;
            }
            .rating-stars {
                display: flex;
                gap: 5px;
            }
            .rating-stars input[type="radio"] {
                display: none;
            }
            .rating-stars label {
                cursor: pointer;
                font-size: 24px;
                color: #ddd;
                transition: color 0.2s;
            }
            .rating-stars label:hover,
            .rating-stars label:hover ~ label,
            .rating-stars input[type="radio"]:checked ~ label {
                color: #ffc107;
            }
            .help-text {
                background: #e7f3ff;
                border-left: 4px solid #007cba;
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 4px;
                font-size: 13px;
                line-height: 1.6;
            }
            .help-text strong {
                display: block;
                margin-bottom: 8px;
                color: #007cba;
            }
        </style>
        
        <div class="help-text">
            <strong>💡 使い方</strong>
            このエピソードで紹介した映画、ドラマ、ゲーム、アニメなどの作品情報を追加できます。<br>
            追加した作品は「作品データベース」ページに自動的に表示されます。
        </div>
        
        <div id="works-list">
            <?php 
            if (!empty($mentioned_works)) {
                foreach ($mentioned_works as $index => $work) {
                    contentfreaks_render_work_item($index, $work);
                }
            }
            ?>
        </div>
        
        <button type="button" class="add-work-btn" onclick="addWorkItem()">
            <span>➕</span> 作品を追加
        </button>
    </div>
    
    <script>
    let workIndex = <?php echo count($mentioned_works); ?>;
    
    function addWorkItem() {
        const worksList = document.getElementById('works-list');
        const template = `
            <div class="work-item" data-index="${workIndex}">
                <div class="work-item-header">
                    <div class="work-item-title">作品 #${workIndex + 1}</div>
                    <button type="button" class="remove-work-btn" onclick="removeWorkItem(this)">削除</button>
                </div>
                
                <div class="work-field-group full-width">
                    <div class="work-field">
                        <label>作品名 <span style="color: red;">*</span></label>
                        <input type="text" name="mentioned_works[${workIndex}][title]" required>
                    </div>
                </div>
                
                <div class="work-field-group">
                    <div class="work-field">
                        <label>ジャンル</label>
                        <select name="mentioned_works[${workIndex}][genre]">
                            <option value="映画">🎬 映画</option>
                            <option value="ドラマ">📺 ドラマ</option>
                            <option value="アニメ">🎨 アニメ</option>
                            <option value="ゲーム">🎮 ゲーム</option>
                            <option value="書籍">📚 書籍</option>
                            <option value="漫画">📖 漫画</option>
                            <option value="その他">🎭 その他</option>
                        </select>
                    </div>
                    
                    <div class="work-field">
                        <label>リリース年</label>
                        <input type="text" name="mentioned_works[${workIndex}][year]" placeholder="例: 2024">
                    </div>
                </div>
                
                <div class="work-field-group">
                    <div class="work-field">
                        <label>評価（1-5）</label>
                        <select name="mentioned_works[${workIndex}][rating]">
                            <option value="0">未評価</option>
                            <option value="1">★ 1</option>
                            <option value="2">★★ 2</option>
                            <option value="3">★★★ 3</option>
                            <option value="4">★★★★ 4</option>
                            <option value="5">★★★★★ 5</option>
                        </select>
                    </div>
                    
                    <div class="work-field">
                        <label>画像URL（任意）</label>
                        <input type="url" name="mentioned_works[${workIndex}][image]" placeholder="https://...">
                    </div>
                </div>
                
                <div class="work-field-group full-width">
                    <div class="work-field">
                        <label>詳細リンク（任意）</label>
                        <input type="url" name="mentioned_works[${workIndex}][url]" placeholder="https://...">
                    </div>
                </div>
            </div>
        `;
        
        worksList.insertAdjacentHTML('beforeend', template);
        workIndex++;
    }
    
    function removeWorkItem(button) {
        if (confirm('この作品を削除してもよろしいですか？')) {
            button.closest('.work-item').remove();
        }
    }
    </script>
    <?php
}

/**
 * 作品アイテムのHTMLをレンダリング
 */
function contentfreaks_render_work_item($index, $work) {
    $title = isset($work['title']) ? esc_attr($work['title']) : '';
    $genre = isset($work['genre']) ? esc_attr($work['genre']) : '映画';
    $year = isset($work['year']) ? esc_attr($work['year']) : '';
    $rating = isset($work['rating']) ? esc_attr($work['rating']) : '0';
    $image = isset($work['image']) ? esc_attr($work['image']) : '';
    $url = isset($work['url']) ? esc_attr($work['url']) : '';
    
    ?>
    <div class="work-item" data-index="<?php echo $index; ?>">
        <div class="work-item-header">
            <div class="work-item-title">作品 #<?php echo $index + 1; ?>: <?php echo $title ? esc_html($title) : '未設定'; ?></div>
            <button type="button" class="remove-work-btn" onclick="removeWorkItem(this)">削除</button>
        </div>
        
        <div class="work-field-group full-width">
            <div class="work-field">
                <label>作品名 <span style="color: red;">*</span></label>
                <input type="text" name="mentioned_works[<?php echo $index; ?>][title]" value="<?php echo $title; ?>" required>
            </div>
        </div>
        
        <div class="work-field-group">
            <div class="work-field">
                <label>ジャンル</label>
                <select name="mentioned_works[<?php echo $index; ?>][genre]">
                    <option value="映画" <?php selected($genre, '映画'); ?>>🎬 映画</option>
                    <option value="ドラマ" <?php selected($genre, 'ドラマ'); ?>>📺 ドラマ</option>
                    <option value="アニメ" <?php selected($genre, 'アニメ'); ?>>🎨 アニメ</option>
                    <option value="ゲーム" <?php selected($genre, 'ゲーム'); ?>>🎮 ゲーム</option>
                    <option value="書籍" <?php selected($genre, '書籍'); ?>>📚 書籍</option>
                    <option value="漫画" <?php selected($genre, '漫画'); ?>>📖 漫画</option>
                    <option value="その他" <?php selected($genre, 'その他'); ?>>🎭 その他</option>
                </select>
            </div>
            
            <div class="work-field">
                <label>リリース年</label>
                <input type="text" name="mentioned_works[<?php echo $index; ?>][year]" value="<?php echo $year; ?>" placeholder="例: 2024">
            </div>
        </div>
        
        <div class="work-field-group">
            <div class="work-field">
                <label>評価（1-5）</label>
                <select name="mentioned_works[<?php echo $index; ?>][rating]">
                    <option value="0" <?php selected($rating, '0'); ?>>未評価</option>
                    <option value="1" <?php selected($rating, '1'); ?>>★ 1</option>
                    <option value="2" <?php selected($rating, '2'); ?>>★★ 2</option>
                    <option value="3" <?php selected($rating, '3'); ?>>★★★ 3</option>
                    <option value="4" <?php selected($rating, '4'); ?>>★★★★ 4</option>
                    <option value="5" <?php selected($rating, '5'); ?>>★★★★★ 5</option>
                </select>
            </div>
            
            <div class="work-field">
                <label>画像URL（任意）</label>
                <input type="url" name="mentioned_works[<?php echo $index; ?>][image]" value="<?php echo $image; ?>" placeholder="https://...">
            </div>
        </div>
        
        <div class="work-field-group full-width">
            <div class="work-field">
                <label>詳細リンク（任意）</label>
                <input type="url" name="mentioned_works[<?php echo $index; ?>][url]" value="<?php echo $url; ?>" placeholder="https://...">
            </div>
        </div>
    </div>
    <?php
}

/**
 * メタデータを保存
 */
function contentfreaks_save_works_meta($post_id) {
    // nonce検証
    if (!isset($_POST['contentfreaks_works_meta_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['contentfreaks_works_meta_nonce'], 'contentfreaks_save_works_meta')) {
        return;
    }
    
    // 自動保存時はスキップ
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // データを取得・検証
    $mentioned_works = array();
    
    if (isset($_POST['mentioned_works']) && is_array($_POST['mentioned_works'])) {
        foreach ($_POST['mentioned_works'] as $work) {
            // 作品名が必須
            if (empty($work['title'])) {
                continue;
            }
            
            $mentioned_works[] = array(
                'title' => sanitize_text_field($work['title']),
                'genre' => isset($work['genre']) ? sanitize_text_field($work['genre']) : '映画',
                'year' => isset($work['year']) ? sanitize_text_field($work['year']) : '',
                'rating' => isset($work['rating']) ? intval($work['rating']) : 0,
                'image' => isset($work['image']) ? esc_url_raw($work['image']) : '',
                'url' => isset($work['url']) ? esc_url_raw($work['url']) : ''
            );
        }
    }
    
    // メタデータを更新
    update_post_meta($post_id, 'mentioned_works', $mentioned_works);
}
add_action('save_post', 'contentfreaks_save_works_meta');
