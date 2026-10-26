<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\query;

use DateTimeZone;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\db\Now;
use app\models\TurfwarWinBonus;
use yii\db\ActiveQuery;
use yii\db\Expression;

use function is_numeric;

/**
 * @extends ActiveQuery<TurfwarWinBonus>
 */
final class TurfwarWinBonusQuery extends ActiveQuery
{
    public function current(): self
    {
        return $this->at(new Now());
    }

    /**
     * @param Expression|int|string $time
     */
    public function at($time): self
    {
        return $this
            ->orderBy('[[start_at]] DESC')
            ->limit(1)
            ->andWhere(['<=', '[[start_at]]',
                $time instanceof Expression
                        ? $time
                        : (is_numeric($time)
                            ? DateTimeFormatter::unixTimeToString($time, new DateTimeZone('Etc/UTC'))
                            : (string)$time
                        ),
            ]);
    }
}
