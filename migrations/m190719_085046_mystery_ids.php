<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190719_085046_mystery_ids extends Migration
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
            'mystery_11' => 111,
        ];
    }
}
