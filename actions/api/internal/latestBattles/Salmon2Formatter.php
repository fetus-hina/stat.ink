<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal\latestBattles;

use Yii;
use app\assets\GameModeIconsAsset;
use app\models\Salmon2;
use statink\yii2\stages\spl2\StagesAsset;
use yii\helpers\Url;

use function sprintf;
use function strtotime;

trait Salmon2Formatter
{
    use UserFormatter;

    protected function formatSalmon2(Salmon2 $battle): array
    {
        $am = Yii::$app->assetManager;
        $modeAsset = $am->getBundle(GameModeIconsAsset::class, true);
        $stageAsset = $am->getBundle(StagesAsset::class, true);

        return [
            'id' => $battle->id,
            'image' => null,
            'thumbnail' => null,
            'isWin' => $battle->clear_waves === null ? null : $battle->clear_waves >= 3,
            'mode' => [
                'icon' => Url::to($am->getAssetUrl($modeAsset, 'spl2/salmon.png'), true),
                'key' => 'salmon',
                'name' => Yii::t('app-salmon2', 'Salmon Run'),
            ],
            'stage' => $battle->stage
                ? [
                    'name' => Yii::t('app-salmon-map2', $battle->stage->name),
                    'key' => $battle->stage->key,
                    'image' => [
                        'lose' => Url::to(
                            $am->getAssetUrl($stageAsset, "gray-blur/{$battle->stage->key}.jpg"),
                            true,
                        ),
                        'normal' => Url::to(
                            $am->getAssetUrl($stageAsset, "daytime/{$battle->stage->key}.jpg"),
                            true,
                        ),
                        'win' => Url::to(
                            $am->getAssetUrl($stageAsset, "daytime-blur/{$battle->stage->key}.jpg"),
                            true,
                        ),
                    ],
                ]
                : null,
            'summary' => (function () use ($battle): ?string {
                if (!$battle->stage && $battle->clear_waves === null) {
                    return null;
                }

                $map = '?';
                $result = '?';
                if ($battle->stage) {
                    $map = Yii::t('app-salmon-map2', $battle->stage->name);
                }

                if ($battle->clear_waves !== null) {
                    if ($battle->clear_waves === 3) {
                        $result = Yii::t('app-salmon2', 'Cleared');
                    } else {
                        $result = Yii::t('app-salmon2', 'Failed in wave {waveNumber}', [
                            'waveNumber' => $battle->clear_waves + 1,
                        ]);
                    }
                }

                return sprintf('%s @%s', $result, $map);
            })(),
            'summary2' => Yii::t('app-salmon2', 'Salmon Run'),
            'time' => strtotime($battle->end_at ?: $battle->created_at),
            'rule' => null,
            'url' => Url::to(
                ['salmon/view',
                    'id' => $battle->id,
                    'screen_name' => $battle->user->screen_name,
                ],
                true,
            ),
            'user' => self::formatUser($battle->user),
            'variant' => 'salmon2',
        ];
    }
}
