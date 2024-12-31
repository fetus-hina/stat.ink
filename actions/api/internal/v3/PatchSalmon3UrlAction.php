<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\internal\v3;

use Throwable;
use Yii;
use app\components\formatters\api\v3\SalmonApiFormatter;
use app\models\Salmon3;
use app\models\api\internal\PatchBattle3UrlForm;
use jp3cki\uuid\Uuid;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function is_string;

final class PatchSalmon3UrlAction extends Action
{
    public Salmon3|null $model = null;

    public function init()
    {
        parent::init();

        Yii::$app->language = 'en-US';
        Yii::$app->timeZone = 'Etc/UTC';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $req = Yii::$app->request;
        if (!$req->isPatch) {
            throw new MethodNotAllowedHttpException();
        }

        if (Yii::$app->user->isGuest) {
            throw new BadRequestHttpException();
        }

        $id = $req->get('id');
        if (!is_string($id)) {
            throw new BadRequestHttpException();
        }

        // is valid UUID?
        try {
            Uuid::fromString($id);
        } catch (Throwable $e) {
            throw new BadRequestHttpException();
        }

        $this->model = Salmon3::find()
            ->with([
                'agent',
                'bigStage.bigrunMap3Aliases',
                'bosses.salmonBoss3Aliases',
                'failReason',
                'kingSalmonid.salmonKing3Aliases',
                'salmonBossAppearance3s',
                'salmonPlayer3s.salmonPlayerWeapon3s.weapon',
                'salmonPlayer3s.salmonPlayerWeapon3s.weapon.salmonWeapon3Aliases',
                'salmonPlayer3s.special',
                'salmonPlayer3s.splashtagTitle',
                'salmonPlayer3s.uniform',
                'salmonWave3s.event.salmonEvent3Aliases',
                'salmonWave3s.salmonSpecialUse3s.special',
                'salmonWave3s.tide',
                'schedule',
                'stage.salmonMap3Aliases',
                'titleAfter.salmonTitle3Aliases',
                'titleBefore.salmonTitle3Aliases',
                'user',
                'variables',
                'version',
            ])
            ->andWhere([
                'is_deleted' => false,
                'user_id' => (int)Yii::$app->user->getId(),
                'uuid' => $id,
            ])
            ->limit(1)
            ->one();
        if (!$this->model) {
            throw new NotFoundHttpException();
        }
    }

    public function run()
    {
        $form = Yii::createObject(PatchBattle3UrlForm::class);
        $form->attributes = $_POST;
        if (!$form->validate()) {
            return $this->formatError($form->getErrors());
        }

        $model = $this->model;
        foreach ($form->attributes as $key => $value) {
            if ($value !== null) {
                $model->$key = $value === '' ? null : $value;
            }
        }
        if (!$model->save()) {
            return $this->formatError($model->getErrors());
        }

        return SalmonApiFormatter::toJson(
            $model,
            isAuthenticated: true,
            fullTranslate: false,
        );
    }

    public function formatError(array $errors, int $code = 400): array
    {
        $resp = Yii::$app->getResponse();
        $resp->format = Response::FORMAT_JSON;
        $resp->statusCode = $code;
        return [
            'error' => $errors,
        ];
    }
}
