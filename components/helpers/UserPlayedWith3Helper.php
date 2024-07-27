<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Yii;
use app\components\helpers\userPlayedWith3\LockTrait;
use app\components\helpers\userPlayedWith3\RebuildTrait;
use app\components\helpers\userPlayedWith3\UpdateTrait;
use app\models\Battle3;
use app\models\Battle3PlayedWith;
use app\models\Salmon3;
use app\models\Salmon3PlayedWith;
use app\models\User;
use yii\db\Connection;
use yii\db\Transaction;

final class UserPlayedWith3Helper
{
    use LockTrait;
    use RebuildTrait;
    use UpdateTrait;

    public static function rebuildUserBattle(User $user): bool
    {
        if (!self::tryLock(Battle3PlayedWith::tableName(), $user)) {
            return false;
        }

        return TypeHelper::instanceOf(Yii::$app->db, Connection::class)
            ->transaction(
                fn (Connection $db): true => self::rebuildBattleImpl($db, $user),
                Transaction::REPEATABLE_READ,
            );
    }

    public static function rebuildUserSalmon(User $user): bool
    {
        if (!self::tryLock(Salmon3PlayedWith::tableName(), $user)) {
            return false;
        }

        return TypeHelper::instanceOf(Yii::$app->db, Connection::class)
            ->transaction(
                fn (Connection $db): true => self::rebuildSalmonImpl($db, $user),
                Transaction::REPEATABLE_READ,
            );
    }

    public static function updateBattle(User $user, Battle3 $battle): bool
    {
        if (!self::tryLock(Battle3PlayedWith::tableName(), $user)) {
            return false;
        }

        return TypeHelper::instanceOf(Yii::$app->db, Connection::class)
            ->transaction(
                fn (Connection $db): true => self::updateBattleImpl($db, $user, $battle),
                Transaction::REPEATABLE_READ,
            );
    }

    public static function updateSalmon(User $user, Salmon3 $salmon): bool
    {
        if (!self::tryLock(Salmon3PlayedWith::tableName(), $user)) {
            return false;
        }

        return TypeHelper::instanceOf(Yii::$app->db, Connection::class)
            ->transaction(
                fn (Connection $db): true => self::updateSalmonImpl($db, $user, $salmon),
                Transaction::REPEATABLE_READ,
            );
    }
}
