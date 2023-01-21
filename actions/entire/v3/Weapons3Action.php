<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Yii;
use app\components\helpers\Season3Helper;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\SplatoonVersion3;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
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
        $version = $this->getVersion($db, (string)Yii::$app->request->get('version'));
        $season = $version ? null : Season3Helper::getUrlTargetSeason(self::PARAM_SEASON_ID);
        $lobby = $this->getLobby($db, $lobbyKey);
        $rule = $this->getRule($db, $ruleKey);
        if (
            !($season || $version) ||
            !$lobby ||
            !$rule
        ) {
            $season = $season ?? Season3Helper::getCurrentSeason();
            if (!$version && !$season) {
                throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            }

            return $controller->redirect(['entire/weapons3',
                'lobby' => $this->getDefaultLobbyKey($season, $version),
                'rule' => $rule?->key ?? 'area',
                'version' => $version?->tag,
                self::PARAM_SEASON_ID => $season?->id,
            ]);
        }

        if ($rule->key === 'nawabari' && $lobby->key !== 'regular') {
            return $controller->redirect(['entire/weapons3',
                'lobby' => 'regular',
                'rule' => 'nawabari',
                'version' => $version?->tag,
                self::PARAM_SEASON_ID => $season?->id,
            ]);
        }

        if ($rule->key !== 'nawabari' && $lobby->key === 'regular') {
            return $controller->redirect(['entire/weapons3',
                'lobby' => $this->getDefaultLobbyKey($season, $version),
                'rule' => $rule->key,
                'version' => $version?->tag,
                self::PARAM_SEASON_ID => $season?->id,
            ]);
        }

        return [
            'data' => $this->getData($db, $season, $version, $lobby, $rule),
            'lobbies' => $this->getLobbies($db),
            'lobby' => $lobby,
            'rule' => $rule,
            'rules' => $this->getRules($db),
            'season' => $season,
            'seasons' => Season3Helper::getSeasons(),
            'version' => $version,
            'versions' => $this->getVersions($db),

            'seasonUrl' => fn (Season3 $season): string => Url::to(
                ['entire/weapons3',
                    'lobby' => $lobby->key,
                    'rule' => $rule->key,
                    self::PARAM_SEASON_ID => $season->id,
                ],
            ),

            'ruleUrl' => fn (Rule3 $rule): string => Url::to(
                ['entire/weapons3',
                    'lobby' => $rule->key === 'nawabari'
                        ? 'regular'
                        : (
                            $lobby->key === 'regular'
                                ? $this->getDefaultLobbyKey($season, $version)
                                : $lobby->key
                        ),
                    'rule' => $rule->key,
                    'version' => $version?->tag,
                    self::PARAM_SEASON_ID => $season?->id,
                ],
            ),

            'lobbyUrl' => fn (Lobby3 $lobby): string => Url::to(
                ['entire/weapons3',
                    'lobby' => $lobby->key,
                    'rule' => match (true) {
                        $lobby->key === 'regular' => 'nawabari',
                        $lobby->key !== 'regular' && $rule->key === 'nawabari' => 'area',
                        default => $rule->key,
                    },
                    'version' => $version?->tag,
                    self::PARAM_SEASON_ID => $season?->id,
                ],
            ),

            'versionUrl' => fn (SplatoonVersion3 $version): string => Url::to(
                ['entire/weapons3',
                    'lobby' => $lobby->key,
                    'rule' => $rule->key,
                    'version' => $version->tag,
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

    private function getVersion(Connection $db, ?string $key): ?SplatoonVersion3
    {
        if (!$key) {
            return null;
        }

        return SplatoonVersion3::find()
            ->andWhere(['tag' => $key])
            ->limit(1)
            ->one($db);
    }

    private function getDefaultLobbyKey(?Season3 $season, ?SplatoonVersion3 $version): string
    {
        if ($version) {
            return version_compare($version->tag, '2.0.0', '>=') ? 'xmatch' : 'bankara_challenge';
        }

        if ($season) {
            return $season->key === 'season202209' ? 'bankara_challenge' : 'xmatch';
        }

        return 'xmatch';
    }

    /**
     * @return StatWeapon3Usage[]|StatWeapon3UsagePerVersion[]
     */
    private function getData(
        Connection $db,
        ?Season3 $season,
        ?SplatoonVersion3 $version,
        Lobby3 $lobby,
        Rule3 $rule,
    ): array {
        if ($version) {
            return StatWeapon3UsagePerVersion::find()
                ->with([
                    'weapon',
                    'weapon.special',
                    'weapon.subweapon',
                ])
                ->andWhere([
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'version_id' => $version->id,
                ])
                ->all($db);
        }

        if ($season) {
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
                ->all($db);
        }

        return null;
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

    private function getVersions(Connection $db): array
    {
        $ts = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
            ->setTime(0, 0, 0)
            ->sub(new DateInterval('P1D'));

        return SplatoonVersion3::find()
            ->andWhere(['<=', 'release_at', $ts->format(DateTimeInterface::ATOM)])
            ->orderBy(['release_at' => SORT_DESC])
            ->all();
    }
}
