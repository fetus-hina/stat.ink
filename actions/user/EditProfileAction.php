<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Exception;
use Throwable;
use Yii;
use app\models\Environment;
use app\models\Language;
use app\models\ProfileForm;
use app\models\Region;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction as BaseAction;

use function array_map;
use function base64_encode;
use function hash;
use function preg_replace;
use function rtrim;
use function sprintf;
use function trim;

class EditProfileAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $ident = Yii::$app->user->getIdentity();
        $form = new ProfileForm();
        if ($request->isPost) {
            $form->load($request->bodyParams);
            if ($form->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $ident->attributes = $form->attributes;
                    $ident->env_id = $this->findOrCreateEnvironmentId($form->env);
                    if ($ident->save()) {
                        $transaction->commit();
                        $this->controller->redirect(['user/profile']);
                        return;
                    }
                } catch (Throwable $e) {
                }
                $transaction->rollback();
            }
        } else {
            $form->attributes = $ident->attributes;
            if ($ident->env) {
                $form->env = $ident->env->text;
            }
        }

        return $this->controller->render('edit-profile', [
            'user' => $ident,
            'form' => $form,
            'languages' => ArrayHelper::map(
                array_map(
                    function ($row) {
                        $row['_name'] = sprintf(
                            '%s / %s',
                            $row['name'],
                            $row['name_en'],
                        );
                        return $row;
                    },
                    Language::find()->orderBy('name')->asArray()->all(),
                ),
                'id',
                '_name',
            ),
            'regions' => ArrayHelper::map(
                array_map(
                    fn (array $row): array => [
                        'id' => $row['id'],
                        'name' => Yii::t('app-region', $row['name']),
                    ],
                    Region::find()->orderBy('id')->asArray()->all(),
                ),
                'id',
                'name',
            ),
        ]);
    }

    protected function findOrCreateEnvironmentId($text)
    {
        $text = preg_replace('/\x0d\x0a|\x0d|\x0a/', "\n", (string)$text);
        $text = trim($text);
        if ($text === '') {
            return null;
        }

        $hash = rtrim(base64_encode(hash('sha256', $text, true)), '=');
        $model = Environment::findOne(['sha256sum' => $hash]);
        if ($model) {
            return $model->id;
        }

        $model = new Environment();
        $model->sha256sum = $hash;
        $model->text = $text;
        if (!$model->save()) {
            throw new Exception();
        }
        return $model->id;
    }
}
