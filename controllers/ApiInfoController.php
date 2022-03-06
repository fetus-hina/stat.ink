<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\controllers;

use app\components\web\Controller;

class ApiInfoController extends Controller
{
    public $layout = 'main';

    public function actions()
    {
        $prefix = 'app\actions\api\info';
        return [
            'gear-clothing' => [ 'class' => $prefix . '\GearAction', 'type' => 'clothing'],
            'gear-headgear' => [ 'class' => $prefix . '\GearAction', 'type' => 'headgear'],
            'gear-shoes' => [ 'class' => $prefix . '\GearAction', 'type' => 'shoes'],
            'gear2-clothing' => [ 'class' => $prefix . '\Gear2Action', 'type' => 'clothing'],
            'gear2-headgear' => [ 'class' => $prefix . '\Gear2Action', 'type' => 'headgear'],
            'gear2-shoes' => [ 'class' => $prefix . '\Gear2Action', 'type' => 'shoes'],
            'stage2' => [ 'class' => $prefix . '\Stage2Action' ],
            'weapon' => [ 'class' => $prefix . '\WeaponAction' ],
            'weapon2' => [ 'class' => $prefix . '\Weapon2Action' ],
        ];
    }
}
