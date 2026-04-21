<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\models\UserPasskey;
use yii\web\ViewAction as BaseAction;

use const SORT_ASC;

final class PasskeyAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();

        $passkeys = UserPasskey::find()
            ->andWhere(['user_id' => $ident->id])
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        return $this->controller->render('passkey', [
            'user' => $ident,
            'passkeys' => $passkeys,
        ]);
    }
}
