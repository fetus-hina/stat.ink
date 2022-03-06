<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\internal;

use Yii;
use app\models\Battle;
use app\models\api\internal\PatchBattleForm as Form;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction;

class PatchBattleAction extends ViewAction
{
    public $battle;

    public function init()
    {
        parent::init();

        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';

        Yii::$app->response->format = 'json';

        $req = Yii::$app->request;
        if (!$req->isPatch) {
            throw new MethodNotAllowedHttpException();
        }

        if (Yii::$app->user->isGuest) {
            throw new BadRequestHttpException();
        }

        $id = $req->get('id');
        if (!is_scalar($id) || !preg_match('/^\d+$/', $id)) {
            throw new BadRequestHttpException();
        }

        if (!$this->battle = Battle::findOne(['id' => $id])) {
            throw new NotFoundHttpException();
        }

        if ($this->battle->user_id != Yii::$app->user->identity->id) {
            throw new ForbiddenHttpException();
        }
    }

    public function run()
    {
        $form = new Form();
        $form->attributes = $_POST;
        if (!$form->validate()) {
            return $this->formatError($form->getErrors());
        }

        $battle = $this->battle;
        foreach ($form->attributes as $key => $value) {
            if ($value !== null) {
                $battle->$key = $value === '' ? null : $value;
            }
        }
        if (!$battle->save()) {
            return $this->formatError($battle->getErrors());
        }

        return $battle->toJsonArray();
    }

    public function formatError(array $errors, int $code = 400): array
    {
        $resp = Yii::$app->getResponse();
        $resp->format = 'json';
        $resp->statusCode = (int)$code;
        return [
            'error' => $errors,
        ];
    }
}
