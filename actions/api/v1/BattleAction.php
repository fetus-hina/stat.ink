<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/IkaLogLog/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\v1;

use Yii;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UploadedFile;
use yii\web\ViewAction as BaseAction;
use app\models\api\v1\PostBattleForm;

class BattleAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        if ($request->isPost) {
            return $this->runPost();
        }
        throw new MethodNotAllowedHttpException();
    }

    private function runPost()
    {
        $request = Yii::$app->getRequest();
        $form = new PostBattleForm();
        $form->attributes = $request->getBodyParams();
        foreach (['image_judge', 'image_result'] as $key) {
            if ($form->$key == '') {
                $form->$key = UploadedFile::getInstance($form, $key);
            }
        }
        var_dump($form->attributes);
        var_dump($form->validate());
        var_dump($form->getErrors());
    }
}
