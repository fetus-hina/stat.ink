<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Override;
use Throwable;
use Yii;
use app\models\Language;
use app\models\SlackAddForm;
use yii\web\ViewAction as BaseAction;

use function is_array;
use function sprintf;
use function strnatcasecmp;
use function uasort;

class SlackAddAction extends BaseAction
{
    #[Override]
    public function run(): ?string
    {
        $request = Yii::$app->request;
        $form = new SlackAddForm();
        if ($request->isPost) {
            $params = $request->bodyParams;
            $form->load(is_array($params) ? $params : []);
            if ($form->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $ident = Yii::$app->user->getIdentity();
                    if ($form->save($ident)) {
                        $transaction->commit();
                        $this->controller->redirect(['user/profile']);
                        return null;
                    }
                } catch (Throwable $e) {
                }
                $transaction->rollback();
            }
        } else {
            $lang = Language::findOne(['lang' => Yii::$app->language]);
            $form->language_id = $lang->id ?? null;
        }

        $langs = [];
        foreach (Language::find()->asArray()->all() as $row) {
            $langs[$row['id']] = sprintf(
                '%s / %s',
                $row['name'],
                $row['name_en'],
            );
        }
        uasort($langs, strnatcasecmp(...));

        return $this->controller->render('slack-add', [
            'form' => $form,
            'languages' => $langs,
        ]);
    }
}
