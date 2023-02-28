<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal\schedule;

use Yii;
use app\assets\GameModeIconsAsset;
use app\assets\Spl3StageAsset;
use app\components\helpers\Battle as BattleHelper;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Rule3;
use app\models\Schedule3;
use app\models\ScheduleMap3;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\AssetBundle;
use yii\web\View;

use function array_combine;
use function array_map;
use function vsprintf;

use const SORT_ASC;

trait Battle3
{
    private function getBattle3(): array
    {
        $period = $this->currentPeriod;
        $lobbies = $this->getLobbies3($period);
        return array_combine(
            // keys
            array_map(
                fn (Lobby3 $lobby): string => $lobby->key,
                $lobbies,
            ),
            // values
            array_map(
                fn (Lobby3 $lobby): array => [
                    'key' => $lobby->key,
                    'game' => 'splatoon3',
                    'name' => $lobby->key !== 'splatfest_open'
                        ? Yii::t('app-lobby3', $lobby->name)
                        : Yii::t('app-lobby3', 'Splatfest'),
                    'image' => $lobby->key !== 'regular'
                        ? $this->getIconUrlForLobby3($lobby)
                        : null,
                    'source' => 's3ink',
                    'schedules' => $this->getBattleSchedules3($period, $lobby),
                ],
                $lobbies,
            ),
        );
    }

    // 直近に開催されるロビーの一覧を取得
    private function getLobbies3(int $period): array
    {
        $availableLobbyIds = (new Query())
            ->select('lobby_id')
            ->from('{{%schedule3}}')
            ->where(['between', 'period', $period, $period + 3])
            ->groupBy(['lobby_id'])
            ->column();

        return Lobby3::find()
            ->andWhere(['id' => $availableLobbyIds])
            ->orderBy(['rank' => SORT_ASC])
            ->all();
    }

    private function getBattleSchedules3(int $period, Lobby3 $lobby): array
    {
        $schedules = Schedule3::find()
            ->with([
                'rule',
                'scheduleMap3s' => function (Query $q): void {
                    $q->orderBy(['id' => SORT_ASC]);
                },
                'scheduleMap3s.map',
            ])
            ->andWhere(['and',
                ['lobby_id' => $lobby->id],
                ['between', 'period', $period, $period + 3],
            ])
            ->orderBy(['period' => SORT_ASC])
            ->all();

        return array_map(
            function (Schedule3 $schedule): array {
                $rule = $schedule->rule;
                return [
                    'time' => BattleHelper::periodToRange2($schedule->period),
                    'rule' => [
                        'key' => $rule->key,
                        'name' => Yii::t('app-rule3', $rule->name),
                        'short' => Yii::t('app-rule3', $rule->short_name),
                        'icon' => $this->getIconUrlForRule3($rule),
                    ],
                    'maps' => array_map(
                        function (ScheduleMap3 $model): array {
                            $map = $model->map;
                            return [
                                'key' => $map?->key,
                                'name' => Yii::t('app-map3', $map?->name ?? '???'),
                                'image' => $this->getImageUrlForMap3($map),
                            ];
                        },
                        $schedule->scheduleMap3s,
                    ),
                ];
            },
            $schedules,
        );
    }

    private function getIconUrlForLobby3(Lobby3 $lobby): ?string
    {
        return self::getAssetUrl3(
            GameModeIconsAsset::class,
            vsprintf('spl3/%s.png', [
                $lobby->key,
            ]),
        );
    }

    private function getIconUrlForRule3(Rule3 $rule): ?string
    {
        return self::getAssetUrl3(
            GameModeIconsAsset::class,
            vsprintf('spl3/%s.png', [
                $rule->key,
            ]),
        );
    }

    private function getImageUrlForMap3(?Map3 $map): ?string
    {
        return self::getAssetUrl3(
            Spl3StageAsset::class,
            vsprintf('color-normal/%s.jpg', [
                $map?->key ?? 'unknown',
            ]),
        );
    }

    /**
     * @param class-string<AssetBundle> $assetClass
     */
    private function getAssetUrl3(string $assetClass, string $path): ?string
    {
        if (!Yii::$app->view instanceof View) {
            return null;
        }

        $am = Yii::$app->assetManager;
        return Url::to(
            $am->getAssetUrl($am->getBundle($assetClass, true), $path),
            true,
        );
    }
}
