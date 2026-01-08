<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal\latestBattles;

use Yii;
use app\assets\GameModeIconsAsset;
use app\models\Battle2;
use statink\yii2\stages\spl2\StagesAsset;
use yii\helpers\Url;

use function in_array;
use function sprintf;
use function strtotime;
use function vsprintf;

trait Battle2Formatter
{
    use UserFormatter;

    protected function formatBattle2(Battle2 $battle): array
    {
        $am = Yii::$app->assetManager;
        $modeAsset = $am->getBundle(GameModeIconsAsset::class, true);
        $stageAsset = $am->getBundle(StagesAsset::class, true);

        return [
            'id' => $battle->id,
            'image' => $battle->battleImageResult
                ? Url::to(
                    Yii::getAlias('@imageurl') . '/' . $battle->battleImageResult->filename,
                    true,
                )
                : null,
            'thumbnail' => null,
            'isWin' => $battle->is_win,
            'mode' => $battle->mode
                ? [
                    'icon' => $battle->mode->key === 'regular'
                        ? null
                        : Url::to(
                            $am->getAssetUrl($modeAsset, vsprintf('spl2/%s.png', [
                                $battle->mode->key,
                            ])),
                            true,
                        ),
                    'key' => $battle->mode->key,
                    'name' => Yii::t('app-rule2', $battle->mode->name),
                ]
                : null,
            'stage' => $battle->map
                ? [
                    'name' => Yii::t('app-map2', $battle->map->name),
                    'key' => $battle->map->key,
                    'image' => [
                        'lose' => Url::to(
                            $am->getAssetUrl($stageAsset, "gray-blur/{$battle->map->key}.jpg"),
                            true,
                        ),
                        'normal' => Url::to(
                            $am->getAssetUrl($stageAsset, "daytime/{$battle->map->key}.jpg"),
                            true,
                        ),
                        'win' => Url::to(
                            $am->getAssetUrl($stageAsset, "daytime-blur/{$battle->map->key}.jpg"),
                            true,
                        ),
                    ],
                ]
                : null,
            'summary' => (function () use ($battle): ?string {
                if (!$battle->map && $battle->is_win === null) {
                    return null;
                }

                $map = '?';
                $result = '?';
                if ($battle->map) {
                    $map = Yii::t('app-map2', $battle->map->name);
                }
                if ($battle->is_win !== null) {
                    $result = Yii::t('app', $battle->is_win ? 'Won' : 'Lost');
                }
                return sprintf('%s @%s', $result, $map);
            })(),
            'summary2' => (function () use ($battle): ?string {
                $lobby = $battle->lobby;
                $mode = $battle->mode;
                $rule = $battle->rule;
                if (!$lobby || !$mode || !$rule) {
                    return null;
                }

                switch ($mode->key) {
                    case 'regular':
                        if ($rule->key === 'nawabari') {
                            return Yii::t('app-rule2', 'Turf War');
                        }
                        break;

                    case 'gachi':
                        if (
                            in_array($rule->key, ['area', 'asari', 'hoko', 'yagura'], true) &&
                            in_array($lobby->key, ['standard', 'squad_2', 'squad_4'], true)
                        ) {
                            return vsprintf('%s, %s', [
                                Yii::t('app-rule2', $rule->name),
                                (function () use ($lobby): string {
                                    switch ($lobby->key) {
                                        case 'squad_2':
                                            return Yii::t('app-rule2', 'League (Twin)');

                                        case 'squad_4':
                                            return Yii::t('app-rule2', 'League (Quad)');

                                        default:
                                            return Yii::t('app-rule2', 'Ranked Battle');
                                    }
                                })(),
                            ]);
                        }
                        break;

                    case 'fest':
                        return Yii::t('app-rule2', 'Splatfest');

                    case 'private':
                        return Yii::t('app-rule2', 'Private Battle');
                }

                return null;
            })(),
            'time' => strtotime($battle->end_at ?: $battle->created_at),
            'rule' => $battle->rule
                ? [
                    'icon' => Url::to(
                        $am->getAssetUrl($modeAsset, sprintf('spl2/%s.png', $battle->rule->key)),
                        true,
                    ),
                    'key' => $battle->rule->key,
                    'name' => Yii::t('app-rule2', $battle->rule->name),
                ]
                : null,
            'url' => Url::to(
                ['show-v2/battle',
                    'battle' => $battle->id,
                    'screen_name' => $battle->user->screen_name,
                ],
                true,
            ),
            'user' => self::formatUser($battle->user),
            'variant' => 'splatoon2',
        ];
    }
}
