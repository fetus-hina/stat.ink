<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m190622_180334_shifty_map2 extends Migration
{
    public function safeUp()
    {
        $this->createTable('shifty_map2', [
            'id' => $this->primaryKey(),
            'period_range' => 'int4range NOT NULL',
            'range_hint' => 'tstzrange NOT NULL',
            'map_id' => $this->pkRef('map2')->notNull(),
            'EXCLUDE USING GIST ([[period_range]] WITH &&)',
            'EXCLUDE USING GIST ([[range_hint]] WITH &&)',
        ]);


        $db = $this->getDb();
        $stages = $this->getStages();
        $this->batchInsert(
            'shifty_map2',
            ['period_range', 'range_hint', 'map_id'],
            array_map(
                function (array $row) use ($db, $stages): array {
                    return [
                        new Expression(vsprintf('%s::int4range', [
                            $db->quoteValue(vsprintf('[%d,%d)', [
                                static::timestamp2period($row[0]) - 1,
                                static::timestamp2period($row[1]) + 1,
                            ])),
                        ])),
                        new Expression(vsprintf('%s::tstzrange', [
                            $db->quoteValue(vsprintf('[%s,%s)', [
                                $db->quoteValue($row[0]->format(DateTime::ATOM)),
                                $db->quoteValue($row[1]->format(DateTime::ATOM)),
                            ])),
                        ])),
                        $stages[$row[2]],
                    ];
                },
                iterator_to_array($this->getLayouts()),
            ),
        );
    }

    public function safeDown()
    {
        $this->dropTable('shifty_map2');
    }

    public function getStages(): array
    {
        $query = (new Query())
            ->select(['id', 'key'])
            ->from('map2')
            ->andWhere(['like', 'map2.key', 'mystery_%', false]);

        return ArrayHelper::map($query->all(), 'key', 'id');
    }

    public function getLayouts() //: generator
    {
        if (!$fh = @fopen(__FILE__, 'rt')) {
            throw new Exception('Could not open the file');
        }

        fseek($fh, __COMPILER_HALT_OFFSET__, SEEK_SET);
        while (!feof($fh)) {
            $line = trim((string)fgets($fh));
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            $items = preg_split('/\s+/', $line, 3);
            yield [
                new DateTimeImmutable($items[0]),
                new DateTimeImmutable($items[1]),
                $items[2],
            ];
        }
        fclose($fh);
    }

    private static function timestamp2period(DateTimeImmutable $ts): int
    {
        return (int)floor($ts->getTimestamp() / 7200);
    }
}

// phpcs:disable
__halt_compiler();
2017-08-04T06:00:00+00:00   2017-08-06T14:00:00+00:00   mystery_01
2017-09-02T04:00:00+00:00   2017-09-03T14:00:00+00:00   mystery_02
2017-09-09T06:00:00+00:00   2017-09-10T06:00:00+00:00   mystery_03
2017-10-14T04:00:00+00:00   2017-10-15T04:00:00+00:00   mystery_04
2017-11-11T06:00:00+00:00   2017-11-12T06:00:00+00:00   mystery_02
2017-11-18T05:00:00+00:00   2017-11-19T05:00:00+00:00   mystery_03
2017-12-09T06:00:00+00:00   2017-12-10T15:00:00+00:00   mystery_05
2018-01-13T04:00:00+00:00   2018-01-14T12:00:00+00:00   mystery_06
2018-02-03T06:00:00+00:00   2018-02-04T06:00:00+00:00   mystery_07
2018-03-03T06:00:00+00:00   2018-03-04T06:00:00+00:00   mystery_08
2018-03-24T06:00:00+00:00   2018-03-25T06:00:00+00:00   mystery_09
2018-04-21T06:00:00+00:00   2018-04-22T06:00:00+00:00   mystery_10
2018-05-05T04:00:00+00:00   2018-05-06T14:00:00+00:00   mystery_02
2018-05-12T04:00:00+00:00   2018-05-13T14:00:00+00:00   mystery_03
2018-05-26T06:00:00+00:00   2018-05-27T06:00:00+00:00   mystery_04
2018-06-09T06:00:00+00:00   2018-06-10T06:00:00+00:00   mystery_11
2018-06-23T04:00:00+00:00   2018-06-24T14:00:00+00:00   mystery_10
2018-07-21T04:00:00+00:00   2018-07-22T14:00:00+00:00   mystery_12
2018-08-18T06:00:00+00:00   2018-08-19T14:00:00+00:00   mystery_13
2018-09-22T04:00:00+00:00   2018-09-24T06:00:00+00:00   mystery_14
2018-10-19T08:00:00+00:00   2018-10-21T22:00:00+00:00   mystery_15
2018-11-10T06:00:00+00:00   2018-11-11T06:00:00+00:00   mystery_16
2018-12-15T04:00:00+00:00   2018-12-16T14:00:00+00:00   mystery_17
2019-01-04T08:00:00+00:00   2019-01-06T22:00:00+00:00   mystery_18
2019-02-02T06:00:00+00:00   2019-02-03T06:00:00+00:00   mystery_19
2019-03-16T04:00:00+00:00   2019-03-17T12:00:00+00:00   mystery_20
2019-04-19T08:00:00+00:00   2019-04-21T22:00:00+00:00   mystery_21
2019-05-11T06:00:00+00:00   2019-05-12T06:00:00+00:00   mystery_22
2019-06-15T04:00:00+00:00   2019-06-16T14:00:00+00:00   mystery_23
