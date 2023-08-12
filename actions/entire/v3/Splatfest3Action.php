<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use LogicException;
use Yii;
use app\models\Lobby3;
use app\models\Rule3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function assert;
use function hexdec;
use function implode;
use function preg_match;
use function sort;
use function substr;
use function vsprintf;

use const SORT_NUMERIC;

final class Splatfest3Action extends Action
{
    private const START_AT = '2023-08-12T00:00:00+00:00';
    private const END_AT = '2023-08-14T00:00:00+00:00';

    private const TEAM_NAME_1 = 'Money / 富 / 财富 / 財富';
    private const TEAM_NAME_2 = 'Fame / 名声 / 名聲';
    private const TEAM_NAME_3 = 'Love / 愛 / 爱';

    private const TEAM_COLOR_1 = 'c7742d';
    private const TEAM_COLOR_2 = '73bd48';
    private const TEAM_COLOR_3 = 'b74879';

    private const TEAM_COLOR_PROGRESS_1 = self::TEAM_COLOR_1;
    private const TEAM_COLOR_PROGRESS_2 = self::TEAM_COLOR_2;
    private const TEAM_COLOR_PROGRESS_3 = self::TEAM_COLOR_3;

    public function run(): Response|string
    {
        $controller = $this->controller;
        assert($controller instanceof Controller);

        $data = Yii::$app->db->transaction(
            function (Connection $db): array {
                $themes = $this->getThemes($db);
                return [
                    'votes' => ArrayHelper::map($this->getVotes($db, $themes), 'theme', 'count'),
                    'names' => [
                        'team1' => self::TEAM_NAME_1,
                        'team2' => self::TEAM_NAME_2,
                        'team3' => self::TEAM_NAME_3,
                    ],
                    'colors' => [
                        'team1' => self::TEAM_COLOR_PROGRESS_1,
                        'team2' => self::TEAM_COLOR_PROGRESS_2,
                        'team3' => self::TEAM_COLOR_PROGRESS_3,
                    ],
                ];
            },
            Transaction::REPEATABLE_READ,
        );

        return $controller->render('v3/splatfest3', $data);
    }

    private function getThemes(Connection $db): array
    {
        return Yii::$app->cache->getOrSet(
            [__METHOD__, self::TEAM_COLOR_1, self::TEAM_COLOR_2, self::TEAM_COLOR_3],
            fn (): array => [
                'team1' => $this->getTeamIdsByColor($db, self::TEAM_COLOR_1),
                'team2' => $this->getTeamIdsByColor($db, self::TEAM_COLOR_2),
                'team3' => $this->getTeamIdsByColor($db, self::TEAM_COLOR_3),
            ],
            600,
        );
    }

    private function getTeamIdsByColor(Connection $db, string $hexColor): array
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
            $this->getTeamIdsByColorImpl($db, $colors, 'our_team_color', 'our_team_theme_id'),
            $this->getTeamIdsByColorImpl($db, $colors, 'their_team_color', 'their_team_theme_id'),
        );

        sort($ids, SORT_NUMERIC);
        return array_values(array_unique($ids));
    }

    private function getTeamIdsByColorImpl(Connection $db, array $colors, string $attrColor, string $attrTheme): array
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
                ['between', '{{%battle3}}.[[start_at]]', self::START_AT, self::END_AT],
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

    private function getVotes(Connection $db, array $themes)
    {
        return Yii::$app->cache->getOrSet(
            [__METHOD__, $themes],
            function () use ($db, $themes): array {
                $themeSql = $this->buildThemeAggregator($db, $themes);
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
                        ['between', '{{%battle3}}.[[start_at]]', self::START_AT, self::END_AT],
                        '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
                    ])
                    ->groupBy([$themeSql]);
                return $query->all($db);
            },
            180,
        );
    }

    private function buildThemeAggregator(Connection $db, array $themes): string
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
        return vsprintf('(CASE %s END)', [
            implode(' ', $cases),
        ]);
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
