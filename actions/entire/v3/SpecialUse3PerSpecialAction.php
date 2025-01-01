<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
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
use app\models\StatSpecialUseCount3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function assert;

use const SORT_ASC;

final class SpecialUse3PerSpecialAction extends Action
{
    private const PARAM_SEASON_ID = Season3Helper::DEFAULT_SEASON_PARAM_NAME;

    public function run(string $special): Response|string
    {
        $controller = $this->controller;
        assert($controller instanceof Controller);

        $params = Yii::$app->db->transaction(
            fn (Connection $db): Response|array => $this->doRun($controller, $db, $special),
            Transaction::REPEATABLE_READ,
        );

        return $params instanceof Response
            ? $params
            : $controller->render('v3/special-use3-per-special', $params);
    }

    private function doRun(
        Controller $controller,
        Connection $db,
        string $specialKey,
    ): Response|array {
        $specialModel = $this->getTargetSpecial($db, $specialKey); // throw 404 if not valid
        $season = $this->getTargetSeason($db, $specialModel->key, $controller);
        if ($season instanceof Response) {
            return $season;
        }

        return [
            'data' => $this->getData($db, $season, $specialModel),
            'lobbies' => $this->getLobbies($db),
            'rules' => $this->getRules($db),
            'season' => $season,
            'seasons' => Season3Helper::getSeasons(),
            'special' => $specialModel,
            'specials' => $this->getSpecials($db),

            'seasonUrl' => fn (Season3 $season): string => Url::to(
                ['entire/special-use3-per-special',
                    self::PARAM_SEASON_ID => $season->id,
                    'special' => $specialModel->key,
                ],
            ),

            'specialUrl' => fn (Special3 $special): string => Url::to(
                ['entire/special-use3-per-special',
                    self::PARAM_SEASON_ID => $season->id,
                    'special' => $special->key,
                ],
            ),
        ];
    }

    private function getTargetSpecial(Connection $db, string $key): Special3
    {
        $model = Special3::find()->andWhere(['key' => $key])->limit(1)->one($db);
        return $model
            ? $model
            : throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    private function getTargetSeason(
        Connection $db,
        string $specialKey,
        Controller $controller,
    ): Response|Season3 {
        $season = Season3Helper::getUrlTargetSeason(self::PARAM_SEASON_ID);
        if ($season) {
            return $season;
        }

        $season = Season3Helper::getCurrentSeason();
        return $season
            ? $controller->redirect(['entire/special-use3-per-special',
                'special' => $specialKey,
                self::PARAM_SEASON_ID => $season->id,
            ])
            : throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * @return array<int, array<int, array{battles: int, wins: int}>> `rule => count => [...]`
     */
    private function getData(Connection $db, Season3 $season, Special3 $special): array
    {
        $models = StatSpecialUseCount3::find()
            ->andWhere([
                'season_id' => $season->id,
                'special_id' => $special->id,
            ])
            ->orderBy([
                'rule_id' => SORT_ASC,
                'use_count' => SORT_ASC,
            ])
            ->all();

        return ArrayHelper::map(
            $models,
            'use_count',
            fn (StatSpecialUseCount3 $v): array => [
                'battles' => $v->sample_size,
                'wins' => $v->win,
            ],
            'rule_id',
        );
    }

    /**
     * @return array<string, Lobby3>
     */
    private function getLobbies(Connection $db): array
    {
        return ArrayHelper::map(
            Lobby3::find()->orderBy(['rank' => SORT_ASC])->all(),
            'key',
            fn (Lobby3 $v): Lobby3 => $v,
        );
    }

    /**
     * @return array<int, Rule3>
     */
    private function getRules(Connection $db): array
    {
        return ArrayHelper::map(
            Rule3::find()
                ->andWhere(['not', ['key' => 'tricolor']])
                ->orderBy(['rank' => SORT_ASC])
                ->all($db),
            'id',
            fn (Rule3 $v): Rule3 => $v,
        );
    }

    /**
     * @return array<int, Special3>
     */
    private function getSpecials(Connection $db): array
    {
        return ArrayHelper::map(
            Special3::find()->orderBy(['rank' => SORT_ASC])->all($db),
            'id',
            fn (Special3 $v): Special3 => $v,
        );
    }
}
