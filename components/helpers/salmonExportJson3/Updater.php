<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\salmonExportJson3;

use RuntimeException;
use Yii;
use app\components\formatters\api\v3\SalmonApiFormatter;
use app\models\Salmon3;
use app\models\SalmonExportJson3;
use app\models\User;
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
        foreach (self::getJobsForUpdate($user) as $battle) {
            $json .= Json::encode(
                SalmonApiFormatter::toJson(
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
     * @return Salmon3[]
     */
    private static function getJobsForUpdate(User $user): array
    {
        return Salmon3::find()
            ->with([
                'agent',
                'bigStage.map3Aliases',
                'bosses.salmonBoss3Aliases',
                'failReason',
                'kingSalmonid.salmonKing3Aliases',
                'salmonBossAppearance3s',
                'salmonPlayer3s.salmonPlayerWeapon3s.weapon',
                'salmonPlayer3s.salmonPlayerWeapon3s.weapon.salmonWeapon3Aliases',
                'salmonPlayer3s.special',
                'salmonPlayer3s.splashtagTitle',
                'salmonPlayer3s.uniform',
                'salmonPlayer3s.uniform.salmonUniform3Aliases',
                'salmonWave3s.event.salmonEvent3Aliases',
                'salmonWave3s.salmonSpecialUse3s.special',
                'salmonWave3s.tide',
                'schedule',
                'stage.salmonMap3Aliases',
                'titleAfter.salmonTitle3Aliases',
                'titleBefore.salmonTitle3Aliases',
                'user',
                'variables',
                'version',
            ])
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->andWhere(['>', 'id', self::getLastSavedBattleId($user)])
            ->orderBy(['id' => SORT_ASC])
            ->limit(200)
            ->all();
    }

    private static function getLastSavedBattleId(User $user): int
    {
        $model = SalmonExportJson3::find()
            ->andWhere(['user_id' => $user->id])
            ->limit(1)
            ->one();
        return $model?->last_battle_id ?? -1;
    }

    private static function setLastSavedBattleId(User $user, int $id): void
    {
        $model = SalmonExportJson3::find()->andWhere(['user_id' => $user->id])->limit(1)->one()
            ?? Yii::createObject([
                'class' => SalmonExportJson3::class,
                'user_id' => $user->id,
            ]);

        $model->last_battle_id = $id;
        $model->updated_at = date('Y-m-d\TH:i:sP', time());
        $model->save();
    }
}
