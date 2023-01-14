<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use DomainException;
use LogicException;
use Yii;
use app\components\helpers\Color;
use app\models\StatInkColor3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

use function array_map;
use function array_slice;
use function hexdec;
use function pow;
use function preg_match;
use function sqrt;
use function strcasecmp;
use function substr;

use const SORT_DESC;

final class InkColor3Action extends Action
{
    public function run(): string|Response
    {
        $controller = $this->controller;
        if (!$controller instanceof Controller) {
            throw new LogicException();
        }

        return $controller->render(
            'v3/ink-color3',
            Yii::$app->db->transaction(
                fn (Connection $db): array => [
                    'models' => self::makeData($db),
                ],
                Transaction::REPEATABLE_READ,
            ),
        );
    }

    /**
     * @return StatInkColor3[]
     */
    private static function makeData(Connection $db): array
    {
        $data = StatInkColor3::find()
            ->orderBy([
                'battles' => SORT_DESC,
                'wins' => SORT_DESC,
                'color1' => SORT_DESC,
                'color2' => SORT_DESC,
            ])
            ->all();

        $results = [];
        foreach ($data as $model) {
            $merged = false;
            foreach ($results as $resultModel) {
                if (self::isMergeable($model, $resultModel)) {
                    $resultModel->battles += $model->battles;
                    $resultModel->wins += $model->wins;
                    $merged = true;
                    break;
                }
            }

            if (!$merged) {
                $results[] = $model;
            }
        }

        // color1 の方が明るいとき、色を入れ替える
        $results = ArrayHelper::getColumn(
            $results,
            function (StatInkColor3 $model): StatInkColor3 {
                $y1 = self::brightness($model->color1);
                $y2 = self::brightness($model->color2);
                if ($y1 > $y2) {
                    [$model->color1, $model->color2] = [$model->color2, $model->color1];
                    $model->wins = $model->battles - $model->wins;
                }
                return $model;
            },
        );

        return ArrayHelper::sort(
            $results,
            fn (StatInkColor3 $a, StatInkColor3 $b): int => $b->wins / $b->battles <=> $a->wins / $a->battles
                ?: $b->battles <=> $a->battles
                ?: $b->wins <=> $a->wins
                ?: strcasecmp($b->color1, $a->color1)
                ?: strcasecmp($b->color2, $a->color2),
        );
    }

    private static function isMergeable(StatInkColor3 $model1, StatInkColor3 $model2): bool
    {
        return self::isMergeableColor($model1->color1, $model2->color1) &&
            self::isMergeableColor($model1->color2, $model2->color2);
    }

    private static function isMergeableColor(string $color1, string $color2): bool
    {
        $c1 = self::splitRGB($color1);
        $c2 = self::splitRGB($color2);

        // 3D distance
        $distance = sqrt(pow($c1[0] - $c2[0], 2) + pow($c1[1] - $c2[1], 2) + pow($c1[2] - $c2[2], 2));
        return $distance <= 2.0;
    }

    private static function brightness(string $rgb): float
    {
        return Color::getYUVFromRGB(
            hexdec(substr($rgb, 0, 2)),
            hexdec(substr($rgb, 2, 2)),
            hexdec(substr($rgb, 4, 2)),
        )[0];
    }

    /**
     * @return array{int, int, int}
     */
    private static function splitRGB(string $color): array
    {
        if (!preg_match('/^([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})/', $color, $match)) {
            throw new DomainException();
        }

        return array_map(
            fn (string $hex): int => hexdec($hex), // PHP 8.1: hexdec(...)
            array_slice($match, 1, 3),
        );
    }
}
