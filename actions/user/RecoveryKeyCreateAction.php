<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use DateTime;
use ParagonIE\ConstantTime\Base64UrlSafe;
use Yii;
use app\components\helpers\Password;
use app\components\helpers\TypeHelper;
use app\models\UserPasswordRecoveryKey;
use jp3cki\uuid\Uuid;
use yii\web\ViewAction as BaseAction;

use function date;
use function random_bytes;

final class RecoveryKeyCreateAction extends BaseAction
{
    public const KEY_LIMIT = 10;

    public function run()
    {
        $ident = Yii::$app->user->getIdentity();
        $req = Yii::$app->request;
        $session = Yii::$app->session;

        $activeCount = (int)UserPasswordRecoveryKey::find()
            ->andWhere(['user_id' => $ident->id, 'used_at' => null, 'revoked_at' => null])
            ->count();
        if ($activeCount >= self::KEY_LIMIT) {
            $session->setFlash(
                'recoveryKeyError',
                Yii::t(
                    'app-recovery-key',
                    'You can have at most {n} active recovery keys.',
                    ['n' => self::KEY_LIMIT],
                ),
            );
            return $this->controller->redirect(['user/recovery-key']);
        }

        $publicId = (string)Uuid::v4();
        $secret = Base64UrlSafe::encodeUnpadded(random_bytes(32));

        $model = Yii::createObject([
            'class' => UserPasswordRecoveryKey::class,
            'user_id' => $ident->id,
            'public_id' => $publicId,
            'secret_hash' => Password::hash($secret),
            'created_at' => date(
                DateTime::ATOM,
                TypeHelper::int($_SERVER['REQUEST_TIME']),
            ),
            'created_ip' => $req->userIP,
        ]);

        if (!$model->save()) {
            $session->setFlash(
                'recoveryKeyError',
                Yii::t('app-recovery-key', 'Failed to create a recovery key.'),
            );
            return $this->controller->redirect(['user/recovery-key']);
        }

        $session->setFlash('justCreatedRecoveryKey', "{$publicId}.{$secret}");
        return $this->controller->redirect(['user/recovery-key']);
    }
}
