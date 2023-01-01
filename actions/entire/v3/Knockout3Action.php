<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use Yii;
use app\components\helpers\Season3Helper;
use app\models\Knockout3;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Rule3;
use app\models\Season3;
use yii\base\Action;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

use function assert;
use function strnatcasecmp;

use const SORT_ASC;

final class Knockout3Action extends Action
{
    private const PARAM_SEASON_ID = 'season';

    public function run(): Response|string
    {
        $controller = $this->controller;
        assert($controller instanceof Controller);

        if (!$season = Season3Helper::getUrlTargetSeason(self::PARAM_SEASON_ID)) {
            $season = Season3Helper::getCurrentSeason();
            return $season
                ? $controller->redirect(['entire/knockout3', self::PARAM_SEASON_ID => $season->id])
                : throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $maps = ArrayHelper::map(
            ArrayHelper::sort(
                Map3::find()
                    ->andWhere(['<', 'release_at', $season->end_at])
                    ->all(),
                fn (Map3 $a, Map3 $b): int => strnatcasecmp(
                    Yii::t('app-map3', $a->name),
                    Yii::t('app-map3', $b->name),
                ),
            ),
            'id',
            fn (Map3 $v): Map3 => $v,
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
                'maps' => $maps,
                'rules' => $rules,
                'season' => $season,
                'seasons' => Season3Helper::getSeasons(xSupported: true),
                'total' => $this->getTotalData($season),
                'xMatch' => $xMatch,
                'seasonUrl' => fn (Season3 $season): string => Url::to(
                    ['entire/knockout3', self::PARAM_SEASON_ID => $season->id],
                ),
            ],
            Transaction::REPEATABLE_READ,
        );

        return $this->controller->render('v3/knockout3', $params);
    }

    /**
     * @return Knockout3[]
     */
    private function getTotalData(Season3 $season): array
    {
        return Knockout3::find()
            ->andWhere([
                'map_id' => null,
                'season_id' => $season->id,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->all();
    }

    /**
     * @return Knockout3[]
     */
    private function getData(Season3 $season): array
    {
        return Knockout3::find()
            ->andWhere(['season_id' => $season->id])
            ->andWhere(['not', ['map_id' => null]])
            ->orderBy(['id' => SORT_ASC])
            ->all();
    }
}
