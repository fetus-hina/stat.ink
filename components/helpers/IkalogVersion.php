<?php

/**
 * @copyright Copyright (C) 2016-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\helpers;

use app\models\Agent;
use app\models\Battle;
use app\models\IkalogVersion as ARIkalogVersion;

use function in_array;
use function preg_match;
use function strtotime;

class IkalogVersion
{
    public const V2_7_0_MINIMUM_REVISION = '16bb777';
    public const V2_8_0_MINIMUM_REVISION = '579408a';

    public static function isOutdated(Battle $battle): bool
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
        // v2.7.0/v2.8.0 のバトルでそのバージョンに非対応のクライアントを使っていれば怒る
        $gameVersion = $battle->splatoonVersion;
        if (!$gameVersion) {
            return false;
        }
        if (!in_array($gameVersion->tag, ['2.7.0', '2.8.0'], true)) {
            return false;
        }

        $agentRevision = ARIkalogVersion::findOneByRevision($gitRevision);
        if (!$agentRevision) {
            return false;
        }

        $minimumRevision = ARIkalogVersion::findOneByRevision((function ($tag) {
            switch ($tag) {
                case '2.7.0':
                    return static::V2_7_0_MINIMUM_REVISION;
                case '2.8.0':
                    return static::V2_8_0_MINIMUM_REVISION;
                default:
                    return null;
            }
        })($gameVersion->tag));
        if (!$minimumRevision) {
            return false;
        }

        return strtotime($agentRevision->at) < strtotime($minimumRevision->at);
    }

    public static function isIkaLog(Agent $agent): bool
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
