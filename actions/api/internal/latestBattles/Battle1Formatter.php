<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal\latestBattles;

use Yii;
use app\models\Battle;
use statink\yii2\stages\spl1\StagesAsset;
use yii\helpers\Url;

use function in_array;
use function sprintf;
use function strtotime;
use function vsprintf;

trait Battle1Formatter
{
    use UserFormatter;

    protected function formatBattle1(Battle $battle): array
    {
        $am = Yii::$app->assetManager;
        $stageAsset = $am->getBundle(StagesAsset::class, true);

        return [
            'id' => $battle->id,
            'image' => $battle->battleImageResult
                ? $battle->battleImageResult->url
                : null,
            'thumbnail' => null,
            'isWin' => $battle->is_win,
            'mode' => null,
            'stage' => $battle->map
                ? [
                    'name' => Yii::t('app-map', $battle->map->name),
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
                    $map = Yii::t('app-map', $battle->map->name);
                }
                if ($battle->is_win !== null) {
                    $result = Yii::t('app', $battle->is_win ? 'Won' : 'Lost');
                }
                return sprintf('%s @%s', $result, $map);
            })(),
            'summary2' => (function () use ($battle): ?string {
                $lobby = $battle->lobby;
                $rule = $battle->rule;
                if (!$lobby || !$rule) {
                    return null;
                }

                switch ($lobby->key) {
                    case 'fest':
                        return Yii::t('app-rule', 'Splatfest');

                    case 'private':
                        return Yii::t('app-rule', 'Private Battle');

                    case 'squad_2':
                    case 'squad_3':
                    case 'squad_4':
                        if (in_array($rule->key, ['area', 'yagura', 'hoko'], true)) {
                            return vsprintf('%s, %s', [
                                Yii::t('app-rule', $rule->name),
                                (function () use ($lobby): string {
                                    switch ($lobby->key) {
                                        case 'squad_2':
                                            return Yii::t('app-rule', 'Squad Battle (Twin)');

                                        case 'squad_3':
                                            return Yii::t('app-rule', 'Squad Battle (Tri)');

                                        case 'squad_4':
                                            return Yii::t('app-rule', 'Squad Battle (Quad)');

                                        default:
                                            return '';
                                    }
                                })(),
                            ]);
                        }
                        break;

                    case 'standard': // turf war or ranked battle
                        if ($rule->key === 'nawabari') {
                            return Yii::t('app-rule', 'Turf War');
                        } elseif (in_array($rule->key, ['area', 'yagura', 'hoko'], true)) {
                            return vsprintf('%s, %s', [
                                Yii::t('app-rule', $rule->name),
                                Yii::t('app-rule', 'Ranked Battle'),
                            ]);
                        }
                        break;
                }
                return null;
            })(),
            'time' => strtotime($battle->end_at ?: $battle->created_at),
            'rule' => $battle->rule
                ? [
                    'icon' => null,
                    'key' => $battle->rule->key,
                    'name' => Yii::t('app-rule', $battle->rule->name),
                ]
                : null,
            'url' => Url::to(
                ['show/battle',
                    'battle' => $battle->id,
                    'screen_name' => $battle->user->screen_name,
                ],
                true,
            ),
            'user' => self::formatUser($battle->user),
            'variant' => 'splatoon1',
        ];
    }
}
