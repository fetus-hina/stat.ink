<?php
use yii\db\Migration;

class m150803_090002_first_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 1,
            'name'      => 'ごはん vs パン',
            'start_at'  => strtotime('2015-06-13 18:00:00+09:00'),
            'end_at'    => strtotime('2015-06-14 18:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword' ],
            [
                [
                    'fest_id' => 1,
                    'color_id' => 1,
                    'name' => 'ごはん',
                    'keyword' => 'ごはん',
                ],
                [
                    'fest_id' => 1,
                    'color_id' => 2,
                    'name' => 'パン',
                    'keyword' => 'パン',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 1]);
        $this->delete('fest', 'fest.id = :id', [':id' => 1]);
    }
}
