<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use Yii;
use app\components\helpers\Season3Helper;
use app\models\Rule3;
use app\models\Season3;
use yii\base\Action;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function assert;

use const SORT_ASC;

final class XPowerDistrib3Action extends Action
{
    private const PARAM_SEASON_ID = 'season';

    public function run(): Response|string
    {
        $controller = $this->controller;
        assert($controller instanceof Controller);

        if (!$season = Season3Helper::getUrlTargetSeason(self::PARAM_SEASON_ID)) {
            $season = Season3Helper::getCurrentSeason();
            return $season
                ? $controller->redirect(['entire/xpower-distrib3', self::PARAM_SEASON_ID => $season->id])
                : throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $rules = ArrayHelper::map(
            Rule3::find()
                ->innerJoinWith(['group'], false)
                ->andWhere(['{{%rule_group3}}.[[key]]' => 'gachi'])
                ->orderBy(['rank' => SORT_ASC])
                ->all(),
            'id',
            fn (Rule3 $v): Rule3 => $v,
        );

        return Yii::$app->db->transaction(
            fn (): string => $controller->render('v3/xpower-distrib3', [
                'rules' => $rules,
                'season' => $season,
                'seasons' => Season3Helper::getSeasons(xSupported: true),
                'seasonUrl' => fn (Season3 $season): string => Url::to(
                    ['entire/xpower-distrib3', self::PARAM_SEASON_ID => $season->id],
                ),
            ]),
            Transaction::REPEATABLE_READ,
        );
    }
}
