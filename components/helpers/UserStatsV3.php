<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use LogicException;
use PDO;
use Yii;
use app\models\Battle3;
use app\models\Lobby3;
use app\models\Rank3;
use app\models\User;
use app\models\UserStat3;
use yii\db\Connection;
use yii\db\Exception as DbException;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\Json;

use const SORT_ASC;
use const SORT_DESC;

final class UserStatsV3
{
    public static function create(User $user): bool
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('Etc/UTC'));
        return Yii::$app->db->transaction(
            fn (Connection $db): bool => self::createImpl($db, $user, $now),
            Transaction::READ_COMMITTED,
        );
    }

    private static function createImpl(Connection $db, User $user, DateTimeImmutable $now): bool
    {
        try {
            UserStat3::deleteAll(['user_id' => $user->id]);

            $rows = self::createAggregateQuery($db, $user, $now)
                ->createCommand($db)
                ->queryAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                if (!self::saveAggregatedStats($db, $user, $now, $row)) {
                    $db->transaction->rollBack();
                    return false;
                }
            }

            return true;
        } catch (DbException $e) {
            Yii::error(
                \vsprintf('Catch %s, message=%s', [
                    \get_class($e),
                    $e->getMessage(),
                ]),
                __METHOD__,
            );
            $db->transaction->rollBack();
            return false;
        }
    }

    private static function createAggregateQuery(
        Connection $db,
        User $user,
        DateTimeImmutable $now
    ): Query {
        $null = new Expression('NULL');

        return (new Query())
            ->from(['b' => '{{%battle3}}'])
            ->leftJoin(['r' => '{{%result3}}'], '{{b}}.[[result_id]] = {{r}}.[[id]]')
            ->leftJoin(['rank_before' => '{{%rank3}}'], '{{b}}.[[rank_before_id]] = {{rank_before}}.[[id]]')
            ->leftJoin(['rank_after' => '{{%rank3}}'], '{{b}}.[[rank_after_id]] = {{rank_after}}.[[id]]')
            ->andWhere([
                '{{b}}.[[user_id]]' => (int)$user->id,
                '{{b}}.[[is_deleted]]' => false,
            ])
            ->groupBy([
                '{{b}}.[[user_id]]',
                '{{b}}.[[lobby_id]]',
            ])
            ->orderBy([
                '{{b}}.[[user_id]]' => SORT_ASC,
                '{{b}}.[[lobby_id]]' => SORT_ASC,
            ])
            ->select([
                'user_id' => '{{b}}.[[user_id]]',
                'lobby_id' => '{{b}}.[[lobby_id]]',
                'battles' => 'COUNT(*)',
                'agg_battles' => self::sum('1'),
                'agg_seconds' => self::sum(\vsprintf('(%s) - (%s)', [
                    self::timestamp('{{b}}.[[end_at]]'),
                    self::timestamp('{{b}}.[[start_at]]'),
                ])),
                'wins' => self::sum('1', ['{{r}}.[[is_win]] = TRUE']),
                'kills' => self::sum('{{b}}.[[kill]]'),
                'assists' => self::sum('{{b}}.[[assist]]'),
                'deaths' => self::sum('{{b}}.[[death]]'),
                'specials' => self::sum('{{b}}.[[special]]'),
                'inked' => self::sum('{{b}}.[[inked]]'),
                'max_inked' => self::max('{{b}}.[[inked]]'),
                '_peak_rank_rank' => \vsprintf('GREATEST(%s, %s)', [
                    self::max('{{rank_before}}.[[rank]]', self::condBankara()),
                    self::max('{{rank_after}}.[[rank]]', self::condBankara()),
                ]),
                'peak_rank_id' => $null,
                'peak_s_plus' => \vsprintf('GREATEST(%s, %s)', [
                    self::max('{{b}}.[[rank_before_s_plus]]', self::condBankara()),
                    self::max('{{b}}.[[rank_after_s_plus]]', self::condBankara()),
                ]),
                'peak_x_power' => $null,
                'peak_fest_power' => self::max('{{b}}.[[fest_power]]', self::condSplatfestChallenge()),
                'peak_season' => $null,
                'current_rank_id' => $null,
                'current_s_plus' => $null,
                'current_x_power' => $null,
                'current_season' => $null,
                'updated_at' => new Expression(
                    \sprintf(
                        '%s::TIMESTAMP(0) WITH TIME ZONE',
                        $db->quoteValue($now->format(DateTimeInterface::ATOM)),
                    ),
                ),
            ]);
    }

    /**
     * @param string[] $additionalConds
     */
    private static function sum(string $valueExpr, array $additionalConds = []): string
    {
        return self::aggregate('SUM', $valueExpr, $additionalConds);
    }

    /**
     * @param string[] $additionalConds
     */
    private static function max(string $valueExpr, array $additionalConds = []): string
    {
        return self::aggregate('MAX', $valueExpr, $additionalConds);
    }

    /**
     * @param string[] $additionalConds
     */
    private static function aggregate(string $func, string $valueExpr, array $additionalConds = []): string
    {
        $positive = fn (string $column): string => \sprintf('(%1$s IS NOT NULL) AND (%1$s >= 0)', $column);
        $conds = \array_merge(
            [
                '{{b}}.[[result_id]] IS NOT NULL',
                '{{r}}.[[aggregatable]] = TRUE',
                $positive('{{b}}.[[kill]]'),
                $positive('{{b}}.[[death]]'),
                $positive('{{b}}.[[assist]]'),
                $positive('{{b}}.[[special]]'),
                $positive('{{b}}.[[inked]]'),
                '{{b}}.[[start_at]] IS NOT NULL',
                '{{b}}.[[end_at]] IS NOT NULL',
                '{{b}}.[[start_at]] < {{b}}.[[end_at]]',
                '{{b}}.[[has_disconnect]] = FALSE',
            ],
            $additionalConds,
        );
        return \sprintf(
            '%1$s(CASE WHEN %3$s THEN %2$s ELSE 0 END)',
            $func,
            $valueExpr,
            \implode(
                ' AND ',
                \array_map(
                    fn (string $cond): string => \sprintf('(%s)', $cond),
                    $conds,
                ),
            ),
        );
    }

    private static function timestamp(string $column): string
    {
        return \sprintf('EXTRACT(EPOCH FROM %s)', $column);
    }

    /**
     * @return string[]
     */
    private static function condBankara(): array
    {
        static $cache = null;
        if (!$cache) {
            $cache = self::condLobby('bankara_%', true);
        }
        return $cache;
    }

    /**
     * @return string[]
     */
    private static function condSplatfestChallenge(): array
    {
        static $cache = null;
        if (!$cache) {
            $cache = self::condLobby('splatfest_challenge', false);
        }
        return $cache;
    }

    /**
     * @return string[]
     */
    private static function condLobby(string $lobby, bool $like = false): array
    {
        $list = Lobby3::find()
            ->andWhere(
                $like
                    ? ['like', 'key', $lobby, false]
                    : ['key' => $lobby]
            )
            ->all();

        if (!$list) {
            throw new LogicException();
        }

        return [
            \vsprintf('{{b}}.[[lobby_id]] IN (%s)', [
                \implode(
                    ', ',
                    \array_map(
                        fn (Lobby3 $model): string => (string)$model->id,
                        $list,
                    ),
                ),
            ]),
        ];
    }

    private static function saveAggregatedStats(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
        array $row
    ): bool {
        $lobby = $row['lobby_id']
            ? Lobby3::find()
                ->andWhere(['id' => $row['lobby_id']])
                ->limit(1)
                ->one()
            : null;

        $model = Yii::createObject(UserStat3::class);
        $model->attributes = $row;
        $model->id = ((int)$user->id << 32) | (int)$row['lobby_id'];

        if (\str_starts_with($lobby->key ?? '', 'bankara_')) {
            $rankModel = Rank3::find()
                ->andWhere(['rank' => (int)$row['_peak_rank_rank']])
                ->limit(1)
                ->one();
            if ($rankModel) {
                $model->peak_rank_id = $rankModel->id;
                $model->peak_s_plus = $rankModel->key === 's+' ? (int)$model->peak_s_plus : null;
                $model->peak_season = self::getPeakSeasonByRank(
                    $user,
                    $lobby,
                    $rankModel,
                    $model->peak_s_plus,
                );
            } else {
                $model->peak_rank_id = null;
                $model->peak_s_plus = null;
                $model->peak_season = null;
            }

            // TODO: current
        } else {
            $model->peak_rank_id = null;
            $model->peak_s_plus = null;
            $model->peak_x_power = null;
            $model->current_rank_id = null;
            $model->current_s_plus = null;
            $model->current_x_power = null;
        }

        if (
            ($lobby->key ?? '') === 'splatfest_challenge' &&
            (float)$model->peak_fest_power > 0.0
        ) {
            $model->peak_fest_power = (float)$model->peak_fest_power;
            $model->peak_season = self::getPeakSeasonByFestPower($user, $lobby, $model->peak_fest_power);
        } else {
            $model->peak_fest_power = null;
        }

        if ($model->save()) {
            return true;
        }

        Yii::error('Failed to save the model, ' . Json::encode($model->getErrors()), __METHOD__);
        return false;
    }

    private static function getPeakSeasonByRank(
        User $user,
        Lobby3 $lobby,
        Rank3 $rank,
        ?int $sPlus
    ): ?string {
        $battle = Battle3::find()
            ->andWhere([
                'is_deleted' => false,
                'lobby_id' => $lobby->id,
                'user_id' => $user->id,
            ])
            ->andWhere(['or',
                ['rank_before_id' => $rank->id, 'rank_before_s_plus' => $sPlus],
                ['rank_after_id' => $rank->id, 'rank_after_s_plus' => $sPlus],
            ])
            ->orderBy(['end_at' => SORT_DESC])
            ->limit(1)
            ->one();
        return $battle
            ? self::periodToSeason($battle->period)
            : null;
    }

    private static function getPeakSeasonByFestPower(User $user, Lobby3 $lobby, float $power): ?string
    {
        $battle = Battle3::find()
            ->andWhere([
                'fest_power' => \sprintf('%.1f', $power),
                'is_deleted' => false,
                'lobby_id' => $lobby->id,
                'user_id' => $user->id,
            ])
            ->orderBy(['end_at' => SORT_DESC])
            ->limit(1)
            ->one();
        return $battle
            ? self::periodToSeason($battle->period)
            : null;
    }

    private static function periodToSeason(int $period): ?string
    {
        $date = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
            ->setTimestamp($period * 7200);
        switch ((int)$date->format('n')) {
            case 1:
            case 2:
                return \sprintf('%04d-%02d-%02d', (int)$date->format('Y') - 1, 12, 1);

            case 3:
            case 4:
            case 5:
                return \sprintf('%04d-%02d-%02d', (int)$date->format('Y'), 3, 1);

            case 6:
            case 7:
            case 8:
                return \sprintf('%04d-%02d-%02d', (int)$date->format('Y'), 6, 1);

            case 9:
            case 10:
            case 11:
                return \sprintf('%04d-%02d-%02d', (int)$date->format('Y'), 9, 1);

            case 12:
                return \sprintf('%04d-%02d-%02d', (int)$date->format('Y'), 12, 1);

            default:
                throw new LogicException();
        }
    }
}
