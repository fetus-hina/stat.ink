<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\splatoon3ink\scheduleParser;

use Throwable;
use app\components\helpers\TypeHelper;
use app\models\api\v3\postBattle\TypeHelperTrait;
use yii\helpers\ArrayHelper;

use function fwrite;
use function is_array;
use function is_string;
use function max;
use function min;
use function round;
use function uasort;
use function vsprintf;

use const STDERR;

trait Festivals
{
    use TypeHelperTrait;
    use Common;

    protected static function festivals(array $nodes): array
    {
        $festivals = [];

        // 日本でのみ実施されるフェスは情報が日本語で格納されているので、
        // 他の地域を先に処理することで日本語が格納されるのを最小限にする
        foreach (['US', 'EU', 'AP', 'JP'] as $regionCode) {
            $festNodes = ArrayHelper::getValue($nodes, [$regionCode, 'data', 'festRecords', 'nodes']);
            if (is_array($festNodes) && $festNodes) {
                foreach ($festNodes as $festNode) {
                    $id = ArrayHelper::getValue($festNode, '__splatoon3ink_id');
                    if (!$id || !is_string($id)) {
                        fwrite(STDERR, "Festival ID is not string\n");
                        continue;
                    }

                    if (!isset($festivals[$id])) {
                        try {
                            $festivals[$id] = static::parseFestivalNode($festNode);
                        } catch (Throwable $e) {
                            fwrite(STDERR, __METHOD__ . '():' . __LINE__ . ': ' . $e->getMessage() . "\n");
                            continue;
                        }
                    }

                    $festivals[$id]['regions'][] = $regionCode;
                }
            }
        }

        uasort(
            $festivals,
            fn (array $a, array $b): int => ($a['startAt'] ?? 0) <=> ($b['startAt'] ?? 0),
        );

        return $festivals;
    }

    private static function parseFestivalNode(array $node): array
    {
        return [
            'title' => TypeHelper::string(ArrayHelper::getValue($node, 'title')),
            'startAt' => self::parseTimestamp(
                TypeHelper::string(ArrayHelper::getValue($node, 'startTime')),
            ),
            'endAt' => self::parseTimestamp(
                TypeHelper::string(ArrayHelper::getValue($node, 'endTime')),
            ),
            'teams' => [
                'alpha' => self::parseFestivalTeam(
                    TypeHelper::array(ArrayHelper::getValue($node, 'teams.0')),
                ),
                'bravo' => self::parseFestivalTeam(
                    TypeHelper::array(ArrayHelper::getValue($node, 'teams.1')),
                ),
                'charlie' => self::parseFestivalTeam(
                    TypeHelper::array(ArrayHelper::getValue($node, 'teams.2')),
                ),
            ],
            'regions' => [],
        ];
    }

    private static function parseFestivalTeam(array $node): array
    {
        return [
            'name' => TypeHelper::string(ArrayHelper::getValue($node, 'teamName')),
            'color' => self::festivalTeamColor(
                TypeHelper::array(ArrayHelper::getValue($node, 'color')),
            ),
        ];
    }

    private static function festivalTeamColor(array $color): string
    {
        $r = round(TypeHelper::float(ArrayHelper::getValue($color, 'r')) * 255);
        $g = round(TypeHelper::float(ArrayHelper::getValue($color, 'g')) * 255);
        $b = round(TypeHelper::float(ArrayHelper::getValue($color, 'b')) * 255);
        $a = round(TypeHelper::float(ArrayHelper::getValue($color, 'a')) * 255);

        return vsprintf('%02x%02x%02x%02x', [
            min(255, max(0, $r)),
            min(255, max(0, $g)),
            min(255, max(0, $b)),
            min(255, max(0, $a)),
        ]);
    }
}
