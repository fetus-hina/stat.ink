<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\splatoon3ink;

use app\components\helpers\splatoon3ink\scheduleParser\Event;
use app\components\helpers\splatoon3ink\scheduleParser\Festivals;
use app\components\helpers\splatoon3ink\scheduleParser\Matches;
use app\components\helpers\splatoon3ink\scheduleParser\Salmon;
use yii\helpers\ArrayHelper;

final class ScheduleParser
{
    use Event;
    use Festivals;
    use Matches;
    use Salmon;

    public static function parseAll(array $json): array
    {
        // keys are lobby3.key
        return [
            'regular' => self::regularMatch(
                ArrayHelper::getValue($json, 'data.regularSchedules.nodes'),
            ),
            'bankara_open' => self::bankaraOpen(
                ArrayHelper::getValue($json, 'data.bankaraSchedules.nodes'),
            ),
            'bankara_challenge' => self::bankaraChallenge(
                ArrayHelper::getValue($json, 'data.bankaraSchedules.nodes'),
            ),
            'xmatch' => self::xMatch(
                ArrayHelper::getValue($json, 'data.xSchedules.nodes'),
            ),
            'event' => self::event(
                ArrayHelper::getValue($json, 'data.eventSchedules.nodes'),
            ),
            'splatfest_open' => self::splatfestMatch(
                ArrayHelper::getValue($json, 'data.festSchedules.nodes'),
            ),
            'salmon_regular' => self::salmon(
                ArrayHelper::getValue($json, 'data.coopGroupingSchedule.regularSchedules.nodes'),
                isBigRun: false,
            ),
            'salmon_bigrun' => self::salmon(
                ArrayHelper::getValue($json, 'data.coopGroupingSchedule.bigRunSchedules.nodes'),
                isBigRun: true,
            ),
            'salmon_eggstra' => self::salmon(
                ArrayHelper::getValue($json, 'data.coopGroupingSchedule.teamContestSchedules.nodes'),
                isBigRun: false,
            ),
        ];
    }

    public static function parseFestivals(array $json): array
    {
        return self::festivals($json);
    }
}
