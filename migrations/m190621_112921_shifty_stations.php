<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190621_112921_shifty_stations extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('map2', 'name', $this->string(48)->notNull());
        $this->batchInsert(
            'map2',
            ['key', 'name', 'short_name', 'area', 'splatnet', 'release_at'],
            [
                [
                    'mystery_01',
                    'Shifty: Wayslide Cool',
                    'Shifty 1',
                    2011,
                    null,
                    '2017-08-04T06:00:00+00',
                ],
                [
                    'mystery_02',
                    'Shifty: The Secret of S.P.L.A.T.',
                    'Shifty 2',
                    1885,
                    null,
                    '2017-09-02T04:00:00+00',
                ],
                [
                    'mystery_03',
                    'Shifty: Goosponge',
                    'Shifty 3',
                    1900,
                    null,
                    '2017-09-09T06:00:00+00',
                ],
                [
                    'mystery_04',
                    'Shifty: Windmill House on the Pearlie',
                    'Shifty 4',
                    2061,
                    null,
                    '2017-10-14T04:00:00+00',
                ],
                [
                    'mystery_05',
                    'Shifty: Fancy Spew',
                    'Shifty 5',
                    2455,
                    null,
                    '2017-12-09T06:00:00+00',
                ],
                [
                    'mystery_06',
                    'Shifty: Zone of Glass',
                    'Shifty 6',
                    2027,
                    null,
                    '2018-01-13T04:00:00+00',
                ],
                [
                    'mystery_07',
                    'Shifty: Cannon Fire Pearl',
                    'Shifty 7',
                    2390,
                    null,
                    '2018-02-03T06:00:00+00',
                ],
                [
                    'mystery_08',
                    'Shifty: The Bunker Games',
                    'Shifty 8',
                    2770,
                    null,
                    '2018-03-03T06:00:00+00',
                ],
                [
                    'mystery_09',
                    'Shifty: Grapplink Girl',
                    'Shifty 9',
                    2199,
                    null,
                    '2018-03-24T06:00:00+00',
                ],
                [
                    'mystery_10',
                    'Shifty: Zappy Longshocking',
                    'Shifty 10',
                    2185,
                    null,
                    '2018-04-21T06:00:00+00',
                ],
                [
                    'mystery_11',
                    'Shifty: A Swiftly Tilting Balance',
                    'Shifty 11',
                    2300,
                    null,
                    '2018-05-19T04:00:00+00',
                ],
                [
                    'mystery_12',
                    'Shifty: Sweet Valley Tentacles',
                    'Shifty 12',
                    2593,
                    null,
                    '2018-07-21T04:00:00+00',
                ],
                [
                    'mystery_13',
                    'Shifty: The Switches',
                    'Shifty 13',
                    2360,
                    null,
                    '2018-08-18T06:00:00+00',
                ],
                [
                    'mystery_14',
                    'Shifty: The Bouncey Twins',
                    'Shifty 14',
                    1783,
                    null,
                    '2018-09-22T04:00:00+00',
                ],
                [
                    'mystery_15',
                    'Shifty: Railway Chillin\'',
                    'Shifty 15',
                    2188,
                    null,
                    '2018-10-19T08:00:00+00',
                ],
                [
                    'mystery_16',
                    'Shifty: Gusher Towns',
                    'Shifty 16',
                    2297,
                    null,
                    '2018-11-10T06:00:00+00',
                ],
                [
                    'mystery_17',
                    'Shifty: The Maze Dasher',
                    'Shifty 17',
                    2327,
                    null,
                    '2018-12-15T04:00:00+00',
                ],
                [
                    'mystery_18',
                    'Shifty: Flooders in the Attic',
                    'Shifty 18',
                    2254,
                    null,
                    '2019-01-04T08:00:00+00',
                ],
                [
                    'mystery_19',
                    'Shifty: The Splat in Our Zones',
                    'Shifty 19',
                    2583,
                    null,
                    '2019-02-02T06:00:00+00',
                ],
                [
                    'mystery_20',
                    'Shifty: The Ink is Spreading',
                    'Shifty 20',
                    2259,
                    null,
                    '2019-03-16T04:00:00+00',
                ],
                [
                    'mystery_21',
                    'Shifty: Bridge to Tentaswitchia',
                    'Shifty 21',
                    2832,
                    null,
                    '2019-04-19T08:00:00+00',
                ],
                [
                    'mystery_22',
                    'Shifty: The Chronicles of Rolonium',
                    'Shifty 22',
                    2059,
                    null,
                    '2019-05-11T06:00:00+00',
                ],
                [
                    'mystery_23',
                    'Shifty: Furler in the Ashes',
                    'Shifty 23',
                    2373,
                    null,
                    '2019-06-15T04:00:00+00',
                ],
                [
                    'mystery_24',
                    'Shifty: MC.Princess Diaries',
                    'Shifty 24',
                    null,
                    null,
                    '2019-07-19T12:00:00+00',
                ],
            ],
        );
    }

    public function safeDown()
    {
        $this->delete('map2', [
            'key' => array_map(
                fn (int $i): string => sprintf('mystery_%02d', $i),
                range(1, 24),
            ),
        ]);
        $this->alterColumn('map2', 'name', $this->string(32)->notNull());
    }
}
