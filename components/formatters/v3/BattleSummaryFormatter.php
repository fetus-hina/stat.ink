<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\v3;

use Yii;
use app\models\Battle3;
use app\models\Map3;
use app\models\Result3;
use app\models\Rule3;

use function array_filter;
use function implode;
use function vsprintf;

final class BattleSummaryFormatter
{
    public static function format(Battle3 $model): string
    {
        return implode(
            ' | ',
            array_filter(
                [
                    self::formatRule($model->rule),
                    self::formatMap($model->map),
                    self::formatResult($model->result, $model->is_knockout, $model->rule),
                ],
                fn (?string $t): bool => $t !== null,
            ),
        );
    }

    private static function formatRule(?Rule3 $rule): ?string
    {
        return $rule
            ? Yii::t('app-rule3', $rule->name)
            : null;
    }

    private static function formatMap(?Map3 $map): ?string
    {
        return $map
            ? Yii::t('app-map3', $map->name)
            : null;
    }

    private static function formatResult(?Result3 $result, ?bool $isKO, ?Rule3 $rule): ?string
    {
        if (!$result) {
            return null;
        }

        if ($rule === null || $isKO === null || $rule->key === 'nawabari') {
            return Yii::t('app', $result->name);
        }

        return vsprintf('%s (%s)', [
            Yii::t('app', $result->name),
            $isKO ? Yii::t('app', 'Knockout') : Yii::t('app', 'Time is up'),
        ]);
    }
}
