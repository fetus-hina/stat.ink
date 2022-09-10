<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\actions\api\v3\BattleAction;
use app\actions\api\v3\LobbyAction;
use app\actions\api\v3\RankAction;
use app\actions\api\v3\RuleAction;
use app\actions\api\v3\StageAction;
use app\actions\api\v3\WeaponAction;
use app\components\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\auth\HttpBearerAuth;

final class ApiV3Controller extends Controller
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
                    'battle' => ['head', 'get', 'post', 'put'],
                    '*' => ['head', 'get'],
                ],
            ],
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'only' => [
                    'battle',
                ],
                'optional' => [
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'battle' => BattleAction::class,
            'lobby' => LobbyAction::class,
            'rank' => RankAction::class,
            'rule' => RuleAction::class,
            'stage' => StageAction::class,
            'weapon' => WeaponAction::class,
        ];
    }
}
