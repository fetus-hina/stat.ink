<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\userPlayedWith3;

use Throwable;
use Yii;
use app\components\helpers\CriticalSection;
use app\components\helpers\Resource;
use app\models\User;

use function hash_hmac;
use function sprintf;

trait LockTrait
{
    private static function tryLock(string $tableName, User $user): ?Resource
    {
        try {
            return CriticalSection::lock(
                name: hash_hmac(
                    'sha256',
                    sprintf('%d@%s', $user->id, $tableName),
                    static::class,
                ),
                timeout: 60,
                mutex: Yii::$app->pgMutex,
            );
        } catch (Throwable) {
        }

        return null;
    }
}
