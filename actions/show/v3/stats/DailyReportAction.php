<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\show\v3\stats;

use DateTimeInterface;
use LogicException;
use Override;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Rule3;
use app\models\Spl3YearMonthForm;
use app\models\User;
use app\models\Weapon3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_filter;
use function array_keys;
use function array_map;
use function array_unique;
use function array_values;
use function implode;
use function krsort;
use function sprintf;
use function strnatcasecmp;
use function usort;
use function vsprintf;

use const SORT_STRING;

/**
 * @phpstan-type DailyReportSummary array{
 *   battles: int,
 *   wins: int,
 *   kills_for_ratio: int,
 *   deaths_for_ratio: int,
 *   stats: array<string, array{avg: ?float, min: ?float, max: ?float, med: ?float, mod: ?float, sd: ?float}>
 * }
 * @phpstan-type DailyReportRow array{
 *   lobby: Lobby3,
 *   rule: Rule3,
 *   map: Map3,
 *   weapon: Weapon3,
 *   summary: DailyReportSummary
 * }
 * @phpstan-type DailyReportDay array{
 *   date: string,
 *   summary: DailyReportSummary,
 *   rows: list<DailyReportRow>
 * }
 */
final class DailyReportAction extends Action
{
    private const int CACHE_DURATION = 300;

    public ?User $user = null;

    /**
     * Columns of battle3 to summarize, with their display settings
     *
     * `mode`: whether the mode (most frequent value) is meaningful for the column.
     * It is not for "inked", whose values rarely coincide.
     *
     * @return array<string, array{label: string, decimals: int, mode: bool}>
     */
    public static function getStatsColumns(): array
    {
        return [
            'kill' => ['label' => Yii::t('app-results', 'k'), 'decimals' => 1, 'mode' => true],
            'death' => ['label' => Yii::t('app-results', 'd'), 'decimals' => 1, 'mode' => true],
            'kill_or_assist' => ['label' => Yii::t('app-results', 'k+a'), 'decimals' => 1, 'mode' => true],
            'special' => ['label' => Yii::t('app-results', 'sp'), 'decimals' => 1, 'mode' => true],
            'inked' => ['label' => Yii::t('app-results', 'Inked'), 'decimals' => 0, 'mode' => false],
        ];
    }

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
                'show-v3/stats-daily-report',
                'screen_name' => $user->screen_name,
                'year' => (int)$now->format('Y'),
                'month' => (int)$now->format('n'),
            ]);
        }

        return $controller->render('stats/daily-report', [
            'form' => $form,
            'list' => $this->makeList(Yii::$app->db, $user, $form),
            'user' => $user,
        ]);
    }

    /**
     * @return list<DailyReportDay>
     */
    private function makeList(Connection $db, User $user, Spl3YearMonthForm $form): array
    {
        $rows = $this->query($db, $user, $form);
        if (!$rows) {
            return [];
        }

        // GROUPING(...) = 1 means the row is the daily total (grouped by date only)
        $dailyRows = array_values(array_filter($rows, fn (array $row): bool => (int)$row['is_daily'] === 1));
        $detailRows = array_values(array_filter($rows, fn (array $row): bool => (int)$row['is_daily'] !== 1));

        $lobbies = self::fetchModels(Lobby3::class, $detailRows, 'lobby_id');
        $rules = self::fetchModels(Rule3::class, $detailRows, 'rule_id');
        $maps = self::fetchModels(Map3::class, $detailRows, 'map_id');
        $weapons = self::fetchModels(Weapon3::class, $detailRows, 'weapon_id');

        /** @var array<string, DailyReportDay> $days */
        $days = [];
        foreach ($dailyRows as $row) {
            $date = TypeHelper::string($row['date']);
            $days[$date] = [
                'date' => $date,
                'summary' => self::makeSummary($row),
                'rows' => [],
            ];
        }

        foreach ($detailRows as $row) {
            $date = TypeHelper::string($row['date']);
            if (!isset($days[$date])) {
                throw new LogicException("Daily total is missing for {$date}");
            }

            $days[$date]['rows'][] = [
                'lobby' => TypeHelper::instanceOf($lobbies[(int)$row['lobby_id']] ?? null, Lobby3::class),
                'rule' => TypeHelper::instanceOf($rules[(int)$row['rule_id']] ?? null, Rule3::class),
                'map' => TypeHelper::instanceOf($maps[(int)$row['map_id']] ?? null, Map3::class),
                'weapon' => TypeHelper::instanceOf($weapons[(int)$row['weapon_id']] ?? null, Weapon3::class),
                'summary' => self::makeSummary($row),
            ];
        }

        foreach ($days as &$day) {
            usort(
                $day['rows'],
                fn (array $a, array $b): int => $a['lobby']->rank <=> $b['lobby']->rank
                    ?: $a['rule']->rank <=> $b['rule']->rank
                    ?: strnatcasecmp(Yii::t('app-map3', $a['map']->name), Yii::t('app-map3', $b['map']->name))
                    ?: strnatcasecmp(
                        Yii::t('app-weapon3', $a['weapon']->name),
                        Yii::t('app-weapon3', $b['weapon']->name),
                    ),
            );
        }
        unset($day);

        krsort($days, SORT_STRING);

        return array_values($days);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function query(Connection $db, User $user, Spl3YearMonthForm $form): array
    {
        $from = $form->getFirstDayOfMonth();
        $to = $form->getFirstDayOfNextMonth();

        // Date of the battle in the form's (= user's) time zone.
        // Use `start_at` to be consistent with the battle list filter.
        $date = sprintf(
            '(TIMEZONE(%s, {{%%battle3}}.[[start_at]]))::date',
            $db->quoteValue($from->getTimezone()->getName()),
        );

        $columns = [
            'is_daily' => 'GROUPING({{%battle3}}.[[lobby_id]])',
            'date' => $date,
            'lobby_id' => '{{%battle3}}.[[lobby_id]]',
            'rule_id' => '{{%battle3}}.[[rule_id]]',
            'map_id' => '{{%battle3}}.[[map_id]]',
            'weapon_id' => '{{%battle3}}.[[weapon_id]]',
            'battles' => 'COUNT(*)',
            'wins' => 'SUM(CASE WHEN {{%result3}}.[[is_win]] THEN 1 ELSE 0 END)',
            'kills_for_ratio' => self::sumIfKillDeathPresent('kill'),
            'deaths_for_ratio' => self::sumIfKillDeathPresent('death'),
        ];
        foreach (array_keys(self::getStatsColumns()) as $name) {
            $c = "{{%battle3}}.[[{$name}]]";
            $columns["avg_{$name}"] = "AVG({$c})";
            $columns["min_{$name}"] = "MIN({$c})";
            $columns["max_{$name}"] = "MAX({$c})";
            $columns["med_{$name}"] = "PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {$c})";
            $columns["mod_{$name}"] = "MODE() WITHIN GROUP (ORDER BY {$c})";
            $columns["sd_{$name}"] = "STDDEV_POP({$c})";
        }

        $query = new Query()
            ->select($columns)
            ->from('{{%battle3}}')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[user_id]]' => (int)$user->id,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%battle3}}.[[map_id]]' => null]],
                ['not', ['{{%battle3}}.[[weapon_id]]' => null]],
                ['<>', '{{%lobby3}}.[[key]]', 'private'],
                ['>=', '{{%battle3}}.[[start_at]]', $from->format(DateTimeInterface::ATOM)],
                ['<', '{{%battle3}}.[[start_at]]', $to->format(DateTimeInterface::ATOM)],
                // Lets the index (user_id, end_at) be used. end_at is never earlier than start_at.
                // No upper bound on end_at: it is the upload time if the client did not send it.
                ['>=', '{{%battle3}}.[[end_at]]', $from->format(DateTimeInterface::ATOM)],
            ])
            ->groupBy(
                new Expression(
                    vsprintf('GROUPING SETS ((%s), (%s))', [
                        implode(', ', [
                            $date,
                            '{{%battle3}}.[[lobby_id]]',
                            '{{%battle3}}.[[rule_id]]',
                            '{{%battle3}}.[[map_id]]',
                            '{{%battle3}}.[[weapon_id]]',
                        ]),
                        $date,
                    ]),
                ),
            );

        $command = $query->createCommand($db);

        /** @var list<array<string, mixed>> */
        return Yii::$app->cache->getOrSet(
            [__METHOD__, $command->rawSql],
            fn (): array => $command->queryAll(),
            self::CACHE_DURATION,
        );
    }

    private static function sumIfKillDeathPresent(string $column): string
    {
        return vsprintf('SUM(CASE %s END)', [
            implode(' ', [
                'WHEN {{%battle3}}.[[kill]] IS NULL THEN 0',
                'WHEN {{%battle3}}.[[death]] IS NULL THEN 0',
                "ELSE {{%battle3}}.[[{$column}]]",
            ]),
        ]);
    }

    /**
     * @param array<string, mixed> $row
     * @return DailyReportSummary
     */
    private static function makeSummary(array $row): array
    {
        return [
            'battles' => TypeHelper::int($row['battles']),
            'wins' => TypeHelper::int($row['wins']),
            'kills_for_ratio' => TypeHelper::int($row['kills_for_ratio']),
            'deaths_for_ratio' => TypeHelper::int($row['deaths_for_ratio']),
            'stats' => self::extractStats($row),
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, array{avg: ?float, min: ?float, max: ?float, med: ?float, mod: ?float, sd: ?float}>
     */
    private static function extractStats(array $row): array
    {
        $ret = [];
        foreach (array_keys(self::getStatsColumns()) as $name) {
            $ret[$name] = [
                'avg' => TypeHelper::floatOrNull($row["avg_{$name}"] ?? null),
                'min' => TypeHelper::floatOrNull($row["min_{$name}"] ?? null),
                'max' => TypeHelper::floatOrNull($row["max_{$name}"] ?? null),
                'med' => TypeHelper::floatOrNull($row["med_{$name}"] ?? null),
                'mod' => TypeHelper::floatOrNull($row["mod_{$name}"] ?? null),
                'sd' => TypeHelper::floatOrNull($row["sd_{$name}"] ?? null),
            ];
        }

        return $ret;
    }

    /**
     * @template T of Lobby3|Rule3|Map3|Weapon3
     * @param class-string<T> $class
     * @param list<array<string, mixed>> $rows
     * @return array<int, T>
     */
    private static function fetchModels(string $class, array $rows, string $column): array
    {
        $ids = array_values(
            array_unique(
                array_map(fn (array $row): int => TypeHelper::int($row[$column]), $rows),
            ),
        );

        /** @var array<int, T> */
        return $class::find()
            ->andWhere(['id' => $ids])
            ->indexBy('id')
            ->all();
    }
}
