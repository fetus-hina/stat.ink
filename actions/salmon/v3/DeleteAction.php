<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3;

use Yii;
use app\components\helpers\UuidRegexp;
use app\models\Salmon3;
use yii\base\Action;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

use const DATE_ATOM;

final class DeleteAction extends Action
{
    public function run(string $screen_name, string $battle): Response
    {
        if (!\preg_match(UuidRegexp::get(true), $battle)) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $user = Yii::$app->user->identity;
        if (!$user) {
            throw new ForbiddenHttpException();
        }

        $model = Salmon3::find()
            ->innerJoinWith(['user'], false)
            ->andWhere([
                '{{%salmon3}}.[[is_deleted]]' => false,
                '{{%salmon3}}.[[user_id]]' => $user->id,
                '{{%salmon3}}.[[uuid]]' => $battle,
                '{{%user}}.[[screen_name]]' => $screen_name,
            ])
            ->limit(1)
            ->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $model->is_deleted = true;
        $model->updated_at = \date(DATE_ATOM, $_SERVER['REQUEST_TIME'] ?? time());
        if (!$model->save()) {
            throw new ServerErrorHttpException();
        }

        $c = $this->controller;
        assert($c instanceof Controller);

        return $c->redirect(['salmon-v3/index', 'screen_name' => $user->screen_name]);
    }
}
