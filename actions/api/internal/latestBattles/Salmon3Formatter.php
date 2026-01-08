<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal\latestBattles;

use Yii;
use app\assets\s3PixelIcons\ModeBackgroundAsset;
use app\assets\s3PixelIcons\SalmonModeIconAsset;
use app\components\helpers\TypeHelper;
use app\models\Salmon3;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\AssetManager;

use function rawurlencode;
use function sprintf;
use function strtotime;
use function vsprintf;

trait Salmon3Formatter
{
    use UserFormatter;

    protected function formatSalmon3(Salmon3 $battle): array
    {
        $am = TypeHelper::instanceOf(Yii::$app->assetManager, AssetManager::class);

        return [
            'id' => $battle->uuid,
            'image' => self::salmonImage3($battle),
            'thumbnail' => self::salmonThumb3($battle),
            'isWin' => match (true) {
                $battle->is_eggstra_work === true && $battle->clear_waves !== null => $battle->clear_waves >= 5,
                $battle->is_eggstra_work !== true && $battle->clear_waves !== null => $battle->clear_waves >= 3,
                default => null,
            },
            'mode' => match (true) {
                $battle->is_big_run === true => [
                    'icon' => $am->getAssetUrl($am->getBundle(SalmonModeIconAsset::class), 'bigrun.png'),
                    'key' => 'salmon',
                    'name' => Yii::t('app-salmon3', 'Big Run'),
                ],
                $battle->is_eggstra_work === true => [
                    'icon' => $am->getAssetUrl($am->getBundle(SalmonModeIconAsset::class), 'eggstra.png'),
                    'key' => 'salmon',
                    'name' => Yii::t('app-salmon3', 'Eggstra Work'),
                ],
                default => [
                    'icon' => $am->getAssetUrl($am->getBundle(SalmonModeIconAsset::class), 'salmon.png'),
                    'key' => 'salmon',
                    'name' => Yii::t('app-salmon2', 'Salmon Run'),
                ],
            },
            'stage' => $this->salmonStage3($battle),
            'summary' => (function () use ($battle): ?string {
                $stage = $battle->stage ?? $battle->bigStage;
                if (!$stage && !$battle->clear_waves === null) {
                    return null;
                }

                $stageName = '?';
                $result = '?';
                if ($stage) {
                    $stageName = Yii::t('app-map3', $stage->name);
                }

                if ($battle->clear_waves !== null) {
                    $expectWaves = $battle->is_eggstra_work ? 5 : 3;
                    if ($battle->clear_waves >= $expectWaves) {
                        if (
                            !$battle->is_eggstra_work &&
                            $battle->kingSalmonid &&
                            $battle->clear_extra !== null
                        ) {
                            $result = vsprintf('%s:%s', [
                                Yii::t('app-salmon-boss3', $battle->kingSalmonid->name),
                                Yii::t('app-salmon2', $battle->clear_extra ? '✓' : '✘'),
                            ]);
                        } else {
                            $result = Yii::t('app-salmon2', 'Cleared');
                        }
                    } else {
                        $result = Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                            'waveNumber' => $battle->clear_waves + 1,
                        ]);
                    }
                }

                return sprintf('%s @%s', $result, $stageName);
            })(),
            'summary2' => match (true) {
                $battle->is_private === true => Yii::t('app-salmon3', 'Private Job'),
                $battle->is_big_run === true => Yii::t('app-salmon3', 'Big Run'),
                $battle->is_eggstra_work === true => Yii::t('app-salmon3', 'Eggstra Work'),
                default => Yii::t('app-salmon3', 'Salmon Run'),
            },
            'time' => strtotime($battle->start_at ?: $battle->created_at),
            'rule' => null,
            'url' => Url::to(
                ['salmon-v3/view',
                    'battle' => $battle->uuid,
                    'screen_name' => $battle->user->screen_name,
                ],
                true,
            ),
            'user' => self::formatUser($battle->user),
            'variant' => 'salmon3',
        ];
    }

    private static function salmonImage3(Salmon3 $model): ?string
    {
        if (ArrayHelper::getValue(Yii::$app->params, 'useS3ImgGen')) {
            return vsprintf('https://s3-img-gen.stats.ink/salmon/%s/%s.jpg', [
                rawurlencode(Yii::$app->language),
                rawurlencode($model->uuid),
            ]);
        }

        return null;
    }

    private static function salmonThumb3(Salmon3 $model): ?string
    {
        if (ArrayHelper::getValue(Yii::$app->params, 'useS3ImgGen')) {
            return vsprintf('https://s3-img-gen.stats.ink/salmon/thumb-<w>x<h>/%s/%s.jpg', [
                rawurlencode(Yii::$app->language),
                rawurlencode($model->uuid),
            ]);
        }

        $am = TypeHelper::instanceOf(Yii::$app->assetManager, AssetManager::class);
        return $am->getAssetUrl(
            $am->getBundle(ModeBackgroundAsset::class),
            "salmon.png",
        );
    }

    private function salmonStage3(Salmon3 $battle): ?array
    {
        return null;
    }
}
