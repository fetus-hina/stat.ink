<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Throwable;
use Yii;
use app\components\helpers\T;
use app\models\Language;
use app\models\SlackAddForm;
use yii\base\Action;

final class SlackAddAction extends Action
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
                        T::webController($this->controller)
                            ->redirect(['user/profile']);
                        return;
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
                $row['name_en']
            );
        }
        uasort($langs, 'strnatcasecmp');

        return $this->controller->render('slack-add', [
            'form' => $form,
            'languages' => $langs,
        ]);
    }
}
