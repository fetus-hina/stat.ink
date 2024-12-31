<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use LogicException;
use TypeError;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\Language;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Rule3;
use app\models\Splatfest3;
use app\models\Splatfest3StatsWeapon;
use app\models\Splatfest3Theme;
use app\models\SplatfestTeam3;
use app\models\TricolorRole3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function count;
use function gmdate;
use function implode;
use function sprintf;
use function vsprintf;

use const DATE_ATOM;
use const SORT_ASC;
use const SORT_DESC;

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
                $names = [
                    'team1' => $teams[0]->name,
                    'team2' => $teams[1]->name,
                    'team3' => $teams[2]->name,
                ];
                $colors = [
                    'team1' => $teams[0]->color,
                    'team2' => $teams[1]->color,
                    'team3' => $teams[2]->color,
                ];
                return [
                    'colors' => $colors,
                    'dragonStats' => $this->getDragonStats(
                        $db,
                        $model,
                        $this->flattenThemeIds($themes),
                    ),
                    'festList' => $this->getFestList($db),
                    'names' => $names,
                    'splatfest' => $model,
                    'stages' => $this->getStages($db),
                    'tricolorStats' => $this->getTricolorStats(
                        $db,
                        $model,
                        $this->flattenThemeIds($themes),
                    ),
                    'votes' => ArrayHelper::map(
                        $this->getVotes($db, $model, $themes),
                        'theme',
                        'count',
                    ),
                    'weaponsChallenge' => $this->getWeaponsChallenge($db, $model),
                    'weaponsOpen' => $this->getWeaponsOpen($db, $model),
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
            ->orderBy([
                'start_at' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->all($db);
    }

    /**
     * @param SplatfestTeam3[] $teams
     */
    private function getThemesOnBattle3(Connection $db, Splatfest3 $fest, array $teams): array
    {
        return Yii::$app->cache->getOrSet(
            [
                __METHOD__,
                ArrayHelper::getColumn($teams, 'id'),
            ],
            fn (): array => [
                'team1' => $this->getTeamIds($db, $fest, $teams[0]),
                'team2' => $this->getTeamIds($db, $fest, $teams[1]),
                'team3' => $this->getTeamIds($db, $fest, $teams[2]),
            ],
            600,
        );
    }

    /**
     * @return int[]
     */
    private function getTeamIds(Connection $db, Splatfest3 $fest, SplatfestTeam3 $team): array
    {
        $langs = ArrayHelper::getColumn(
            Language::find()->standard()->cache(86400)->all(),
            'lang',
        );

        $names = array_values(
            array_unique(
                array_map(
                    fn (string $lang): string => Yii::t('db/splatfest3/team', $team->name, [], $lang),
                    $langs,
                ),
            ),
        );

        return ArrayHelper::getColumn(
            Splatfest3Theme::find()
                ->andWhere(['name' => $names])
                ->all($db),
            fn (Splatfest3Theme $theme): int => TypeHelper::int($theme->id),
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

    /**
     * @param int[] $themeIds
     * @return array{map_id: int, battles: int, attacker_wins: int}[]
     */
    private function getTricolorStats(Connection $db, Splatfest3 $fest, array $themeIds): array
    {
        $attackers = TricolorRole3::find()
            ->andWhere(['key' => 'attacker'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        $lobby = Lobby3::find()
            ->andWhere(['key' => 'splatfest_open'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        $tricolor = Rule3::find()
            ->andWhere(['key' => 'tricolor'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        if (!$attackers || !$lobby || !$tricolor) {
            throw new ServerErrorHttpException();
        }

        $isAttackerWins = new Expression(
            vsprintf('(%s.%s = %s) = %s.%s', [
                $db->quoteTableName('{{%battle3}}'),
                $db->quoteColumnName('our_team_role_id'),
                (string)$db->quoteValue($attackers->id),
                $db->quoteTableName('{{%result3}}'),
                $db->quoteColumnName('is_win'),
            ]),
        );

        $query = (new Query())
            ->select([
                'map_id' => '{{%battle3}}.[[map_id]]',
                'battles' => 'COUNT(*)',
                'attacker_wins' => vsprintf('SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)', [
                    (string)$isAttackerWins,
                ]),
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                    '{{%battle3}}.[[our_team_theme_id]]' => $themeIds,
                    '{{%battle3}}.[[rule_id]]' => $tricolor->id,
                    '{{%battle3}}.[[their_team_theme_id]]' => $themeIds,
                    '{{%battle3}}.[[third_team_theme_id]]' => $themeIds,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[map_id]]' => null]],
                ['not', ['{{%battle3}}.[[our_team_role_id]]' => null]],
                ['not', ['{{%battle3}}.[[their_team_role_id]]' => null]],
                ['not', ['{{%battle3}}.[[third_team_role_id]]' => null]],
                '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
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
            ])
            ->groupBy([
                '{{battle3}}.[[map_id]]',
            ])
            ->orderBy([
                'battles' => SORT_DESC,
                'attacker_wins' => SORT_DESC,
                'map_id' => SORT_DESC,
            ]);

        return $query->createCommand($db)
            ->cache(300)
            ->queryAll();
    }

    /**
     * @return array<int, Map3>
     */
    private function getStages(Connection $db): array
    {
        return ArrayHelper::index(
            Map3::find()
                ->orderBy(['id' => SORT_ASC])
                ->cache(3600)
                ->all($db),
            'id',
        );
    }

    /**
     * @param int[] $themeIds
     * @return array{lobby_id: int, fest_dragon_id: int, battles: int}[]
     */
    private function getDragonStats(Connection $db, Splatfest3 $fest, array $themeIds): array
    {
        $lobbyIds = ArrayHelper::getColumn(
            Lobby3::find()
                ->andWhere([
                    'key' => [
                        'splatfest_open',
                        'splatfest_challenge',
                    ],
                ])
                ->cache(86400)
                ->all($db),
            'id',
        );

        $nawabari = Rule3::find()
            ->andWhere(['key' => 'nawabari'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        if (count($lobbyIds) !== 2 || !$nawabari) {
            throw new ServerErrorHttpException();
        }

        $query = (new Query())
            ->select([
                '{{%battle3}}.[[lobby_id]]',
                '{{%battle3}}.[[fest_dragon_id]]',
                'battles' => 'COUNT(*)',
            ])
            ->from('{{%battle3}}')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => $lobbyIds,
                    '{{%battle3}}.[[our_team_theme_id]]' => $themeIds,
                    '{{%battle3}}.[[rule_id]]' => $nawabari->id,
                    '{{%battle3}}.[[their_team_theme_id]]' => $themeIds,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                ],
                '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
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
                ['not', ['{{%battle3}}.[[our_team_theme_id]]' => null]],
                ['not', ['{{%battle3}}.[[their_team_theme_id]]' => null]],
                '{{%battle3}}.[[our_team_theme_id]] <> {{%battle3}}.[[their_team_theme_id]]',
            ])
            ->groupBy([
                '{{%battle3}}.[[lobby_id]]',
                '{{%battle3}}.[[fest_dragon_id]]',
            ])
            ->orderBy([
                '{{%battle3}}.[[lobby_id]]' => SORT_ASC,
                '{{%battle3}}.[[fest_dragon_id]]' => SORT_ASC,
            ]);
        return $query->createCommand($db)
            ->cache(300)
            ->queryAll();
    }

    /**
     * @return Splatfest3StatsWeapon[]
     */
    private function getWeaponsChallenge(Connection $db, Splatfest3 $fest): array
    {
        return $this->getWeapons(
            $db,
            $fest,
            TypeHelper::instanceOf(Lobby3::findOne(['key' => 'splatfest_challenge']), Lobby3::class),
        );
    }

    /**
     * @return Splatfest3StatsWeapon[]
     */
    private function getWeaponsOpen(Connection $db, Splatfest3 $fest): array
    {
        return $this->getWeapons(
            $db,
            $fest,
            TypeHelper::instanceOf(Lobby3::findOne(['key' => 'splatfest_open']), Lobby3::class),
        );
    }

    /**
     * @return Splatfest3StatsWeapon[]
     */
    private function getWeapons(Connection $db, Splatfest3 $fest, Lobby3 $lobby): array
    {
        return Splatfest3StatsWeapon::find()
            ->with([
                'weapon',
                'weapon.special',
                'weapon.subweapon',
            ])
            ->andWhere([
                'fest_id' => $fest->id,
                'lobby_id' => $lobby->id,
            ])
            ->all($db);
    }
}
