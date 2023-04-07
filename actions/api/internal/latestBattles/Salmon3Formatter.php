<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal\latestBattles;

use Yii;
use app\assets\GameModeIconsAsset;
use app\assets\Spl3StageAsset;
use app\models\Salmon3;
use yii\helpers\Url;

use function sprintf;
use function strtotime;
use function vsprintf;

trait Salmon3Formatter
{
    use UserFormatter;

    protected function formatSalmon3(Salmon3 $battle): array
    {
        $am = Yii::$app->assetManager;
        $modeAsset = $am->getBundle(GameModeIconsAsset::class, true);

        return [
            'id' => $battle->uuid,
            'image' => null,
            'isWin' => $battle->clear_waves === null ? null : $battle->clear_waves >= 3,
            'mode' => match (true) {
                $battle->is_big_run === true => [
                    'icon' => Url::to($am->getAssetUrl($modeAsset, 'spl3/salmon-bigrun-36x36.png'), true),
                    'key' => 'salmon',
                    'name' => Yii::t('app-salmon3', 'Big Run'),
                ],
                $battle->is_eggstra_work === true => [
                    'icon' => Url::to($am->getAssetUrl($modeAsset, 'spl3/salmon-eggstra-36x36.png'), true),
                    'key' => 'salmon',
                    'name' => Yii::t('app-salmon3', 'Eggstra Work'),
                ],
                default => [
                    'icon' => Url::to($am->getAssetUrl($modeAsset, 'spl3/salmon36x36.png'), true),
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
                                Yii::t('app-salmon2', $battle->clear_extra ? '✓' : '✗'),
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

    private function salmonStage3(Salmon3 $battle): ?array
    {
        if (!$stage = $battle->stage ?? $battle->bigStage) {
            return null;
        }

        $am = Yii::$app->assetManager;
        $stageAsset = $am->getBundle(Spl3StageAsset::class, true);
        return [
            'name' => Yii::t('app-map3', $stage->name),
            'key' => $stage->key,
            'image' => [
                'lose' => Url::to(
                    $am->getAssetUrl($stageAsset, "gray-blur/{$stage->key}.jpg"),
                    true,
                ),
                'normal' => Url::to(
                    $am->getAssetUrl($stageAsset, "color-normal/{$stage->key}.jpg"),
                    true,
                ),
                'win' => Url::to(
                    $am->getAssetUrl($stageAsset, "color-blur/{$stage->key}.jpg"),
                    true,
                ),
            ],
        ];
    }
}
