<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\actions\api\v2\CauseOfDeathAction;
use app\actions\api\v2\GearAction;
use app\actions\api\v2\RuleAction;
use app\actions\api\v2\StageAction;
use app\actions\api\v2\UserStatsAction;
use app\actions\api\v2\WeaponAction;
use app\components\web\Controller;
use yii\filters\VerbFilter;

use function array_merge;

final class ApiV2Controller extends Controller
{
    public $enableCsrfValidation = false;

    public function init()
    {
        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';

        parent::init();
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    '*' => ['head', 'get'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'cause-of-death' => CauseOfDeathAction::class,
            'gear' => GearAction::class,
            'rule' => RuleAction::class,
            'stage' => StageAction::class,
            'user-stats' => UserStatsAction::class,
            'weapon' => WeaponAction::class,
        ];
    }

    public function actionMap()
    {
        return $this->redirect(
            array_merge(Yii::$app->request->get(), ['/api-v2/stage']),
            301,
        );
    }
}
