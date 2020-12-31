<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\assets\GameModeIconsAsset;
use app\components\helpers\CombinedBattles;
use app\models\Battle2;
use app\models\Battle;
use app\models\Salmon2;
use statink\yii2\stages\spl1\StagesAsset as Spl1StagesAsset;
use statink\yii2\stages\spl2\StagesAsset as Spl2StagesAsset;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ViewAction;

use function array_filter;
use function strtotime;
use function time;

abstract class BaseLatestBattlesAction extends ViewAction
{
    private DateTimeImmutable $now;

    abstract protected function fetchBattles(): array;
    abstract protected function getHeading(): string;

    protected function isPrecheckOK(): bool
    {
        return true;
    }

    public function init()
    {
        parent::init();

        $t = (int)($_SERVER['REQUEST_TIME'] ?? time());
        $this->now = (new DateTimeImmutable())
            ->setTimestamp($t)
            ->setTimezone(new DateTimeZone('Etc/UTC'));
    }

    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'compact-json';

        if (!$this->isPrecheckOK()) {
            return [
                'battles' => [],
                'translations' => [],
                'user' => null,
            ];
        }

        return [
            'battles' => $this->getBattles(),
            'translations' => $this->getTranslations(),
            'user' => null,
        ];
    }

    private function getTranslations(): array
    {
        $reltimes = [
            'year' => '{delta} yr',
            'month' => '{delta} mo',
            'day' => '{delta} d',
            'hour' => '{delta} h',
            'minute' => '{delta} m',
            'second' => '{delta} s',
        ];

        return [
            'heading' => $this->getHeading(),
            'reltime' => array_merge(
                ['now' => Yii::t('app-reltime', 'now')],
                ArrayHelper::getColumn(
                    $reltimes,
                    function (string $format): array {
                        return [
                            'one' => preg_replace(
                                '/\b1\b/',
                                '{delta}',
                                Yii::t('app-reltime', $format, ['delta' => 1])
                            ),
                            'many' => preg_replace(
                                '/\b42\b/',
                                '{delta}',
                                Yii::t('app-reltime', $format, ['delta' => 42])
                            ),
                        ];
                    }
                )
            ),
        ];
    }

    private function getBattles(): array
    {
        return array_filter(
            ArrayHelper::getColumn(
                $this->fetchBattles(),
                function ($battle): ?array {
                    if ($battle instanceof Battle2) {
                        return $this->formatBattle2($battle);
                    } elseif ($battle instanceof Salmon2) {
                        return $this->formatSalmon2($battle);
                    } elseif ($battle instanceof Battle) {
                        return $this->formatBattle1($battle);
                    }
                    return null;
                }
            )
        );
    }

    private function formatBattle2(Battle2 $battle): array
    {
        $am = Yii::$app->assetManager;
        $modeAsset = $am->getBundle(GameModeIconsAsset::class, true);
        $stageAsset = $am->getBundle(Spl2StagesAsset::class, true);

        return [
            'id' => $battle->id,
            'image' => $battle->battleImageResult
                ? Url::to(
                    Yii::getAlias('@imageurl') . '/' . $battle->battleImageResult->filename,
                    true
                )
                : null,
            'isWin' => $battle->is_win,
            'mode' => $battle->mode
                ? [
                    'icon' => $battle->mode->key === 'regular'
                        ? null
                        : Url::to(
                            $am->getAssetUrl($modeAsset, vsprintf('spl2/%s.png', [
                                $battle->mode->key,
                            ])),
                            true
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
                            true
                        ),
                        'normal' => Url::to(
                            $am->getAssetUrl($stageAsset, "daytime/{$battle->map->key}.jpg"),
                            true
                        ),
                        'win' => Url::to(
                            $am->getAssetUrl($stageAsset, "daytime-blur/{$battle->map->key}.jpg"),
                            true
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
            'time' => strtotime($battle->end_at ?: $battle->created_at),
            'rule' => $battle->rule
                ? [
                    'icon' => Url::to(
                        $am->getAssetUrl($modeAsset, sprintf('spl2/%s.png', $battle->rule->key)),
                        true
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
                true
            ),
            'user' => [
                'icon' => array_values(array_filter([
                    $battle->user->userIcon
                        ? Url::to($battle->user->userIcon->url, true)
                        : null,
                    Url::to($battle->user->jdenticonUrl, true),
                ])),
                'name' => $battle->user->name,
                'url' => Url::to(
                    ['show-user/profile', 'screen_name' => $battle->user->screen_name],
                    true
                ),
            ],
            'variant' => 'splatoon2',
        ];
    }

    private function formatSalmon2(Salmon2 $battle): array
    {
        $am = Yii::$app->assetManager;
        $modeAsset = $am->getBundle(GameModeIconsAsset::class, true);
        $stageAsset = $am->getBundle(Spl2StagesAsset::class, true);

        return [
            'id' => $battle->id,
            'image' => null,
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
                            true
                        ),
                        'normal' => Url::to(
                            $am->getAssetUrl($stageAsset, "daytime/{$battle->stage->key}.jpg"),
                            true
                        ),
                        'win' => Url::to(
                            $am->getAssetUrl($stageAsset, "daytime-blur/{$battle->stage->key}.jpg"),
                            true
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
            'time' => strtotime($battle->end_at ?: $battle->created_at),
            'rule' => null,
            'url' => Url::to(
                ['salmon/view',
                    'id' => $battle->id,
                    'screen_name' => $battle->user->screen_name,
                ],
                true
            ),
            'user' => [
                'icon' => array_values(array_filter([
                    $battle->user->userIcon
                        ? Url::to($battle->user->userIcon->url, true)
                        : null,
                    Url::to($battle->user->jdenticonUrl, true),
                ])),
                'name' => $battle->user->name,
                'url' => Url::to(
                    ['show-user/profile', 'screen_name' => $battle->user->screen_name],
                    true
                ),
            ],
            'variant' => 'salmon2',
        ];
    }

    private function formatBattle1(Battle $battle): array
    {
        $am = Yii::$app->assetManager;
        $modeAsset = $am->getBundle(GameModeIconsAsset::class, true);
        $stageAsset = $am->getBundle(Spl1StagesAsset::class, true);

        return [
            'id' => $battle->id,
            'image' => $battle->battleImageResult
                ? $battle->battleImageResult->url
                : null,
            'isWin' => $battle->is_win,
            'mode' => null,
            'stage' => $battle->map
                ? [
                    'name' => Yii::t('app-map', $battle->map->name),
                    'key' => $battle->map->key,
                    'image' => [
                        'lose' => Url::to(
                            $am->getAssetUrl($stageAsset, "gray-blur/{$battle->map->key}.jpg"),
                            true
                        ),
                        'normal' => Url::to(
                            $am->getAssetUrl($stageAsset, "daytime/{$battle->map->key}.jpg"),
                            true
                        ),
                        'win' => Url::to(
                            $am->getAssetUrl($stageAsset, "daytime-blur/{$battle->map->key}.jpg"),
                            true
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
                true
            ),
            'user' => [
                'icon' => array_values(array_filter([
                    $battle->user->userIcon
                        ? Url::to($battle->user->userIcon->url, true)
                        : null,
                    Url::to($battle->user->jdenticonUrl, true),
                ])),
                'name' => $battle->user->name,
                'url' => Url::to(
                    ['show-user/profile', 'screen_name' => $battle->user->screen_name],
                    true
                ),
            ],
            'variant' => 'splatoon1',
        ];
    }

}
