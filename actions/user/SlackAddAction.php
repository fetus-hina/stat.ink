<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction as BaseAction;
use app\models\Language;
use app\models\Slack;
use app\models\SlackAddForm;

class SlackAddAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $form = new SlackAddForm();
        if ($request->isPost) {
            $form->load($request->bodyParams);
            if ($form->validate()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $ident = Yii::$app->user->getIdentity();
                    if ($form->save($ident)) {
                        $transaction->commit();
                        $this->controller->redirect(['user/profile']);
                        return;
                    }
                } catch (\Throwable $e) {
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
        uasort($langs, 'strnatcasecmp');

        return $this->controller->render('slack-add', [
            'form' => $form,
            'languages' => $langs,
        ]);
    }
}
