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
use app\models\Map3;
use app\models\Map3Alias;
use app\models\Rule3;
use app\models\Rule3Alias;
use app\models\api\v3\postBattle\TypeHelperTrait;
use yii\helpers\ArrayHelper;

use function array_filter;
use function array_map;
use function array_values;
use function fwrite;
use function strtolower;

use const STDERR;

trait Event
{
    use Common;
    use TypeHelperTrait;

    protected static function event(array $nodes): array
    {
        return array_values(
            array_filter(
                array_map(
                    fn (array $schedule): ?array => self::processEventSchedule($schedule),
                    $nodes,
                ),
            ),
        );
    }

    private static function processEventSchedule(array $schedule): ?array
    {
        try {
            return [
                'id' => TypeHelper::string(
                    ArrayHelper::getValue($schedule, 'leagueMatchSetting.leagueMatchEvent.id'),
                ),
                'name' => TypeHelper::string(
                    ArrayHelper::getValue($schedule, 'leagueMatchSetting.leagueMatchEvent.name'),
                ),
                'desc' => TypeHelper::stringOrNull(
                    ArrayHelper::getValue($schedule, 'leagueMatchSetting.leagueMatchEvent.desc'),
                ),
                'regulation' => TypeHelper::stringOrNull(
                    ArrayHelper::getValue(
                        $schedule,
                        'leagueMatchSetting.leagueMatchEvent.regulation',
                    ),
                ),
                'rule_id' => TypeHelper::int(
                    self::key2id(
                        strtolower(
                            TypeHelper::string(
                                ArrayHelper::getValue($schedule, 'leagueMatchSetting.vsRule.rule'),
                            ),
                        ),
                        Rule3::class,
                        Rule3Alias::class,
                        'rule_id',
                    ),
                ),
                'map_ids' => array_map(
                    fn (array $stage): int => TypeHelper::int(
                        self::key2id(
                            (string)TypeHelper::int(
                                ArrayHelper::getValue($stage, 'vsStageId'),
                            ),
                            Map3::class,
                            Map3Alias::class,
                            'map_id',
                        ),
                    ),
                    ArrayHelper::getValue($schedule, 'leagueMatchSetting.vsStages'),
                ),
                'periods' => array_map(
                    fn (array $period): array => [
                        'start_at' => self::parseTimestamp(
                            TypeHelper::string(
                                ArrayHelper::getValue($period, 'startTime'),
                            ),
                        ),
                        'end_at' => self::parseTimestamp(
                            TypeHelper::string(
                                ArrayHelper::getVAlue($period, 'endTime'),
                            ),
                        ),
                    ],
                    ArrayHelper::getValue($schedule, 'timePeriods'),
                ),
            ];
        } catch (Throwable $e) {
            fwrite(STDERR, __METHOD__ . '(): Exception: ' . $e->getMessage() . "\n");
            return null;
        }
    }
}
