<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m151217_071742_map_release_data extends Migration
{
    public function safeUp()
    {
        $data = [
            '2015-05-28 00:00:00+09' => [
                'arowana',
                'bbass',
                'dekaline',
                'hakofugu',
                'hokke',
                'shionome',
            ],
            '2015-06-11 11:00:00+09' => [
                'mozuku',
            ],
            '2015-06-20 11:00:00+09' => [
                'negitoro',
            ],
            '2015-07-11 11:00:00+09' => [
                'tachiuo',
            ],
            '2015-07-25 11:00:00+09' => [
                'mongara',
            ],
            '2015-08-21 11:00:00+09' => [
                'hirame',
            ],
            '2015-09-18 11:00:00+09' => [
                'masaba',
            ],
            '2015-11-14 11:00:00+09' => [
                'kinmedai',
            ],
            '2015-12-04 11:00:00+09' => [
                'mahimahi',
            ],
        ];
        foreach ($data as $date => $keys) {
            $this->update('map', ['release_at' => $date], ['key' => $keys]);
        }
    }

    public function safeDown()
    {
        $this->update('map', ['release_at' => null]);
    }
}
