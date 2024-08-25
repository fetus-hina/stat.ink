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
use app\models\StatBigrunDistribJobAbstract3;
use app\models\StatBigrunDistribJobHistogram3;
use app\models\StatBigrunDistribUserAbstract3;
use app\models\StatBigrunDistribUserHistogram3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function abs;
use function array_keys;
use function array_shift;
use function assert;
use function filter_var;
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

        $abstract = StatBigrunDistribUserAbstract3::find()
            ->andWhere(['schedule_id' => $scheduleId])
            ->limit(1)
            ->one($db);

        $histogram = ArrayHelper::map(
            StatBigrunDistribUserHistogram3::find()
                ->andWhere(['schedule_id' => $scheduleId])
                ->orderBy(['class_value' => SORT_ASC])
                ->all($db),
            'class_value',
            'count',
        );

        $jobAbstract = StatBigrunDistribJobAbstract3::find()
            ->andWhere(['schedule_id' => $scheduleId])
            ->limit(1)
            ->one($db);

        $jobHistogram = ArrayHelper::map(
            StatBigrunDistribJobHistogram3::find()
                ->andWhere(['schedule_id' => $scheduleId])
                ->orderBy(['class_value' => SORT_ASC])
                ->all($db),
            'class_value',
            'count',
        );

        $normalDistrib = null;
        $estimatedDistrib = null;
        $ruleOfThumbDistrib = null;
        $chartMax = null;
        if (
            $abstract &&
            $histogram &&
            $abstract->average >= 1 &&
            $abstract->p50 !== null &&
            $abstract->p25 !== null &&
            $abstract->p75 !== null &&
            $abstract->stddev !== null &&
            $abstract->users >= 10 &&
            $abstract->histogram_width !== null
        ) {
            $normalDistrib = new NormalDistribution(
                (float)$abstract->average,
                (float)$abstract->stddev,
            );
            $estimatedDistrib = self::estimatedDistrib($schedule->bigrunOfficialResult3);
            $ruleOfThumbDistrib = self::ruleOfThumbDistrib($jobAbstract);
            $chartMax = max(array_keys($histogram)) + $abstract->histogram_width / 2;
        }

        return [
            'abstract' => $abstract,
            'chartMax' => $chartMax,
            'estimatedDistrib' => $estimatedDistrib,
            'histogram' => $histogram,
            'jobAbstract' => $jobAbstract,
            'jobHistogram' => $jobHistogram,
            'normalDistrib' => $normalDistrib,
            'ruleOfThumbDistrib' => $ruleOfThumbDistrib,
            'schedule' => $schedule,
            'schedules' => $schedules,
        ];
    }

    private static function estimatedDistrib(?BigrunOfficialResult3 $official): ?NormalDistribution
    {
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
        $z5 = $nd->inverse(0.90 + (1 - 0.90) / 2); // 1.64485
        unset($nd);

        $estimatedAverage = (float)$official->bronze + 0.5;

        return new NormalDistribution(
            $estimatedAverage,
            ((float)$official->gold + 0.5 - $estimatedAverage) / $z5, // stddev
        );
    }

    private static function ruleOfThumbDistrib(
        StatBigrunDistribJobAbstract3 $abstract,
    ): ?NormalDistribution {
        if (
            $abstract->users < 50 ||
            $abstract->average === null ||
            $abstract->stddev === null ||
            $abstract->p95 === null ||
            $abstract->p80 === null ||
            $abstract->p50 === null ||
            $abstract->p95 <= $abstract->p80 &&
            $abstract->p80 <= $abstract->p50
        ) {
            return null;
        }

        // Ref. http://homepages.math.uic.edu/~bpower6/stat101/Confidence%20Intervals.pdf
        $nd = new NormalDistribution(0.0, 1.0);
        $z20 = $nd->inverse(0.60 + (1 - 0.60) / 2);
        $z5 = $nd->inverse(0.90 + (1 - 0.90) / 2); // 1.64485
        unset($nd);

        assert(abs($z5 - $z20) > 0.000001);
        assert(abs($z5) > 0.000001);

        $n5 = $abstract->p95;
        $n20 = $abstract->p80;
        $estimatedAverage = ($z5 * $n20 - $z20 * $n5) / ($z5 - $z20);

        return new NormalDistribution(
            $estimatedAverage,
            ($n5 - $estimatedAverage) / $z5,
        );
    }

    /**
     * @return array<int, SalmonSchedule3>
     */
    private static function getBigrunSchedules(Connection $db): array
    {
        $date = gmdate('Y-m-d', $_SERVER['REQUEST_TIME']);
        $version = 4;

        return Yii::$app->cache->getOrSet(
            [__METHOD__, $date, $version],
            fn (): array => ArrayHelper::map(
                SalmonSchedule3::find()
                    ->with(['bigMap'])
                    ->andWhere([
                        'or',
                        ['not', ['big_map_id' => null]],
                        ['is_random_map_big_run' => true],
                    ])
                    ->andWhere(['<=', 'start_at', "{$date}T00:00:00+00:00"])
                    ->orderBy(['start_at' => SORT_DESC])
                    ->all($db),
                'id',
                fn (SalmonSchedule3 $v): SalmonSchedule3 => $v,
            ),
            YII_ENV_PROD ? 86400 : 10,
        );
    }
}
