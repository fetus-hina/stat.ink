<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160902_132006_lobby extends Migration
{
    public function safeUp()
    {
        foreach ($this->getData() as $key => $names) {
            $this->update('lobby', ['name' => $names[1]], ['key' => $key]);
        }
    }

    public function safeDown()
    {
        foreach ($this->getData() as $key => $names) {
            $this->update('lobby', ['name' => $names[0]], ['key' => $key]);
        }
    }

    private function getData()
    {
        return [
            'standard' => [
                'Standard Battle',
                'Solo Queue',
            ],
            'squad_2' => [
                'Squad Battle (2 Players)',
                'Squad Battle (Twin)',
            ],
            'squad_3' => [
                'Squad Battle (3 Players)',
                'Squad Battle (Tri)',
            ],
            'squad_4' => [
                'Squad Battle (4 Players)',
                'Squad Battle (Quad)',
            ],
        ];
    }
}
