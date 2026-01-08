<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use Yii;
use app\components\helpers\Season3Helper;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\Special3;
use app\models\StatSpecialUse3;
use yii\base\Action;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

use function assert;
use function filter_var;
use function is_float;

use const FILTER_VALIDATE_FLOAT;
use const SORT_ASC;

final class SpecialUse3Action extends Action
{
    private const PARAM_SEASON_ID = Season3Helper::DEFAULT_SEASON_PARAM_NAME;

    public function run(): Response|string
    {
        $controller = $this->controller;
        assert($controller instanceof Controller);

        if (!$season = Season3Helper::getUrlTargetSeason(self::PARAM_SEASON_ID)) {
            $season = Season3Helper::getCurrentSeason();
            return $season
                ? $controller->redirect(['entire/special-use3',
                    self::PARAM_SEASON_ID => $season->id,
                ])
                : throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $specials = ArrayHelper::map(
            Special3::find()
                ->orderBy(['rank' => SORT_ASC])
                ->all(),
            'id',
            fn (Special3 $v): Special3 => $v,
        );

        $rules = ArrayHelper::map(
            Rule3::find()
                ->innerJoinWith(['group'], false)
                ->andWhere(['{{%rule_group3}}.[[key]]' => 'gachi'])
                ->orderBy(['rank' => SORT_ASC])
                ->all(),
            'id',
            fn (Rule3 $v): Rule3 => $v,
        );

        $xMatch = Lobby3::find()
            ->andWhere(['key' => 'xmatch'])
            ->limit(1)
            ->one();
        if (!$xMatch) {
            throw new ServerErrorHttpException();
        }

        $params = Yii::$app->db->transaction(
            fn () => [
                'data' => $this->getData($season),
                'maxAvgUses' => $this->getMaxAvgUses($season),
                'rules' => $rules,
                'season' => $season,
                'seasons' => Season3Helper::getSeasons(xSupported: true),
                'specials' => $specials,
                'total' => $this->getTotalData($season),
                'xMatch' => $xMatch,
                'seasonUrl' => fn (Season3 $season): string => Url::to(
                    ['entire/special-use3', self::PARAM_SEASON_ID => $season->id],
                ),
            ],
            Transaction::REPEATABLE_READ,
        );

        return $this->controller->render('v3/special-use3', $params);
    }

    /**
     * @return StatSpecialUse3[]
     */
    private function getTotalData(Season3 $season): array
    {
        return StatSpecialUse3::find()
            ->innerJoinWith(['special'], false)
            ->andWhere([
                'rule_id' => null,
                'season_id' => $season->id,
            ])
            ->orderBy(['{{%special3}}.[[rank]]' => SORT_ASC])
            ->all();
    }

    /**
     * @return array<int, StatSpecialUse3[]>
     */
    private function getData(Season3 $season): array
    {
        $models = StatSpecialUse3::find()
            ->innerJoinWith(['special'], false)
            ->andWhere(['season_id' => $season->id])
            ->andWhere(['not', ['rule_id' => null]])
            ->orderBy([
                '[[rule_id]]' => SORT_ASC,
                '{{%special3}}.[[rank]]' => SORT_ASC,
            ])
            ->all();

        $results = [];
        foreach ($models as $model) {
            if (!isset($results[$model->rule_id])) {
                $results[$model->rule_id] = [];
            }
            $results[$model->rule_id][] = $model;
        }

        return $results;
    }

    private function getMaxAvgUses(Season3 $season): ?float
    {
        $v = filter_var(
            StatSpecialUse3::find()
                ->andWhere(['season_id' => $season->id])
                ->max('avg_uses'),
            FILTER_VALIDATE_FLOAT,
        );
        return is_float($v) ? $v : null;
    }
}
