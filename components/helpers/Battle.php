<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\Battle as BattleModel;
use app\models\Battle2 as Battle2Model;
use app\models\Battle2FilterForm;
use app\models\BattleFilterForm;
use app\models\User;
use yii\db\Query;

class Battle
{
    public static function calcPeriod(int $unixTime): int
    {
        // 2 * 3600: UTC 02:00 に切り替わるのでその分を引く
        // 4 * 3600: 4時間ごとにステージ変更
        return (int)floor(($unixTime - 2 * 3600) / (4 * 3600));
    }

    public static function periodToRange(int $period, int $offset = 0): array
    {
        $from = $period * (4 * 3600) + (2 * 3600) + $offset;
        $to = $from + 4 * 3600;
        return [$from, $to];
    }

    public static function calcPeriod2(int $unixTime): int
    {
        return (int)floor($unixTime / (2 * 3600));
    }

    public static function periodToRange2(int $period, int $offset = 0): array
    {
        $from = $period * (2 * 3600) + $offset;
        $to = $from + 2 * 3600;
        return [$from, $to];
    }

    public static function periodToRange2DT(int $period, int $offset = 0): array
    {
        [$from, $to] = static::periodToRange2($period, $offset);
        return [
            static::timestamp2datetime($from),
            static::timestamp2datetime($to),
        ];
    }

    public static function getNBattlesRange(BattleFilterForm $filter, int $num): ?array
    {
        $filter = clone $filter;
        $filter->term = null;
        $subQuery = BattleModel::find()
            ->select([
                'id' => '{{battle}}.[[id]]',
                'at' => '{{battle}}.[[at]]',
            ])
            ->filter($filter)
            ->offset(0)
            ->limit($num);

        $query = (new Query())
            ->select([
                'min_id' => 'MIN({{t}}.[[id]])',
                'max_id' => 'MAX({{t}}.[[id]])',
                'min_at' => 'MIN({{t}}.[[at]])',
                'max_at' => 'MAX({{t}}.[[at]])',
            ])
            ->from(sprintf(
                '(%s) {{t}}',
                $subQuery->createCommand()->rawSql,
            ));
        return $query->createCommand()->queryOne();
    }

    public static function getNBattlesRange2(Battle2FilterForm $filter, int $num): ?array
    {
        $filter = clone $filter;
        $filter->term = null;
        $subQuery = Battle2Model::find()
            ->select([
                'id' => '{{battle2}}.[[id]]',
                'at' => '{{battle2}}.[[created_at]]',
            ])
            ->applyFilter($filter)
            ->orderBy([
                '{{battle2}}.[[id]]' => SORT_DESC,
            ])
            ->offset(0)
            ->limit($num);

        $query = (new Query())
            ->select([
                'min_id' => 'MIN({{t}}.[[id]])',
                'max_id' => 'MAX({{t}}.[[id]])',
                'min_at' => 'MIN({{t}}.[[at]])',
                'max_at' => 'MAX({{t}}.[[at]])',
            ])
            ->from(sprintf(
                '(%s) {{t}}',
                $subQuery->createCommand()->rawSql,
            ));

        return $query->createCommand()->queryOne();
    }

    public static function getActivityDisplayRange(): array
    {
        $today = (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone('Etc/UTC'))
            ->setTimestamp(time())
            ->setTime(23, 59, 59);

        $aYearAgo = $today->sub(new DateInterval('P1Y'));
        return [
            (new DateTimeImmutable())
                ->setTimezone(new DateTimeZone('Etc/UTC'))
                ->setDate(
                    (int)$aYearAgo->format('Y'),
                    (int)$aYearAgo->format('n') + 1,
                    1,
                )
                ->setTime(0, 0, 0),
            (new DateTimeImmutable())
                ->setTimezone(new DateTimeZone('Etc/UTC'))
                ->setDate(
                    (int)$today->format('Y'),
                    (int)$today->format('n'),
                    (int)$today->format('t') + 1,
                )
                ->setTime(0, 0, -1),
        ];
    }

    public static function getLastPlayedSplatfestPeriodRange2(User $user): ?array
    {
        // 一番最後に登録されたフェスバトルを取得
        $lastBattle = Battle2Model::find()
            ->innerJoinWith([
                'lobby',
                'mode',
                'rule',
            ], false)
            ->andWhere([
                'battle2.user_id' => $user->id,
                'lobby2.key' => ['standard', 'fest_normal'],
                'mode2.key' => 'fest',
                'rule2.key' => 'nawabari',
            ])
            ->orderBy(['battle2.id' => SORT_DESC])
            ->limit(1)
            ->one();
        if (!$lastBattle || !$lastBattle->period) {
            return null;
        }

        // そのバトルから4日以内の一番最初のフェスバトルを取得
        $firstBattle = Battle2Model::find()
            ->innerJoinWith([
                'lobby',
                'mode',
                'rule',
            ], false)
            ->andWhere([
                'battle2.user_id' => $user->id,
                'lobby2.key' => ['standard', 'fest_normal'],
                'mode2.key' => 'fest',
                'rule2.key' => 'nawabari',
            ])
            ->andWhere([
                'between',
                'battle2.period',
                $lastBattle->period - 47,
                $lastBattle->period,
            ])
            ->orderBy(['battle2.id' => SORT_ASC])
            ->limit(1)
            ->one();
        if (!$firstBattle || !$firstBattle->period) {
            return null;
        }

        return [
            (int)$firstBattle->period,
            (int)$lastBattle->period,
        ];
    }

    private static function timestamp2datetime(int $timestamp): DateTimeImmutable
    {
        return (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
            ->setTimestamp($timestamp);
    }
}
