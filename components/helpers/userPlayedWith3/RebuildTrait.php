<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\userPlayedWith3;

use app\models\Battle3PlayedWith;
use app\models\Salmon3PlayedWith;
use app\models\User;
use yii\db\Connection;

trait RebuildTrait
{
    use UpdateTrait;

    private static function rebuildBattleImpl(Connection $db, User $user): true
    {
        Battle3PlayedWith::deleteAll(['user_id' => $user->id]);
        self::updateBattleImpl($db, $user, null);

        return true;
    }

    private static function rebuildSalmonImpl(Connection $db, User $user): true
    {
        Salmon3PlayedWith::deleteAll(['user_id' => $user->id]);
        self::updateSalmonImpl($db, $user, null);

        return true;
    }
}
