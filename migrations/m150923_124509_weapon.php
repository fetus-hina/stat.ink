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
            [ $shooter, '52gal_deco',       '.52ガロンデコ' ],
            [ $shooter, '96gal',            '.96ガロン' ],
            [ $shooter, '96gal_deco',       '.96ガロンデコ' ],
            [ $shooter, 'bold',             'ボールドマーカー' ],
            [ $shooter, 'dualsweeper',      'デュアルスイーパー' ],
            [ $shooter, 'dualsweeper_custom', 'デュアルスイーパーカスタム' ],
            [ $shooter, 'h3reelgun',        'H3リールガン' ],
            [ $shooter, 'heroshooter_replica', 'ヒーローシューターレプリカ' ],
            [ $shooter, 'hotblaster',       'ホットブラスター' ],
            [ $shooter, 'hotblaster_custom', 'ホットブラスターカスタム' ],
            [ $shooter, 'jetsweeper',       'ジェットスイーパー' ],
            [ $shooter, 'jetsweeper_custom', 'ジェットスイーパーカスタム' ],
            [ $shooter, 'l3reelgun',        'L3リールガン' ],
            [ $shooter, 'l3reelgun_d',      'L3リールガンD' ],
            [ $shooter, 'longblaster',      'ロングブラスター' ],
            [ $shooter, 'momiji',           'もみじシューター' ],
            [ $shooter, 'nova',             'ノヴァブラスター' ],
            [ $shooter, 'nzap85',           "N_ZAP'85" ],
            [ $shooter, 'nzap89',           "N_ZAP'89" ],
            [ $shooter, 'octoshooter_replica', 'オクタシューターレプリカ' ],
            [ $shooter, 'prime',            'プライムシューター' ],
            [ $shooter, 'prime_collabo',    'プライムシューターコラボ' ],
            [ $shooter, 'promodeler_mg',    'プロモデラーMG' ],
            [ $shooter, 'promodeler_rg',    'プロモデラーRG' ],
            [ $shooter, 'rapid',            'ラピッドブラスター' ],
            [ $shooter, 'rapid_deco',       'ラピッドブラスターデコ' ],
            [ $shooter, 'sharp',            'シャープマーカー' ],
            [ $shooter, 'sharp_neo',        'シャープマーカーネオ' ],
            [ $shooter, 'sshooter',         'スプラシューター' ],
            [ $shooter, 'sshooter_collabo', 'スプラシューターコラボ' ],
            [ $shooter, 'wakaba',           'わかばシューター' ],

            [ $roller, 'carbon',            'カーボンローラー' ],
            [ $roller, 'dynamo',            'ダイナモローラー' ],
            [ $roller, 'dynamo_tesla',      'ダイナモローラーテスラ' ],
            [ $roller, 'heroroller_replica', 'ヒーローローラーレプリカ' ],
            [ $roller, 'hokusai',           'ホクサイ' ],
            [ $roller, 'pablo',             'パブロ' ],
            [ $roller, 'pablo_hue',         'パブロ・ヒュー' ],
            [ $roller, 'splatroller',       'スプラローラー' ],
            [ $roller, 'splatroller_collabo', 'スプラローラーコラボ' ],

            [ $charger, 'bamboo14mk1',      '14式竹筒銃・甲' ],
            [ $charger, 'herocharger_replica', 'ヒーローチャージャーレプリカ' ],
            [ $charger, 'liter3k',          'リッター3K' ],
            [ $charger, 'liter3k_custom',   'リッター3Kカスタム' ],
            [ $charger, 'liter3k_scope',    '3Kスコープ' ],
            [ $charger, 'splatcharger',     'スプラチャージャー' ],
            [ $charger, 'splatcharger_wakame', 'スプラチャージャーワカメ' ],
            [ $charger, 'splatscope',       'スプラスコープ' ],
            [ $charger, 'splatscope_wakame', 'スプラスコープワカメ' ],
            [ $charger, 'squiclean_a',      'スクイックリンα' ],
            [ $charger, 'squiclean_b',      'スクイックリンβ' ],

            [ $slosher, 'bucketslosher',    'バケットスロッシャー' ],
            [ $slosher, 'hissen',           'ヒッセン' ],

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
