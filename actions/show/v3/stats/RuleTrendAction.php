<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\show\v3\stats;

use Override;
use Yii;
use app\components\helpers\Rank3Helper;
use app\components\helpers\TrendSeries;
use app\components\helpers\TypeHelper;
use app\models\LobbyGroup3;
use app\models\Rule3;
use app\models\User;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_column;
use function array_keys;
use function array_map;
use function array_merge;
use function array_slice;
use function array_values;
use function count;
use function implode;
use function is_string;
use function vsprintf;

use const SORT_ASC;

/**
 * Trend charts of a rule (turf war or a ranked rule)
 *
 * @phpstan-type TrendRow array{
 *   is_win: bool,
 *   kill: int|null,
 *   death: int|null,
 *   special: int|null,
 *   inked: int|null,
 *   rank: int|null,
 *   x_power: float|null
 * }
 * @phpstan-type RuleTrendSeries array{
 *   battle: list<int>,
 *   winRate: list<float>,
 *   winRate20: list<float>,
 *   winRate50: list<float>,
 *   killRatio: list<float|null>,
 *   avgKill: list<float|null>,
 *   avgDeath: list<float|null>,
 *   avgSpecial: list<float|null>,
 *   inked: list<int|null>,
 *   avgInked: list<float|null>,
 *   rank: list<int|null>,
 *   xPower: list<float|null>
 * }
 * @phpstan-type RuleTrendData array{
 *   battles: int,
 *   thinned: bool,
 *   all: RuleTrendSeries,
 *   recent: RuleTrendSeries|null
 * }
 */
final class RuleTrendAction extends Action
{
    public const string DEFAULT_RULE = 'nawabari';

    /**
     * Rule groups to show. Tricolor is excluded.
     */
    private const array RULE_GROUPS = ['nawabari', 'gachi'];

    /**
     * The maximum number of the points of the chart of all battles
     */
    private const int MAX_POINTS = 1000;

    /**
     * The number of the battles of the chart of recent battles
     */
    public const int RECENT_BATTLES = 200;

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
        $request = Yii::$app->request;

        $rules = self::getRules();
        $ruleKey = $request->get('rule');
        $rule = is_string($ruleKey) ? ($rules[$ruleKey] ?? null) : null;
        if (!$rule) {
            return $controller->redirect([
                'show-v3/stats-rule-trend',
                'screen_name' => $user->screen_name,
                'rule' => self::DEFAULT_RULE,
            ]);
        }

        // Validate the lobby filter before the aggregation, so invalid values cost nothing
        $lobbyGroups = self::getLobbyGroups(Yii::$app->db, $user, $rule);
        $lobbyGroupKey = $request->get('lobby');
        $lobbyGroup = null;
        if ($lobbyGroupKey !== null) {
            $lobbyGroup = is_string($lobbyGroupKey) ? ($lobbyGroups[$lobbyGroupKey] ?? null) : null;
            if (!$lobbyGroup) {
                return $controller->redirect([
                    'show-v3/stats-rule-trend',
                    'screen_name' => $user->screen_name,
                    'rule' => $rule->key,
                ]);
            }
        }

        return $controller->render('stats/rule-trend', [
            'data' => self::calc(Yii::$app->db, $user, $rule, $lobbyGroup),
            'isRanked' => $rule->group->key === 'gachi',
            'lobbyGroup' => $lobbyGroup,
            'lobbyGroups' => array_values($lobbyGroups),
            'rule' => $rule,
            'rules' => array_values($rules),
            'user' => $user,
        ]);
    }

    /**
     * @return array<string, Rule3>
     */
    private static function getRules(): array
    {
        $rules = Rule3::find()
            ->innerJoinWith(['group'], true)
            ->andWhere(['{{%rule_group3}}.[[key]]' => self::RULE_GROUPS])
            ->orderBy(['{{%rule3}}.[[rank]]' => SORT_ASC])
            ->all();

        $ret = [];
        foreach ($rules as $rule) {
            $ret[$rule->key] = $rule;
        }

        return $ret;
    }

    /**
     * Calculates the chart data. The result is cached.
     *
     * @return RuleTrendData
     */
    private static function calc(Connection $db, User $user, Rule3 $rule, ?LobbyGroup3 $lobbyGroup): array
    {
        $query = self::buildBaseQuery($user, $rule)
            ->select([
                'is_win' => '{{%result3}}.[[is_win]]',
                'kill' => '{{%battle3}}.[[kill]]',
                'death' => '{{%battle3}}.[[death]]',
                'special' => '{{%battle3}}.[[special]]',
                'inked' => '{{%battle3}}.[[inked]]',
                'rank_key' => 'COALESCE({{rank_after}}.[[key]], {{rank_before}}.[[key]])',
                'rank_s_plus' => vsprintf('CASE WHEN %s THEN %s ELSE %s END', [
                    '{{rank_after}}.[[key]] IS NOT NULL',
                    '{{%battle3}}.[[rank_after_s_plus]]',
                    '{{%battle3}}.[[rank_before_s_plus]]',
                ]),
                'x_power' => 'COALESCE({{%battle3}}.[[x_power_after]], {{%battle3}}.[[x_power_before]])',
            ])
            ->leftJoin('{{%rank3}} {{rank_before}}', '{{%battle3}}.[[rank_before_id]] = {{rank_before}}.[[id]]')
            ->leftJoin('{{%rank3}} {{rank_after}}', '{{%battle3}}.[[rank_after_id]] = {{rank_after}}.[[id]]')
            ->orderBy([
                // Some clients upload battles out of order.
                // Estimate the start time if unknown, like Splatoon 2's one.
                new Expression(vsprintf('COALESCE(%s) ASC', [
                    implode(', ', [
                        '{{%battle3}}.[[start_at]]',
                        "{{%battle3}}.[[end_at]] - '3 minutes'::interval",
                        "{{%battle3}}.[[created_at]] - '4 minutes'::interval",
                    ]),
                ])),
                '{{%battle3}}.[[id]]' => SORT_ASC,
            ]);

        if ($lobbyGroup) {
            $query->andWhere(['{{%lobby3}}.[[group_id]]' => (int)$lobbyGroup->id]);
        }

        $command = $query->createCommand($db);

        /** @var RuleTrendData */
        return Yii::$app->cache->getOrSet(
            [__METHOD__, $command->rawSql],
            fn (): array => self::makeData(self::convertRows(array_values($command->queryAll()))),
            self::CACHE_DURATION,
        );
    }

    /**
     * Lobby groups the user played the rule in, e.g. Anarchy, X, Challenge for the ranked rules
     *
     * @return array<string, LobbyGroup3>
     */
    private static function getLobbyGroups(Connection $db, User $user, Rule3 $rule): array
    {
        $command = self::buildBaseQuery($user, $rule)
            ->select(['id' => '{{%lobby3}}.[[group_id]]'])
            ->distinct()
            ->createCommand($db);

        $ids = array_map(
            TypeHelper::int(...),
            Yii::$app->cache->getOrSet([__METHOD__, $command->rawSql], $command->queryColumn(...), self::CACHE_DURATION),
        );
        if (!$ids) {
            return [];
        }

        $groups = LobbyGroup3::find()
            ->andWhere(['id' => $ids])
            ->orderBy(['rank' => SORT_ASC])
            ->all();

        $ret = [];
        foreach ($groups as $group) {
            $ret[$group->key] = $group;
        }

        return $ret;
    }

    /**
     * @param list<TrendRow> $rows
     * @return RuleTrendData
     */
    private static function makeData(array $rows): array
    {
        $wins = array_column($rows, 'is_win');

        /** @var RuleTrendSeries $series */
        $series = array_merge(
            [
                'battle' => array_map(fn (int $i): int => $i + 1, array_keys($rows)),
                'winRate20' => TrendSeries::movingRate($wins, 20),
                'winRate50' => TrendSeries::movingRate($wins, 50),
                'inked' => array_column($rows, 'inked'),
                // The rank and X power at the time; filled so that thinning out does not drop them
                'rank' => TrendSeries::carryForward(array_column($rows, 'rank')),
                'xPower' => TrendSeries::carryForward(array_column($rows, 'x_power')),
            ],
            self::makeCumulativeSeries($rows),
        );

        $battles = count($rows);
        $indices = TrendSeries::pickIndices($battles, self::MAX_POINTS);

        return [
            'battles' => $battles,
            'thinned' => count($indices) < $battles,
            'all' => self::mapSeries($series, fn (array $values): array => TrendSeries::pick($values, $indices)),
            'recent' => $battles > self::RECENT_BATTLES
                ? array_merge(
                    self::mapSeries(
                        $series,
                        fn (array $values): array => array_slice($values, -self::RECENT_BATTLES),
                    ),
                    // Cumulative values within the recent battles
                    self::makeCumulativeSeries(array_slice($rows, -self::RECENT_BATTLES)),
                )
                : null,
        ];
    }

    /**
     * @param list<TrendRow> $rows
     * @return array{
     *   winRate: list<float>,
     *   killRatio: list<float|null>,
     *   avgKill: list<float|null>,
     *   avgDeath: list<float|null>,
     *   avgSpecial: list<float|null>,
     *   avgInked: list<float|null>
     * }
     */
    private static function makeCumulativeSeries(array $rows): array
    {
        $kills = array_column($rows, 'kill');
        $deaths = array_column($rows, 'death');

        // Kills and deaths are counted only when both of them are known
        $hasKD = array_map(
            fn (array $row): bool => $row['kill'] !== null && $row['death'] !== null,
            $rows,
        );

        return [
            'winRate' => TrendSeries::cumulativeRate(array_column($rows, 'is_win')),
            'killRatio' => TrendSeries::cumulativeRatio($kills, $deaths),
            'avgKill' => TrendSeries::cumulativeAverage(
                array_map(fn (?int $v, bool $ok): ?int => $ok ? $v : null, $kills, $hasKD),
            ),
            'avgDeath' => TrendSeries::cumulativeAverage(
                array_map(fn (?int $v, bool $ok): ?int => $ok ? $v : null, $deaths, $hasKD),
            ),
            'avgSpecial' => TrendSeries::cumulativeAverage(array_column($rows, 'special')),
            'avgInked' => TrendSeries::cumulativeAverage(array_column($rows, 'inked')),
        ];
    }

    /**
     * @param RuleTrendSeries $series
     * @param callable(list<mixed>): list<mixed> $callback
     * @return RuleTrendSeries
     */
    private static function mapSeries(array $series, callable $callback): array
    {
        $ret = [];
        foreach ($series as $key => $values) {
            $ret[$key] = $callback($values);
        }

        /** @var RuleTrendSeries */
        return $ret;
    }

    /**
     * Battles of the rule, excluding private battles and the battles with disconnected players
     */
    private static function buildBaseQuery(User $user, Rule3 $rule): Query
    {
        return new Query()
            ->from('{{%battle3}}')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%lobby_group3}}', '{{%lobby3}}.[[group_id]] = {{%lobby_group3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[rule_id]]' => (int)$rule->id,
                    '{{%battle3}}.[[user_id]]' => (int)$user->id,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['<>', '{{%lobby_group3}}.[[key]]', 'private'],
            ]);
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<TrendRow>
     */
    private static function convertRows(array $rows): array
    {
        return array_map(
            fn (array $row): array => [
                'is_win' => (bool)$row['is_win'],
                'kill' => TypeHelper::intOrNull($row['kill']),
                'death' => TypeHelper::intOrNull($row['death']),
                'special' => TypeHelper::intOrNull($row['special']),
                'inked' => TypeHelper::intOrNull($row['inked']),
                'rank' => Rank3Helper::toNumber(
                    TypeHelper::stringOrNull($row['rank_key']),
                    TypeHelper::intOrNull($row['rank_s_plus']),
                ),
                'x_power' => TypeHelper::floatOrNull($row['x_power']),
            ],
            $rows,
        );
    }
}
