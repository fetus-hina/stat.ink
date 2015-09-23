<?php
use yii\db\Migration;
use app\models\WeaponType;

class m150923_124509_weapon extends Migration
{
    public function up()
    {
        $this->createTable('weapon', [
            'id'        => $this->primaryKey(),
            'type_id'   => $this->integer()->notNull(),
            'key'       => $this->string(32)->notNull()->unique(),
            'name'      => $this->string(16)->notNull()->unique(),
        ]);
        $this->addForeignKey('fk_weapon_type', 'weapon', 'type_id', 'weapon_type', 'id', 'RESTRICT');

        $shooter    = $this->getType('shooter');
        $roller     = $this->getType('roller');
        $charger    = $this->getType('charger');
        $slosher    = $this->getType('slosher');
        $splatling  = $this->getType('splatling');

        $this->batchInsert('weapon', [ 'type_id', 'key', 'name' ], [
            [ $shooter, '52gal',            '.52ガロン' ],
            [ $shooter, '52gal-deco',       '.52ガロンデコ' ],
            [ $shooter, '96gal',            '.96ガロン' ],
            [ $shooter, '96gal-deco',       '.96ガロンデコ' ],
            [ $shooter, 'bold',             'ボールドマーカー' ],
            [ $shooter, 'dualsweeper',      'デュアルスイーパー' ],
            [ $shooter, 'dualsweeper-custom', 'デュアルスイーパーカスタム' ],
            [ $shooter, 'h3reelgun',        'H3リールガン' ],
            [ $shooter, 'heroshooter-replica', 'ヒーローシューターレプリカ' ],
            [ $shooter, 'hotblaster',       'ホットブラスター' ],
            [ $shooter, 'hotblaster-custom', 'ホットブラスターカスタム' ],
            [ $shooter, 'jetsweeper',       'ジェットスイーパー' ],
            [ $shooter, 'jetsweeper-custom', 'ジェットスイーパーカスタム' ],
            [ $shooter, 'l3reelgun',        'L3リールガン' ],
            [ $shooter, 'l3reelgun-d',      'L3リールガンD' ],
            [ $shooter, 'longblaster',      'ロングブラスター' ],
            [ $shooter, 'momiji',           'もみじシューター' ],
            [ $shooter, 'nova',             'ノヴァブラスター' ],
            [ $shooter, 'nzap85',           "N-ZAP'85" ],
            [ $shooter, 'nzap89',           "N-ZAP'89" ],
            [ $shooter, 'octoshooter-replica', 'オクタシューターレプリカ' ],
            [ $shooter, 'prime',            'プライムシューター' ],
            [ $shooter, 'prime-collabo',    'プライムシューターコラボ' ],
            [ $shooter, 'promodeler-mg',    'プロモデラーMG' ],
            [ $shooter, 'promodeler-rg',    'プロモデラーRG' ],
            [ $shooter, 'rapid',            'ラピッドブラスター' ],
            [ $shooter, 'rapid-deco',       'ラピッドブラスターデコ' ],
            [ $shooter, 'sharp',            'シャープマーカー' ],
            [ $shooter, 'sharp-neo',        'シャープマーカーネオ' ],
            [ $shooter, 'sshooter',         'スプラシューター' ],
            [ $shooter, 'sshooter-collabo', 'スプラシューターコラボ' ],
            [ $shooter, 'wakaba',           'わかばシューター' ],

            [ $roller, 'carbon',            'カーボンローラー' ],
            [ $roller, 'dynamo',            'ダイナモローラー' ],
            [ $roller, 'dynamo-tesla',      'ダイナモローラーテスラ' ],
            [ $roller, 'heroroller-replica', 'ヒーローローラーレプリカ' ],
            [ $roller, 'hokusai',           'ホクサイ' ],
            [ $roller, 'pablo',             'パブロ' ],
            [ $roller, 'pablo-hue',         'パブロ・ヒュー' ],
            [ $roller, 'splatroller',       'スプラローラー' ],
            [ $roller, 'splatroller-collabo', 'スプラローラーコラボ' ],

            [ $charger, 'bamboo14mk1',      '14式竹筒銃・甲' ],
            [ $charger, 'herocharger-replica', 'ヒーローチャージャーレプリカ' ],
            [ $charger, 'liter3k',          'リッター3K' ],
            [ $charger, 'liter3k-custom',   'リッター3Kカスタム' ],
            [ $charger, 'liter3k-scope',    '3Kスコープ' ],
            [ $charger, 'splatcharger',     'スプラチャージャー' ],
            [ $charger, 'splatcharger-wakame', 'スプラチャージャーワカメ' ],
            [ $charger, 'splatscope',       'スプラスコープ' ],
            [ $charger, 'splatscope-wakame', 'スプラスコープワカメ' ],
            [ $charger, 'squiclean-a',      'スクイックリンα' ],
            [ $charger, 'squiclean-b',      'スクイックリンβ' ],

            [ $slosher, 'bucketslosher',    'バケットスロッシャー' ],

            [ $splatling, 'barrelspinner',  'バレルスピナー' ],
            [ $splatling, 'splatspinner',   'スプラスピナー' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('weapon');
    }

    private function getType($key)
    {
        return WeaponType::findOne(['key' => $key])->id;
    }
}
