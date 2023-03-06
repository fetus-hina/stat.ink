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
use app\models\BigrunOfficialResult3;
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

use function array_keys;
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

        $schedule = $schedules[$scheduleId];

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

        $normalDistrib = $this->normalDistrib($abstract, $histogram);
        $estimatedDistrib = match (true) {
            !empty($normalDistrib) => $this->estimatedDistrib(
                official: $schedule->bigrunOfficialResult3,
                min: 0,
                max: (int)max(array_keys($normalDistrib)),
                samples: (int)$abstract->users,
            ),
            default => null,
        };

        return [
            'abstract' => $abstract,
            'estimatedAverage' => $estimatedDistrib ? $estimatedDistrib['avg'] : null,
            'estimatedDistrib' => $estimatedDistrib ? $estimatedDistrib['histogram'] : null,
            'estimatedStddev' => $estimatedDistrib ? $estimatedDistrib['stddev'] : null,
            'histogram' => $histogram,
            'normalDistrib' => $normalDistrib,
            'schedule' => $schedule,
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

        $calcStep = 2;
        $dataStep = 5;
        $min = 0;
        $max = max(
            (int)ceil(($abstract->q3 + 1.5 * $iqr) / $dataStep) * $dataStep,
            (int)floor($abstract->max / $dataStep) * $dataStep,
        );
        $nd = new NormalDistribution((float)$abstract->average, (float)$abstract->stddev);

        $results = [];
        for ($x = $min; $x <= $max; $x += $calcStep) {
            $results[$x] = $abstract->users * $dataStep * $nd->pdf($x);
        }

        return $results;
    }

    /**
     * @return array{avg: float, stddev: float, histogram: array<int, float>}|null
     */
    private function estimatedDistrib(
        ?BigrunOfficialResult3 $official,
        int $min, // should be 0
        int $max,
        int $samples,
        int $dataStep = 5,
    ): ?array {
        if (
            !$official ||
            $official->gold < 1 ||
            $official->silver >= $official->gold ||
            $official->bronze >= $official->silver
        ) {
            return null;
        }

        // Ref. http://homepages.math.uic.edu/~bpower6/stat101/Confidence%20Intervals.pdf
        $nd = new NormalDistribution(0.0, 1.0);
        $z20 = $nd->inverse(0.60 + (1 - 0.60) / 2);
        $z5 = $nd->inverse(0.90 + (1 - 0.90) / 2); // 1.64485
        unset($nd);

        // 綺麗な正規分布であることを想定した上で、
        // 80パーセンタイル値と95パーセンタイル値から平均値を逆算する
        //
        //   SD = (n - avg) / z
        //
        // より
        //
        //   (n5 - avg) / z5 = (n20 - avg) / z20
        //
        // を avg について解いて
        //
        //   avg = (z5 * n20 - z20 * n5) / (z5 - z20)
        //
        // 実際はかたよりがあるので、おそらく50パーセンタイル値すら合わない

        $estimatedAverage = ($z5 * $official->silver - $z20 * $official->gold) / ($z5 - $z20);
        $estimatedSD = ((float)(int)$official->gold - $estimatedAverage) / $z5;
        $calcStep = 2;

        $nd = new NormalDistribution($estimatedAverage, $estimatedSD);
        for ($x = $min; $x <= $max; $x += $calcStep) {
            $results[$x] = $samples * $dataStep * $nd->pdf($x);
        }

        return [
            'avg' => $estimatedAverage,
            'stddev' => $estimatedSD,
            'histogram' => $results,
        ];
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
