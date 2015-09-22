<?php
use yii\db\Migration;

class m150918_134654_official_result_data extends Migration
{
    public function safeUp()
    {
        $this->batchInsert(
            'official_result',
            ['fest_id', 'alpha_people', 'bravo_people', 'alpha_win', 'bravo_win', 'win_rate_times'],
            [
                [1, 58, 42, 55, 45, 2],
                [2, 67, 33, 47, 53, 2],
                [3, 32, 68, 45, 55, 2],
                [4, 61, 39, 53, 47, 4],
                [5, 43, 57, 49, 51, 4],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('official_result', 'fest_id <= 5');
    }
}
