<?php
use yii\db\Migration;

class m150905_041821_fifth_fest extends Migration
{
    public function safeUp()
    {
        $this->insert('fest', [
            'id'        => 5,
            'name'      => 'ボケ vs ツッコミ',
            'start_at'  => strtotime('2015-09-12 12:00:00+09:00'),
            'end_at'    => strtotime('2015-09-13 12:00:00+09:00'),
        ]);
        $this->batchInsert(
            'team',
            [ 'fest_id', 'color_id', 'name', 'keyword', 'ink_color' ],
            [
                [
                    'fest_id' => 5,
                    'color_id' => 1,
                    'name' => 'ボケ',
                    'keyword' => 'ボケ',
                    'ink_color' => 'd9612b',
                ],
                [
                    'fest_id' => 5,
                    'color_id' => 2,
                    'name' => 'ツッコミ',
                    'keyword' => 'ツッコミ',
                    'ink_color' => '5c7cb8',
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('team', 'team.fest_id = :id', [':id' => 5]);
        $this->delete('fest', 'fest.id = :id', [':id' => 5]);
    }
}
