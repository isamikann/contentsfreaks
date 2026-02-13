<?php
/**
 * Template Name: お問い合わせ
 * Description: ContentFreaks カスタムお問い合わせページ
 */

get_header();

$podcast_name = get_theme_mod('podcast_name', 'コンテンツフリークス');
$artwork_url = get_theme_mod('podcast_artwork', '');
?>

<main class="contact-page">

    <!-- ヒーロー -->
    <section class="contact-hero">
        <div class="contact-hero-inner">
            <?php if ($artwork_url) : ?>
            <div class="contact-hero-artwork">
                <img src="<?php echo esc_url($artwork_url); ?>" alt="<?php echo esc_attr($podcast_name); ?>" width="80" height="80" loading="eager">
            </div>
            <?php endif; ?>
            <h1 class="contact-hero-title">Contact</h1>
            <p class="contact-hero-subtitle">お気軽にご連絡ください</p>
        </div>
    </section>

    <!-- タイプ選択 -->
    <section class="contact-type-section">
        <div class="contact-type-inner">
            <div class="contact-type-tabs" role="tablist" aria-label="お問い合わせ種別">
                <button class="contact-type-tab active" role="tab" aria-selected="true" aria-controls="panel-listener" id="tab-listener" data-type="listener">
                    <span class="tab-icon">🎧</span>
                    <span class="tab-label">リスナーの方</span>
                    <span class="tab-desc">感想・リクエスト・ご質問</span>
                </button>
                <button class="contact-type-tab" role="tab" aria-selected="false" aria-controls="panel-business" id="tab-business" data-type="business">
                    <span class="tab-icon">💼</span>
                    <span class="tab-label">お仕事のご依頼</span>
                    <span class="tab-desc">コラボ・タイアップ・出演</span>
                </button>
            </div>
        </div>
    </section>

    <!-- フォームセクション -->
    <section class="contact-form-section">
        <div class="contact-form-inner">

            <!-- リスナー向けパネル -->
            <div class="contact-panel active" id="panel-listener" role="tabpanel" aria-labelledby="tab-listener">
                <div class="panel-header">
                    <h2 class="panel-title">番組への感想・リクエスト</h2>
                    <p class="panel-desc">取り上げてほしい作品、番組への感想、ご質問など、何でもお送りください。<br>いただいたメッセージは番組内で紹介させていただくことがあります。</p>
                </div>

                <form id="contact-form-listener" class="contact-form" data-type="listener">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="listener-name" class="form-label">お名前 <span class="required">*</span></label>
                            <input type="text" id="listener-name" name="name" class="form-input" placeholder="ニックネームでもOK" required maxlength="50" autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label for="listener-email" class="form-label">メールアドレス</label>
                            <input type="email" id="listener-email" name="email" class="form-input" placeholder="返信が必要な場合のみ" maxlength="100" autocomplete="email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="listener-category" class="form-label">カテゴリ</label>
                        <select id="listener-category" name="category" class="form-select">
                            <option value="感想">📝 番組への感想</option>
                            <option value="リクエスト">🎬 作品リクエスト</option>
                            <option value="質問">❓ 質問</option>
                            <option value="その他">💬 その他</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="listener-message" class="form-label">メッセージ <span class="required">*</span></label>
                        <textarea id="listener-message" name="message" class="form-textarea" placeholder="番組への感想や取り上げてほしい作品など…" required maxlength="2000" rows="6"></textarea>
                        <div class="char-count"><span class="char-current">0</span> / 2000</div>
                    </div>

                    <!-- ハニーポット -->
                    <div class="form-group" style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;" aria-hidden="true">
                        <label for="listener-website">ウェブサイト</label>
                        <input type="text" id="listener-website" name="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="form-submit">
                            <span class="submit-text">送信する</span>
                            <span class="submit-loading" style="display:none;">送信中...</span>
                        </button>
                    </div>
                    <div class="form-message" style="display:none;" role="alert"></div>
                </form>
            </div>

            <!-- ビジネス向けパネル -->
            <div class="contact-panel" id="panel-business" role="tabpanel" aria-labelledby="tab-business">
                <div class="panel-header">
                    <h2 class="panel-title">お仕事のご依頼・ご相談</h2>
                    <p class="panel-desc">コラボ出演・タイアップ・イベント出演など、お気軽にご相談ください。<br>通常3営業日以内にご返信いたします。</p>
                    <a href="<?php echo esc_url(contentfreaks_get_page_url('media-kit')); ?>" class="panel-mediakit-link">📊 Media Kit を見る →</a>
                </div>

                <form id="contact-form-business" class="contact-form" data-type="business">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="biz-name" class="form-label">お名前 / ご担当者名 <span class="required">*</span></label>
                            <input type="text" id="biz-name" name="name" class="form-input" placeholder="山田 太郎" required maxlength="50" autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label for="biz-email" class="form-label">メールアドレス <span class="required">*</span></label>
                            <input type="email" id="biz-email" name="email" class="form-input" placeholder="example@company.com" required maxlength="100" autocomplete="email">
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="biz-company" class="form-label">会社名 / 番組名</label>
                            <input type="text" id="biz-company" name="company" class="form-input" placeholder="株式会社○○ / ○○ポッドキャスト" maxlength="100" autocomplete="organization">
                        </div>
                        <div class="form-group">
                            <label for="biz-category" class="form-label">ご依頼内容 <span class="required">*</span></label>
                            <select id="biz-category" name="category" class="form-select" required>
                                <option value="">選択してください</option>
                                <option value="ゲスト出演">🎤 ゲスト出演 / コラボ配信</option>
                                <option value="タイアップ">📺 作品紹介タイアップ</option>
                                <option value="イベント">🎪 イベント出演</option>
                                <option value="スポンサー">📢 スポンサー / 広告</option>
                                <option value="その他">💬 その他</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="biz-message" class="form-label">ご依頼内容の詳細 <span class="required">*</span></label>
                        <textarea id="biz-message" name="message" class="form-textarea" placeholder="ご依頼の背景、希望する内容、スケジュール感などをお聞かせください" required maxlength="5000" rows="8"></textarea>
                        <div class="char-count"><span class="char-current">0</span> / 5000</div>
                    </div>

                    <!-- ハニーポット -->
                    <div class="form-group" style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;" aria-hidden="true">
                        <label for="biz-website">ウェブサイト</label>
                        <input type="text" id="biz-website" name="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="form-submit form-submit-business">
                            <span class="submit-text">送信する</span>
                            <span class="submit-loading" style="display:none;">送信中...</span>
                        </button>
                    </div>
                    <div class="form-message" style="display:none;" role="alert"></div>
                </form>
            </div>

        </div>
    </section>

    <!-- 補足情報 -->
    <section class="contact-info-section">
        <div class="contact-info-inner">
            <div class="contact-info-grid">
                <div class="contact-info-card">
                    <div class="info-icon">⏱️</div>
                    <h3>返信について</h3>
                    <p>リスナーの方からのメッセージは基本的に番組内でご紹介します。個別の返信が必要な場合はメールアドレスをご記入ください。</p>
                </div>
                <div class="contact-info-card">
                    <div class="info-icon">🔒</div>
                    <h3>プライバシー</h3>
                    <p>いただいた個人情報はお問い合わせへの対応のみに使用し、第三者に提供することはありません。</p>
                </div>
                <div class="contact-info-card">
                    <div class="info-icon">📱</div>
                    <h3>SNSでも</h3>
                    <p>各プラットフォームのDMやコメントでもお気軽にお声がけください。</p>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
