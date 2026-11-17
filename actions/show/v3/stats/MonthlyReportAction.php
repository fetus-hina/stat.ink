<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\show\v3\stats;

use DateTimeInterface;
use Override;
use Yii;
use app\components\helpers\Battle3WinLoseStats;
use app\components\helpers\TypeHelper;
use app\models\Lobby3;
use app\models\LobbyGroup3;
use app\models\Map3;
use app\models\Rule3;
use app\models\Spl3YearMonthForm;
use app\models\User;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_filter;
use function array_map;
use function array_unique;
use function array_values;
use function implode;
use function sprintf;
use function strnatcasecmp;
use function usort;
use function vsprintf;

use const SORT_ASC;

/**
 * @phpstan-import-type WinLoseStats from Battle3WinLoseStats
 * @phpstan-type MonthlyReportGroup array{
 *   group: LobbyGroup3,
 *   firstLobby: Lobby3|null,
 *   stats: WinLoseStats|null
 * }
 * @phpstan-type MonthlyReportMap array{
 *   map: Map3,
 *   stats: WinLoseStats
 * }
 * @phpstan-type MonthlyReportRule array{
 *   rule: Rule3,
 *   stats: WinLoseStats,
 *   maps: list<MonthlyReportMap>
 * }
 * @phpstan-type MonthlyReportLobby array{
 *   lobby: Lobby3,
 *   rules: list<MonthlyReportRule>
 * }
 * @phpstan-type MonthlyReportRow array{
 *   lobby: Lobby3,
 *   rule: Rule3|null,
 *   map: Map3|null,
 *   stats: WinLoseStats
 * }
 */
final class MonthlyReportAction extends Action
{
    private const int CACHE_DURATION = 300;

    public ?User $user = null;

    #[Override]
    public function init(): void
    {
        parent::init();

        $this->user = User::find()
            ->andWhere(['screen_name' => (string)Yii::$app->request->get('screen_name')])
            ->limit(1)
            ->one();

        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run(): string|Response
    {
        $user = TypeHelper::instanceOf($this->user, User::class);
        $controller = TypeHelper::instanceOf($this->controller, Controller::class);

        $form = Yii::createObject(Spl3YearMonthForm::class);
        $form->attributes = Yii::$app->request->get();
        if (!$form->validate()) {
            $now = $form->getCurrentTimestamp();
            return $controller->redirect([
                'show-v3/stats-monthly-report',
                'screen_name' => $user->screen_name,
                'year' => (int)$now->format('Y'),
                'month' => (int)$now->format('n'),
            ]);
        }

        $rows = $this->makeRows(Yii::$app->db, $user, $form);
        $lobbies = self::makeLobbies($rows);

        return $controller->render('stats/monthly-report', [
            'form' => $form,
            'groups' => self::makeGroups($rows, $lobbies),
            'lobbies' => $lobbies,
            'user' => $user,
        ]);
    }

    /**
     * Summary by lobby group, including private battles and battles without the rule or the stage
     *
     * @param list<MonthlyReportRow> $rows
     * @param list<MonthlyReportLobby> $lobbies
     * @return list<MonthlyReportGroup>
     */
    private static function makeGroups(array $rows, array $lobbies): array
    {
        /** @var array<int, list<WinLoseStats>> $statsByGroup */
        $statsByGroup = [];
        foreach ($rows as $row) {
            $statsByGroup[(int)$row['lobby']->group_id][] = $row['stats'];
        }

        // $lobbies is sorted by the group, then by the lobby, so the first match is the first section of the group
        /** @var array<int, Lobby3> $firstLobbies */
        $firstLobbies = [];
        foreach ($lobbies as $lobbyInfo) {
            $firstLobbies[(int)$lobbyInfo['lobby']->group_id] ??= $lobbyInfo['lobby'];
        }

        $groups = LobbyGroup3::find()->orderBy(['rank' => SORT_ASC])->all();

        return array_values(
            array_map(
                fn (LobbyGroup3 $group): array => [
                    'group' => $group,
                    'firstLobby' => $firstLobbies[(int)$group->id] ?? null,
                    'stats' => isset($statsByGroup[(int)$group->id])
                        ? Battle3WinLoseStats::sum($statsByGroup[(int)$group->id])
                        : null,
                ],
                $groups,
            ),
        );
    }

    /**
     * Details by lobby, rule, and stage. Private battles are excluded.
     *
     * The rule total includes battles without the stage, so that it matches the battle list.
     *
     * @param list<MonthlyReportRow> $rows
     * @return list<MonthlyReportLobby>
     */
    private static function makeLobbies(array $rows): array
    {
        /** @var array<int, array{lobby: Lobby3, rules: array<int, array{rule: Rule3, stats: list<WinLoseStats>, maps: list<MonthlyReportMap>}>}> $tree */
        $tree = [];
        foreach ($rows as $row) {
            $rule = $row['rule'];
            if ($rule === null || self::isPrivate($row['lobby'])) {
                continue;
            }

            $lobbyId = (int)$row['lobby']->id;
            $ruleId = (int)$rule->id;
            $tree[$lobbyId] ??= ['lobby' => $row['lobby'], 'rules' => []];
            $tree[$lobbyId]['rules'][$ruleId] ??= ['rule' => $rule, 'stats' => [], 'maps' => []];
            $tree[$lobbyId]['rules'][$ruleId]['stats'][] = $row['stats'];
            if ($row['map'] !== null) {
                $tree[$lobbyId]['rules'][$ruleId]['maps'][] = [
                    'map' => $row['map'],
                    'stats' => $row['stats'],
                ];
            }
        }

        $lobbies = array_map(
            fn (array $lobbyInfo): array => [
                'lobby' => $lobbyInfo['lobby'],
                'rules' => self::sortRules(
                    array_map(
                        fn (array $ruleInfo): array => [
                            'rule' => $ruleInfo['rule'],
                            'stats' => Battle3WinLoseStats::sum($ruleInfo['stats']),
                            'maps' => self::sortMaps($ruleInfo['maps']),
                        ],
                        array_values($lobbyInfo['rules']),
                    ),
                ),
            ],
            array_values($tree),
        );

        usort(
            $lobbies,
            fn (array $a, array $b): int => $a['lobby']->group->rank <=> $b['lobby']->group->rank
                ?: $a['lobby']->rank <=> $b['lobby']->rank,
        );

        return $lobbies;
    }

    private static function isPrivate(Lobby3 $lobby): bool
    {
        return $lobby->group->key === 'private';
    }

    /**
     * @param list<MonthlyReportRule> $rules
     * @return list<MonthlyReportRule>
     */
    private static function sortRules(array $rules): array
    {
        usort($rules, fn (array $a, array $b): int => $a['rule']->rank <=> $b['rule']->rank);
        return $rules;
    }

    /**
     * @param list<MonthlyReportMap> $maps
     * @return list<MonthlyReportMap>
     */
    private static function sortMaps(array $maps): array
    {
        usort(
            $maps,
            fn (array $a, array $b): int => Battle3WinLoseStats::compareByWinRate($a['stats'], $b['stats'])
                ?: strnatcasecmp(Yii::t('app-map3', $a['map']->name), Yii::t('app-map3', $b['map']->name)),
        );

        return $maps;
    }

    /**
     * @return list<MonthlyReportRow>
     */
    private function makeRows(Connection $db, User $user, Spl3YearMonthForm $form): array
    {
        $rows = $this->query($db, $user, $form);
        if (!$rows) {
            return [];
        }

        $lobbies = Lobby3::find()->with(['group'])->indexBy('id')->all();
        $rules = Rule3::find()->with(['group'])->indexBy('id')->all();
        $maps = Map3::find()
            ->andWhere([
                'id' => array_values(
                    array_unique(
                        array_map(
                            fn (array $row): int => TypeHelper::int($row['map_id']),
                            array_filter($rows, fn (array $row): bool => $row['map_id'] !== null),
                        ),
                    ),
                ),
            ])
            ->indexBy('id')
            ->all();

        return array_map(
            function (array $row) use ($lobbies, $maps, $rules): array {
                $ruleId = TypeHelper::intOrNull($row['rule_id']);
                $mapId = TypeHelper::intOrNull($row['map_id']);
                $rule = $ruleId === null
                    ? null
                    : TypeHelper::instanceOf($rules[$ruleId] ?? null, Rule3::class);

                return [
                    'lobby' => TypeHelper::instanceOf($lobbies[TypeHelper::int($row['lobby_id'])] ?? null, Lobby3::class),
                    'rule' => $rule,
                    'map' => $mapId === null ? null : TypeHelper::instanceOf($maps[$mapId] ?? null, Map3::class),
                    'stats' => Battle3WinLoseStats::fromCounts(
                        [
                            'winKnockout' => TypeHelper::int($row['winKnockout']),
                            'winTime' => TypeHelper::int($row['winTime']),
                            'winUnknown' => TypeHelper::int($row['winUnknown']),
                            'loseKnockout' => TypeHelper::int($row['loseKnockout']),
                            'loseTime' => TypeHelper::int($row['loseTime']),
                            'loseUnknown' => TypeHelper::int($row['loseUnknown']),
                        ],
                        $rule?->group->key,
                    ),
                ];
            },
            $rows,
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function query(Connection $db, User $user, Spl3YearMonthForm $form): array
    {
        $from = $form->getFirstDayOfMonth();
        $to = $form->getFirstDayOfNextMonth();

        $columns = [
            'lobby_id' => '{{%battle3}}.[[lobby_id]]',
            'rule_id' => '{{%battle3}}.[[rule_id]]',
            'map_id' => '{{%battle3}}.[[map_id]]',
        ];
        foreach (Battle3WinLoseStats::COUNTERS as $key => [$isWin, $isKnockout]) {
            $columns[$key] = vsprintf('SUM(CASE WHEN %s THEN 1 ELSE 0 END)', [
                implode(' AND ', [
                    sprintf('{{%%result3}}.[[is_win]] = %s', $isWin ? 'TRUE' : 'FALSE'),
                    match ($isKnockout) {
                        null => '{{%battle3}}.[[is_knockout]] IS NULL',
                        true => '{{%battle3}}.[[is_knockout]] = TRUE',
                        false => '{{%battle3}}.[[is_knockout]] = FALSE',
                    },
                ]),
            ]);
        }

        // rule_id and map_id may be NULL: such battles are counted in the totals,
        // so that the numbers match the battle lists linked from them.
        $query = new Query()
            ->select($columns)
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[user_id]]' => (int)$user->id,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[lobby_id]]' => null]],
                // Same as DailyReportAction: the month is determined by `start_at` in the user's time zone
                ['>=', '{{%battle3}}.[[start_at]]', $from->format(DateTimeInterface::ATOM)],
                ['<', '{{%battle3}}.[[start_at]]', $to->format(DateTimeInterface::ATOM)],
                // Lets the index (user_id, end_at) be used. end_at is never earlier than start_at.
                // No upper bound on end_at: it is the upload time if the client did not send it.
                ['>=', '{{%battle3}}.[[end_at]]', $from->format(DateTimeInterface::ATOM)],
            ])
            ->groupBy([
                '{{%battle3}}.[[lobby_id]]',
                '{{%battle3}}.[[rule_id]]',
                '{{%battle3}}.[[map_id]]',
            ]);

        $command = $query->createCommand($db);

        /** @var list<array<string, mixed>> */
        return Yii::$app->cache->getOrSet(
            [__METHOD__, $command->rawSql],
            fn (): array => $command->queryAll(),
            self::CACHE_DURATION,
        );
    }
}
