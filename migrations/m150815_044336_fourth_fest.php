<?php
use yii\db\Schema;
use yii\db\Migration;

class m150815_044336_fourth_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 4,
            'name'      => 'キリギリス vs アリ',
            'start_at'  => strtotime('2015-08-22 12:00:00+09:00'),
            'end_at'    => strtotime('2015-08-23 12:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword' ],
            [
                [
                    'fest_id' => 4,
                    'color_id' => 1,
                    'name' => 'キリギリス',
                    'keyword' => 'キリギリス',
                ],
                [
                    'fest_id' => 4,
                    'color_id' => 2,
                    'name' => 'アリ',
                    'keyword' => 'アリ',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 4]);
        $this->delete('fest', 'fest.id = :id', [':id' => 4]);
    }
}
