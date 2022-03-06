<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
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
use app\components\web\Controller;
use yii\filters\VerbFilter;

class EntireController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    '*' => [ 'head', 'get' ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'agent' => [ 'class' => AgentAction::class ],
            'combined-agent' => [ 'class' => CombinedAgentAction::class ],
            'festpower2' => [ 'class' => Festpower2Action::class ],
            'kd-win' => [ 'class' => KDWinAction::class ],
            'kd-win2' => [ 'class' => KDWin2Action::class ],
            'knockout' => [ 'class' => KnockoutAction::class ],
            'knockout2' => [ 'class' => Knockout2Action::class ],
            'salmon-clear' => [ 'class' => SalmonClearAction::class ],
            'users' => [ 'class' => UsersAction::class ],
            'weapon' => [ 'class' => WeaponAction::class ],
            'weapon2' => [ 'class' => Weapon2Action::class ],
            'weapons' => [ 'class' => WeaponsAction::class ],
            'weapons-use' => [ 'class' => WeaponsUseAction::class ],
            'weapons2' => [ 'class' => Weapons2Action::class ],
            'weapons2-tier' => ['class' => Weapons2TierAction::class ],
        ];
    }
}
