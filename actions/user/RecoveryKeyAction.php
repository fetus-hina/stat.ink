<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\models\UserPasswordRecoveryKey;
use yii\web\ViewAction as BaseAction;

use const SORT_ASC;

final class RecoveryKeyAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();

        $recoveryKeys = UserPasswordRecoveryKey::find()
            ->andWhere(['user_id' => $ident->id])
            ->andWhere(['used_at' => null, 'revoked_at' => null])
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        $session = Yii::$app->session;
        return $this->controller->render('recovery-key', [
            'user' => $ident,
            'recoveryKeys' => $recoveryKeys,
            'justCreated' => $session->getFlash('justCreatedRecoveryKey'),
            'errorMessage' => $session->getFlash('recoveryKeyError'),
        ]);
    }
}
