<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\query;

use DateTimeZone;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\db\Now;
use yii\db\ActiveQuery;
use yii\db\Expression;

use const SORT_DESC;

final class TurfwarWinBonusQuery extends ActiveQuery
{
    public function current(): self
    {
        return $this->at(new Now());
    }

    /**
     * @param Expression|int|float|string $time
     */
    public function at($time): self
    {
        $timeString = $time instanceof Expression
            ? $time
            : (
                is_numeric($time)
                    ? DateTimeFormatter::unixTimeToString(
                        $time,
                        new DateTimeZone('Etc/UTC'),
                    )
                    : (string)$time
            );

        return $this
            ->andWhere(['<=', 'start_at', $timeString])
            ->orderBy(['start_at' => SORT_DESC])
            ->limit(1);
    }
}
