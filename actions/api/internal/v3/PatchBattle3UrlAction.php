<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\internal\v3;

use Throwable;
use Yii;
use app\components\formatters\api\v3\BattleApiFormatter;
use app\models\Battle3;
use app\models\api\internal\PatchBattle3UrlForm;
use jp3cki\uuid\Uuid;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_map;
use function is_string;

final class PatchBattle3UrlAction extends Action
{
    public Battle3|null $model = null;

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

        $this->model = Battle3::find()
            ->with(
                ArrayHelper::toFlatten([
                    [
                        'battlePlayer3s',
                        'battlePlayer3s.splashtagTitle',
                        'battlePlayer3s.weapon',
                        'battlePlayer3s.weapon.canonical',
                        'battlePlayer3s.weapon.mainweapon',
                        'battlePlayer3s.weapon.mainweapon.type',
                        'battlePlayer3s.weapon.special',
                        'battlePlayer3s.weapon.subweapon',
                        'battlePlayer3s.weapon.weapon3Aliases',
                    ],
                    array_map(
                        fn (string $base): array => [
                            "battlePlayer3s.{$base}",
                            "battlePlayer3s.{$base}.ability",
                            "battlePlayer3s.{$base}.gearConfigurationSecondary3s",
                            "battlePlayer3s.{$base}.gearConfigurationSecondary3s.ability",
                        ],
                        ['clothing', 'headgear', 'shoes'],
                    ),
                ]),
            )
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

        return BattleApiFormatter::toJson(
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
