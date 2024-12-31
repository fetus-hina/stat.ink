<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Yii;
use app\components\helpers\userExportJson3\Updater;
use app\models\User;
use app\models\UserExportJson3;

use function hash_hmac;
use function vsprintf;

final class UserExportJson3Helper
{
    use Updater;

    public static function update(User $user): void
    {
        self::updateJson($user, self::getPath($user));

        // TODO: recompress
    }

    public static function lockName(User $user): string
    {
        return hash_hmac(
            'sha256',
            (string)$user->id,
            UserExportJson3::class,
        );
    }

    public static function getPath(User $user): string
    {
        return vsprintf('%s/%02d/%d.json.gz', [
            Yii::getAlias('@app/runtime/user-json3'),
            $user->id % 100,
            $user->id,
        ]);
    }
}
