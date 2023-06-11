<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats;

use LogicException;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\Salmon3;
use app\models\SalmonBoss3;
use app\models\SalmonKing3;
use app\models\SalmonSchedule3;
use app\models\User;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use function array_merge;
use function implode;
use function is_int;
use function is_string;
use function sprintf;

use const SORT_ASC;
use const SORT_DESC;

final class ScheduleAction extends Action
{
    public ?User $user = null;
    public ?SalmonSchedule3 $schedule = null;

    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        parent::init();

        $screenName = Yii::$app->request->get('screen_name');
        $this->user = is_string($screenName)
            ? User::find()
                ->andWhere(['screen_name' => $screenName])
                ->limit(1)
                ->one()
            : null;

        $scheduleId = TypeHelper::intOrNull(Yii::$app->request->get('schedule'));
        $this->schedule = is_int($scheduleId)
            ? SalmonSchedule3::find()
                ->andWhere(['id' => $scheduleId])
                ->limit(1)
                ->one()
            : null;

        if (!$this->user || !$this->schedule) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run(): string
    {
        if (
            !($user = $this->user) ||
            !($schedule = $this->schedule)
        ) {
            throw new LogicException();
        }

        $data = Yii::$app->db->transaction(
            fn (Connection $db): array => Yii::$app->cache->getOrSet(
                [
                    'id' => __METHOD__,
                    'version' => 2,
                    'user' => $user->id,
                    'schedule' => $schedule->id,
                    'cond' => $this->getCachingCondition($db, $user, $schedule),
                ],
                fn (): array => [
                    'bossStats' => $this->getBossStats($db, $user, $schedule),
                    'bosses' => $this->getBosses($db),
                    'kingStats' => $this->getKingStats($db, $user, $schedule),
                    'kings' => $this->getKings($db),
                    'map' => $schedule->map ?? $schedule->bigMap ?? null,
                    'stats' => $this->getStats($db, $user, $schedule),
                    // 'results' => $this->getResults($db, $user, $schedule),
                ],
                duration: 7 * 24 * 60 * 60,
            ),
            Transaction::REPEATABLE_READ,
        );

        return TypeHelper::instanceOf($this->controller, Controller::class)
            ->render(
                'stats/schedule',
                array_merge(
                    $data,
                    [
                        'user' => $user,
                        'schedule' => $schedule,
                    ],
                ),
            );
    }

    private function getCachingCondition(
        Connection $db,
        User $user,
        SalmonSchedule3 $schedule,
    ): array {
        return (new Query())
            ->select([
                'max' => 'MAX([[id]])',
                'count' => 'COUNT(*)',
            ])
            ->from('{{%salmon3}}')
            ->andWhere([
                'is_deleted' => false,
                'is_private' => false,
                'schedule_id' => $schedule->id,
                'user_id' => $user->id,
            ])
            ->one($db);
    }

    /**
     * @return array<int, array{boss_id: int, appearances: int, defeated: int, defeated_by_me: int}>
     */
    private function getBossStats(Connection $db, User $user, SalmonSchedule3 $schedule): array
    {
        return ArrayHelper::index(
            (new Query())
                ->select([
                    'boss_id' => '{{%salmon_boss_appearance3}}.[[boss_id]]',
                    'appearances' => 'SUM({{%salmon_boss_appearance3}}.[[appearances]])',
                    'defeated' => 'SUM({{%salmon_boss_appearance3}}.[[defeated]])',
                    'defeated_by_me' => 'SUM({{%salmon_boss_appearance3}}.[[defeated_by_me]])',
                ])
                ->from('{{%salmon3}}')
                ->innerJoin(
                    '{{%salmon_boss_appearance3}}',
                    '{{%salmon3}}.[[id]] = {{%salmon_boss_appearance3}}.[[salmon_id]]',
                )
                ->andWhere([
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                    '{{%salmon3}}.[[schedule_id]]' => $schedule->id,
                    '{{%salmon3}}.[[user_id]]' => $user->id,
                ])
                ->groupBy([
                    '{{%salmon_boss_appearance3}}.[[boss_id]]',
                ])
                ->orderBy([
                    'appearances' => SORT_DESC,
                    'boss_id' => SORT_ASC,
                ])
                ->all($db),
            'boss_id',
        );
    }

    /**
     * @return array<int, SalmonBoss3>
     */
    private function getBosses(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonBoss3::find()->orderBy(['id' => SORT_ASC])->all(),
            'id',
        );
    }

    /**
     * @return array<int, array{king_id: int, appearances: int, defeated: int}>
     */
    private function getKingStats(Connection $db, User $user, SalmonSchedule3 $schedule): array
    {
        return ArrayHelper::index(
            (new Query())
                ->select([
                    'king_id' => '{{%salmon3}}.[[king_salmonid_id]]',
                    'appearances' => 'COUNT(*)',
                    'defeated' => 'SUM(CASE WHEN [[clear_extra]] = TRUE THEN 1 ELSE 0 END)',
                    'gold_scale' => 'SUM({{%salmon3}}.[[gold_scale]])',
                    'silver_scale' => 'SUM({{%salmon3}}.[[silver_scale]])',
                    'bronze_scale' => 'SUM({{%salmon3}}.[[bronze_scale]])',
                ])
                ->from('{{%salmon3}}')
                ->andWhere(['and',
                    [
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                        '{{%salmon3}}.[[schedule_id]]' => $schedule->id,
                        '{{%salmon3}}.[[user_id]]' => $user->id,
                    ],
                    ['not', ['{{%salmon3}}.[[king_salmonid_id]]' => null]],
                    ['not', ['{{%salmon3}}.[[clear_extra]]' => null]],
                ])
                ->groupBy([
                    '{{%salmon3}}.[[king_salmonid_id]]',
                ])
                ->orderBy([
                    'appearances' => SORT_DESC,
                    'king_id' => SORT_ASC,
                ])
                ->all($db),
            'king_id',
        );
    }

    /**
     * @return array<int, SalmonKing3>
     */
    private function getKings(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonKing3::find()->orderBy(['id' => SORT_ASC])->all(),
            'id',
        );
    }

    private function getStats(Connection $db, User $user, SalmonSchedule3 $schedule): array
    {
        $waves = $schedule->is_eggstra_work ? 5 : 3;
        return TypeHelper::array(
            (new Query())
                ->select([
                    'count' => 'COUNT(*)',
                    'cleared' => sprintf(
                        'SUM(CASE WHEN [[clear_waves]] >= %d THEN 1 ELSE 0 END)',
                        $waves,
                    ),
                    'avg_waves' => sprintf(
                        'AVG(CASE WHEN [[clear_waves]] >= %1$d THEN %1$d ELSE [[clear_waves]] END)',
                        $waves,
                    ),
                    'max_danger_rate' => sprintf(
                        'MAX(CASE %s END)',
                        implode(' ', [
                            sprintf('WHEN [[clear_waves]] >= %d THEN [[danger_rate]]', $waves),
                            'ELSE NULL',
                        ]),
                    ),
                    'king_appears' => sprintf(
                        'SUM(CASE %s END)',
                        implode(' ', [
                            sprintf(
                                'WHEN [[clear_waves]] >= %d AND [[king_salmonid_id]] IS NOT NULL THEN 1',
                                $waves,
                            ),
                            'ELSE 0',
                        ]),
                    ),
                    'king_defeated' => sprintf(
                        'SUM(CASE %s END)',
                        implode(' ', [
                            sprintf(
                                'WHEN %s THEN 1',
                                implode(' AND ', [
                                    sprintf('[[clear_waves]] >= %d', $waves),
                                    '[[king_salmonid_id]] IS NOT NULL',
                                    '[[clear_extra]] = TRUE',
                                ]),
                            ),
                            'ELSE 0',
                        ]),
                    ),
                    'total_gold_scale' => 'SUM({{%salmon3}}.[[gold_scale]])',
                    'total_silver_scale' => 'SUM({{%salmon3}}.[[silver_scale]])',
                    'total_bronze_scale' => 'SUM({{%salmon3}}.[[bronze_scale]])',
                    'max_golden' => 'MAX({{%salmon3}}.[[golden_eggs]])',
                    'total_golden' => 'SUM({{%salmon3}}.[[golden_eggs]])',
                    'avg_golden' => 'AVG({{%salmon3}}.[[golden_eggs]])',
                    'max_power' => 'MAX({{%salmon3}}.[[power_eggs]])',
                    'total_power' => 'SUM({{%salmon3}}.[[power_eggs]])',
                    'avg_power' => 'AVG({{%salmon3}}.[[power_eggs]])',
                    'total_rescues' => 'SUM({{%salmon_player3}}.[[rescue]])',
                    'avg_rescues' => 'AVG({{%salmon_player3}}.[[rescue]])',
                    'total_rescued' => 'SUM({{%salmon_player3}}.[[rescued]])',
                    'avg_rescued' => 'AVG({{%salmon_player3}}.[[rescued]])',
                    'total_defeat_boss' => 'SUM({{%salmon_player3}}.[[defeat_boss]])',
                    'avg_defeat_boss' => 'AVG({{%salmon_player3}}.[[defeat_boss]])',
                ])
                ->from('{{%salmon3}}')
                ->leftJoin(
                    '{{%salmon_player3}}',
                    implode(' AND ', [
                        '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                        '{{%salmon_player3}}.[[is_me]] = TRUE',
                    ]),
                )
                ->andWhere([
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                    '{{%salmon3}}.[[schedule_id]]' => $schedule->id,
                    '{{%salmon3}}.[[user_id]]' => $user->id,
                ])
                ->one($db),
        );
    }

    private function getResults(Connection $db, User $user, SalmonSchedule3 $schedule): array
    {
        return Salmon3::find()
            ->andWhere([
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'is_deleted' => false,
                'is_private' => false,
            ])
            ->andWhere(['not', ['start_at' => null]])
            ->orderBy([
                'start_at' => SORT_ASC,
            ])
            ->all($db);
    }
}
