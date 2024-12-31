<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use LogicException;
use Yii;
use app\components\helpers\Season3Helper;
use app\models\KDWin3FilterForm;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

use function array_filter;
use function trim;

use const SORT_ASC;

final class KDWin3Action extends Action
{
    public function run(): string|Response
    {
        $controller = $this->controller;
        if (!$controller instanceof Controller) {
            throw new LogicException();
        }

        $filter = Yii::createObject(KDWin3FilterForm::class);
        $filter->load($_GET);
        if ($filter->validate()) {
            if (!$filter->season) {
                $filter->season = Season3Helper::getCurrentSeason()?->key;
                if (!$filter->season) {
                    throw new ServerErrorHttpException();
                }

                return $controller->redirect(
                    ['entire/kd-win3',
                        'filter' => array_filter(
                            $filter->attributes,
                            fn (?string $value): bool => trim((string)$value) !== '',
                        ),
                    ],
                );
            }
        }

        return $controller->render(
            'v3/kd-win3',
            Yii::$app->db->transaction(
                fn (Connection $db): array => [
                    'data' => self::makeData($db, $filter),
                    'filter' => $filter,
                    'lobbies' => self::getLobbies($db),
                    'rules' => self::getRules($db),
                    'seasons' => self::getSeasons(),
                ],
                Transaction::REPEATABLE_READ,
            ),
        );
    }

    /**
     * @return array<int, array<int, array<int, array{battles: int, wins: int}>>> `$data[rule][d][k]`
     */
    private static function makeData(Connection $db, KDWin3FilterForm $filter): array
    {
        if ($filter->hasErrors()) {
            return [];
        }

        $season = Season3::find()
            ->andWhere(['key' => $filter->season])
            ->limit(1)
            ->one($db);
        if (!$season) {
            return [];
        }

        $lobby = $filter->lobby
            ? Lobby3::find()->andWhere(['key' => $filter->lobby])->limit(1)->one($db)
            : null;

        $query = (new Query())
            ->select([
                'rule_id' => '{{%stat_kd_win_rate3}}.[[rule_id]]',
                'kills' => '{{%stat_kd_win_rate3}}.[[kills]]',
                'deaths' => '{{%stat_kd_win_rate3}}.[[deaths]]',
                'battles' => 'SUM({{%stat_kd_win_rate3}}.[[battles]])',
                'wins' => 'SUM({{%stat_kd_win_rate3}}.[[wins]])',
            ])
            ->from('{{%stat_kd_win_rate3}}')
            ->andWhere(['{{%stat_kd_win_rate3}}.[[season_id]]' => $season->id])
            ->andWhere(
                $lobby
                    ? ['{{%stat_kd_win_rate3}}.[[lobby_id]]' => $lobby->id]
                    : '1 = 1',
            )
            ->groupBy([
                '{{%stat_kd_win_rate3}}.[[rule_id]]',
                '{{%stat_kd_win_rate3}}.[[kills]]',
                '{{%stat_kd_win_rate3}}.[[deaths]]',
            ]);

        $results = [];
        $handle = $query->createCommand($db)->query();
        foreach ($handle as $row) {
            $rule = (int)$row['rule_id'];
            $k = (int)$row['kills'];
            $d = (int)$row['deaths'];

            if (!isset($results[$rule])) {
                $results[$rule] = [];
            }

            if (!isset($results[$rule][$d])) {
                $results[$rule][$d] = [];
            }

            $results[$rule][$d][$k] = [
                'battles' => (int)$row['battles'],
                'wins' => (int)$row['wins'],
            ];
        }

        return $results;
    }

    /**
     * @return array<string, Season3>
     */
    private static function getSeasons(): array
    {
        return ArrayHelper::map(
            Season3Helper::getSeasons(),
            'key',
            fn (Season3 $v): Season3 => $v,
        );
    }

    /**
     * @return array<string, Lobby3>
     */
    private static function getLobbies(Connection $db): array
    {
        return ArrayHelper::map(
            Lobby3::find()
                ->andWhere(['<>', 'key', 'private'])
                ->orderBy(['rank' => SORT_ASC])
                ->all($db),
            'key',
            fn (Lobby3 $v): Lobby3 => $v,
        );
    }

    /**
     * @return array<int, Rule3>
     */
    private static function getRules(Connection $db): array
    {
        return ArrayHelper::map(
            Rule3::find()
                ->andWhere(['<>', 'key', 'tricolor'])
                ->orderBy(['rank' => SORT_ASC])
                ->all($db),
            'id',
            fn (Rule3 $v): Rule3 => $v,
        );
    }
}
