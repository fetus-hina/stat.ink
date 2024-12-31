<?php

/**
 * @copyright Copyright (C) 2016-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use Exception;
use Throwable;
use Yii;
use app\assets\s3PixelIcons\LobbyIconAsset;
use app\assets\s3PixelIcons\SalmonModeIconAsset;
use app\assets\s3PixelIcons\UiIconAsset;
use app\models\Battle;
use app\models\Battle2;
use app\models\Salmon2;
use app\models\User;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\Url;
use yii\web\AssetBundle;
use yii\web\AssetManager;
use yii\web\HttpException;
use yii\web\View;

use function array_merge;
use function filter_var;
use function is_int;

use const FILTER_VALIDATE_INT;

final class CounterAction extends Action
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'compact-json';

        for ($retry = 0; $retry < 3; ++$retry) {
            try {
                return $this->make();
            } catch (Throwable $e) {
            }
        }

        throw new HttpException(503, 'Fetch failed');
    }

    private function make(): array
    {
        $data = Yii::$app->cache->getOrSet(
            __METHOD__,
            fn (): array => Yii::$app->db->transaction(
                fn (Connection $db): array => [
                    'battle1' => Battle::getRoughCount(),
                    'battle2' => Battle2::getRoughCount(),
                    'battle3' => self::getBattle3RoughCount($db),
                    'salmon2' => Salmon2::getRoughCount(),
                    'salmon3' => self::getSalmon3RoughCount($db),
                    'user' => User::getRoughCount(),
                ],
                Transaction::REPEATABLE_READ,
            ),
            300,
        );

        return array_merge(
            self::format(
                'battle1',
                'battle',
                'Battles',
                $data['battle1'] ?? null,
                LobbyIconAsset::class,
                'regular.png',
            ),
            self::format(
                'battle2',
                'battle',
                'Battles',
                $data['battle2'] ?? null,
                LobbyIconAsset::class,
                'regular.png',
            ),
            self::format(
                'battle3',
                'battle',
                'Battles',
                $data['battle3'] ?? null,
                LobbyIconAsset::class,
                'regular.png',
            ),
            self::format(
                'salmon2',
                'salmon',
                'Shifts',
                $data['salmon2'] ?? null,
                SalmonModeIconAsset::class,
                'salmon.png',
            ),
            self::format(
                'salmon3',
                'salmon',
                'Shifts',
                $data['salmon3'] ?? null,
                SalmonModeIconAsset::class,
                'salmon.png',
            ),
            self::format(
                'user',
                'user',
                'Users',
                $data['user'] ?? null,
                UiIconAsset::class,
                'inkling.png',
            ),
        );
    }

    /**
     * @param class-string<AssetBundle> $iconAsset
     */
    private static function format(
        string $key,
        string $type,
        string $labelEn,
        ?int $count,
        ?string $iconAsset = null,
        ?string $iconFile = null,
    ): array {
        if ($count === null || $count < 0) {
            throw new Exception();
        }

        $iconUrl = null;
        if ($iconAsset && $iconFile) {
            $am = Yii::$app->assetManager;
            $view = Yii::$app->view;
            if (
                $am instanceof AssetManager &&
                $view instanceof View
            ) {
                $iconUrl = Url::to(
                    $am->getAssetUrl(
                        bundle: $am->getBundle($iconAsset),
                        asset: $iconFile,
                        appendTimestamp: false,
                    ),
                    true,
                );
            }
        }

        return [
            $key => [
                'type' => $type,
                'icon' => $iconUrl,
                'label' => Yii::t('app-counter', $labelEn),
                'count' => (int)$count,
            ],
        ];
    }

    private static function getBattle3RoughCount(Connection $db): ?int
    {
        try {
            $count = filter_var(
                (new Query())
                    ->select('[[last_value]]')
                    ->from('{{%battle3_id_seq}}')
                    ->limit(1)
                    ->scalar($db),
                FILTER_VALIDATE_INT,
            );
            return is_int($count) ? $count : null;
        } catch (Throwable $e) {
        }

        return null;
    }

    private static function getSalmon3RoughCount(Connection $db): ?int
    {
        try {
            $count = filter_var(
                (new Query())
                    ->select('[[last_value]]')
                    ->from('{{%salmon3_id_seq}}')
                    ->limit(1)
                    ->scalar($db),
                FILTER_VALIDATE_INT,
            );
            return is_int($count) ? $count : null;
        } catch (Throwable $e) {
        }

        return null;
    }
}
