<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\v1;

use Yii;
use app\models\Weapon;
use app\models\api\v1\WeaponGetForm;
use yii\web\ViewAction as BaseAction;

use function array_map;

class WeaponAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'json';

        $form = new WeaponGetForm();
        $form->attributes = Yii::$app->getRequest()->get();
        if (!$form->validate()) {
            $response->statusCode = 400;
            return [
                'error' => $form->getErrors(),
            ];
        }

        $query = Weapon::find()
            ->with(['type', 'subweapon', 'special'])
            ->orderBy('[[id]]');
        $form->filterQuery($query);

        return array_map(
            fn ($weapon) => $weapon->toJsonArray(),
            $query->all(),
        );
    }
}
