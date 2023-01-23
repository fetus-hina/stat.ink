<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\show\v3\BattleAction;
use app\actions\show\v3\DeleteBattleAction;
use app\actions\show\v3\UserAction;
use app\actions\show\v3\stats\MapRuleAction;
use app\actions\show\v3\stats\WeaponsAction;
use app\actions\show\v3\stats\WinRateAction;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

final class ShowV3Controller extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-battle' => ['post', 'delete'],
                    '*' => ['head', 'get'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'delete-battle',
                ],
                'rules' => [
                    [
                        'actions' => ['delete-battle'],
                        'roles' => ['@'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'battle' => BattleAction::class,
            'delete-battle' => DeleteBattleAction::class,
            'stats-map-rule' => MapRuleAction::class,
            'stats-weapons' => WeaponsAction::class,
            'stats-win-rate' => WinRateAction::class,
            'user' => UserAction::class,
        ];
    }
}
