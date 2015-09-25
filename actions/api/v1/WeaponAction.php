<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\v1;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Weapon;

class WeaponAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';
        return array_map(
            function ($weapon) {
                return [
                    'key' => $weapon->key,
                    'name' => [
                        'ja_JP' => $weapon->name,
                    ],
                    'type' => [
                        'key' => $weapon->type->key,
                        'name' => [
                            'ja_JP' => $weapon->type->name,
                        ]
                    ],
                ];
            },
            Weapon::find()->with('type')->all()
        );
    }
}
