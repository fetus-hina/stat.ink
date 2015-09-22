<?php
use yii\db\Migration;

class m150803_090005_third_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 3,
            'name'      => 'レモンティー vs ミルクティー',
            'start_at'  => strtotime('2015-07-25 15:00:00+09:00'),
            'end_at'    => strtotime('2015-07-26 15:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword' ],
            [
                [
                    'fest_id' => 3,
                    'color_id' => 1,
                    'name' => 'レモンティー',
                    'keyword' => 'レモン',
                ],
                [
                    'fest_id' => 3,
                    'color_id' => 2,
                    'name' => 'ミルクティー',
                    'keyword' => 'ミルク',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 3]);
        $this->delete('fest', 'fest.id = :id', [':id' => 3]);
    }
}
