<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\controllers;

use Yii;
use app\components\web\Controller;

class ApiInfoController extends Controller
{
    public $layout = "main.tpl";

    public function actions()
    {
        $prefix = 'app\actions\api\info';
        return [
            'weapon'        => [ 'class' => $prefix . '\WeaponAction' ],
            'weapon2'       => [ 'class' => $prefix . '\Weapon2Action' ],
            'gear-headgear' => [ 'class' => $prefix . '\GearAction', 'type' => 'headgear'],
            'gear-clothing' => [ 'class' => $prefix . '\GearAction', 'type' => 'clothing'],
            'gear-shoes'    => [ 'class' => $prefix . '\GearAction', 'type' => 'shoes'],
            'gear2-headgear' => [ 'class' => $prefix . '\Gear2Action', 'type' => 'headgear'],
            'gear2-clothing' => [ 'class' => $prefix . '\Gear2Action', 'type' => 'clothing'],
            'gear2-shoes'    => [ 'class' => $prefix . '\Gear2Action', 'type' => 'shoes'],
        ];
    }
}
