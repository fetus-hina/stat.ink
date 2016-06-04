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
    public function actions()
    {
        $prefix = 'app\actions\api\info';
        return [
            'weapon'        => [ 'class' => $prefix . '\WeaponAction' ],
            'gear-headgear' => [ 'class' => $prefix . '\GearAction', 'type' => 'headgear'],
            'gear-clothing' => [ 'class' => $prefix . '\GearAction', 'type' => 'clothing'],
            'gear-shoes'    => [ 'class' => $prefix . '\GearAction', 'type' => 'shoes'],
        ];
    }
}
