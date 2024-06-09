<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\schedule;

use app\models\SalmonSchedule3;
use app\models\SalmonScheduleWeapon3;
use app\models\SalmonWeapon3;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use function implode;
use function sprintf;
use function vsprintf;

use const SORT_ASC;
use const SORT_DESC;

/**
 * @phpstan-type EventStats array{
 *   weapon_id: int,
 *   normal_waves: int,
 *   normal_cleared: int,
 *   xtra_waves: int,
 *   xtra_cleared: int,
 * }
 */
trait WeaponTrait
{
    /**
     * @return array<int, WeaponStats>
     */
    private function getWeaponStats(Connection $db, User $user, SalmonSchedule3 $schedule): array
    {
        $waves = $schedule->is_eggstra_work ? 5 : 3;
        $maxWaves = $schedule->is_eggstra_work ? 5 : 4;
        return ArrayHelper::index(
            (new Query())
                ->select([
                    'weapon_id' => '{{%salmon_player_weapon3}}.[[weapon_id]]',
                    'normal_waves' => sprintf(
                        'SUM(CASE %s END)',
                        implode(' ', [
                            "WHEN {{%salmon_player_weapon3}}.[[wave]] BETWEEN 1 AND {$waves} THEN 1",
                            'ELSE 0',
                        ]),
                    ),
                    'normal_cleared' => sprintf(
                        'SUM(CASE %s END)',
                        implode(' ', [
                            vsprintf('WHEN (%s) AND (%s) THEN 1', [
                                "{{%salmon_player_weapon3}}.[[wave]] BETWEEN 1 AND {$waves}",
                                "{{%salmon3}}.[[clear_waves]] >= {$waves}",
                            ]),
                            'ELSE 0',
                        ]),
                    ),
                    'xtra_waves' => sprintf(
                        'SUM(CASE %s END)',
                        implode(' ', [
                            "WHEN {{%salmon_player_weapon3}}.[[wave]] <= {$waves} THEN 0",
                            'WHEN {{%salmon3}}.[[king_salmonid_id]] IS NULL THEN 0',
                            'WHEN {{%salmon3}}.[[clear_extra]] IS NULL THEN 0',
                            'ELSE 1',
                        ]),
                    ),
                    'xtra_cleared' => sprintf(
                        'SUM(CASE %s END)',
                        implode(' ', [
                            "WHEN {{%salmon_player_weapon3}}.[[wave]] <= {$waves} THEN 0",
                            'WHEN {{%salmon3}}.[[king_salmonid_id]] IS NULL THEN 0',
                            'WHEN {{%salmon3}}.[[clear_extra]] <> TRUE THEN 0',
                            'ELSE 1',
                        ]),
                    ),
                ])
                ->from('{{%salmon3}}')
                ->innerJoin(
                    '{{%salmon_player3}}',
                    implode(' AND ', [
                        '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                        '{{%salmon_player3}}.[[is_me]] = TRUE',
                    ]),
                )
                ->innerJoin(
                    '{{%salmon_player_weapon3}}',
                    '{{%salmon_player3}}.[[id]] = {{%salmon_player_weapon3}}.[[player_id]]',
                )
                ->andWhere(['and',
                    [
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                        '{{%salmon3}}.[[schedule_id]]' => $schedule->id,
                        '{{%salmon3}}.[[user_id]]' => $user->id,
                    ],
                    ['between', '{{%salmon_player_weapon3}}.[[wave]]', 1, $maxWaves],
                    ['not', ['{{%salmon_player_weapon3}}.[[weapon_id]]' => null]],
                ])
                ->groupBy([
                    '{{%salmon_player_weapon3}}.[[weapon_id]]',
                ])
                ->orderBy([
                    'COUNT(*)' => SORT_DESC,
                    'normal_waves' => SORT_DESC,
                    'xtra_waves' => SORT_DESC,
                    'weapon_id' => SORT_ASC,
                ])
                ->all($db),
            'weapon_id',
        );
    }

    /**
     * @return array<int, SalmonWeapon3>
     */
    private function getWeapons(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonWeapon3::find()
                ->andWhere(['not', ['key' => ['splatscope', 'liter4k_scope']]])
                ->orderBy(['id' => SORT_ASC])
                ->all(),
            'id',
        );
    }

    private function isRandomWeaponSchedule(Connection $connection, SalmonSchedule3 $schedule): bool
    {
        return array_reduce(
            $schedule->salmonScheduleWeapon3s,
            fn (bool $carry, SalmonScheduleWeapon3 $weapon): bool => $carry || $weapon->random_id !== null,
            false,
        );
    }
}
