<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\api\internal\v3;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Lobby3;
use app\models\Schedule3;
use app\models\ScheduleMap3;
use yii\base\Action;
use yii\db\Query;

use function array_combine;
use function array_map;
use function time;

use const SORT_ASC;

final class CurrentSchedule3Action extends Action
{
    public function run(): array
    {
        Yii::$app->response->format = YII_ENV_PROD ? 'compact-json' : 'json';

        $now = (int)($_SERVER['REQUEST_TIME'] ?? time());
        $period = BattleHelper::calcPeriod2($now);

        return [
            'current' => $this->buildPeriodPayload($period),
        ];
    }

    /**
     * @return array{period: int, start_at: int, end_at: int, lobbies: array<string, array>}
     */
    private function buildPeriodPayload(int $period): array
    {
        [$startAt, $endAt] = BattleHelper::periodToRange2($period);
        $lobbies = $this->getActiveLobbies($period);

        return [
            'period' => $period,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'lobbies' => array_combine(
                array_map(fn (Lobby3 $lobby): string => $lobby->key, $lobbies),
                array_map(
                    fn (Lobby3 $lobby): array => $this->buildLobbyPayload($period, $lobby),
                    $lobbies,
                ),
            ),
        ];
    }

    /**
     * @return Lobby3[]
     */
    private function getActiveLobbies(int $period): array
    {
        $lobbyIds = (new Query())
            ->select('lobby_id')
            ->from('{{%schedule3}}')
            ->where(['period' => $period])
            ->groupBy(['lobby_id'])
            ->column();

        if (!$lobbyIds) {
            return [];
        }

        return Lobby3::find()
            ->andWhere(['id' => $lobbyIds])
            ->orderBy(['rank' => SORT_ASC])
            ->all();
    }

    /**
     * @return array{rule: ?array{key: string, name: string}, stages: list<array{key: ?string, name: ?string}>}
     */
    private function buildLobbyPayload(int $period, Lobby3 $lobby): array
    {
        $schedule = Schedule3::find()
            ->with([
                'rule',
                'scheduleMap3s' => fn (Query $q) => $q->orderBy(['id' => SORT_ASC]),
                'scheduleMap3s.map',
            ])
            ->andWhere([
                'lobby_id' => $lobby->id,
                'period' => $period,
            ])
            ->limit(1)
            ->one();

        if (!$schedule) {
            return [
                'rule' => null,
                'stages' => [],
            ];
        }

        $rule = $schedule->rule;
        return [
            'rule' => $rule
                ? [
                    'key' => $rule->key,
                    'name' => Yii::t('app-rule3', $rule->name),
                ]
                : null,
            'stages' => array_map(
                function (ScheduleMap3 $entry): array {
                    $map = $entry->map;
                    return [
                        'key' => $map?->key,
                        'name' => $map ? Yii::t('app-map3', $map->name) : null,
                    ];
                },
                $schedule->scheduleMap3s,
            ),
        ];
    }
}
