<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use LogicException;
use Yii;
use app\components\helpers\Season3Helper;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Season3;
use app\models\SplatoonVersion3;
use app\models\StatWeapon3Assist;
use app\models\StatWeapon3AssistPerVersion;
use app\models\StatWeapon3Death;
use app\models\StatWeapon3DeathPerVersion;
use app\models\StatWeapon3Inked;
use app\models\StatWeapon3InkedPerVersion;
use app\models\StatWeapon3Kill;
use app\models\StatWeapon3KillOrAssist;
use app\models\StatWeapon3KillOrAssistPerVersion;
use app\models\StatWeapon3KillPerVersion;
use app\models\StatWeapon3Special;
use app\models\StatWeapon3SpecialPerVersion;
use app\models\Weapon3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function assert;
use function version_compare;

use const SORT_ASC;
use const SORT_DESC;

/**
 * @phpstan-type SAssists StatWeapon3Assist[]|StatWeapon3AssistPerVersion[]
 * @phpstan-type SDeaths StatWeapon3Death[]|StatWeapon3DeathPerVersion[]
 * @phpstan-type SInkeds StatWeapon3Inked[]|StatWeapon3InkedPerVersion[]
 * @phpstan-type SKillOrAssists StatWeapon3KillOrAssist[]|StatWeapon3KillOrAssistPerVersion[]
 * @phpstan-type SKills StatWeapon3Kill[]|StatWeapon3KillPerVersion[]
 * @phpstan-type SSpecials StatWeapon3Special[]|StatWeapon3SpecialPerVersion[]
 * @phsptan-type DataType array{assist: SAssists, death: SDeaths, inked: SInkeds, ka: SKillOrAssists, kill: SKills, special: SSpecials}
 */
final class Weapon3Action extends Action
{
    public function run(
        ?string $lobby = null,
        ?string $rule = null,
        ?string $weapon = null,
    ): Response|string {
        $controller = $this->controller;
        assert($controller instanceof Controller);

        $params = Yii::$app->db->transaction(
            fn (Connection $db): Response|array => $this->doRun(
                $controller,
                $db,
                $lobby,
                $rule,
                $weapon,
            ),
            Transaction::REPEATABLE_READ,
        );

        return $params instanceof Response
            ? $params
            : $controller->render('v3/weapon3', $params);
    }

    private function doRun(
        Controller $controller,
        Connection $db,
        ?string $lobbyKey,
        ?string $ruleKey,
        ?string $weaponKey,
    ): Response|array {
        $version = $this->getVersion($db, (string)Yii::$app->request->get('version'));
        $season = $version ? null : Season3Helper::getUrlTargetSeason('season');
        $lobby = $this->getLobby($db, $lobbyKey);
        $rule = $this->getRule($db, $ruleKey);
        $weapon = $this->getWeapon($db, $weaponKey);
        if (
            !($season || $version) ||
            !$lobby ||
            !$rule ||
            !$weapon
        ) {
            $season = $season ?? Season3Helper::getCurrentSeason();
            if (!$version && !$season) {
                throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
            }

            return $controller->redirect(['entire/weapon3',
                'lobby' => $this->getDefaultLobbyKey($season, $version),
                'rule' => $rule?->key ?? 'area',
                'season' => $season?->id,
                'version' => $version?->tag,
                'weapon' => $weapon?->key,
            ]);
        }

        if ($rule->key === 'nawabari' && $lobby->key !== 'regular') {
            return $controller->redirect(['entire/weapon3',
                'lobby' => 'regular',
                'rule' => 'nawabari',
                'season' => $season?->id,
                'version' => $version?->tag,
                'weapon' => $weapon?->key,
            ]);
        }

        if ($rule->key !== 'nawabari' && $lobby->key === 'regular') {
            return $controller->redirect(['entire/weapon3',
                'lobby' => $this->getDefaultLobbyKey($season, $version),
                'rule' => $rule->key,
                'season' => $season?->id,
                'version' => $version?->tag,
                'weapon' => $weapon?->key,
            ]);
        }

        return [
            'data' => $this->getData($db, $season, $version, $lobby, $rule, $weapon),
            'lobbies' => $this->getLobbies($db),
            'lobby' => $lobby,
            'rule' => $rule,
            'rules' => $this->getRules($db),
            'season' => $season,
            'seasons' => Season3Helper::getSeasons(),
            'version' => $version,
            'versions' => $this->getVersions($db),
            'weapon' => $weapon,
            'weapons' => $this->getWeapons($db),

            'seasonUrl' => fn (Season3 $season): string => Url::to(
                ['entire/weapon3',
                    'lobby' => $lobby->key,
                    'rule' => $rule->key,
                    'season' => $season->id,
                    'weapon' => $weapon->key,
                ],
            ),

            'ruleUrl' => fn (Rule3 $rule): string => Url::to(
                ['entire/weapon3',
                    'lobby' => $rule->key === 'nawabari'
                        ? 'regular'
                        : (
                            $lobby->key === 'regular'
                                ? $this->getDefaultLobbyKey($season, $version)
                                : $lobby->key
                        ),
                    'rule' => $rule->key,
                    'season' => $season?->id,
                    'version' => $version?->tag,
                    'weapon' => $weapon->key,
                ],
            ),

            'lobbyUrl' => fn (Lobby3 $lobby): string => Url::to(
                ['entire/weapon3',
                    'lobby' => $lobby->key,
                    'rule' => match (true) {
                        $lobby->key === 'regular' => 'nawabari',
                        $lobby->key !== 'regular' && $rule->key === 'nawabari' => 'area',
                        default => $rule->key,
                    },
                    'season' => $season?->id,
                    'version' => $version?->tag,
                    'weapon' => $weapon->key,
                ],
            ),

            'versionUrl' => fn (SplatoonVersion3 $version): string => Url::to(
                ['entire/weapon3',
                    'lobby' => $lobby->key,
                    'rule' => $rule->key,
                    'version' => $version->tag,
                    'weapon' => $weapon->key,
                ],
            ),

            'weaponUrl' => fn (Weapon3 $weapon): string => Url::to(
                ['entire/weapon3',
                    'lobby' => $lobby->key,
                    'rule' => $rule->key,
                    'season' => $season?->id,
                    'version' => $version?->tag,
                    'weapon' => $weapon->key,
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

    private function getWeapon(Connection $db, ?string $key): ?Weapon3
    {
        if (!$key) {
            return null;
        }

        return Weapon3::find()
            ->andWhere(['key' => $key])
            ->limit(1)
            ->one($db);
    }

    /**
     * @return Weapon3[]
     */
    private function getWeapons(Connection $db): array
    {
        return Weapon3::find()
            ->innerJoinWith(['mainweapon.type'], false)
            ->orderBy([
                '{{%weapon_type3}}.[[rank]]' => SORT_ASC,
                '{{%weapon3}}.[[name]]' => SORT_ASC,
            ])
            ->all();
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
     * @return DataType
     */
    private function getData(
        Connection $db,
        ?Season3 $season,
        ?SplatoonVersion3 $version,
        Lobby3 $lobby,
        Rule3 $rule,
        Weapon3 $weapon,
    ): array {
        return [
            'assist' => $this->getAssistData($db, $season, $version, $lobby, $rule, $weapon),
            'death' => $this->getDeathData($db, $season, $version, $lobby, $rule, $weapon),
            'inked' => $this->getInkedData($db, $season, $version, $lobby, $rule, $weapon),
            'ka' => $this->getKillOrAssistData($db, $season, $version, $lobby, $rule, $weapon),
            'kill' => $this->getKillData($db, $season, $version, $lobby, $rule, $weapon),
            'special' => $this->getSpecialData($db, $season, $version, $lobby, $rule, $weapon),
        ];
    }

    /**
     * @return SAssists
     */
    private function getAssistData(
        Connection $db,
        ?Season3 $season,
        ?SplatoonVersion3 $version,
        Lobby3 $lobby,
        Rule3 $rule,
        Weapon3 $weapon,
    ): array {
        if ($season) {
            return StatWeapon3Assist::find()
                ->andWhere([
                    'season_id' => $season->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['assist' => SORT_ASC])
                ->all();
        }

        if ($version) {
            return StatWeapon3AssistPerVersion::find()
                ->andWhere([
                    'version_id' => $version->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['assist' => SORT_ASC])
                ->all();
        }

        throw new LogicException();
    }

    /**
     * @return SDeaths
     */
    private function getDeathData(
        Connection $db,
        ?Season3 $season,
        ?SplatoonVersion3 $version,
        Lobby3 $lobby,
        Rule3 $rule,
        Weapon3 $weapon,
    ): array {
        if ($season) {
            return StatWeapon3Death::find()
                ->andWhere([
                    'season_id' => $season->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['death' => SORT_ASC])
                ->all();
        }

        if ($version) {
            return StatWeapon3DeathPerVersion::find()
                ->andWhere([
                    'version_id' => $version->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['death' => SORT_ASC])
                ->all();
        }

        throw new LogicException();
    }

    /**
     * @return SInkeds
     */
    private function getInkedData(
        Connection $db,
        ?Season3 $season,
        ?SplatoonVersion3 $version,
        Lobby3 $lobby,
        Rule3 $rule,
        Weapon3 $weapon,
    ): array {
        if ($season) {
            return StatWeapon3Inked::find()
                ->andWhere([
                    'season_id' => $season->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['inked' => SORT_ASC])
                ->all();
        }

        if ($version) {
            return StatWeapon3InkedPerVersion::find()
                ->andWhere([
                    'version_id' => $version->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['inked' => SORT_ASC])
                ->all();
        }

        throw new LogicException();
    }

    /**
     * @return SKillOrAssists
     */
    private function getKillOrAssistData(
        Connection $db,
        ?Season3 $season,
        ?SplatoonVersion3 $version,
        Lobby3 $lobby,
        Rule3 $rule,
        Weapon3 $weapon,
    ): array {
        if ($season) {
            return StatWeapon3KillOrAssist::find()
                ->andWhere([
                    'season_id' => $season->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['kill_or_assist' => SORT_ASC])
                ->all();
        }

        if ($version) {
            return StatWeapon3KillOrAssistPerVersion::find()
                ->andWhere([
                    'version_id' => $version->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['kill_or_assist' => SORT_ASC])
                ->all();
        }

        throw new LogicException();
    }

    /**
     * @return SKills
     */
    private function getKillData(
        Connection $db,
        ?Season3 $season,
        ?SplatoonVersion3 $version,
        Lobby3 $lobby,
        Rule3 $rule,
        Weapon3 $weapon,
    ): array {
        if ($season) {
            return StatWeapon3Kill::find()
                ->andWhere([
                    'season_id' => $season->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['kill' => SORT_ASC])
                ->all();
        }

        if ($version) {
            return StatWeapon3KillPerVersion::find()
                ->andWhere([
                    'version_id' => $version->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['kill' => SORT_ASC])
                ->all();
        }

        throw new LogicException();
    }

    /**
     * @return SSpecials
     */
    private function getSpecialData(
        Connection $db,
        ?Season3 $season,
        ?SplatoonVersion3 $version,
        Lobby3 $lobby,
        Rule3 $rule,
        Weapon3 $weapon,
    ): array {
        if ($season) {
            return StatWeapon3Special::find()
                ->andWhere([
                    'season_id' => $season->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['special' => SORT_ASC])
                ->all();
        }

        if ($version) {
            return StatWeapon3SpecialPerVersion::find()
                ->andWhere([
                    'version_id' => $version->id,
                    'lobby_id' => $lobby->id,
                    'rule_id' => $rule->id,
                    'weapon_id' => $weapon->id,
                ])
                ->orderBy(['special' => SORT_ASC])
                ->all();
        }

        throw new LogicException();
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
