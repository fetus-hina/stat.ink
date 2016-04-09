<?php
use yii\db\Migration;

class m160408_113941_version2_7 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatoon_version', [ 'tag', 'name', 'released_at' ], [
            [ '2.7.0', '2.7.0', '2016-04-13T10:00:00+09:00' ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('splatoon_version', ['tag' => '2.7.0']);
    }

}
