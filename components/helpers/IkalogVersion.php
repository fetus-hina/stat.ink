<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

use app\models\Agent;
use app\models\Battle;
use app\models\IkalogVersion as ARIkalogVersion;

class IkalogVersion
{
    const V2_7_0_MINIMUM_REVISION = '16bb777';

    public static function isOutdated(Battle $battle) : bool
    {
        $agent = $battle->agent;
        if (!$agent || !static::isIkaLog($agent)) {
            return false;
        }

        $gitRevision = static::extractGitRevision($agent);
        if (!$gitRevision) {
            return false;
        }

        // とりあえず目の前で必要なこととして、
        // v2.7.0 のバトルで v2.7.0 非対応クライアントを使っていれば怒る
        $gameVersion = $battle->splatoonVersion;
        if (!$gameVersion || $gameVersion->tag !== '2.7.0') {
            return false;
        }

        $agentRevision = ARIkalogVersion::findOneByRevision($gitRevision);
        if (!$agentRevision) {
            return false;
        }

        $minimumRevision = ARIkalogVersion::findOneByRevision(static::V2_7_0_MINIMUM_REVISION);
        if (!$minimumRevision) {
            return false;
        }

        if (strtotime($agentRevision->at) <= strtotime($minimumRevision->at)) {
            return true;
        }

        return false;
    }

    public static function isIkaLog(Agent $agent) : bool
    {
        return $agent->name === 'IkaLog' || $agent->name === 'TakoLog';
    }

    public static function extractGitRevision(Agent $agent)
    {
        if (!static::isIkaLog($agent)) {
            return null;
        }

        if (!preg_match('/^([0-9a-f]{7,})/', $agent->version, $match)) {
            return null;
        }

        return $match[1];
    }
}
