<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use Yii;
use app\commands\stat\actions\MatchingRange3Action as MatchingRange3StatAction;
use app\components\helpers\Season3Helper;
use app\components\helpers\TypeHelper;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatMatchingRangeDiff3;
use app\models\StatMatchingRangeSpread3;
use app\models\StatMatchingRangeSpreadDiff3;
use app\models\StatMatchingRangeTeam3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_keys;
use function is_string;
use function ksort;
use function vsprintf;

use const SORT_ASC;
use const SORT_DESC;
use const SORT_NUMERIC;

/**
 * @phpstan-type Series array{key: string, label: string, color: string}
 * @phpstan-type CountRows array<string, array<string, int>>
 * @phpstan-type WinRows array<string, array<string, array{battles: int, wins: int|null}>>
 */
final class MatchingRange3Action extends Action
{
    private const PARAM_SEASON_ID = Season3Helper::DEFAULT_SEASON_PARAM_NAME;
    private const PARAM_RULE_ID = 'rule';

    public function run(): Response|string
    {
        $controller = TypeHelper::instanceOf($this->controller, Controller::class);

        $params = TypeHelper::instanceOf(Yii::$app->db, Connection::class)->transaction(
            fn (Connection $db): Response|array => $this->doRun($controller, $db),
            Transaction::REPEATABLE_READ,
        );

        return $params instanceof Response
            ? $params
            : $controller->render('v3/matching-range3', $params);
    }

    /**
     * @return Response|array<string, mixed>
     */
    private function doRun(Controller $controller, Connection $db): Response|array
    {
        $seasons = $this->getSeasons($db);
        $season = Season3Helper::getUrlTargetSeason(self::PARAM_SEASON_ID);
        if (!$season) {
            $season = $seasons[0] ?? throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            return $controller->redirect(['entire/matching-range3', self::PARAM_SEASON_ID => $season->id]);
        }

        $rules = $this->getRules($db);
        $ruleKey = Yii::$app->request->get(self::PARAM_RULE_ID);
        $rule = null;
        if (is_string($ruleKey) && $ruleKey !== '') {
            $rule = ArrayHelper::getValue(
                ArrayHelper::index($rules, 'key'),
                $ruleKey,
            ) ?? throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $series = $this->getSeries($db);

        return [
            'rule' => $rule,
            'rules' => $rules,
            'season' => $season,
            'seasons' => $seasons,
            'series' => $series,
            'teamMr' => $this->getCountRows($db, StatMatchingRangeTeam3::class, 'mr', 'teams', $season, $rule, $series),
            'spread' => $this->getCountRows($db, StatMatchingRangeSpread3::class, 'spread', 'teams', $season, $rule, $series),
            'mrDiff' => $this->getWinRows($db, StatMatchingRangeDiff3::class, 'mr_diff', 'higher_wins', $season, $rule, $series),
            'spreadDiff' => $this->getWinRows($db, StatMatchingRangeSpreadDiff3::class, 'spread_diff', 'wider_wins', $season, $rule, $series),
            'ruleUrl' => fn (?Rule3 $rule): string => Url::to(
                ['entire/matching-range3',
                    self::PARAM_SEASON_ID => $season->id,
                    self::PARAM_RULE_ID => $rule?->key,
                ],
            ),
            'seasonUrl' => fn (Season3 $season): string => Url::to(
                ['entire/matching-range3',
                    self::PARAM_SEASON_ID => $season->id,
                    self::PARAM_RULE_ID => $rule?->key,
                ],
            ),
        ];
    }

    /**
     * Seasons that have data, newest first
     *
     * @return Season3[]
     */
    private function getSeasons(Connection $db): array
    {
        return Season3::find()
            ->andWhere([
                'id' => StatMatchingRangeTeam3::find()->select('season_id')->distinct(),
            ])
            ->orderBy(['start_at' => SORT_DESC])
            ->all($db);
    }

    /**
     * @return array<int, Rule3>
     */
    private function getRules(Connection $db): array
    {
        return ArrayHelper::map(
            Rule3::find()
                ->innerJoinWith('group', false)
                ->andWhere(['{{%rule_group3}}.[[key]]' => 'gachi'])
                ->orderBy(['{{%rule3}}.[[rank]]' => SORT_ASC])
                ->all($db),
            'id',
            fn (Rule3 $v): Rule3 => $v,
        );
    }

    /**
     * @return array<string, Series>
     */
    private function getSeries(Connection $db): array
    {
        $lobbies = ArrayHelper::map(
            Lobby3::find()->andWhere(['key' => ['xmatch', 'bankara_challenge']])->all($db),
            'key',
            fn (Lobby3 $v): Lobby3 => $v,
        );

        $threshold = Yii::$app->formatter->asInteger(MatchingRange3StatAction::X_POWER_THRESHOLD);
        $xName = Yii::t('app-lobby3', 'X Battle');
        $list = [];
        if ($x = $lobbies['xmatch'] ?? null) {
            $list[] = [
                'key' => self::seriesKey($x->id, 0),
                'label' => vsprintf('%s (< %s)', [$xName, $threshold]),
                'color' => 'window.colorScheme._accent.sky',
            ];
            $list[] = [
                'key' => self::seriesKey($x->id, MatchingRange3StatAction::X_POWER_THRESHOLD),
                'label' => vsprintf('%s (≥ %s)', [$xName, $threshold]),
                'color' => 'window.colorScheme._accent.blue',
            ];
        }

        if ($challenge = $lobbies['bankara_challenge'] ?? null) {
            $list[] = [
                'key' => self::seriesKey($challenge->id, 0),
                'label' => Yii::t('app-lobby3', $challenge->name),
                'color' => 'window.colorScheme._accent.orange',
            ];
        }

        return ArrayHelper::index($list, 'key');
    }

    /**
     * Counts per value, summed over the rules
     *
     * @param class-string<StatMatchingRangeTeam3|StatMatchingRangeSpread3> $modelClass
     * @param array<string, Series> $series
     * @return CountRows series key => (value => count), values sorted in ascending order
     */
    private function getCountRows(
        Connection $db,
        string $modelClass,
        string $valueColumn,
        string $countColumn,
        Season3 $season,
        ?Rule3 $rule,
        array $series,
    ): array {
        $result = [];
        foreach ($this->findRows($db, $modelClass, $season, $rule) as $row) {
            $key = self::seriesKey($row->lobby_id, $row->x_power_class);
            if (!isset($series[$key])) {
                continue;
            }

            $value = TypeHelper::string($row->getAttribute($valueColumn));
            $result[$key][$value] = ($result[$key][$value] ?? 0) + TypeHelper::int($row->getAttribute($countColumn));
        }

        return self::sortValues($result);
    }

    /**
     * Battles and wins per value, summed over the rules
     *
     * @param class-string<StatMatchingRangeDiff3|StatMatchingRangeSpreadDiff3> $modelClass
     * @param array<string, Series> $series
     * @return WinRows series key => (value => [battles, wins]), values sorted in ascending order
     */
    private function getWinRows(
        Connection $db,
        string $modelClass,
        string $valueColumn,
        string $winsColumn,
        Season3 $season,
        ?Rule3 $rule,
        array $series,
    ): array {
        $result = [];
        foreach ($this->findRows($db, $modelClass, $season, $rule) as $row) {
            $key = self::seriesKey($row->lobby_id, $row->x_power_class);
            if (!isset($series[$key])) {
                continue;
            }

            $value = TypeHelper::string($row->getAttribute($valueColumn));
            $wins = TypeHelper::intOrNull($row->getAttribute($winsColumn));
            $current = $result[$key][$value] ?? ['battles' => 0, 'wins' => null];
            $result[$key][$value] = [
                'battles' => $current['battles'] + TypeHelper::int($row->battles),
                'wins' => $wins === null ? $current['wins'] : ($current['wins'] ?? 0) + $wins,
            ];
        }

        return self::sortValues($result);
    }

    /**
     * @template T of StatMatchingRangeTeam3|StatMatchingRangeSpread3|StatMatchingRangeDiff3|StatMatchingRangeSpreadDiff3
     * @param class-string<T> $modelClass
     * @return T[]
     */
    private function findRows(Connection $db, string $modelClass, Season3 $season, ?Rule3 $rule): array
    {
        $query = $modelClass::find()->andWhere(['season_id' => $season->id]);
        if ($rule) {
            $query->andWhere(['rule_id' => $rule->id]);
        }

        /** @var T[] */
        return $query->all($db);
    }

    /**
     * @template T
     * @param array<string, array<string, T>> $data
     * @return array<string, array<string, T>>
     */
    private static function sortValues(array $data): array
    {
        foreach (array_keys($data) as $key) {
            ksort($data[$key], SORT_NUMERIC);
        }

        return $data;
    }

    private static function seriesKey(int $lobbyId, int $xPowerClass): string
    {
        return vsprintf('%d-%d', [$lobbyId, $xPowerClass]);
    }
}
