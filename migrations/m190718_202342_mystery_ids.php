<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190718_202342_mystery_ids extends Migration
{
    public function safeUp()
    {
        $db = Yii::$app->db;
        $data = $this->getData();
        $case = vsprintf('(CASE %s %s END)', [
            $db->quoteColumnName('key'),
            implode(' ', array_map(
                function (string $key, int $id) use ($db): string {
                    return vsprintf('WHEN %s THEN %s', [
                        $db->quoteValue($key),
                        $db->quoteValue($id),
                    ]);
                },
                array_keys($data),
                array_values($data),
            )),
        ]);
        $sql = vsprintf('UPDATE %1$s SET %4$s = %5$s WHERE %2$s IN (%3$s)', [
            $db->quoteTableName('map2'),
            $db->quoteColumnName('key'),
            implode(', ', array_map(
                function (string $key) use ($db) {
                    return $db->quoteValue($key);
                },
                array_keys($data),
            )),
            $db->quoteColumnName('splatnet'),
            $case,
        ]);
        $this->execute($sql);
    }

    public function safeDown()
    {
        $this->update(
            'map2',
            ['splatnet' => null],
            ['key' => array_keys($this->getData())],
        );
    }

    public function getData(): array
    {
        return [
            'mystery_01' => 101,
            'mystery_02' => 102,
            'mystery_03' => 103,
            'mystery_04' => 100,
            'mystery_05' => 107,
            // 'mystery_06' => null,
            // 'mystery_07' => null,
            // 'mystery_08' => null,
            // 'mystery_09' => null,
            // 'mystery_10' => null,
            // 'mystery_11' => null,
            'mystery_12' => 113,
            // 'mystery_13' => null,
            // 'mystery_14' => null,
            'mystery_15' => 115,
            'mystery_16' => 116,
            'mystery_17' => 117,
            'mystery_18' => 118,
            // 'mystery_19' => null,
            // 'mystery_20' => null,
            // 'mystery_21' => null,
            // 'mystery_22' => null,
            // 'mystery_23' => null,
            // 'mystery_24' => null,
        ];
    }
}
