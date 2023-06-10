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
use app\models\SalmonKing3;
use app\models\SalmonSchedule3;
use app\models\User;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use function array_merge;
use function implode;
use function is_int;
use function is_string;
use function sprintf;
use function strtotime;

use const SORT_ASC;

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
                    'version' => 1,
                    'user' => $user->id,
                    'schedule' => $schedule->id,
                    'cond' => $this->getCachingCondition($db, $user, $schedule),
                ],
                fn (): array => [
                    'king' => $this->getKing($db, $schedule),
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

    private function getKing(Connection $db, SalmonSchedule3 $schedule): ?SalmonKing3
    {
        if ($schedule->is_eggstra_work) {
            return null;
        }

        if ($king = $schedule->king) {
            return $king;
        }

        $startAt = @strtotime($schedule->start_at);
        $tatsu = @strtotime('2023-03-04T00:00:00+00:00');
        if (
            is_int($startAt) &&
            is_int($tatsu) &&
            $startAt < $tatsu
        ) {
            return SalmonKing3::find()
                ->andWhere(['key' => 'yokozuna'])
                ->limit(1)
                ->one($db);
        }

        return null;
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
