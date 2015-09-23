<?php
use yii\db\Migration;

class m150923_120331_rule extends Migration
{
    public function up()
    {
        $this->createTable('rule', [
            'id'    => $this->primarykey(),
            'key'   => $this->string(16)->notNull()->unique(),
            'name'  => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('rule', ['key', 'name'], [
            [ 'nawabari',   'ナワバリバトル' ], // Turf War
            [ 'area',       'ガチエリア' ],     // Splat Zones
            [ 'yagura',     'ガチヤグラ' ],     // Tower Control
            [ 'hoko',       'ガチホコ'],        // Rainmaker
        ]);
    }

    public function down()
    {
        $this->dropTable('rule');
    }
}
