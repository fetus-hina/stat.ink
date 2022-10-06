<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\splatoon3ink;

use app\components\helpers\splatoon3ink\scheduleParser\Matches;
use app\components\helpers\splatoon3ink\scheduleParser\Salmon;
use yii\helpers\ArrayHelper;

final class ScheduleParser
{
    use Matches;
    use Salmon;

    public static function parseAll(array $json): array
    {
        return [
            'regular' => self::regularMatch(
                ArrayHelper::getValue($json, 'data.regularSchedules.nodes')
            ),
            'bankara_open' => self::bankaraOpen(
                ArrayHelper::getValue($json, 'data.bankaraSchedules.nodes')
            ),
            'bankara_challenge' => self::bankaraChallenge(
                ArrayHelper::getValue($json, 'data.bankaraSchedules.nodes')
            ),
            'xmatch' => self::xMatch(
                ArrayHelper::getValue($json, 'data.xSchedules.nodes')
            ),
            'league' => self::leagueMatch(
                ArrayHelper::getValue($json, 'data.leagueSchedules.nodes')
            ),
            'salmon_regular' => self::salmon(
                ArrayHelper::getValue($json, 'data.coopGroupingSchedule.regularSchedules.nodes')
            ),
        ];
    }
}
