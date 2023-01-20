<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use Yii;
use app\components\helpers\Season3Helper;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatWeapon3Usage;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function assert;

use const SORT_ASC;
use const SORT_DESC;

final class Weapons3Action extends Action
{
    private const PARAM_SEASON_ID = Season3Helper::DEFAULT_SEASON_PARAM_NAME;

    public function run(?string $lobby = null, ?string $rule = null): Response|string
    {
        $controller = $this->controller;
        assert($controller instanceof Controller);

        $params = Yii::$app->db->transaction(
            fn (Connection $db): Response|array => $this->doRun($controller, $db, $lobby, $rule),
            Transaction::REPEATABLE_READ,
        );

        return $params instanceof Response
            ? $params
            : $controller->render('v3/weapons3', $params);
    }

    private function doRun(
        Controller $controller,
        Connection $db,
        ?string $lobbyKey,
        ?string $ruleKey,
    ): Response|array {
        $season = Season3Helper::getUrlTargetSeason(self::PARAM_SEASON_ID);
        $lobby = $this->getLobby($db, $lobbyKey);
        $rule = $this->getRule($db, $ruleKey);
        if (!$season || !$lobby || !$rule) {
            $season = $season ?? Season3Helper::getCurrentSeason();
            if (!$season) {
                throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            }

            return $controller->redirect(['entire/weapons3',
                self::PARAM_SEASON_ID => $season->id,
                'lobby' => $lobby?->key ?? ($season->key === 'season202209' ? 'bankara_challenge' : 'xmatch'),
                'rule' => $rule?->key ?? 'area',
            ]);
        }

        if ($rule->key === 'nawabari' && $lobby->key !== 'regular') {
            return $controller->redirect(['entire/weapons3',
                self::PARAM_SEASON_ID => $season->id,
                'lobby' => 'regular',
                'rule' => 'nawabari',
            ]);
        }

        if ($rule->key !== 'nawabari' && $lobby->key === 'regular') {
            return $controller->redirect(['entire/weapons3',
                self::PARAM_SEASON_ID => $season->id,
                'lobby' => $season->key === 'season202209' ? 'bankara_challenge' : 'xmatch',
                'rule' => $rule->key,
            ]);
        }

        return [
            'data' => $this->getData($db, $season, $lobby, $rule),
            'lobbies' => $this->getLobbies($db),
            'lobby' => $lobby,
            'rule' => $rule,
            'rules' => $this->getRules($db),
            'season' => $season,
            'seasons' => Season3Helper::getSeasons(),

            'seasonUrl' => fn (Season3 $season): string => Url::to(
                ['entire/weapons3',
                    self::PARAM_SEASON_ID => $season->id,
                    'lobby' => $lobby->key,
                    'rule' => $rule->key,
                ],
            ),

            'ruleUrl' => fn (Rule3 $rule): string => Url::to(
                ['entire/weapons3',
                    self::PARAM_SEASON_ID => $season->id,
                    'lobby' => $rule->key === 'nawabari'
                        ? 'regular'
                        : (
                            $lobby->key === 'regular'
                                ? ($season->key === 'season202209' ? 'bankara_challenge' : 'xmatch')
                                : $lobby->key
                        ),
                    'rule' => $rule->key,
                ],
            ),

            'lobbyUrl' => fn (Lobby3 $lobby): string => Url::to(
                ['entire/weapons3',
                    self::PARAM_SEASON_ID => $season->id,
                    'lobby' => $lobby->key,
                    'rule' => match (true) {
                        $lobby->key === 'regular' => 'nawabari',
                        $lobby->key !== 'regular' && $rule->key === 'nawabari' => 'area',
                        default => $rule->key,
                    },
                ],
            ),
        ];
    }

    private function getLobby(Connection $db, ?string $key): ?Lobby3
    {
        if (!$key) {
            return null;
        }

        return Lobby3::find()
            ->andWhere(['key' => $key])
            ->limit(1)
            ->one($db);
    }

    private function getRule(Connection $db, ?string $key): ?Rule3
    {
        if (!$key) {
            return null;
        }

        return Rule3::find()
            ->andWhere(['key' => $key])
            ->limit(1)
            ->one($db);
    }

    /**
     * @return StatWeapon3Usage[]
     */
    private function getData(Connection $db, Season3 $season, Lobby3 $lobby, Rule3 $rule): array
    {
        return StatWeapon3Usage::find()
            ->with([
                'weapon',
                'weapon.special',
                'weapon.subweapon',
            ])
            ->andWhere([
                'lobby_id' => $lobby->id,
                'rule_id' => $rule->id,
                'season_id' => $season->id,
            ])
            ->orderBy([
                'battles' => SORT_DESC,
                'wins' => SORT_DESC,
                'weapon_id' => SORT_DESC,
            ])
            ->all($db);
    }

    /**
     * @return array<string, Lobby3>
     */
    private function getLobbies(Connection $db): array
    {
        return ArrayHelper::map(
            Lobby3::find()
                ->andWhere([
                    'key' => [
                        'bankara_challenge',
                        'regular',
                        'xmatch',
                    ],
                ])
                ->orderBy(['rank' => SORT_ASC])
                ->all(),
            'key',
            fn (Lobby3 $v): Lobby3 => $v,
        );
    }

    /**
     * @return array<int, Rule3>
     */
    private function getRules(Connection $db): array
    {
        return ArrayHelper::map(
            Rule3::find()
                ->andWhere(['not', ['key' => 'tricolor']])
                ->orderBy(['rank' => SORT_ASC])
                ->all($db),
            'id',
            fn (Rule3 $v): Rule3 => $v,
        );
    }
}
