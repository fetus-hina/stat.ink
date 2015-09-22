<?php
use yii\db\Migration;

class m150803_090003_second_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 2,
            'name'      => '赤いきつね vs 緑のたぬき',
            'start_at'  => strtotime('2015-07-03 15:00:00+09:00'),
            'end_at'    => strtotime('2015-07-04 15:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword' ],
            [
                [
                    'fest_id' => 2,
                    'color_id' => 1,
                    'name' => '赤いきつね',
                    'keyword' => '赤いきつね',
                ],
                [
                    'fest_id' => 2,
                    'color_id' => 2,
                    'name' => '緑のたぬき',
                    'keyword' => '緑のたぬき',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 2]);
        $this->delete('fest', 'fest.id = :id', [':id' => 2]);
    }
}
