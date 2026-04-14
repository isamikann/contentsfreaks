<?php
/**
 * 固有名詞正規化モジュール
 *
 * ポッドキャスト記事自動生成ツール（Gemini API使用）において、
 * 作品名・俳優名・キャラクター名などの固有名詞を正規化するモジュールです。
 * RSSで取得したエピソードタイトルから作品名を抽出し、
 * Wikipedia/Wikidata APIで正式名・出演者・キャラ名を取得して保存します。
 *
 * @package ContentFreaks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ============================================================
// 定数
// ============================================================

define( 'CF_WORK_DICT_OPTION', 'contentfreaks_work_dict' );

// ============================================================
// 1. 文字列正規化
// ============================================================

/**
 * 文字列を正規化する
 *
 * - 全角英数字・記号 → 半角
 * - 半角カタカナ → 全角カタカナ
 * - 小文字統一
 * - 特定記号の統一
 * - 連続空白を1つに、trim
 *
 * @param string $str 正規化する文字列
 * @return string 正規化済み文字列
 */
function contentfreaks_normalize_string( $str ) {
    if ( ! is_string( $str ) || $str === '' ) {
        return '';
    }

    // 全角英数字・記号 → 半角、半角カタカナ → 全角カタカナ
    $str = mb_convert_kana( $str, 'rnsa', 'UTF-8' );
    // 半角カタカナ → 全角カタカナ（KV: カタカナ変換 + 濁点結合）
    $str = mb_convert_kana( $str, 'KV', 'UTF-8' );

    // 小文字統一
    $str = mb_strtolower( $str, 'UTF-8' );

    // 特定記号の統一
    $str = str_replace(
        [ '：', '　', '〜' ],
        [ ':',  ' ',  '~'  ],
        $str
    );

    // 連続空白を1つに
    $str = preg_replace( '/\s+/u', ' ', $str );

    // trim
    $str = trim( $str );

    return $str;
}

// ============================================================
// 2. エピソードタイトルから作品名抽出
// ============================================================

/**
 * エピソードタイトルから作品名を抽出する
 *
 * 優先順位:
 * 1. 『』で囲まれたテキスト
 * 2. 「」で囲まれたテキスト
 * 3. 【○○】の後に続くパターン（感想/考察/レビューの前まで）
 *
 * @param string $episode_title エピソードタイトル
 * @return string[] 重複排除済みの作品名配列
 */
function contentfreaks_extract_work_titles_from_episode( $episode_title ) {
    $titles = [];

    // 1. 『』で囲まれたテキスト（最優先）
    if ( preg_match_all( '/『([^』]+)』/u', $episode_title, $matches ) ) {
        foreach ( $matches[1] as $m ) {
            $m = trim( $m );
            if ( $m !== '' ) {
                $titles[] = $m;
            }
        }
    }

    // 2. 「」で囲まれたテキスト
    if ( preg_match_all( '/「([^」]+)」/u', $episode_title, $matches ) ) {
        foreach ( $matches[1] as $m ) {
            $m = trim( $m );
            if ( $m !== '' ) {
                $titles[] = $m;
            }
        }
    }

    // 3. 【○○】の後に続くパターン（感想/考察/レビューの前まで）
    if ( preg_match_all( '/【[^】]*】(.+?)(?:感想|考察|レビュー|$)/u', $episode_title, $matches ) ) {
        foreach ( $matches[1] as $m ) {
            $m = trim( $m );
            if ( $m !== '' ) {
                $titles[] = $m;
            }
        }
    }

    // 重複排除（順序維持）
    $seen   = [];
    $unique = [];
    foreach ( $titles as $title ) {
        $key = contentfreaks_normalize_string( $title );
        if ( ! isset( $seen[ $key ] ) ) {
            $seen[ $key ] = true;
            $unique[]     = $title;
        }
    }

    return $unique;
}

// ============================================================
// 3. Wikipedia検索
// ============================================================

/**
 * Wikipedia OpenSearch APIで作品を検索する
 *
 * @param string $query  検索クエリ
 * @param int    $limit  最大件数（デフォルト5）
 * @return array{title: string, description: string, url: string}[] 検索結果配列
 */
function contentfreaks_wikipedia_search( $query, $limit = 5 ) {
    $endpoint = 'https://ja.wikipedia.org/w/api.php';
    $params   = [
        'action'    => 'opensearch',
        'search'    => $query,
        'namespace' => '0',
        'limit'     => (int) $limit,
        'format'    => 'json',
    ];

    $url      = $endpoint . '?' . http_build_query( $params );
    $response = wp_remote_get( $url, [
        'timeout'    => 10,
        'user-agent' => 'ContentFreaks/1.0 (https://contentsfreaks.com)',
    ] );

    if ( is_wp_error( $response ) ) {
        error_log( '[CF ProperNoun] Wikipedia search error: ' . $response->get_error_message() );
        return [];
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( ! is_array( $data ) || count( $data ) < 4 ) {
        return [];
    }

    // OpenSearch レスポンス形式: [query, titles[], descriptions[], urls[]]
    $titles       = isset( $data[1] ) ? (array) $data[1] : [];
    $descriptions = isset( $data[2] ) ? (array) $data[2] : [];
    $urls         = isset( $data[3] ) ? (array) $data[3] : [];

    $results = [];
    foreach ( $titles as $i => $title ) {
        $results[] = [
            'title'       => (string) $title,
            'description' => isset( $descriptions[ $i ] ) ? (string) $descriptions[ $i ] : '',
            'url'         => isset( $urls[ $i ] )         ? (string) $urls[ $i ]         : '',
        ];
    }

    return $results;
}

// ============================================================
// 4. Wikipediaページ詳細取得
// ============================================================

/**
 * WikipediaページのAPIから詳細情報を取得する
 *
 * @param string $title Wikipediaページタイトル
 * @return array{canonical_title: string, wikidata_id: string|null, wikipedia_url: string, extract: string, page_id: int}|null
 */
function contentfreaks_wikipedia_get_page( $title ) {
    $endpoint = 'https://ja.wikipedia.org/w/api.php';
    $params   = [
        'action'      => 'query',
        'titles'      => $title,
        'prop'        => 'extracts|pageprops|info',
        'exintro'     => '1',
        'explaintext' => '1',
        'inprop'      => 'url',
        'redirects'   => '1',
        'format'      => 'json',
    ];

    $url      = $endpoint . '?' . http_build_query( $params );
    $response = wp_remote_get( $url, [
        'timeout'    => 10,
        'user-agent' => 'ContentFreaks/1.0 (https://contentsfreaks.com)',
    ] );

    if ( is_wp_error( $response ) ) {
        error_log( '[CF ProperNoun] Wikipedia get_page error: ' . $response->get_error_message() );
        return null;
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( ! isset( $data['query']['pages'] ) ) {
        return null;
    }

    $pages = $data['query']['pages'];
    $page  = reset( $pages );

    if ( ! $page || isset( $page['missing'] ) ) {
        return null;
    }

    $page_id = (int) $page['pageid'];

    // リダイレクト先タイトルを追跡
    $canonical_title = $page['title'] ?? $title;
    if ( isset( $data['query']['redirects'] ) ) {
        foreach ( $data['query']['redirects'] as $redirect ) {
            if ( $redirect['from'] === $title ) {
                $canonical_title = $redirect['to'];
                break;
            }
        }
        // リダイレクト先と実際のページタイトルを一致させる
        $canonical_title = $page['title'] ?? $canonical_title;
    }

    // Wikidata ID
    $wikidata_id = null;
    if ( isset( $page['pageprops']['wikibase_item'] ) ) {
        $wikidata_id = $page['pageprops']['wikibase_item'];
    }

    // Wikipedia URL
    $wikipedia_url = '';
    if ( isset( $page['fullurl'] ) ) {
        $wikipedia_url = $page['fullurl'];
    } else {
        $wikipedia_url = 'https://ja.wikipedia.org/wiki/' . rawurlencode( $canonical_title );
    }

    // 導入部テキスト
    $extract = isset( $page['extract'] ) ? (string) $page['extract'] : '';

    return [
        'canonical_title' => $canonical_title,
        'wikidata_id'     => $wikidata_id,
        'wikipedia_url'   => $wikipedia_url,
        'extract'         => $extract,
        'page_id'         => $page_id,
    ];
}

// ============================================================
// 5. Wikidata検索
// ============================================================

/**
 * Wikidata エンティティを検索する
 *
 * @param string $query  検索クエリ
 * @param int    $limit  最大件数（デフォルト5）
 * @return array Wikidata APIのsearch配列
 */
function contentfreaks_wikidata_search( $query, $limit = 5 ) {
    $endpoint = 'https://www.wikidata.org/w/api.php';
    $params   = [
        'action'   => 'wbsearchentities',
        'search'   => $query,
        'language' => 'ja',
        'limit'    => (int) $limit,
        'format'   => 'json',
        'uselang'  => 'ja',
    ];

    $url      = $endpoint . '?' . http_build_query( $params );
    $response = wp_remote_get( $url, [
        'timeout'    => 10,
        'user-agent' => 'ContentFreaks/1.0 (https://contentsfreaks.com)',
    ] );

    if ( is_wp_error( $response ) ) {
        error_log( '[CF ProperNoun] Wikidata search error: ' . $response->get_error_message() );
        return [];
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    return isset( $data['search'] ) ? (array) $data['search'] : [];
}

// ============================================================
// 6. WikidataエンティティからキャストID抽出
// ============================================================

/**
 * WikidataエンティティIDから詳細情報を取得する
 *
 * @param string $wikidata_id WikidataエンティティID（例: Q12345）
 * @return array{label: string|null, aliases: string[], official_url: string|null, cast_names: string[], character_names: string[]}|null
 */
function contentfreaks_wikidata_get_entity_details( $wikidata_id ) {
    if ( empty( $wikidata_id ) ) {
        return null;
    }

    $endpoint = 'https://www.wikidata.org/w/api.php';
    $params   = [
        'action'    => 'wbgetentities',
        'ids'       => $wikidata_id,
        'languages' => 'ja|en',
        'props'     => 'labels|aliases|claims|descriptions',
        'format'    => 'json',
    ];

    $url      = $endpoint . '?' . http_build_query( $params );
    $response = wp_remote_get( $url, [
        'timeout'    => 15,
        'user-agent' => 'ContentFreaks/1.0 (https://contentsfreaks.com)',
    ] );

    if ( is_wp_error( $response ) ) {
        error_log( '[CF ProperNoun] Wikidata get_entity error: ' . $response->get_error_message() );
        return null;
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( ! isset( $data['entities'][ $wikidata_id ] ) ) {
        return null;
    }

    $entity = $data['entities'][ $wikidata_id ];

    // ラベル取得（ja優先→en）
    $label = null;
    if ( isset( $entity['labels']['ja']['value'] ) ) {
        $label = $entity['labels']['ja']['value'];
    } elseif ( isset( $entity['labels']['en']['value'] ) ) {
        $label = $entity['labels']['en']['value'];
    }

    // エイリアス取得（ja + en）
    $aliases = [];
    foreach ( [ 'ja', 'en' ] as $lang ) {
        if ( isset( $entity['aliases'][ $lang ] ) ) {
            foreach ( $entity['aliases'][ $lang ] as $alias ) {
                if ( isset( $alias['value'] ) && $alias['value'] !== '' ) {
                    $aliases[] = $alias['value'];
                }
            }
        }
    }
    $aliases = array_unique( $aliases );
    $aliases = array_values( $aliases );

    $claims = isset( $entity['claims'] ) ? $entity['claims'] : [];

    // P161: cast member（出演者）
    $cast_qids = [];
    if ( isset( $claims['P161'] ) ) {
        foreach ( array_slice( $claims['P161'], 0, 5 ) as $claim ) {
            $qid = _cf_extract_qid_from_claim( $claim );
            if ( $qid ) {
                $cast_qids[] = $qid;
            }
        }
    }

    // P674: characters（キャラクター）
    $character_qids = [];
    if ( isset( $claims['P674'] ) ) {
        foreach ( array_slice( $claims['P674'], 0, 5 ) as $claim ) {
            $qid = _cf_extract_qid_from_claim( $claim );
            if ( $qid ) {
                $character_qids[] = $qid;
            }
        }
    }

    // P856: official website（公式URL）
    $official_url = null;
    if ( isset( $claims['P856'][0]['mainsnak']['datavalue']['value'] ) ) {
        $official_url = $claims['P856'][0]['mainsnak']['datavalue']['value'];
    }

    // QID配列を名前解決（上限5件に絞りAPIレスポンス時間を短縮）
    $cast_names      = ! empty( $cast_qids )      ? contentfreaks_wikidata_resolve_labels( $cast_qids,      5 ) : [];
    $character_names = ! empty( $character_qids ) ? contentfreaks_wikidata_resolve_labels( $character_qids, 5 ) : [];

    return [
        'label'           => $label,
        'aliases'         => $aliases,
        'official_url'    => $official_url,
        'cast_names'      => $cast_names,
        'character_names' => $character_names,
    ];
}

/**
 * Wikidataクレームからエンティティ QID を取り出すヘルパー（内部用）
 *
 * @param array $claim Wikidataクレーム
 * @return string|null QID または null
 */
function _cf_extract_qid_from_claim( $claim ) {
    if (
        isset( $claim['mainsnak']['snaktype'] )
        && $claim['mainsnak']['snaktype'] === 'value'
        && isset( $claim['mainsnak']['datavalue']['value']['id'] )
    ) {
        return $claim['mainsnak']['datavalue']['value']['id'];
    }
    return null;
}

// ============================================================
// 7. WikidataラベルバッチID解決
// ============================================================

/**
 * WikidataエンティティQID配列を名前の配列に解決する
 *
 * @param string[] $qids QID配列
 * @param int      $max  最大件数（デフォルト10）
 * @return string[] 名前の配列
 */
function contentfreaks_wikidata_resolve_labels( $qids, $max = 10 ) {
    if ( empty( $qids ) ) {
        return [];
    }

    // 最大件数に切り詰め
    $qids = array_slice( array_values( $qids ), 0, (int) $max );

    $endpoint = 'https://www.wikidata.org/w/api.php';
    $params   = [
        'action'    => 'wbgetentities',
        'ids'       => implode( '|', $qids ),
        'languages' => 'ja|en',
        'props'     => 'labels',
        'format'    => 'json',
    ];

    $url      = $endpoint . '?' . http_build_query( $params );
    $response = wp_remote_get( $url, [
        'timeout'    => 15,
        'user-agent' => 'ContentFreaks/1.0 (https://contentsfreaks.com)',
    ] );

    if ( is_wp_error( $response ) ) {
        error_log( '[CF ProperNoun] Wikidata resolve_labels error: ' . $response->get_error_message() );
        return [];
    }

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( ! isset( $data['entities'] ) ) {
        return [];
    }

    $names = [];
    // 入力順を維持
    foreach ( $qids as $qid ) {
        if ( ! isset( $data['entities'][ $qid ] ) ) {
            continue;
        }
        $entity = $data['entities'][ $qid ];

        if ( isset( $entity['labels']['ja']['value'] ) ) {
            $names[] = $entity['labels']['ja']['value'];
        } elseif ( isset( $entity['labels']['en']['value'] ) ) {
            $names[] = $entity['labels']['en']['value'];
        }
        // ラベルなしの場合はスキップ
    }

    return $names;
}

// ============================================================
// 8. ファジースコア計算
// ============================================================

/**
 * 2文字列間のファジースコアを計算する（0.0〜1.0）
 *
 * @param string $a 比較文字列A
 * @param string $b 比較文字列B
 * @return float スコア（0.0〜1.0、小数3桁）
 */
function contentfreaks_fuzzy_score( $a, $b ) {
    $a = contentfreaks_normalize_string( $a );
    $b = contentfreaks_normalize_string( $b );

    if ( $a === '' || $b === '' ) {
        return 0.0;
    }

    if ( $a === $b ) {
        return 1.0;
    }

    // similar_text でパーセント取得
    similar_text( $a, $b, $percent );
    $score = $percent / 100.0;

    // 部分一致チェック（一方が他方に含まれる）
    if ( mb_strpos( $a, $b, 0, 'UTF-8' ) !== false || mb_strpos( $b, $a, 0, 'UTF-8' ) !== false ) {
        if ( $score < 0.85 ) {
            $score = 0.85;
        }
    }

    // 長さ比率ペナルティ
    $len_a   = mb_strlen( $a, 'UTF-8' );
    $len_b   = mb_strlen( $b, 'UTF-8' );
    $min_len = min( $len_a, $len_b );
    $max_len = max( $len_a, $len_b );

    if ( $max_len > 0 ) {
        $score = $score * ( 0.3 + 0.7 * ( $min_len / $max_len ) );
    }

    return round( min( 1.0, max( 0.0, $score ) ), 3 );
}

// ============================================================
// 9. 最良マッチ探索
// ============================================================

/**
 * クエリに対して最もスコアの高い候補を返す
 *
 * @param string       $query      検索クエリ
 * @param array        $candidates ['title'=>string, ...] の配列 または string[]
 * @param float        $threshold  最小スコア閾値（デフォルト0.7）
 * @return array|null  _scoreキーを追加した候補要素、またはnull
 */
function contentfreaks_find_best_match( $query, $candidates, $threshold = 0.7 ) {
    if ( empty( $candidates ) ) {
        return null;
    }

    $norm_query     = contentfreaks_normalize_string( $query );
    $best_score     = -1.0;
    $best_candidate = null;

    foreach ( $candidates as $candidate ) {
        // string配列の場合は連想配列に変換
        if ( is_string( $candidate ) ) {
            $candidate = [ 'title' => $candidate ];
        }

        $title = isset( $candidate['title'] ) ? (string) $candidate['title'] : '';
        if ( $title === '' ) {
            continue;
        }

        $score      = contentfreaks_fuzzy_score( $query, $title );
        $norm_title = contentfreaks_normalize_string( $title );

        // 包含チェック: クエリが候補タイトルに含まれる、または候補タイトルがクエリに含まれる場合は
        // 通常スコアをそのまま使う。どちらにも含まれない場合（例: 「リブート」vs「再起動」）は
        // 高閾値 0.85 を要求してカタカナ→漢字語の誤マッチを防ぐ。
        $has_containment = (
            mb_strpos( $norm_title, $norm_query, 0, 'UTF-8' ) !== false ||
            mb_strpos( $norm_query, $norm_title, 0, 'UTF-8' ) !== false
        );

        if ( ! $has_containment && $score < 0.85 ) {
            error_log( '[CF ProperNoun] find_best_match: 包含なし・スコア不足でスキップ query=' . $query . ' title=' . $title . ' score=' . $score );
            continue;
        }

        if ( $score > $best_score ) {
            $best_score     = $score;
            $best_candidate = $candidate;
        }
    }

    if ( $best_candidate === null || $best_score < $threshold ) {
        return null;
    }

    $best_candidate['_score'] = $best_score;
    return $best_candidate;
}

// ============================================================
// 10. キャッシュ管理（WordPress options）
// ============================================================

/**
 * 作品辞書をWordPress optionsから取得する
 *
 * @return array 作品辞書配列
 */
function contentfreaks_get_work_dict() {
    $dict = get_option( CF_WORK_DICT_OPTION, [] );
    return is_array( $dict ) ? $dict : [];
}

/**
 * 作品辞書をWordPress optionsに保存する
 *
 * @param array $dict 保存する作品辞書
 * @return bool 保存成功かどうか
 */
function contentfreaks_save_work_dict( $dict ) {
    return update_option( CF_WORK_DICT_OPTION, $dict, false );
}

/**
 * キャッシュから作品データを検索する
 *
 * canonical_title、aliases[]、source_titles[] のいずれかと正規化後に一致したら返す
 *
 * @param string $raw_title 検索する作品名（生）
 * @return array|null 見つかった作品データ、またはnull
 */
function contentfreaks_find_work_in_cache( $raw_title ) {
    $normalized = contentfreaks_normalize_string( $raw_title );
    if ( $normalized === '' ) {
        return null;
    }

    $dict = contentfreaks_get_work_dict();

    foreach ( $dict as $entry ) {
        // canonical_title との照合
        if ( isset( $entry['canonical_title'] ) ) {
            if ( contentfreaks_normalize_string( $entry['canonical_title'] ) === $normalized ) {
                return $entry;
            }
        }

        // aliases との照合
        if ( isset( $entry['aliases'] ) && is_array( $entry['aliases'] ) ) {
            foreach ( $entry['aliases'] as $alias ) {
                if ( contentfreaks_normalize_string( $alias ) === $normalized ) {
                    return $entry;
                }
            }
        }

        // source_titles との照合
        if ( isset( $entry['source_titles'] ) && is_array( $entry['source_titles'] ) ) {
            foreach ( $entry['source_titles'] as $src ) {
                if ( contentfreaks_normalize_string( $src ) === $normalized ) {
                    return $entry;
                }
            }
        }
    }

    return null;
}

/**
 * 作品データをキャッシュに追加または更新する
 *
 * wikidata_idまたはcanonical_title(normalized)で既存エントリを検索し、
 * あれば array_merge で更新、なければ追加する。
 * 200件を超えたら古いエントリを削除する。
 *
 * @param array $work_data 保存する作品データ
 * @return array 保存された作品データ
 */
function contentfreaks_upsert_work_to_cache( $work_data ) {
    $dict             = contentfreaks_get_work_dict();
    $found_index      = null;
    $normalized_title = contentfreaks_normalize_string( $work_data['canonical_title'] ?? '' );

    foreach ( $dict as $i => $entry ) {
        // wikidata_idで検索
        if (
            ! empty( $work_data['wikidata_id'] )
            && isset( $entry['wikidata_id'] )
            && $entry['wikidata_id'] === $work_data['wikidata_id']
        ) {
            $found_index = $i;
            break;
        }

        // canonical_title（normalized）で検索
        if (
            $normalized_title !== ''
            && isset( $entry['canonical_title'] )
            && contentfreaks_normalize_string( $entry['canonical_title'] ) === $normalized_title
        ) {
            $found_index = $i;
            break;
        }
    }

    if ( $found_index !== null ) {
        // 既存エントリを更新（source_titlesはマージ）
        $existing = $dict[ $found_index ];
        $merged   = array_merge( $existing, $work_data );

        // source_titles はユニークにマージ
        $existing_sources      = isset( $existing['source_titles'] ) && is_array( $existing['source_titles'] ) ? $existing['source_titles'] : [];
        $new_sources           = isset( $work_data['source_titles'] ) && is_array( $work_data['source_titles'] ) ? $work_data['source_titles'] : [];
        $merged['source_titles'] = array_values( array_unique( array_merge( $existing_sources, $new_sources ) ) );

        $dict[ $found_index ] = $merged;
        $work_data            = $merged;
    } else {
        // 新規追加
        $dict[] = $work_data;
    }

    // 200件を超えたら updated_at 昇順で古いものを削除
    if ( count( $dict ) > 200 ) {
        usort( $dict, function ( $a, $b ) {
            $ta = isset( $a['updated_at'] ) ? $a['updated_at'] : '';
            $tb = isset( $b['updated_at'] ) ? $b['updated_at'] : '';
            return strcmp( $ta, $tb );
        } );
        $dict = array_slice( $dict, count( $dict ) - 200 );
    }

    contentfreaks_save_work_dict( $dict );

    return $work_data;
}

// ============================================================
// 11. 作品解決メイン関数
// ============================================================

/**
 * 作品名からメタデータを解決する
 *
 * キャッシュ検索→Wikipedia検索→Wikidata取得の順で処理し、
 * 結果をキャッシュに保存して返す。
 *
 * @param string $raw_title 解決する作品名（生）
 * @return array 作品データ
 */
function contentfreaks_resolve_work_meta( $raw_title ) {
    error_log( '[CF ProperNoun] resolve_work_meta 開始: ' . $raw_title );

    // デフォルト work_data
    $work_data = [
        'source_titles'   => [ $raw_title ],
        'canonical_title' => $raw_title,
        'aliases'         => [],
        'wikidata_id'     => null,
        'wikipedia_url'   => null,
        'official_url'    => null,
        'cast_names'      => [],
        'character_names' => [],
        'confidence'      => 0.0,
        'updated_at'      => current_time( 'mysql' ),
    ];

    // 1. キャッシュから検索
    $cached = contentfreaks_find_work_in_cache( $raw_title );
    if ( $cached !== null ) {
        error_log( '[CF ProperNoun] キャッシュヒット: ' . $cached['canonical_title'] );
        return $cached;
    }

    // 2. Wikipedia検索
    error_log( '[CF ProperNoun] Wikipedia検索: ' . $raw_title );
    $search_results = contentfreaks_wikipedia_search( $raw_title, 5 );

    // 3. 最良候補選択（threshold=0.6）
    $best_match = contentfreaks_find_best_match( $raw_title, $search_results, 0.6 );

    if ( $best_match !== null ) {
        $confidence = $best_match['_score'];
        error_log( '[CF ProperNoun] Wikipedia最良マッチ: ' . $best_match['title'] . ' (score=' . $confidence . ')' );

        // 4. Wikipediaページ詳細取得
        $page_detail = contentfreaks_wikipedia_get_page( $best_match['title'] );

        if ( $page_detail !== null ) {
            error_log( '[CF ProperNoun] Wikipediaページ取得成功: ' . $page_detail['canonical_title'] );

            $work_data['canonical_title'] = $page_detail['canonical_title'];
            $work_data['wikipedia_url']   = $page_detail['wikipedia_url'];
            $work_data['wikidata_id']     = $page_detail['wikidata_id'];
            $work_data['confidence']      = $confidence;

            // 5. Wikidata IDがあればエンティティ詳細取得
            if ( ! empty( $page_detail['wikidata_id'] ) ) {
                error_log( '[CF ProperNoun] Wikidata取得: ' . $page_detail['wikidata_id'] );
                $entity_details = contentfreaks_wikidata_get_entity_details( $page_detail['wikidata_id'] );

                if ( $entity_details !== null ) {
                    // 6. work_data構築（Wikidataラベル優先→Wikipediaタイトル→$raw_title）
                    if ( ! empty( $entity_details['label'] ) ) {
                        $work_data['canonical_title'] = $entity_details['label'];
                    }
                    $work_data['aliases']         = $entity_details['aliases'];
                    $work_data['official_url']    = $entity_details['official_url'];
                    $work_data['cast_names']      = $entity_details['cast_names'];
                    $work_data['character_names'] = $entity_details['character_names'];

                    error_log( '[CF ProperNoun] キャスト数: ' . count( $entity_details['cast_names'] ) . ', キャラ数: ' . count( $entity_details['character_names'] ) );
                }
            }
        }
    } else {
        error_log( '[CF ProperNoun] Wikipedia候補なし (threshold=0.6未満): ' . $raw_title );
    }

    // 7. キャッシュ保存
    $work_data = contentfreaks_upsert_work_to_cache( $work_data );

    error_log( '[CF ProperNoun] resolve_work_meta 完了: ' . $work_data['canonical_title'] . ' (confidence=' . $work_data['confidence'] . ')' );

    return $work_data;
}

// ============================================================
// 12. Post Meta 保存・取得
// ============================================================

/**
 * 作品データをポストメタに保存する
 *
 * @param int   $post_id   投稿ID
 * @param array $work_data 作品データ
 */
function contentfreaks_save_work_meta_to_post( $post_id, $work_data ) {
    update_post_meta( $post_id, 'cf_work_canonical_title', $work_data['canonical_title'] ?? '' );
    update_post_meta( $post_id, 'cf_work_wikidata_id',     $work_data['wikidata_id']     ?? '' );
    update_post_meta( $post_id, 'cf_work_wikipedia_url',   $work_data['wikipedia_url']   ?? '' );
    update_post_meta( $post_id, 'cf_work_official_url',    $work_data['official_url']    ?? '' );
    update_post_meta( $post_id, 'cf_work_confidence',      $work_data['confidence']      ?? 0.0 );
    update_post_meta( $post_id, 'cf_work_updated_at',      $work_data['updated_at']      ?? current_time( 'mysql' ) );

    update_post_meta(
        $post_id,
        'cf_work_aliases',
        wp_json_encode( $work_data['aliases'] ?? [], JSON_UNESCAPED_UNICODE )
    );
    update_post_meta(
        $post_id,
        'cf_work_cast_names',
        wp_json_encode( $work_data['cast_names'] ?? [], JSON_UNESCAPED_UNICODE )
    );
    update_post_meta(
        $post_id,
        'cf_work_character_names',
        wp_json_encode( $work_data['character_names'] ?? [], JSON_UNESCAPED_UNICODE )
    );

    $source_titles = $work_data['source_titles'] ?? [];
    update_post_meta( $post_id, 'cf_work_source_title', ! empty( $source_titles ) ? $source_titles[0] : '' );
}

/**
 * ポストメタから作品データを取得する
 *
 * @param int $post_id 投稿ID
 * @return array|null 作品データ、またはnull（canonical_titleが空の場合）
 */
function contentfreaks_get_work_meta_from_post( $post_id ) {
    $canonical_title = get_post_meta( $post_id, 'cf_work_canonical_title', true );

    if ( empty( $canonical_title ) ) {
        return null;
    }

    $aliases_json         = get_post_meta( $post_id, 'cf_work_aliases',         true );
    $cast_names_json      = get_post_meta( $post_id, 'cf_work_cast_names',      true );
    $character_names_json = get_post_meta( $post_id, 'cf_work_character_names', true );

    $aliases         = ! empty( $aliases_json )         ? json_decode( $aliases_json,         true ) : [];
    $cast_names      = ! empty( $cast_names_json )      ? json_decode( $cast_names_json,      true ) : [];
    $character_names = ! empty( $character_names_json ) ? json_decode( $character_names_json, true ) : [];

    return [
        'canonical_title' => $canonical_title,
        'wikidata_id'     => get_post_meta( $post_id, 'cf_work_wikidata_id',   true ),
        'wikipedia_url'   => get_post_meta( $post_id, 'cf_work_wikipedia_url', true ),
        'official_url'    => get_post_meta( $post_id, 'cf_work_official_url',  true ),
        'confidence'      => (float) get_post_meta( $post_id, 'cf_work_confidence', true ),
        'updated_at'      => get_post_meta( $post_id, 'cf_work_updated_at',    true ),
        'source_title'    => get_post_meta( $post_id, 'cf_work_source_title',  true ),
        'aliases'         => is_array( $aliases )         ? $aliases         : [],
        'cast_names'      => is_array( $cast_names )      ? $cast_names      : [],
        'character_names' => is_array( $character_names ) ? $character_names : [],
    ];
}

// ============================================================
// 13. Geminiプロンプト用コンテキスト生成
// ============================================================

/**
 * Geminiプロンプト用の固有名詞コンテキスト文字列を生成する
 *
 * @param int $post_id 投稿ID
 * @return string プロンプト用コンテキスト文字列（作品情報がなければ空文字列）
 */
function contentfreaks_build_gemini_proper_noun_context( $post_id ) {
    $meta = contentfreaks_get_work_meta_from_post( $post_id );

    if ( empty( $meta['canonical_title'] ) ) {
        return '';
    }

    $lines = [];
    $lines[] = '■ 固有名詞（必ずこの表記を使うこと）';
    $lines[] = '- 作品名: ' . $meta['canonical_title'];

    if ( ! empty( $meta['aliases'] ) ) {
        $lines[] = '- 別名・略称（使用禁止、正式名に統一すること）: ' . implode( '、', $meta['aliases'] );
    }

    if ( ! empty( $meta['cast_names'] ) ) {
        $lines[] = '- 出演俳優名: ' . implode( '、', $meta['cast_names'] );
    }

    if ( ! empty( $meta['character_names'] ) ) {
        $lines[] = '- 登場キャラクター名: ' . implode( '、', $meta['character_names'] );
    }

    if ( ! empty( $meta['wikipedia_url'] ) ) {
        $lines[] = '- 参考（Wikipedia）: ' . $meta['wikipedia_url'];
    }

    $lines[] = '- 上記以外の固有名詞は、文字起こしに明記されているものだけ使うこと';
    $lines[] = '- 不明な場合は推測せず、記載しないこと';

    return implode( "\n", $lines );
}

// ============================================================
// 14. 生成後の固有名詞修正
// ============================================================

/**
 * 生成済み記事HTMLの固有名詞を正規化する
 *
 * $work_dataのaliasesをcanonical_titleにstr_replaceで置換する
 *
 * @param string $article_html 記事HTML
 * @param array  $work_data    作品データ
 * @return string 固有名詞修正済みHTML
 */
function contentfreaks_verify_and_fix_proper_nouns( $article_html, $work_data ) {
    if ( empty( $work_data['canonical_title'] ) || empty( $work_data['aliases'] ) ) {
        return $article_html;
    }

    $canonical = $work_data['canonical_title'];

    foreach ( $work_data['aliases'] as $alias ) {
        if ( ! empty( $alias ) && $alias !== $canonical ) {
            $article_html = str_replace( $alias, $canonical, $article_html );
        }
    }

    return $article_html;
}

// ============================================================
// 15. ヘルパー関数
// ============================================================

/**
 * エピソードタイトルから作品名を抽出し、メタデータを解決してポストに保存する
 *
 * @param int    $post_id       投稿ID
 * @param string $episode_title エピソードタイトル
 * @return array|null 作品データ、または抽出失敗時はnull
 */
function contentfreaks_resolve_and_save_work_meta_for_post( $post_id, $episode_title ) {
    error_log( '[CF ProperNoun] resolve_and_save 開始 post_id=' . $post_id . ' title=' . $episode_title );

    $extracted_titles = contentfreaks_extract_work_titles_from_episode( $episode_title );

    if ( empty( $extracted_titles ) ) {
        error_log( '[CF ProperNoun] 作品名の抽出に失敗しました: ' . $episode_title );
        return null;
    }

    $raw_title = $extracted_titles[0];
    error_log( '[CF ProperNoun] 抽出された作品名: ' . $raw_title );

    $work_data = contentfreaks_resolve_work_meta( $raw_title );

    contentfreaks_save_work_meta_to_post( $post_id, $work_data );

    error_log( '[CF ProperNoun] 保存完了 post_id=' . $post_id . ' canonical_title=' . $work_data['canonical_title'] );

    return $work_data;
}

// ============================================================
// 16. 管理画面メタボックス
// ============================================================

/**
 * 作品情報メタボックスを登録する
 */
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'cf_work_meta_box',
        '🎬 作品情報（固有名詞）',
        'contentfreaks_render_work_meta_box',
        'post',
        'side',
        'default'
    );
} );

/**
 * 作品情報メタボックスの内容をレンダリングする
 *
 * @param WP_Post $post 投稿オブジェクト
 */
function contentfreaks_render_work_meta_box( $post ) {
    wp_nonce_field( 'cf_work_meta_save', 'cf_work_meta_nonce' );

    $meta            = contentfreaks_get_work_meta_from_post( $post->ID );
    $confidence      = $meta ? (float) $meta['confidence'] : 0.0;
    $confidence_pct  = round( $confidence * 100 );
    $canonical_title = $meta ? esc_attr( $meta['canonical_title'] ) : '';
    $wikidata_id     = $meta ? esc_attr( $meta['wikidata_id'] ) : '';
    $wikipedia_url   = $meta ? esc_url( $meta['wikipedia_url'] ) : '';
    $cast_names      = $meta && ! empty( $meta['cast_names'] )      ? implode( '、', $meta['cast_names'] )      : '';
    $character_names = $meta && ! empty( $meta['character_names'] ) ? implode( '、', $meta['character_names'] ) : '';

    // 確信度アイコン
    if ( $confidence >= 0.8 ) {
        $confidence_icon = '✅';
    } elseif ( $confidence >= 0.5 ) {
        $confidence_icon = '⚠️';
    } else {
        $confidence_icon = '❓';
    }

    ?>
    <div id="cf-work-meta-box-content">
        <p>
            <strong>確信度:</strong>
            <?php echo esc_html( $confidence_icon ); ?>
            <?php echo esc_html( $confidence_pct ); ?>%
        </p>

        <p>
            <label for="cf_work_canonical_title_input"><strong>正式作品名:</strong></label><br>
            <input
                type="text"
                id="cf_work_canonical_title_input"
                name="cf_work_canonical_title"
                value="<?php echo $canonical_title; ?>"
                style="width:100%;"
            >
        </p>

        <?php if ( $cast_names ) : ?>
        <p>
            <strong>出演者:</strong><br>
            <span id="cf-cast-names"><?php echo esc_html( $cast_names ); ?></span>
        </p>
        <?php endif; ?>

        <?php if ( $character_names ) : ?>
        <p>
            <strong>キャラクター:</strong><br>
            <span id="cf-character-names"><?php echo esc_html( $character_names ); ?></span>
        </p>
        <?php endif; ?>

        <?php if ( $wikipedia_url ) : ?>
        <p>
            <a href="<?php echo $wikipedia_url; ?>" target="_blank" rel="noopener noreferrer">📖 Wikipedia</a>
            <?php if ( $wikidata_id ) : ?>
            &nbsp;|&nbsp;
            <a href="<?php echo esc_url( 'https://www.wikidata.org/wiki/' . $wikidata_id ); ?>" target="_blank" rel="noopener noreferrer">🔗 Wikidata (<?php echo esc_html( $wikidata_id ); ?>)</a>
            <?php endif; ?>
        </p>
        <?php endif; ?>

        <p>
            <button
                type="button"
                id="cf-resolve-work-meta-btn"
                class="button button-secondary"
                data-post-id="<?php echo esc_attr( $post->ID ); ?>"
                data-nonce="<?php echo esc_attr( wp_create_nonce( 'cf_resolve_work_ajax' ) ); ?>"
            >
                🔄 作品情報を再取得
            </button>
            <span id="cf-resolve-work-meta-status" style="margin-left:8px;"></span>
        </p>
    </div>

    <script>
    (function() {
        var btn = document.getElementById('cf-resolve-work-meta-btn');
        if (!btn) return;

        btn.addEventListener('click', function() {
            var postId = btn.getAttribute('data-post-id');
            var nonce  = btn.getAttribute('data-nonce');
            var status = document.getElementById('cf-resolve-work-meta-status');

            btn.disabled = true;
            status.textContent = '取得中...';

            var data = new FormData();
            data.append('action',  'cf_resolve_work_meta');
            data.append('post_id', postId);
            data.append('nonce',   nonce);

            var controller = new AbortController();
            var timeoutId  = setTimeout(function() { controller.abort(); }, 90000);

            fetch(ajaxurl, { method: 'POST', body: data, signal: controller.signal })
                .then(function(r) { return r.json(); })
                .then(function(json) {
                    clearTimeout(timeoutId);
                    if (json.success) {
                        status.textContent = '✅ 取得完了。ページをリロードしてください。';
                        status.style.color = 'green';
                    } else {
                        status.textContent = '❌ エラー: ' + (json.data || '不明なエラー');
                        status.style.color = 'red';
                    }
                    btn.disabled = false;
                })
                .catch(function(e) {
                    clearTimeout(timeoutId);
                    if (e.name === 'AbortError') {
                        status.textContent = '❌ タイムアウト（90秒）: Wikipedia/Wikidataの応答が遅すぎます。しばらく後に再試行してください。';
                    } else {
                        status.textContent = '❌ 通信エラー: ' + e.message;
                    }
                    status.style.color = 'red';
                    btn.disabled = false;
                });
        });
    })();
    </script>
    <?php
}

/**
 * メタボックスからの手動編集を保存する
 */
add_action( 'save_post', function ( $post_id ) {
    // 自動保存は無視
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // nonceチェック
    if (
        ! isset( $_POST['cf_work_meta_nonce'] )
        || ! wp_verify_nonce( $_POST['cf_work_meta_nonce'], 'cf_work_meta_save' )
    ) {
        return;
    }

    // 権限チェック
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['cf_work_canonical_title'] ) ) {
        update_post_meta(
            $post_id,
            'cf_work_canonical_title',
            sanitize_text_field( wp_unslash( $_POST['cf_work_canonical_title'] ) )
        );
    }
} );

/**
 * AJAX: 作品情報再取得ハンドラー
 */
add_action( 'wp_ajax_cf_resolve_work_meta', function () {
    // 外部API複数呼び出しのため実行時間を延長
    @set_time_limit( 120 );

    // nonceチェック
    if (
        ! isset( $_POST['nonce'] )
        || ! wp_verify_nonce( $_POST['nonce'], 'cf_resolve_work_ajax' )
    ) {
        wp_send_json_error( 'nonce検証に失敗しました' );
        return;
    }

    $post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;

    if ( $post_id <= 0 ) {
        wp_send_json_error( '無効なpost_idです' );
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( '権限がありません' );
        return;
    }

    $post = get_post( $post_id );
    if ( ! $post ) {
        wp_send_json_error( '投稿が見つかりません' );
        return;
    }

    $episode_title = $post->post_title;
    $work_data     = contentfreaks_resolve_and_save_work_meta_for_post( $post_id, $episode_title );

    if ( $work_data === null ) {
        wp_send_json_error( 'タイトルから作品名を抽出できませんでした: ' . $episode_title );
        return;
    }

    wp_send_json_success( [
        'canonical_title' => $work_data['canonical_title'],
        'confidence'      => $work_data['confidence'],
        'wikidata_id'     => $work_data['wikidata_id'],
    ] );
} );
