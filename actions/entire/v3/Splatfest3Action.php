<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use LogicException;
use TypeError;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Splatfest3;
use app\models\SplatfestTeam3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function count;
use function gmdate;
use function hexdec;
use function implode;
use function preg_match;
use function sort;
use function sprintf;
use function substr;
use function vsprintf;

use const DATE_ATOM;
use const SORT_DESC;
use const SORT_NUMERIC;

final class Splatfest3Action extends Action
{
    public function run(?string $id = null): Response|string
    {
        $controller = TypeHelper::instanceOf($this->controller, Controller::class);
        if ($id === null) {
            $model = Splatfest3::find()
                ->andWhere(['<=', 'start_at', gmdate(DATE_ATOM, $_SERVER['REQUEST_TIME'])])
                ->orderBy(['start_at' => SORT_DESC])
                ->limit(1)
                ->one();
            return $model
                ? $controller->redirect(['entire/splatfest3', 'id' => $model->id])
                : throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        try {
            $id = TypeHelper::int($id);
        } catch (TypeError $e) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $model = Splatfest3::find()
            ->andWhere(['id' => $id])
            ->andWhere(['<=', 'start_at', gmdate(DATE_ATOM, $_SERVER['REQUEST_TIME'])])
            ->orderBy(['start_at' => SORT_DESC])
            ->limit(1)
            ->one();

        $data = Yii::$app->db->transaction(
            function (Connection $db) use ($model): array {
                $teams = ArrayHelper::sort(
                    $model->splatfestTeam3s,
                    fn (SplatfestTeam3 $a, SplatfestTeam3 $b) => $a->camp_id <=> $b->camp_id,
                );
                if (count($teams) !== 3) {
                    throw new LogicException();
                }

                $themes = $this->getThemesOnBattle3($db, $model, $teams);

                return [
                    'splatfest' => $model,
                    'festList' => $this->getFestList($db),
                    'votes' => ArrayHelper::map($this->getVotes($db, $model, $themes), 'theme', 'count'),
                    'names' => [
                        'team1' => $teams[0]->name,
                        'team2' => $teams[1]->name,
                        'team3' => $teams[2]->name,
                    ],
                    'colors' => [
                        'team1' => $teams[0]->color,
                        'team2' => $teams[1]->color,
                        'team3' => $teams[2]->color,
                    ],
                ];
            },
            Transaction::REPEATABLE_READ,
        );

        return $controller->render('v3/splatfest3', $data);
    }

    private function getFestList(Connection $db): array
    {
        return Splatfest3::find()
            ->andWhere(['<=', 'start_at', gmdate(DATE_ATOM, $_SERVER['REQUEST_TIME'])])
            ->orderBy(['start_at' => SORT_DESC])
            ->all($db);
    }

    /**
     * @param SplatfestTeam3[] $teams
     */
    private function getThemesOnBattle3(Connection $db, Splatfest3 $fest, array $teams): array
    {
        $colors = Arrayhelper::getColumn($teams, 'color');
        if (count($colors) !== 3) {
            throw new LogicException();
        }

        return Yii::$app->cache->getOrSet(
            [__METHOD__, $colors],
            fn (): array => [
                'team1' => $this->getTeamIdsByColor($db, $fest, $colors[0]),
                'team2' => $this->getTeamIdsByColor($db, $fest, $colors[1]),
                'team3' => $this->getTeamIdsByColor($db, $fest, $colors[2]),
            ],
            600,
        );
    }

    private function getTeamIdsByColor(Connection $db, Splatfest3 $fest, string $hexColor): array
    {
        if (!preg_match('/^[0-9a-f]{6,}$/', $hexColor)) {
            throw new LogicException();
        }

        $colors = [];
        $baseR = hexdec(substr($hexColor, 0, 2));
        $baseG = hexdec(substr($hexColor, 2, 2));
        $baseB = hexdec(substr($hexColor, 4, 2));
        $offsets = [-1, 0, 1];
        foreach ($offsets as $offB) {
            $b = $baseB + $offB;
            foreach ($offsets as $offG) {
                $g = $baseG + $offG;
                foreach ($offsets as $offR) {
                    $r = $baseR + $offR;
                    if (
                        (0 <= $r && $r <= 255) &&
                        (0 <= $g && $g <= 255) &&
                        (0 <= $b && $b <= 255)
                    ) {
                        $colors[] = vsprintf('%02x%02x%02x%02x', [
                            $r,
                            $g,
                            $b,
                            255,
                        ]);
                    }
                }
            }
        }

        $ids = array_merge(
            $this->getTeamIdsByColorImpl($db, $fest, $colors, 'our_team_color', 'our_team_theme_id'),
            $this->getTeamIdsByColorImpl($db, $fest, $colors, 'their_team_color', 'their_team_theme_id'),
        );

        sort($ids, SORT_NUMERIC);
        return array_values(array_unique($ids));
    }

    private function getTeamIdsByColorImpl(Connection $db, Splatfest3 $fest, array $colors, string $attrColor, string $attrTheme): array
    {
        $query = (new Query())
            ->select([
                'theme_id' => $attrTheme,
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->andWhere([
                '{{%battle3}}.[[has_disconnect]]' => false,
                '{{%battle3}}.[[is_automated]]' => true,
                '{{%battle3}}.[[is_deleted]]' => false,
                '{{%battle3}}.[[lobby_id]]' => $this->getLobbyIds($db),
                '{{%battle3}}.[[rule_id]]' => $this->getRuleIds($db),
                '{{%battle3}}.[[use_for_entire]]' => true,
                '{{%result3}}.[[aggregatable]]' => true,
            ])
            ->andWhere(['and',
                ['not', ['{{%battle3}}.[[end_at]]' => null]],
                ['not', ['{{%battle3}}.[[our_team_color]]' => null]],
                ['not', ['{{%battle3}}.[[our_team_theme_id]]' => null]],
                ['not', ['{{%battle3}}.[[start_at]]' => null]],
                ['not', ['{{%battle3}}.[[their_team_color]]' => null]],
                ['not', ['{{%battle3}}.[[their_team_theme_id]]' => null]],
                ['between',
                    '{{%battle3}}.[[start_at]]',
                    new Expression(
                        vsprintf('%s::timestamptz - %s::interval', [
                            $db->quoteValue($fest->start_at),
                            $db->quoteValue('1 hour'),
                        ]),
                    ),
                    new Expression(
                        vsprintf('%s::timestamptz + %s::interval', [
                            $db->quoteValue($fest->end_at),
                            $db->quoteValue('10 minute'),
                        ]),
                    ),
                ],
                '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
            ])
            ->andWhere([
                "{{%battle3}}.[[{$attrColor}]]" => $colors,
            ])
            ->groupBy([$attrTheme]);
        return array_map(
            fn (int|string $v): int => (int)$v,
            $query->column($db),
        );
    }

    private function getVotes(Connection $db, Splatfest3 $fest, array $themes): array
    {
        return Yii::$app->cache->getOrSet(
            [__METHOD__, $themes],
            function () use ($db, $fest, $themes): array {
                if (!$themeSql = $this->buildThemeAggregator($db, $themes)) {
                    return [];
                }
                $query = (new Query())
                    ->select([
                        'theme' => $themeSql,
                        'count' => 'COUNT(*)',
                    ])
                    ->from('{{%battle3}}')
                    ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
                    ->andWhere([
                        '{{%battle3}}.[[has_disconnect]]' => false,
                        '{{%battle3}}.[[is_automated]]' => true,
                        '{{%battle3}}.[[is_deleted]]' => false,
                        '{{%battle3}}.[[lobby_id]]' => $this->getLobbyIds($db),
                        '{{%battle3}}.[[our_team_theme_id]]' => $this->flattenThemeIds($themes),
                        '{{%battle3}}.[[rule_id]]' => $this->getRuleIds($db),
                        '{{%battle3}}.[[their_team_theme_id]]' => $this->flattenThemeIds($themes),
                        '{{%battle3}}.[[use_for_entire]]' => true,
                        '{{%result3}}.[[aggregatable]]' => true,
                    ])
                    ->andWhere(['and',
                        ['not', ['{{%battle3}}.[[end_at]]' => null]],
                        ['not', ['{{%battle3}}.[[our_team_color]]' => null]],
                        ['not', ['{{%battle3}}.[[our_team_theme_id]]' => null]],
                        ['not', ['{{%battle3}}.[[start_at]]' => null]],
                        ['not', ['{{%battle3}}.[[their_team_color]]' => null]],
                        ['not', ['{{%battle3}}.[[their_team_theme_id]]' => null]],
                        ['between',
                            '{{%battle3}}.[[start_at]]',
                            new Expression(
                                vsprintf('%s::timestamptz - %s::interval', [
                                    $db->quoteValue($fest->start_at),
                                    $db->quoteValue('1 hour'),
                                ]),
                            ),
                            new Expression(
                                vsprintf('%s::timestamptz + %s::interval', [
                                    $db->quoteValue($fest->end_at),
                                    $db->quoteValue('10 minute'),
                                ]),
                            ),
                        ],
                        '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
                    ])
                    ->groupBy([$themeSql]);
                return $query->all($db);
            },
            180,
        );
    }

    private function buildThemeAggregator(Connection $db, array $themes): ?string
    {
        $cases = [];
        foreach ($themes as $name => $ids) {
            if ($ids) {
                $cases[] = vsprintf('WHEN %s.%s IN (%s) THEN %s', [
                    $db->quoteTableName('{{%battle3}}'),
                    $db->quoteColumnName('their_team_theme_id'),
                    implode(
                        ', ',
                        array_map(
                            fn (int $id): string => (string)$id,
                            $ids,
                        ),
                    ),
                    $db->quoteValue($name),
                ]);
            }
        }

        return $cases
            ? sprintf('(CASE %s END)', implode(' ', $cases))
            : null;
    }

    /**
     * @return int[]
     */
    private function getLobbyIds(Connection $db): array
    {
        return Yii::$app->cache->getOrSet(
            __METHOD__,
            fn (): array => ArrayHelper::getColumn(
                Lobby3::find()
                    ->andWhere(['key' => ['splatfest_challenge', 'splatfest_open']])
                    ->all(),
                'id',
            ),
            86400,
        );
    }

    /**
     * @return int[]
     */
    private function getRuleIds(Connection $db): array
    {
        return Yii::$app->cache->getOrSet(
            __METHOD__,
            fn (): array => ArrayHelper::getColumn(
                Rule3::find()
                    ->andWhere(['key' => 'nawabari'])
                    ->all(),
                'id',
            ),
            86400,
        );
    }

    private function flattenThemeIds(array $themes): array
    {
        $results = [];
        foreach ($themes as $ids) {
            $results = array_merge($results, $ids);
        }
        return $results;
    }
}
