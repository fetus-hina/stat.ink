<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\show\v3\BattleAction;
use app\actions\show\v3\DeleteBattleAction;
use app\actions\show\v3\UserAction;
use app\actions\show\v3\stats\BadgeAction;
use app\actions\show\v3\stats\CorrectionBadgeAction;
use app\actions\show\v3\stats\MapRuleAction;
use app\actions\show\v3\stats\MedalAction;
use app\actions\show\v3\stats\SeasonXPowerAction;
use app\actions\show\v3\stats\WeaponsAction;
use app\actions\show\v3\stats\WinRateAction;
use app\components\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

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
                    'stats-correction-badge' => ['head', 'get', 'post'],
                    '*' => ['head', 'get'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'delete-battle',
                    'stats-correction-badge',
                ],
                'rules' => [
                    [
                        'actions' => [
                            'delete-battle',
                            'stats-correction-badge',
                        ],
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
            'stats-badge' => BadgeAction::class,
            'stats-correction-badge' => CorrectionBadgeAction::class,
            'stats-map-rule' => MapRuleAction::class,
            'stats-medal' => MedalAction::class,
            'stats-weapons' => WeaponsAction::class,
            'stats-win-rate' => WinRateAction::class,
            'stats-season-x-power' => SeasonXPowerAction::class,
            'user' => UserAction::class,
            'user-json' => [
                'class' => UserAction::class,
                'format' => Response::FORMAT_JSON,
            ],
        ];
    }
}
