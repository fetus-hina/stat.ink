<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use Yii;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Splatfest3Theme;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

use function array_map;
use function array_merge;
use function assert;
use function implode;
use function vsprintf;

final class Splatfest3Action extends Action
{
    public function run(): Response|string
    {
        $controller = $this->controller;
        assert($controller instanceof Controller);

        $data = Yii::$app->db->transaction(
            function (Connection $db): array {
                $themes = $this->getThemes($db);
                return [
                    'votes' => ArrayHelper::map($this->getVotes($db, $themes), 'theme', 'count'),
                ];
            },
            Transaction::REPEATABLE_READ,
        );

        return $controller->render('v3/splatfest3', $data);
    }

    private function getThemes(Connection $db): array
    {
        return Yii::$app->cache->getOrSet(
            __METHOD__,
            fn (): array => [
                'dark' => $this->getDarkIds($db),
                'milk' => $this->getMilkIds($db),
                'white' => $this->getWhiteIds($db),
            ],
            600,
        );
    }

    private function getDarkIds(Connection $db): array
    {
        return $this->getThemeIds($db, [
            'Chocolat noir',
            'Dark Chocolate',
            'Puur',
            'ビター',
            '苦甜',
            '苦甜巧克力',
        ]);
    }

    private function getMilkIds(Connection $db): array
    {
        return $this->getThemeIds($db, [
            'Chocolat au lait',
            'Melk',
            'Milk Chocolate',
            'ミルク',
            '牛奶',
            '牛奶巧克力',
        ]);
    }

    private function getWhiteIds(Connection $db): array
    {
        return $this->getThemeIds($db, [
            'Chocolat blanc',
            'White Chocolate',
            'Wit',
            'ホワイト',
            '白',
            '白巧克力',
        ]);
    }

    /**
     * @return int[]
     */
    private function getThemeIds(Connection $db, array $texts): array
    {
        return ArrayHelper::getColumn(
            Splatfest3Theme::find()->andWhere(['name' => $texts])->all($db),
            'id',
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
                        ['between', '{{%battle3}}.[[start_at]]', '2023-02-10T00:00:00+00:00', '2023-02-14T00:00:00+00:00'],
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
