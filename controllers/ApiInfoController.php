<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\controllers;

use app\actions\api\info\Ability3Action;
use app\actions\api\info\BossSalmonid3Action;
use app\actions\api\info\Gear2Action;
use app\actions\api\info\GearAction;
use app\actions\api\info\SalmonTitle3Action;
use app\actions\api\info\Stage2Action;
use app\actions\api\info\Stage3Action;
use app\actions\api\info\Weapon2Action;
use app\actions\api\info\Weapon3Action;
use app\actions\api\info\WeaponAction;
use app\components\web\Controller;

final class ApiInfoController extends Controller
{
    public $layout = 'main';

    public function actions()
    {
        return [
            'ability3' => Ability3Action::class,
            'boss-salmonid3' => BossSalmonid3Action::class,
            'gear-clothing' => [
                'class' => GearAction::class,
                'type' => 'clothing',
            ],
            'gear-headgear' => [
                'class' => GearAction::class,
                'type' => 'headgear',
            ],
            'gear-shoes' => [
                'class' => GearAction::class,
                'type' => 'shoes',
            ],
            'gear2-clothing' => [
                'class' => Gear2Action::class,
                'type' => 'clothing',
            ],
            'gear2-headgear' => [
                'class' => Gear2Action::class,
                'type' => 'headgear',
            ],
            'gear2-shoes' => [
                'class' => Gear2Action::class,
                'type' => 'shoes',
            ],
            'salmon-title3' => SalmonTitle3Action::class,
            'stage2' => Stage2Action::class,
            'stage3' => Stage3Action::class,
            'weapon' => WeaponAction::class,
            'weapon2' => Weapon2Action::class,
            'weapon3' => Weapon3Action::class,
        ];
    }
}
