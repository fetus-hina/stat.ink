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
use app\components\helpers\TypeHelper;
use app\models\EggstraWorkOfficialResult3;
use app\models\SalmonSchedule3;
use app\models\StatEggstraWorkDistrib3;
use app\models\StatEggstraWorkDistribAbstract3;
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

        $abstract = StatEggstraWorkDistribAbstract3::find()
            ->andWhere(['schedule_id' => $scheduleId])
            ->limit(1)
            ->one($db);

        $histogram = ArrayHelper::map(
            StatEggstraWorkDistrib3::find()
                ->andWhere(['schedule_id' => $scheduleId])
                ->orderBy(['golden_egg' => SORT_ASC])
                ->all($db),
            'golden_egg',
            'users',
        );

        $normalDistrib = null;
        $estimatedDistrib = null;
        $ruleOfThumbDistrib = null;
        $chartMax = null;
        if (
            $abstract &&
            $histogram &&
            $abstract->average >= 1 &&
            $abstract->median !== null &&
            $abstract->q1 !== null &&
            $abstract->q3 !== null &&
            $abstract->stddev !== null &&
            $abstract->users >= 10
        ) {
            $normalDistrib = new NormalDistribution(
                (float)$abstract->average,
                (float)$abstract->stddev,
            );
            $estimatedDistrib = self::estimatedDistrib($schedule->eggstraWorkOfficialResult3);
            $ruleOfThumbDistrib = self::ruleOfThumbDistrib($abstract);
            $chartMax = max(array_keys($histogram));
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
        $z20 = $nd->inverse(0.60 + (1 - 0.60) / 2);
        $z5 = $nd->inverse(0.90 + (1 - 0.90) / 2); // 1.64485
        unset($nd);

        assert(abs($z5 - $z20) > 0.000001);
        assert(abs($z5) > 0.000001);

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

        return new NormalDistribution(
            $estimatedAverage,
            ((float)(int)$official->gold - $estimatedAverage) / $z5, // stddev
        );
    }

    private static function ruleOfThumbDistrib(StatEggstraWorkDistribAbstract3 $abstract): ?NormalDistribution
    {
        if (
            $abstract->users < 50 ||
            $abstract->top_5_pct === null ||
            $abstract->top_20_pct === null ||
            $abstract->median === null ||
            $abstract->top_5_pct <= $abstract->top_20_pct &&
            $abstract->top_20_pct <= $abstract->median
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

        $n5 = $abstract->top_20_pct;
        $n20 = $abstract->median;
        $estimatedAverage = ($z5 * $n20 - $z20 * $n5) / ($z5 - $z20);

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

        return Yii::$app->cache->getOrSet(
            [__METHOD__, $date],
            fn (): array => ArrayHelper::map(
                SalmonSchedule3::find()
                    ->with([
                        'eggstraWorkOfficialResult3',
                        'map',
                    ])
                    ->andWhere([
                        'is_eggstra_work' => true,
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
