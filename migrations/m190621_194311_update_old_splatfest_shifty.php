<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m190621_194311_update_old_splatfest_shifty extends Migration
{
    public function safeUp()
    {
        $db = Yii::$app->db;
        $stages = $this->getStages();
        $tmpIndex = 'tmp_battle2_mystery_period';
        $this->execute(vsprintf('CREATE INDEX %s ON %s (%s) WHERE ((%s))', [
            $db->quoteColumnName($tmpIndex),
            $db->quoteTableName('battle2'),
            $db->quoteColumnName('period'),
            implode(') AND (', [
                sprintf('%s IS NOT NULL', $db->quoteColumnName('period')),
                vsprintf('%s = %s', [
                    $db->quoteColumnName('map_id'),
                    $db->quoteValue($stages['mystery']),
                ]),
            ]),
        ]));
        foreach ($this->getLayouts() as $layoutKey => $terms) {
            $or = ['or'];
            foreach ($terms as $term) {
                $or[] = ['BETWEEN', 'battle2.period', $term[0], $term[1]];
            }
            $this->update('battle2', ['map_id' => $stages[$layoutKey]], ['and',
                ['map_id' => $stages['mystery']],
                $or,
            ]);
        }
        $this->execute(vsprintf('DROP INDEX %s', [
            $db->quoteColumnName($tmpIndex),
        ]));
    }

    public function safeDown()
    {
        $stages = $this->getStages();
        $this->update(
            'battle2',
            ['map_id' => $stages['mystery']],
            ['map_id' => array_values($stages)]
        );
    }

    public function getStages(): array
    {
        $query = (new Query())
            ->select(['id', 'key'])
            ->from('map2')
            ->andWhere(['like', 'map2.key', 'mystery%', false]);

        return ArrayHelper::map($query->all(), 'key', 'id');
    }

    public function getLayouts(): array
    {
        $result = [];
        $lastLayout = null;
        $lastEndAt = null;
        foreach ($this->loadLayoutData() as $_) {
            [$startAt, $endAt, $layoutKey] = $_;

            // 異なるレイアウトの期間が重複していないことを保証する
            if ($lastLayout !== null && $lastLayout !== $layoutKey) {
                if ($lastEndAt > $startAt) {
                    throw new Exception(vsprintf('Duplicated term detected: %s, %s-%s', [
                        $layoutKey,
                        $startAt->format(DateTime::ATOM),
                        $endAt->format(DateTime::ATOM),
                    ]));
                }
            }
            $lastLayout = $layoutKey;
            $lastEndAt = $endAt;

            if (!isset($result[$layoutKey])) {
                $result[$layoutKey] = [];
            }
            $result[$layoutKey][] = [
                static::timestamp2period($startAt),
                static::timestamp2period($endAt->sub(new DateInterval('PT1S'))),
            ];
        }
        return $result;
    }

    private function loadLayoutData() //: generator
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
2017-08-04T06:00+00:00  2017-08-05T06:00+00:00  mystery_01
2017-08-05T04:00+00:00  2017-08-06T04:00+00:00  mystery_01
2017-08-05T14:00+00:00  2017-08-06T14:00+00:00  mystery_01
2017-09-02T04:00+00:00  2017-09-03T04:00+00:00  mystery_02
2017-09-02T14:00+00:00  2017-09-03T14:00+00:00  mystery_02
2017-09-09T06:00+00:00  2017-09-10T06:00+00:00  mystery_03
2017-10-07T14:00+00:00  2017-10-08T14:00+00:00  mystery_03
2017-10-14T04:00+00:00  2017-10-15T04:00+00:00  mystery_04
2017-10-14T04:00+00:00  2017-10-15T04:00+00:00  mystery_04
2017-11-04T15:00+00:00  2017-11-05T15:00+00:00  mystery_04
2017-11-11T06:00+00:00  2017-11-12T06:00+00:00  mystery_02
2017-11-18T05:00+00:00  2017-11-19T05:00+00:00  mystery_03
2017-12-09T06:00+00:00  2017-12-10T06:00+00:00  mystery_05
2017-12-09T15:00+00:00  2017-12-10T15:00+00:00  mystery_05
2017-12-16T05:00+00:00  2017-12-17T05:00+00:00  mystery_05
2018-01-13T04:00+00:00  2018-01-14T04:00+00:00  mystery_06
2018-01-13T06:00+00:00  2018-01-14T06:00+00:00  mystery_06
2018-01-13T12:00+00:00  2018-01-14T12:00+00:00  mystery_06
2018-02-03T06:00+00:00  2018-02-04T06:00+00:00  mystery_07
2018-02-10T14:00+00:00  2018-02-11T14:00+00:00  mystery_07
2018-02-17T05:00+00:00  2018-02-18T05:00+00:00  mystery_07
2018-03-03T06:00+00:00  2018-03-04T06:00+00:00  mystery_08
2018-03-10T04:00+00:00  2018-03-11T04:00+00:00  mystery_08
2018-03-10T14:00+00:00  2018-03-11T14:00+00:00  mystery_08
2018-03-24T06:00+00:00  2018-03-25T06:00+00:00  mystery_09
2018-04-07T04:00+00:00  2018-04-08T04:00+00:00  mystery_09
2018-04-07T13:00+00:00  2018-04-08T13:00+00:00  mystery_09
2018-04-21T06:00+00:00  2018-04-22T06:00+00:00  mystery_10
2018-05-05T04:00+00:00  2018-05-06T04:00+00:00  mystery_02
2018-05-05T14:00+00:00  2018-05-06T14:00+00:00  mystery_02
2018-05-12T04:00+00:00  2018-05-13T04:00+00:00  mystery_03
2018-05-12T14:00+00:00  2018-05-13T14:00+00:00  mystery_03
# 2018-05-19T04:00+00:00  2018-05-20T04:00+00:00  mystery_11
# 2018-05-19T06:00+00:00  2018-05-20T06:00+00:00  mystery_03
# 2018-05-19T14:00+00:00  2018-05-20T14:00+00:00  mystery_11
2018-05-26T06:00+00:00  2018-05-27T06:00+00:00  mystery_04
2018-06-09T06:00+00:00  2018-06-10T06:00+00:00  mystery_11
2018-06-23T04:00+00:00  2018-06-24T04:00+00:00  mystery_10
2018-06-23T14:00+00:00  2018-06-24T14:00+00:00  mystery_10
2018-07-21T04:00+00:00  2018-07-22T04:00+00:00  mystery_12
2018-07-21T06:00+00:00  2018-07-22T06:00+00:00  mystery_12
2018-07-21T14:00+00:00  2018-07-22T14:00+00:00  mystery_12
2018-08-18T06:00+00:00  2018-08-19T06:00+00:00  mystery_13
2018-08-18T14:00+00:00  2018-08-19T14:00+00:00  mystery_13
2018-08-25T04:00+00:00  2018-08-26T04:00+00:00  mystery_13
2018-09-22T04:00+00:00  2018-09-23T04:00+00:00  mystery_14
2018-09-22T14:00+00:00  2018-09-23T14:00+00:00  mystery_14
2018-09-23T06:00+00:00  2018-09-24T06:00+00:00  mystery_14
2018-10-19T08:00+00:00  2018-10-21T08:00+00:00  mystery_15
2018-10-19T14:00+00:00  2018-10-21T14:00+00:00  mystery_15
2018-10-19T22:00+00:00  2018-10-21T22:00+00:00  mystery_15
2018-11-10T06:00+00:00  2018-11-11T06:00+00:00  mystery_16
2018-11-17T04:00+00:00  2018-11-18T04:00+00:00  mystery_16
2018-11-24T14:00+00:00  2018-11-25T14:00+00:00  mystery_16
2018-12-15T04:00+00:00  2018-12-16T04:00+00:00  mystery_17
2018-12-15T08:00+00:00  2018-12-16T08:00+00:00  mystery_17
2018-12-15T14:00+00:00  2018-12-16T14:00+00:00  mystery_17
2019-01-04T08:00+00:00  2019-01-06T08:00+00:00  mystery_18
2019-01-04T14:00+00:00  2019-01-06T14:00+00:00  mystery_18
2019-01-04T22:00+00:00  2019-01-06T22:00+00:00  mystery_18
2019-02-02T06:00+00:00  2019-02-03T06:00+00:00  mystery_19
2019-02-09T04:00+00:00  2019-02-10T04:00+00:00  mystery_19
2019-02-09T14:00+00:00  2019-02-10T14:00+00:00  mystery_19
2019-03-16T04:00+00:00  2019-03-17T04:00+00:00  mystery_20
2019-03-16T06:00+00:00  2019-03-17T06:00+00:00  mystery_20
2019-03-16T12:00+00:00  2019-03-17T12:00+00:00  mystery_20
2019-04-19T08:00+00:00  2019-04-21T08:00+00:00  mystery_21
2019-04-19T14:00+00:00  2019-04-21T14:00+00:00  mystery_21
2019-04-19T22:00+00:00  2019-04-21T22:00+00:00  mystery_21
2019-05-11T06:00+00:00  2019-05-12T06:00+00:00  mystery_22
2019-05-18T04:00+00:00  2019-05-19T04:00+00:00  mystery_22
2019-05-18T12:00+00:00  2019-05-19T12:00+00:00  mystery_22
2019-06-15T04:00+00:00  2019-06-16T04:00+00:00  mystery_23
2019-06-15T04:00+00:00  2019-06-16T04:00+00:00  mystery_23
2019-06-15T14:00+00:00  2019-06-16T14:00+00:00  mystery_23
