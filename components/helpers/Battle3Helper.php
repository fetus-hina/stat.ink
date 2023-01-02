<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

final class Battle3Helper
{
    /**
     * @return string[]
     */
    public static function getRelationsForApiResponse(bool $listed): array
    {
        $relations = $listed
            ? [
                'agent',
                'battleImageGear3',
                'battleImageJudge3',
                'battleImageResult3',
                'festDragon',
                'lobby',
                'map',
                'medals',
                'ourTeamRole',
                'ourTeamTheme',
                'rankAfter',
                'rankBefore',
                'result',
                'rule',
                'theirTeamRole',
                'theirTeamTheme',
                'thirdTeamRole',
                'thirdTeamTheme',
                'user',
                'variables',
                'version',
                'weapon',
                'weapon.canonical',
                'weapon.mainweapon',
                'weapon.mainweapon.type',
                'weapon.special',
                'weapon.subweapon',
                'weapon.weapon3Aliases',
            ]
            : [];

        foreach (['battlePlayer3s', 'battleTricolorPlayer3s'] as $playerTable) {
            $relations[] = $playerTable;
            $relations[] = "{$playerTable}.splashtagTitle";
            $relations[] = "{$playerTable}.weapon";
            $relations[] = "{$playerTable}.weapon.canonical";
            $relations[] = "{$playerTable}.weapon.mainweapon";
            $relations[] = "{$playerTable}.weapon.mainweapon.type";
            $relations[] = "{$playerTable}.weapon.special";
            $relations[] = "{$playerTable}.weapon.subweapon";
            $relations[] = "{$playerTable}.weapon.weapon3Aliases";

            foreach (['clothing', 'headgear', 'shoes'] as $gearTable) {
                $relations[] = "{$playerTable}.{$gearTable}";
                $relations[] = "{$playerTable}.{$gearTable}.ability";
                $relations[] = "{$playerTable}.{$gearTable}.gearConfigurationSecondary3s";
                $relations[] = "{$playerTable}.{$gearTable}.gearConfigurationSecondary3s.ability";
            }
        }

        return $relations;
    }
}
