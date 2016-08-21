<?php
function smarty_modifier_weapon_shorten($weapon)
{
    // 現在日本語版のみ対応
    if (strtolower(substr(\Yii::$app->language, 0, 2)) !== 'ja') {
        return $weapon;
    }

    switch ($weapon) {
        case '.52ガロン':
            return '52';
        
        case '.52ガロンデコ':
            return '52デコ';
        
        case '.96ガロン':
            return '96';
        
        case '.96ガロンデコ':
            return '96デコ';
        
        case '14式竹筒銃・丙':
            return '竹丙';
        
        case '14式竹筒銃・乙':
            return '竹乙';
        
        case '14式竹筒銃・甲':
            return '竹甲';
        
        case '3Kスコープ':
            return 'リッスコ';
        
        case '3Kスコープカスタム':
            return 'リッカスコ';
        
        case 'H3リールガン':
            return 'H3';
        
        case 'H3リールガンD':
            return 'H3D';
        
        case 'H3リールガンチェリー':
            return 'チェリー';
        
        case 'L3リールガン':
            return 'L3';
        
        case 'L3リールガンD':
            return 'L3D';
        
        case 'N-ZAP83':
            return 'ファミZAP';
        
        case 'N-ZAP85':
            return '黒ZAP';
        
        case 'N-ZAP89':
            return '赤ZAP';
        
        case 'Rブラスターエリート':
            return 'ラピエリ';
        
        case 'Rブラスターエリートデコ':
            return 'エリデコ';
        
        case 'もみじシューター':
            return 'もみじ';
        
        case 'わかばシューター':
            return 'わかば';
        
        case 'オクタシューター レプリカ':
            return 'オクタ';
        
        case 'カーボンローラー':
            return 'カローラ';
        
        case 'カーボンローラーデコ':
            return 'カロデコ';
        
        case 'シャープマーカー':
            return 'シャプマ';
        
        case 'シャープマーカーネオ':
            return 'シャプネ';
        
        case 'ジェットスイーパー':
            return 'ジェット';
        
        case 'ジェットスイーパーカスタム':
            return 'ジェッカス';
        
        case 'スクイックリンα':
            return 'α';
        
        case 'スクイックリンβ':
            return 'β';
        
        case 'スクイックリンγ':
            return 'γ';
        
        case 'スクリュースロッシャー':
            return 'スクスロ';
        
        case 'スクリュースロッシャーネオ':
            return 'スネオ';
        
        case 'スプラシューター':
            return 'スプシュ';
        
        case 'スプラシューターコラボ':
            return 'スシコラ';
        
        case 'スプラシューターワサビ':
            return 'スシワサ';
        
        case 'スプラスコープ':
            return 'スプスコ';
        
        case 'スプラスコープベントー':
            return 'ベンスコ';
        
        case 'スプラスコープワカメ':
            return 'ワカスコ';
        
        case 'スプラスピナー':
            return 'スプスピ';
        
        case 'スプラスピナーコラボ':
            return 'スピコラ';
        
        case 'スプラスピナーリペア':
            return 'リペア';
        
        case 'スプラチャージャー':
            return 'スプチャ';
        
        case 'スプラチャージャーベントー':
            return 'ベントー';
        
        case 'スプラチャージャーワカメ':
            return 'ワカメ';
        
        case 'スプラローラー':
            return 'スプロラ';
        
        case 'スプラローラーコラボ':
            return 'ロラコラ';
        
        case 'スプラローラーコロコロ':
            return 'コロコロ';
        
        case 'ダイナモローラー':
            return '銀ナモ';
        
        case 'ダイナモローラーテスラ':
            return '金ナモ';
        
        case 'ダイナモローラーバーンド':
            return '焼ナモ';
        
        case 'デュアルスイーパー':
            return 'デュアル';
        
        case 'デュアルスイーパーカスタム':
            return 'デュアカス';
        
        case 'ノヴァブラスター':
            return 'ノヴァ';
        
        case 'ノヴァブラスターネオ':
            return 'ノヴァネオ';
        
        case 'ハイドラント':
            return 'ハイドラ';
        
        case 'ハイドラントカスタム':
            return 'ハイカス';
        
        case 'バケットスロッシャー':
            return 'バケスロ';
        
        case 'バケットスロッシャーソーダ':
            return 'ソーダ';
        
        case 'バケットスロッシャーデコ':
            return 'バケデコ';
        
        case 'バレルスピナー':
            return 'バレスピ';
        
        case 'バレルスピナーデコ':
            return 'バレデコ';
        
        case 'バレルスピナーリミックス':
            return 'バレミク';
        
        case 'パブロ':
            return 'パブロ';
        
        case 'パブロ・ヒュー':
            return 'パヒュー';
        
        case 'パーマネント・パブロ':
            return 'パパブロ';
        
        case 'ヒッセン':
            return 'ヒッセン';
        
        case 'ヒッセン・ヒュー':
            return 'ヒッヒュー';
        
        case 'ヒーローシューター レプリカ':
            return 'ヒロシ';
        
        case 'ヒーローチャージャー レプリカ':
            return 'ヒロチ';
        
        case 'ヒーローローラー レプリカ':
            return 'ヒロロ';
        
        case 'プライムシューター':
            return 'プライム';
        
        case 'プライムシューターコラボ':
            return 'プラコラ';
        
        case 'プライムシューターベリー':
            return 'ベリー';
        
        case 'プロモデラーMG':
            return '銀モデ';
        
        case 'プロモデラーPG':
            return '銅モデ';
        
        case 'プロモデラーRG':
            return '金モデ';
        
        case 'ホクサイ':
            return 'ホクサイ';
        
        case 'ホクサイ・ヒュー':
            return 'ホヒュー';
        
        case 'ホットブラスター':
            return 'ホッブラ';
        
        case 'ホットブラスターカスタム':
            return 'ホッカス';
        
        case 'ボールドマーカー':
            return 'ボールド';
        
        case 'ボールドマーカー7':
            return 'ボルシチ';
        
        case 'ボールドマーカーネオ':
            return 'ボルネオ';
        
        case 'ラピッドブラスター':
            return 'ラピブラ';
        
        case 'ラピッドブラスターデコ':
            return 'ラピデコ';
        
        case 'リッター3K':
            return 'リッター';
        
        case 'リッター3Kカスタム':
            return 'リッカス';
        
        case 'ロングブラスター':
            return 'ロンブラ';
        
        case 'ロングブラスターカスタム':
            return 'ロンカス';
        
        case 'ロングブラスターネクロ':
            return 'ネクロ';

        default:
            return $weapon;
    }
}
