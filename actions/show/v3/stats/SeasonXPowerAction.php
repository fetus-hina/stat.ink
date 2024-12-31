<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v3\stats;

use Yii;
use app\components\helpers\Season3Helper;
use app\components\helpers\TypeHelper;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\User;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_merge;

use const SORT_ASC;

/**
 * @phpstan-type DailyData array{
 *   rule_id: int,
 *   date: string,
 *   count: int,
 *   avg: float,
 *   final: ?float,
 *   max: float,
 *   max_id: int,
 *   min: float
 * }
 */
final class SeasonXPowerAction extends Action
{
    public ?User $user = null;

    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->user = User::find()
            ->andWhere([
                'screen_name' => (string)Yii::$app->request->get('screen_name'),
            ])
            ->limit(1)
            ->one();

        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run(): string|Response
    {
        $data = Yii::$app->db->transaction(
            function (Connection $db): array|Response {
                $db->createCommand("SET LOCAL timezone TO 'Etc/UTC'")->execute();

                $user = TypeHelper::instanceOf($this->user, User::class);
                if (!$season = Season3Helper::getUrlTargetSeason('season')) {
                    if (!$season = Season3Helper::getCurrentSeason()) {
                        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
                    }

                    return TypeHelper::instanceOf($this->controller, Controller::class)
                        ->redirect(['show-v3/stats-season-x-power',
                            'screen_name' => $user->screen_name,
                            'season' => $season->id,
                        ]);
                }

                return [
                    'dailyData' => $this->makeDailyData($db, $user, $season),
                    'rules' => $this->getRules($db),
                    'season' => $season,
                    'seasons' => $this->getSeasons($db),
                    'user' => $user,
                ];
            },
            Transaction::REPEATABLE_READ,
        );

        return match (true) {
            $data instanceof Response => $data,
            default => TypeHelper::instanceOf($this->controller, Controller::class)
                ->render('stats/season-x-power', $data),
        };
    }

    /**
     * @return Season3[]
     */
    private function getSeasons(Connection $db): array
    {
        return Season3Helper::getSeasons(xSupported: true);
    }

    /**
     * @return Rule3[]
     */
    private function getRules(Connection $db): array
    {
        return Rule3::find()
            ->innerJoinWith(['group'], false)
            ->andWhere(['{{%rule_group3}}.[[key]]' => 'gachi'])
            ->orderBy(['{{%rule3}}.[[rank]]' => SORT_ASC])
            ->cache(86400)
            ->all($db);
    }

    /**
     * @return DailyData[]
     */
    private function makeDailyData(Connection $db, User $user, Season3 $season): array
    {
        if (!$list = $this->makeDailyDataCore($db, $user, $season)) {
            return [];
        }

        $xPowers = $this->getXPowerOfBattles($db, $user, ArrayHelper::getColumn($list, 'max_id'));
        return ArrayHelper::getColumn(
            $list,
            fn (array $row): array => array_merge($row, [
                'final' => $xPowers[$row['max_id']] ?? null,
            ]),
        );
    }

    /**
     * @return array{
     *   rule_id: int,
     *   date: string,
     *   count: int,
     *   min: float,
     *   max: float,
     *   avg: float,
     *   max_id: int
     * }[]
     */
    private function makeDailyDataCore(Connection $db, User $user, Season3 $season): array
    {
        $lobby = TypeHelper::instanceOf(
            Lobby3::find()->andWhere(['key' => 'xmatch'])->limit(1)->cache(86400)->one($db),
            Lobby3::class,
        );

        $ruleIds = ArrayHelper::getColumn(
            Rule3::find()
                ->innerJoinWith(['group'], false)
                ->andWhere(['{{%rule_group3}}.[[key]]' => 'gachi'])
                ->orderBy(['{{%rule3}}.[[id]]' => SORT_ASC])
                ->cache(86400)
                ->all($db),
            'id',
        );

        $xPower = 'COALESCE({{%battle3}}.[[x_power_after]], {{%battle3}}.[[x_power_before]])';
        $query = (new Query())
            ->select([
                'rule_id' => '{{%battle3}}.[[rule_id]]',
                'date' => '({{%battle3}}.[[start_at]])::date',
                'count' => 'COUNT(*)',
                'min' => "MIN({$xPower})",
                'max' => "MAX({$xPower})",
                'avg' => "ROUND(AVG({$xPower}), 1)",
                'max_id' => 'MAX({{%battle3}}.[[id]])',
            ])
            ->from('{{%battle3}}')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                    '{{%battle3}}.[[rule_id]]' => $ruleIds,
                    '{{%battle3}}.[[user_id]]' => $user->id,
                ],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%battle3}}.[[start_at]]' => null]],
                ['>=', '{{%battle3}}.[[start_at]]', $season->start_at],
                ['<', '{{%battle3}}.[[start_at]]', $season->end_at],
                ['or',
                    '{{%battle3}}.[[x_power_after]] IS NOT NULL',
                    '{{%battle3}}.[[x_power_before]] IS NOT NULL',
                ],
            ])
            ->groupBy(['{{%battle3}}.[[rule_id]]', '({{%battle3}}.[[start_at]])::date'])
            ->orderBy([
                'rule_id' => SORT_ASC,
                'date' => SORT_ASC,
            ]);
        $command = $query->createCommand($db);
        return ArrayHelper::getColumn(
            Yii::$app->cache->getOrSet([$command->rawSql], fn () => $command->queryAll(), 300),
            fn (array $row): array => array_merge($row, [
                'count' => (int)$row['count'],
                'min' => (float)$row['min'],
                'max' => (float)$row['max'],
                'avg' => (float)$row['avg'],
                'max_id' => (int)$row['max_id'],
            ]),
        );
    }

    /**
     * @param int[] $idList
     * @return array<int, float>
     */
    private function getXPowerOfBattles(Connection $db, User $user, array $idList): array
    {
        $command = (new Query())
            ->select([
                'id' => '{{%battle3}}.[[id]]',
                'xp' => 'COALESCE({{%battle3}}.[[x_power_after]], {{%battle3}}.[[x_power_before]])',
            ])
            ->from('{{%battle3}}')
            ->andWhere([
                'id' => $idList,
                'is_deleted' => false,
                'user_id' => $user->id,
            ])
            ->andWhere(['or',
                '{{%battle3}}.[[x_power_after]] IS NOT NULL',
                '{{%battle3}}.[[x_power_before]] IS NOT NULL',
            ])
            ->createCommand($db);
        return ArrayHelper::map(
            Yii::$app->cache->getOrSet([$command->rawSql], fn () => $command->queryAll(), 86400),
            fn (array $row): int => (int)$row['id'],
            fn (array $row): float => (float)$row['xp'],
        );
    }
}
