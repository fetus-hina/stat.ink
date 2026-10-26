<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use Override;
use Yii;
use app\actions\api\internal\latestBattles\UserFormatter;
use app\components\helpers\CombinedBattles;
use app\models\Battle;
use app\models\Battle2;
use app\models\Battle3;
use app\models\Salmon2;
use app\models\Salmon3;
use yii\db\Transaction;
use yii\web\ServerErrorHttpException;

final class MyLatestBattlesAction extends BaseLatestBattlesAction
{
    use UserFormatter;

    private const BATTLE_LIMIT = 12;

    protected function isPrecheckOK(): bool
    {
        return !Yii::$app->user->isGuest;
    }

    /**
     * @return array<Battle|Battle2|Battle3|Salmon2|Salmon3>
     */
    protected function fetchBattles(): array
    {
        if (!$user = Yii::$app->user->identity) {
            throw new ServerErrorHttpException();
        }

        return Yii::$app->db->transaction(
            fn (): array => CombinedBattles::getUserRecentBattles(
                $user,
                static::BATTLE_LIMIT,
            ),
            Transaction::REPEATABLE_READ,
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function run(): array
    {
        $json = parent::run();
        if ($json['battles']) {
            if (!$user = Yii::$app->user->identity) {
                throw new ServerErrorHttpException();
            }

            $json['user'] = self::formatUser($user);
        }
        return $json;
    }

    protected function getHeading(): string
    {
        return Yii::t('app', '{name}\'s Battles');
    }
}
