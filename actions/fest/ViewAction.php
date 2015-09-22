<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\fest;

use Yii;
use yii\web\ViewAction as BaseAction;
use yii\web\NotFoundHttpException;
use app\models\Fest;

class ViewAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $id = $request->get('id');
        if (!is_scalar($id) ||
                !($fest = Fest::findOne(['id' => $id]))) {
            throw new NotFoundHttpException();
        }

        return $this->controller->render('view.tpl', [
            'fest' => $fest,
        ]);
    }
}
