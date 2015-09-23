<?php
use yii\db\Migration;

class m150923_122013_map extends Migration
{
    public function up()
    {
        $this->createTable('map', [
            'id'    => $this->primaryKey(),
            'key'   => $this->string(16)->notNull()->unique(),
            'name'  => $this->string(16)->notNull()->unique(),
        ]);
        $this->batchInsert('map', [ 'key', 'name' ], [
            [ 'arowana',    'アロワナモール' ],
            [ 'bbass',      'Bバスパーク' ],
            [ 'shionome',   'シオノメ油田' ],
            [ 'dekaline',   'デカライン高架下' ],
            [ 'hakofugu',   'ハコフグ倉庫' ],
            [ 'hokke',      'ホッケふ頭' ],
            [ 'mozuku',     'モズク農園' ],
            [ 'negitoro',   'ネギトロ炭鉱' ],
            [ 'tachiuo',    'タチウオパーキング' ],
            [ 'mongara',    'モンガラキャンプ場' ],
            [ 'hirame',     'ヒラメが丘団地' ],
            [ 'masaba',     'マサバ海峡大橋' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('map');
    }
}
