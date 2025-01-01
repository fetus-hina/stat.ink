<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\bosses;

use Yii;
use app\models\Salmon3;
use app\models\Salmon3FilterForm;
use app\models\User;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

use function array_merge;

use const SORT_DESC;

trait BadgeStats
{
    /**
     * @return array{type: string, key: string, name: string, defeated: int}[]
     */
    private function makeStatsForBadge(Connection $db, User $user, mixed $cacheCondition): array
    {
        return array_merge(
            $this->makeStatsForKingBadge($db, $user, $cacheCondition),
            $this->makeStatsForBossBadge($db, $user, $cacheCondition),
        );
    }

    /**
     * @return array{type: string, key: string, name: string, defeated: int}[]
     */
    private function makeStatsForKingBadge(Connection $db, User $user, mixed $cacheCondition): array
    {
        $query = Salmon3::find()
            ->innerJoinWith(['kingSalmonid'], false)
            ->andWhere([
                '{{%salmon3}}.[[user_id]]' => $user->id,
                '{{%salmon3}}.[[is_deleted]]' => false,
                '{{%salmon3}}.[[clear_extra]]' => true,
            ])
            ->andWhere(['not', ['{{%salmon3}}.[[king_salmonid_id]]' => null]])
            ->select([
                'key' => 'MAX({{%salmon_king3}}.[[key]])',
                'name' => 'MAX({{%salmon_king3}}.[[name]])',
                'defeated' => 'COUNT(*)',
            ])
            ->groupBy([
                '{{%salmon3}}.[[king_salmonid_id]]',
            ])
            ->orderBy([
                'defeated' => SORT_DESC,
            ]);

        Yii::createObject([
            'class' => Salmon3FilterForm::class,
            'lobby' => Salmon3FilterForm::LOBBY_NOT_PRIVATE,
        ])->decorateQuery($query);

        return Yii::$app->cache->getOrSet(
            [
                $query->createCommand($db)->rawSql,
                $cacheCondition,
            ],
            fn (): array => ArrayHelper::getColumn(
                $query->asArray()->all($db),
                fn (array $row): array => [
                    'type' => 'king',
                    'key' => $row['key'],
                    'name' => $row['name'],
                    'defeated' => (int)$row['defeated'],
                ],
            ),
            3600,
        );
    }

    /**
     * @return array{type: string, key: string, name: string, defeated: int}[]
     */
    private function makeStatsForBossBadge(Connection $db, User $user, mixed $cacheCondition): array
    {
        $query = Salmon3::find()
            ->innerJoinWith(
                [
                    'salmonBossAppearance3s',
                    'salmonBossAppearance3s.boss',
                ],
                false,
            )
            ->andWhere([
                '{{%salmon3}}.[[user_id]]' => $user->id,
                '{{%salmon3}}.[[is_deleted]]' => false,
                '{{%salmon_boss3}}.[[has_badge]]' => true,
            ])
            ->select([
                'key' => 'MAX({{%salmon_boss3}}.[[key]])',
                'name' => 'MAX({{%salmon_boss3}}.[[name]])',
                'defeated' => 'SUM({{%salmon_boss_appearance3}}.[[defeated_by_me]])',
            ])
            ->groupBy([
                '{{%salmon_boss_appearance3}}.[[boss_id]]',
            ])
            ->orderBy([
                'defeated' => SORT_DESC,
            ]);

        Yii::createObject([
            'class' => Salmon3FilterForm::class,
            'lobby' => Salmon3FilterForm::LOBBY_NOT_PRIVATE,
        ])->decorateQuery($query);

        return Yii::$app->cache->getOrSet(
            [
                $query->createCommand($db)->rawSql,
                $cacheCondition,
            ],
            fn (): array => ArrayHelper::getColumn(
                $query->asArray()->all($db),
                fn (array $row): array => [
                    'type' => 'boss',
                    'key' => $row['key'],
                    'name' => $row['name'],
                    'defeated' => (int)$row['defeated'],
                ],
            ),
            3600,
        );
    }
}
