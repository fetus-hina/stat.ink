<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\entire\AgentAction;
use app\actions\entire\CombinedAgentAction;
use app\actions\entire\Festpower2Action;
use app\actions\entire\KDWin2Action;
use app\actions\entire\KDWinAction;
use app\actions\entire\Knockout2Action;
use app\actions\entire\KnockoutAction;
use app\actions\entire\SalmonClearAction;
use app\actions\entire\UsersAction;
use app\actions\entire\Weapon2Action;
use app\actions\entire\WeaponAction;
use app\actions\entire\Weapons2Action;
use app\actions\entire\Weapons2TierAction;
use app\actions\entire\WeaponsAction;
use app\actions\entire\WeaponsUseAction;
use app\actions\entire\salmon3\RandomLoanAction;
use app\actions\entire\salmon3\TideAction;
use app\actions\entire\v3\Knockout3Action;
use app\actions\entire\v3\SpecialUse3Action;
use app\actions\entire\v3\XPowerDistrib3Action;
use app\components\web\Controller;
use yii\filters\VerbFilter;

final class EntireController extends Controller
{
    public $layout = 'main';

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'agent' => AgentAction::class,
            'combined-agent' => CombinedAgentAction::class,
            'festpower2' => Festpower2Action::class,
            'kd-win' => KDWinAction::class,
            'kd-win2' => KDWin2Action::class,
            'knockout' => KnockoutAction::class,
            'knockout2' => Knockout2Action::class,
            'knockout3' => Knockout3Action::class,
            'salmon-clear' => SalmonClearAction::class,
            'salmon3-random-loan' => RandomLoanAction::class,
            'salmon3-tide' => TideAction::class,
            'special-use3' => SpecialUse3Action::class,
            'users' => UsersAction::class,
            'weapon' => WeaponAction::class,
            'weapon2' => Weapon2Action::class,
            'weapons' => WeaponsAction::class,
            'weapons-use' => WeaponsUseAction::class,
            'weapons2' => Weapons2Action::class,
            'weapons2-tier' => Weapons2TierAction::class,
            'xpower-distrib3' => XPowerDistrib3Action::class,
        ];
    }
}
