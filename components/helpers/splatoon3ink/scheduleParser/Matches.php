<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\splatoon3ink\scheduleParser;

use Exception;
use app\models\Map3;
use app\models\Map3Alias;
use app\models\Rule3;
use app\models\Rule3Alias;
use app\models\api\v3\postBattle\TypeHelperTrait;
use yii\helpers\ArrayHelper;

use function array_map;
use function array_values;
use function ceil;
use function filter_var;
use function is_int;
use function strtolower;
use function strtotime;
use function usort;

use const FILTER_VALIDATE_INT;

trait Matches
{
    use TypeHelperTrait;

    protected static function regularMatch(array $nodes): array
    {
        return self::process($nodes, 'regularMatchSetting', null);
    }

    protected static function bankaraOpen(array $nodes): array
    {
        return self::process($nodes, 'bankaraMatchSettings', 'OPEN');
    }

    protected static function bankaraChallenge(array $nodes): array
    {
        return self::process($nodes, 'bankaraMatchSettings', 'CHALLENGE');
    }

    protected static function xMatch(array $nodes): array
    {
        return self::process($nodes, 'xMatchSetting', null);
    }

    protected static function leagueMatch(array $nodes): array
    {
        return self::process($nodes, 'leagueMatchSetting', null);
    }

    private static function process(array $nodes, string $structureKey, ?string $modeKey = null): array
    {
        $results = [];
        foreach ($nodes as $node) {
            if ($modeKey === null) {
                $results[] = self::processNode(
                    ArrayHelper::getValue($node, $structureKey),
                    ArrayHelper::getValue($node, 'startTime'),
                );
            } else {
                // バンカラマッチは $structureKey で示される構造がさらに配列になっている
                foreach (ArrayHelper::getValue($node, $structureKey) as $settingNode) {
                    if (ArrayHelper::getValue($settingNode, 'mode') === $modeKey) {
                        $results[] = self::processNode(
                            $settingNode,
                            ArrayHelper::getValue($node, 'startTime'),
                        );
                    }
                }
            }
        }

        usort(
            $results,
            fn (array $a, array $b): int => $a['period'] <=> $b['period']
        );

        return array_values($results);
    }

    private static function processNode(array $dataStructure, string $startTimeStr): array
    {
        return [
            'period' => self::calcPeriod($startTimeStr),
            'rule_id' => self::rule(strtolower(ArrayHelper::getValue($dataStructure, 'vsRule.rule'))),
            'map_ids' => array_map(
                fn (array $stage) => self::map(ArrayHelper::getValue($stage, 'vsStageId')),
                ArrayHelper::getValue($dataStructure, 'vsStages'),
            ),
        ];
    }

    private static function calcPeriod(string $startTimeStr): int
    {
        $timestamp = filter_var(
            @strtotime($startTimeStr),
            FILTER_VALIDATE_INT,
        );
        if (is_int($timestamp)) {
            return (int)ceil($timestamp / 7200);
        }

        throw new Exception("Failed to parse timestamp \"{$startTimeStr}\"");
    }

    private static function rule(string $splatnetKey): ?int
    {
        return self::key2id($splatnetKey, Rule3::class, Rule3Alias::class, 'rule_id');
    }

    private static function map(int $vsStageId): ?int
    {
        return self::key2id((string)$vsStageId, Map3::class, Map3Alias::class, 'map_id');
    }
}
