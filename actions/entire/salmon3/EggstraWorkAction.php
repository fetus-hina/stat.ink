<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\salmon3;

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\EggstraWorkOfficialResult3;
use app\models\SalmonSchedule3;
use app\models\StatEggstraWorkDistribUserAbstract3;
use app\models\StatEggstraWorkDistribUserHistogram3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function abs;
use function array_keys;
use function array_shift;
use function assert;
use function ceil;
use function filter_var;
use function floor;
use function gmdate;
use function is_int;
use function max;
use function round;

use const FILTER_VALIDATE_INT;
use const SORT_ASC;
use const SORT_DESC;

final class EggstraWorkAction extends Action
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

        return TypeHelper::instanceOf($this->controller, Controller::class)
            ->render('salmon3/eggstra-work', $thing);
    }

    private function doRun(Connection $db): Response|array
    {
        $scheduleId = filter_var(Yii::$app->request->get('shift'), FILTER_VALIDATE_INT);

        $schedules = $this->getSchedules($db);
        if (
            !is_int($scheduleId) ||
            !isset($schedules[$scheduleId])
        ) {
            if (!$schedules) {
                throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            }

            return TypeHelper::instanceOf($this->controller, Controller::class)
                ->redirect(
                    ['entire/salmon3-eggstra-work',
                        'shift' => array_shift($schedules)->id,
                    ],
                );
        }

        $schedule = $schedules[$scheduleId];

        $abstract = StatEggstraWorkDistribUserAbstract3::find()
            ->andWhere(['schedule_id' => $scheduleId])
            ->limit(1)
            ->one($db);

        $histogram = ArrayHelper::map(
            StatEggstraWorkDistribUserHistogram3::find()
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
            $estimatedDistrib = self::estimatedDistrib($schedule->eggstraWorkOfficialResult3);
            $ruleOfThumbDistrib = self::ruleOfThumbDistrib($schedule, $abstract);
            $chartMax = max(array_keys($histogram)) + $abstract->histogram_width / 2;
        }

        return [
            'abstract' => $abstract,
            'chartMax' => $chartMax,
            'estimatedDistrib' => $estimatedDistrib,
            'histogram' => $histogram,
            'normalDistrib' => $normalDistrib,
            'ruleOfThumbDistrib' => $ruleOfThumbDistrib,
            'schedule' => $schedule,
            'schedules' => $schedules,
        ];
    }

    private static function estimatedDistrib(?EggstraWorkOfficialResult3 $official): ?NormalDistribution
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
        SalmonSchedule3 $schedule,
        StatEggstraWorkDistribUserAbstract3 $abstract,
    ): ?NormalDistribution {
        if (
            $abstract->users < 50 ||
            $abstract->p95 === null ||
            $abstract->p80 === null ||
            $abstract->p50 === null ||
            $abstract->average === null ||
            $abstract->stddev === null ||
            $abstract->p95 <= $abstract->p80 ||
            $abstract->p80 <= $abstract->p50
        ) {
            return null;
        }

        // 外れ値を除いて再集計する
        $lower = (int)ceil($abstract->average - 2 * $abstract->stddev);
        $upper = (int)floor($abstract->average + 2 * $abstract->stddev);
        $version = 1; // cache version
        $stats = Yii::$app->cache->getOrSet(
            [__METHOD__, $abstract->attributes, [$lower, $upper], $version],
            fn (): array => (new Query())
                ->select([
                    'average' => 'AVG([[golden_eggs]])',
                    'stddev' => 'STDDEV_SAMP([[golden_eggs]])',
                ])
                ->from('{{%user_stat_eggstra_work3}}')
                ->andWhere(['schedule_id' => $schedule->id])
                ->andWhere(['BETWEEN', 'golden_eggs', $lower, $upper])
                ->one(),
            3600,
        );

        // Ref. http://homepages.math.uic.edu/~bpower6/stat101/Confidence%20Intervals.pdf
        $nd = new NormalDistribution(0.0, 1.0);
        $z20 = $nd->inverse(0.60 + (1 - 0.60) / 2);
        $z5 = $nd->inverse(0.90 + (1 - 0.90) / 2); // 1.64485
        unset($nd);

        assert(abs($z5 - $z20) > 0.000001);
        assert(abs($z5) > 0.000001);

        $nd = new NormalDistribution((float)$stats['average'], (float)$stats['stddev']);

        // 第一回はこの数字で正しくなるらしい
        // var_dump(
        //     $nd->inverse(0.7229326514298),
        //     $nd->inverse(0.3574776158447),
        //     $nd->inverse(0.0484890758537),
        // );

        $n5 = (int)round($nd->inverse(0.7229326514298));
        $n20 = (int)round($nd->inverse(0.3574776158447));
        $estimatedAverage1 = ($z5 * $n20 - $z20 * $n5) / ($z5 - $z20);
        $estimatedAverage2 = $nd->inverse(0.0484890758537);
        $estimatedAverage = ($estimatedAverage1 + $estimatedAverage2) / 2;

        return new NormalDistribution(
            $estimatedAverage,
            ($n5 - $estimatedAverage) / $z5,
        );
    }

    /**
     * @return array<int, SalmonSchedule3>
     */
    private static function getSchedules(Connection $db): array
    {
        $date = gmdate('Y-m-d', $_SERVER['REQUEST_TIME']);
        $version = 2;

        return Yii::$app->cache->getOrSet(
            [__METHOD__, $date, $version],
            fn (): array => ArrayHelper::map(
                SalmonSchedule3::find()
                    ->with(['map'])
                    ->andWhere(['is_eggstra_work' => true])
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
