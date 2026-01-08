<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\userExportJson3;

use RuntimeException;
use Yii;
use app\components\formatters\api\v3\BattleApiFormatter;
use app\components\helpers\Battle3Helper;
use app\models\Battle3;
use app\models\User;
use app\models\UserExportJson3;
use yii\helpers\FileHelper;
use yii\helpers\Json;

use function date;
use function dirname;
use function fclose;
use function fflush;
use function flock;
use function fopen;
use function fseek;
use function fwrite;
use function gzencode;
use function time;

use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const LOCK_EX;
use const LOCK_UN;
use const SEEK_END;
use const SORT_ASC;

trait Updater
{
    private static function updateJson(User $user, string $jsonPath): int
    {
        $updated = 0;
        while (true) {
            if ($c = self::updateJsonImpl($user, $jsonPath)) {
                $updated += $c;
            } else {
                break;
            }
        }

        return $updated;
    }

    private static function updateJsonImpl(User $user, string $jsonPath): int
    {
        $json = '';
        $updated = 0;
        $lastId = null;
        foreach (self::getBattlesForUpdate($user) as $battle) {
            $json .= Json::encode(
                BattleApiFormatter::toJson(
                    model: $battle,
                    isAuthenticated: true,
                    fullTranslate: false,
                ),
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            ) . "\n";
            ++$updated;
            $lastId = $battle->id;
        }

        if ($updated) {
            FileHelper::createDirectory(dirname($jsonPath));

            if (!$fh = @fopen($jsonPath, 'cb')) {
                throw new RuntimeException('Failed to open json file');
            }
            try {
                flock($fh, LOCK_EX);
                try {
                    fseek($fh, 0, SEEK_END);
                    fwrite($fh, (string)gzencode($json, 9));
                    fflush($fh);

                    self::setLastSavedBattleId($user, $lastId);
                } finally {
                    flock($fh, LOCK_UN);
                }
            } finally {
                fclose($fh);
            }
        }

        return $updated;
    }

    /**
     * @return Battle3[]
     */
    private static function getBattlesForUpdate(User $user): array
    {
        return Battle3::find()
            ->with(Battle3Helper::getRelationsForApiResponse(listed: true))
            ->andWhere(['and',
                ['user_id' => $user->id],
                ['is_deleted' => false],
                ['>', 'id', self::getLastSavedBattleId($user)],
            ])
            ->orderBy(['id' => SORT_ASC])
            ->limit(200)
            ->all();
    }

    private static function getLastSavedBattleId(User $user): int
    {
        $model = UserExportJson3::find()
            ->andWhere(['user_id' => $user->id])
            ->limit(1)
            ->one();
        return $model?->last_battle_id ?? -1;
    }

    private static function setLastSavedBattleId(User $user, int $id): void
    {
        $model = UserExportJson3::find()->andWhere(['user_id' => $user->id])->limit(1)->one()
            ?? Yii::createObject([
                'class' => UserExportJson3::class,
                'user_id' => $user->id,
            ]);

        $model->last_battle_id = $id;
        $model->updated_at = date('Y-m-d\TH:i:sP', time());
        $model->save();
    }
}
