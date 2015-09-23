<?php
use yii\db\Migration;

class m150923_123420_weapon_type extends Migration
{
    public function up()
    {
        $this->createTable('weapon_type', [
            'id'    => $this->primaryKey(),
            'key'   => $this->string(16)->notNull()->unique(),
            'name'  => $this->string(16)->notNull()->unique(),
        ]);
        $this->batchInsert('weapon_type', [ 'key', 'name' ], [
            [ 'shooter',    'シューター' ],
            [ 'roller',     'ローラー' ],
            [ 'charger',    'チャージャー' ],
            [ 'slosher',    'スロッシャー' ],
            [ 'splatling',  'スピナー' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('weapon_type');
    }
}
