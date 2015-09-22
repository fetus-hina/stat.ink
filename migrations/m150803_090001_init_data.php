<?php
use yii\db\Migration;

class m150803_090001_init_data extends Migration
{
    public function safeUp()
    {
        $this->batchInsert(
            'color',
            [ 'id', 'name', 'leader' ],
            [
                [
                    'id' => 1,
                    'name' => 'red',
                    'leader' => 'アオリ',
                ],
                [
                    'id' => 2,
                    'name' => 'green',
                    'leader' => 'ホタル',
                ]
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('color');
    }
}
