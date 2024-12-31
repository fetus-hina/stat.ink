<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\db;

use Yii;
use yii\db\Expression;

use function array_keys;
use function array_map;
use function array_values;
use function implode;
use function vsprintf;

trait StageMigration
{
    protected function setArea(array $list): void
    {
        $db = Yii::$app->db;
        $value = new Expression(vsprintf('(CASE %s %s END)', [
            $db->quoteColumnName('key'),
            (fn (): string => implode(' ', array_map(
                fn (string $key, ?int $area): string => vsprintf('WHEN %s THEN %s', [
                    $db->quoteValue($key),
                    $area === null ? 'NULL' : $db->quoteValue($area),
                ]),
                array_keys($list),
                array_values($list),
            )))(),
        ]));
        $this->execute(
            $db->createCommand()
                ->update('map2', ['area' => $value], ['key' => array_keys($list)])
                ->rawSql,
        );
    }
}
