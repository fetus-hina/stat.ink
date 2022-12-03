<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\salmon3;

use DateTimeImmutable;
use DateTimeInterface;
use Yii;
use app\models\SalmonSchedule3;
use app\models\SalmonScheduleWeapon3;
use app\models\SalmonWeapon3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

use const SORT_ASC;
use const SORT_DESC;

final class RandomLoanAction extends Action
{
    public function run(?int $id = null): string|Response
    {
        return Yii::$app->db->transaction(
            function (Connection $db) use ($id): string|Response {
                $now = (new DateTimeImmutable())->setTimestamp($_SERVER['REQUEST_TIME']);

                $schedules = $this->getRandomSchedules($now);
                if (!$schedules) {
                    throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
                }

                $controller = $this->controller;
                if (!$controller instanceof Controller) {
                    throw new ServerErrorHttpException();
                }

                // Redirect to latest random schedule
                if ($id === null) {
                    return $controller->redirect(
                        ['entire/salmon3-random-loan',
                            'id' => $schedules[0]->id,
                        ],
                    );
                }

                $schedule = $this->getTargetSchedule($schedules, $id);
                if (!$schedule) {
                    throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
                }

                $counts = $this->getLoanCount($schedule);
                return $controller->render('salmon3/random-loan', [
                    'counts' => $counts,
                    'max' => \max(\array_values($counts)),
                    'schedule' => $schedule,
                    'schedules' => $schedules,
                    'total' => \array_sum(\array_values($counts)),
                    'weapons' => ArrayHelper::map(
                        SalmonWeapon3::find()->all(),
                        'key',
                        fn (SalmonWeapon3 $model): SalmonWeapon3 => $model,
                    ),
                ]);
            },
            Transaction::READ_COMMITTED,
        );
    }

    /**
     * @return SalmonSchedule3[]
     */
    private function getRandomSchedules(DateTimeInterface $now): array
    {
        $subQueryGetIds = (new Query())
            ->select(['schedule_id' => '{{t}}.[[schedule_id]]'])
            ->from(['t' => SalmonScheduleWeapon3::tableName()])
            ->andWhere(['{{t}}.[[weapon_id]]' => null])
            ->andWhere(['not', ['{{t}}.[[random_id]]' => null]])
            ->groupBy(['{{t}}.[[schedule_id]]']);

        return SalmonSchedule3::find()
            ->with(['map'])
            ->andWhere(['id' => $subQueryGetIds])
            ->andWhere(['<=', 'start_at', $now->format(DateTimeInterface::ATOM)])
            ->orderBy(['start_at' => SORT_DESC])
            ->all();
    }

    /**
     * @param SalmonSchedule3[] $schedules
     */
    private function getTargetSchedule(array $schedules, int $id): ?SalmonSchedule3
    {
        foreach ($schedules as $schedule) {
            if ($schedule->id === $id) {
                return $schedule;
            }
        }

        return null;
    }

    /**
     * @return array<string, int>
     */
    private function getLoanCount(SalmonSchedule3 $schedule): array
    {
        $query = (new Query())
            ->select([
                'key' => '{{%salmon_weapon3}}.[[key]]',
                'count' => 'COUNT(*)',
            ])
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%salmon_player3}}',
                '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
            )
            ->innerJoin(
                '{{%salmon_player_weapon3}}',
                '{{%salmon_player3}}.[[id]] = {{%salmon_player_weapon3}}.[[player_id]]',
            )
            ->innerJoin(
                '{{%salmon_weapon3}}',
                '{{%salmon_player_weapon3}}.[[weapon_id]] = {{%salmon_weapon3}}.[[id]]',
            )
            ->andWhere([
                '{{%salmon3}}.[[has_broken_data]]' => false,
                '{{%salmon3}}.[[has_disconnect]]' => false,
                '{{%salmon3}}.[[is_automated]]' => true,
                '{{%salmon3}}.[[is_deleted]]' => false,
                '{{%salmon3}}.[[is_private]]' => false,
            ])
            ->andWhere(['and',
                ['>=', '{{%salmon3}}.[[start_at]]', $schedule->start_at],
                ['<', '{{%salmon3}}.[[start_at]]', $schedule->end_at],
            ])
            ->groupBy(['{{%salmon_weapon3}}.[[key]]'])
            ->orderBy([
                'COUNT(*)' => SORT_DESC,
                '{{%salmon_weapon3}}.[[key]]' => SORT_ASC,
            ]);

        return ArrayHelper::map(
            $query->all(),
            'key',
            fn (array $row): int => (int)$row['count'],
        );
    }
}
