<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\dlStats3;

use app\models\Battle3;
use app\models\BattlePlayer3;
use app\models\GearConfigurationSecondary3;
use app\models\Rank3;
use app\models\Salmon3;
use app\models\Season3;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use function array_map;
use function array_slice;
use function filter_var;
use function gettype;
use function is_bool;
use function is_float;
use function is_int;
use function ksort;
use function sprintf;
use function strtotime;
use function uasort;

use const FILTER_VALIDATE_FLOAT;
use const SORT_ASC;
use const SORT_STRING;

trait ModelUtilTrait
{
    private static function season(Battle3|Salmon3 $battle): string
    {
        static $seasons = [];
        if (!$seasons) {
            $seasons = ArrayHelper::getColumn(
                Season3::find()->orderBY(['start_at' => SORT_ASC])->all(),
                fn (Season3 $model): array => [
                    'start' => strtotime($model->start_at),
                    'end' => strtotime($model->end_at),
                    'name' => $model->name,
                ],
            );
        }

        $t = strtotime($battle->start_at);
        foreach ($seasons as $season) {
            if ($season['start'] <= $t && $t < $season['end']) {
                return $season['name'];
            }
        }

        return '';
    }

    private static function rank(?Rank3 $rank, ?int $splus): string
    {
        return match ($rank?->key) {
            null => '',
            's+' => $splus === null ? $rank->name : sprintf('%s %d', $rank->name, $splus),
            default => $rank->name,
        };
    }

    private static function powerFormat(int|float|string|null $power): string
    {
        $power = filter_var($power, FILTER_VALIDATE_FLOAT);
        return is_float($power) ? sprintf('%.1f', $power) : '';
    }

    private static function gearAbilities(?BattlePlayer3 $player): string
    {
        if (!$player) {
            return '';
        }

        $gears = [
            $player->headgear,
            $player->clothing,
            $player->shoes,
        ];

        $results = [];
        foreach ($gears as $gear) {
            if ($gear?->ability === null) {
                return '';
            }

            // メイン
            $key = $gear->ability->key;
            $double = $key === 'ability_doubler';
            $results[$key] = $gear->ability->primary_only
                ? true
                : ($results[$key] ?? 0) + 10;

            // サブ
            $subs = ArrayHelper::sort(
                $gear->gearConfigurationSecondary3s,
                fn (GearConfigurationSecondary3 $a, GearConfigurationSecondary3 $b): int => $a->id <=> $b->id,
            );

            foreach (array_slice($subs, 0, 3) as $sub) {
                $key = $sub?->ability?->key;
                if ($key) {
                    $results[$key] = ($results[$key] ?? 0) + ($double ? 6 : 3);
                }
            }
        }

        ksort($results, SORT_STRING);
        uasort(
            $results,
            function (int|bool $a, int|bool $b): int {
                if (gettype($a) !== gettype($b)) {
                    return is_bool($a) ? 1 : -1;
                }

                return $b <=> $a;
            },
        );

        return Json::encode(
            array_map(
                fn (int|bool $v) => is_int($v) ? $v / 10 : $v,
                $results,
            ),
        );
    }
}
