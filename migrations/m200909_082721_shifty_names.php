<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;

class m200909_082721_shifty_names extends Migration
{
    public function safeUp()
    {
        $this->updateName('');
    }

    public function safeDown()
    {
        $this->updateName('Shifty: ');
    }

    public function updateName(string $prefix): void
    {
        $db = $this->getDb();
        $data = $this->getData();

        $this->update(
            'map2',
            [
                'name' => new Expression(vsprintf('CASE %s %s END', [
                    $db->quoteColumnName('key'),
                    implode(' ', array_map(
                        fn (string $key, string $name): string => vsprintf('WHEN %s THEN %s', [
                            $db->quoteValue($key),
                            $db->quoteValue($prefix . $name),
                        ]),
                        array_keys($data),
                        array_values($data),
                    )),
                ])),
            ],
            ['key' => array_keys($data)]
        );
    }

    public function getData(): array
    {
        return [
            'mystery_01' => 'Wayslide Cool',
            'mystery_02' => 'The Secret of S.P.L.A.T.',
            'mystery_03' => 'Goosponge',
            'mystery_04' => 'Windmill House on the Pearlie',
            'mystery_05' => 'Fancy Spew',
            'mystery_06' => 'Zone of Glass',
            'mystery_07' => 'Cannon Fire Pearl',
            'mystery_08' => 'The Bunker Games',
            'mystery_09' => 'Grapplink Girl',
            'mystery_10' => 'Zappy Longshocking',
            'mystery_11' => 'A Swiftly Tilting Balance',
            'mystery_12' => 'Sweet Valley Tentacles',
            'mystery_13' => 'The Switches',
            'mystery_14' => 'The Bouncey Twins',
            'mystery_15' => 'Railway Chillin\'',
            'mystery_16' => 'Gusher Towns',
            'mystery_17' => 'The Maze Dasher',
            'mystery_18' => 'Flooders in the Attic',
            'mystery_19' => 'The Splat in Our Zones',
            'mystery_20' => 'The Ink is Spreading',
            'mystery_21' => 'Bridge to Tentaswitchia',
            'mystery_22' => 'The Chronicles of Rolonium',
            'mystery_23' => 'Furler in the Ashes',
            'mystery_24' => 'MC.Princess Diaries',
        ];
    }
}
