<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190719_054921_mystery_ids extends Migration
{
    public function safeUp()
    {
        $db = Yii::$app->db;
        $data = $this->getData();
        $case = vsprintf('(CASE %s %s END)', [
            $db->quoteColumnName('key'),
            implode(' ', array_map(
                fn (string $key, int $id): string => vsprintf('WHEN %s THEN %s', [
                    $db->quoteValue($key),
                    $db->quoteValue($id),
                ]),
                array_keys($data),
                array_values($data),
            )),
        ]);
        $sql = vsprintf('UPDATE %1$s SET %4$s = %5$s WHERE %2$s IN (%3$s)', [
            $db->quoteTableName('map2'),
            $db->quoteColumnName('key'),
            implode(', ', array_map(
                fn (string $key) => $db->quoteValue($key),
                array_keys($data)
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
            ['key' => array_keys($this->getData())]
        );
    }

    public function getData(): array
    {
        return [
            'mystery_07' => 105,
            'mystery_08' => 110,
            'mystery_09' => 108,
            'mystery_10' => 109,
            'mystery_20' => 120,
            'mystery_21' => 121,
            'mystery_22' => 122,
            'mystery_23' => 123,
            'mystery_24' => 124, // maybe...
        ];
    }
}
