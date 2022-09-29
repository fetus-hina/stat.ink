<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220929_151400_area extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        foreach ($this->getData() as $key => $area) {
            $this->update(
                '{{%map3}}',
                ['area' => $area],
                ['key' => $key]
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update(
            '{{%map3}}',
            ['area' => null],
            ['key' => array_keys($this->getData())]
        );

        return true;
    }

    /**
     * @return array<string, int>
     */
    private function getData(): array
    {
        return [
            'amabi' =>  2610,
            'chozame' => 2970,
            'gonzui' => 2746,
            'kinmedai' => 2363,
            'mahimahi' => 1690,
            'masaba' => 2477,
            'mategai' => 2622,
            'namero' => 2186,
            'sumeshi' => 3045,
            'yagara' => 2621,
            'yunohana' => 2142,
            'zatou' => 2265,
        ];
    }
}
