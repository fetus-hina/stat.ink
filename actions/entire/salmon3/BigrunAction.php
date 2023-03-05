<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\salmon3;

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use Yii;
use app\models\SalmonSchedule3;
use app\models\StatBigrunDistrib3;
use app\models\StatBigrunDistribAbstract3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_shift;
use function assert;
use function ceil;
use function filter_var;
use function floor;
use function gmdate;
use function is_int;
use function max;

use const FILTER_VALIDATE_INT;
use const SORT_ASC;
use const SORT_DESC;

final class BigrunAction extends Action
{
    public function run(): Response|string
    {
        $thing = Yii::$app->db->transaction(
            fn (Connection $db): Response|array => $this->doRun($db),
            Transaction::REPEATABLE_READ,
        );

        if ($thing instanceof Response) {
            return $thing;
        }

        $controller = $this->controller;
        assert($controller instanceof Controller);
        return $controller->render('salmon3/bigrun', $thing);
    }

    private function doRun(Connection $db): Response|array
    {
        $scheduleId = filter_var(Yii::$app->request->get('shift'), FILTER_VALIDATE_INT);

        $schedules = $this->getBigrunSchedules($db);
        if (
            !is_int($scheduleId) ||
            !isset($schedules[$scheduleId])
        ) {
            if (!$schedules) {
                throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            }

            $controller = $this->controller;
            assert($controller instanceof Controller);
            return $controller->redirect(
                ['entire/salmon3-bigrun',
                    'shift' => array_shift($schedules)->id,
                ],
            );
        }

        $abstract = StatBigrunDistribAbstract3::find()
            ->andWhere(['schedule_id' => $scheduleId])
            ->limit(1)
            ->one($db);

        $histogram = ArrayHelper::map(
            StatBigrunDistrib3::find()
                ->andWhere(['schedule_id' => $scheduleId])
                ->orderBy(['golden_egg' => SORT_ASC])
                ->all($db),
            'golden_egg',
            'users',
        );

        return [
            'abstract' => $abstract,
            'histogram' => $histogram,
            'normalDistrib' => $this->normalDistrib($abstract, $histogram),
            'schedule' => $schedules[$scheduleId],
            'schedules' => $schedules,
        ];
    }

    /**
     * @param array<int, int> $histogram
     * @return array<int, float>|null
     */
    private function normalDistrib(?StatBigrunDistribAbstract3 $abstract, array $histogram): ?array
    {
        if (
            !$abstract ||
            !$histogram ||
            $abstract->average < 1 ||
            $abstract->median === null ||
            $abstract->q1 === null ||
            $abstract->q3 === null ||
            $abstract->stddev === null ||
            $abstract->users < 10
        ) {
            return null;
        }

        $iqr = $abstract->q3 - $abstract->q1;
        if ($iqr < 1) {
            return null;
        }

        $step = 5;
        $min = 0;
        $max = max(
            (int)ceil(($abstract->q3 + 1.5 * $iqr) / $step) * $step,
            (int)floor($abstract->max / $step) * $step,
        );
        $nd = new NormalDistribution((float)$abstract->average, (float)$abstract->stddev);

        $results = [];
        for ($x = $min; $x <= $max; $x += $step) {
            $results[$x] = $abstract->users * $step * $nd->pdf($x);
        }

        return $results;
    }

    /**
     * @return array<int, SalmonSchedule3>
     */
    private function getBigrunSchedules(Connection $db): array
    {
        $time = $_SERVER['REQUEST_TIME'];
        $date = gmdate('Y-m-d', $time);

        return Yii::$app->cache->getOrSet(
            [__METHOD__, $date],
            fn (): array => ArrayHelper::map(
                SalmonSchedule3::find()
                    ->with([
                        'bigMap',
                        'bigrunOfficialResult3',
                    ])
                    ->andWhere(['not', ['big_map_id' => null]])
                    ->andWhere(['<=', 'start_at', $date])
                    ->orderBy(['start_at' => SORT_DESC])
                    ->all($db),
                'id',
                fn ($v) => $v,
            ),
            1,
        );
    }
}
