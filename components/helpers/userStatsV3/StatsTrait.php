<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\userStatsV3;

use DateTimeImmutable;
use DateTimeInterface;
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
use yii\helpers\Json;

use const SORT_ASC;
use const SORT_DESC;

trait StatsTrait
{
    use AggregateFunctionsTrait;

    protected static function createUserStats(Connection $db, User $user, DateTimeImmutable $now): bool
    {
        try {
            UserStat3::deleteAll(['user_id' => $user->id]);

            $rows = self::createStatsAggregateQuery($db, $user, $now)
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

    private static function createStatsAggregateQuery(
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
                'agg_battles' => self::statsSum('1'),
                'agg_seconds' => self::statsSum(\vsprintf('(%s) - (%s)', [
                    self::statsTimestamp('{{b}}.[[end_at]]'),
                    self::statsTimestamp('{{b}}.[[start_at]]'),
                ])),
                'wins' => self::statsSum('1', ['{{r}}.[[is_win]] = TRUE']),
                'kills' => self::statsSum('{{b}}.[[kill]]'),
                'assists' => self::statsSum('{{b}}.[[assist]]'),
                'deaths' => self::statsSum('{{b}}.[[death]]'),
                'specials' => self::statsSum('{{b}}.[[special]]'),
                'inked' => self::statsSum('{{b}}.[[inked]]'),
                'max_inked' => self::statsMax('{{b}}.[[inked]]'),
                '_peak_rank_rank' => \vsprintf('GREATEST(%s, %s)', [
                    self::statsMax('{{rank_before}}.[[rank]]', self::statsCondBankara()),
                    self::statsMax('{{rank_after}}.[[rank]]', self::statsCondBankara()),
                ]),
                'peak_rank_id' => $null,
                'peak_s_plus' => \vsprintf('GREATEST(%s, %s)', [
                    self::statsMax('{{b}}.[[rank_before_s_plus]]', self::statsCondBankara()),
                    self::statsMax('{{b}}.[[rank_after_s_plus]]', self::statsCondBankara()),
                ]),
                'peak_x_power' => $null,
                'peak_fest_power' => self::statsMax('{{b}}.[[fest_power]]', self::statsCondSplatfestChallenge()),
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
}
