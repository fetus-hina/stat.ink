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
use LogicException;
use PDO;
use Yii;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\User;
use app\models\UserStat3XMatch;
use yii\db\Connection;
use yii\db\Exception as DbException;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use const SORT_ASC;

trait XStatsTrait
{
    use AggregateFunctionsTrait;

    protected static function createUserStatsX(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): bool {
        try {
            UserStat3XMatch::deleteAll(['user_id' => $user->id]);

            $rows = self::createStatsAggregateQueryX($db, $user, $now)
                ->createCommand($db)
                ->queryAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                if (!self::saveAggregatedStatsX($db, $user, $now, $row)) {
                    $db->transaction->rollBack();
                    return false;
                }
            }

            return true;
        } catch (DbException $e) {
            Yii::error(
                \vsprintf('Catch %s, message=%s', [
                    $e::class,
                    $e->getMessage(),
                ]),
                __METHOD__,
            );
            $db->transaction->rollBack();
            return false;
        }
    }

    private static function createStatsAggregateQueryX(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): Query {
        $null = new Expression('NULL');

        if (!$lobby = Lobby3::find()->andWhere(['key' => 'xmatch'])->limit(1)->one()) {
            throw new LogicException();
        }

        $rules = Rule3::find()
            ->innerJoinWith(['group'], false)
            ->andWhere(['{{%rule_group3}}.[[key]]' => 'gachi'])
            ->all();
        if (!$rules) {
            throw new LogicException();
        }

        return (new Query())
            ->from(['b' => '{{%battle3}}'])
            ->leftJoin(['r' => '{{%result3}}'], '{{b}}.[[result_id]] = {{r}}.[[id]]')
            ->andWhere([
                '{{b}}.[[is_deleted]]' => false,
                '{{b}}.[[lobby_id]] ' => (int)$lobby->id,
                '{{b}}.[[rule_id]]' => ArrayHelper::getColumn($rules, 'id'),
                '{{b}}.[[user_id]]' => (int)$user->id,
            ])
            ->groupBy([
                '{{b}}.[[user_id]]',
                '{{b}}.[[rule_id]]',
            ])
            ->orderBy([
                '{{b}}.[[user_id]]' => SORT_ASC,
                '{{b}}.[[rule_id]]' => SORT_ASC,
            ])
            ->select([
                'user_id' => '{{b}}.[[user_id]]',
                'rule_id' => '{{b}}.[[rule_id]]',
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
                'peak_x_power' => \vsprintf('GREATEST(%s, %s)', [
                    self::statsMax('{{b}}.[[x_power_before]]'),
                    self::statsMax('{{b}}.[[x_power_after]]'),
                ]),
                'peak_season' => $null,
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

    private static function saveAggregatedStatsX(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
        array $row,
    ): bool {
        $model = Yii::createObject(UserStat3XMatch::class);
        $model->attributes = $row;

        // TODO: peak_season

        if ($model->save()) {
            return true;
        }

        Yii::error('Failed to save the model, ' . Json::encode($model->getErrors()), __METHOD__);
        return false;
    }
}
