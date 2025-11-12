<?php

/**
 * @copyright Copyright (C) 2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\Query;

use function count;
use function explode;
use function implode;
use function preg_match;
use function trim;
use function vsprintf;

use const SORT_DESC;

/**
 * Virtual model representing an unregistered player identified by ref_id
 * Aggregates data from all battles where this player appeared
 */
final class UnregisteredPlayer3
{
    public ?string $ref_id = null;
    public ?string $name = null;
    public ?string $number = null;
    public int $total_battles = 0;
    public int $total_wins = 0;
    public int $total_disconnects = 0;
    public array $weapon_stats = [];
    public array $performance_stats = [];
    public array $lobby_stats = [];

    /**
     * Check if a player with given splashtag is actually registered and get their username
     * Returns the username if registered, null if unregistered
     */
    public static function getRegisteredUsername(string $name, string $number): ?string
    {
        $userQuery = (new Query())
            ->select(['screen_name' => '{{%user}}.[[screen_name]]'])
            ->from('{{%battle_player3}}')
            ->innerJoin('{{%battle3}}', '{{%battle_player3}}.[[battle_id]] = {{%battle3}}.[[id]]')
            ->innerJoin('{{%user}}', '{{%battle3}}.[[user_id]] = {{%user}}.[[id]]')
            ->where([
                '{{%battle_player3}}.[[name]]' => $name,
                '{{%battle_player3}}.[[number]]' => $number,
                '{{%battle_player3}}.[[is_me]]' => true,
                '{{%battle3}}.[[is_deleted]]' => false,
            ])
            ->limit(1)
            ->one();

        return $userQuery['screen_name'] ?? null;
    }

    /**
     * Find an unregistered player by name and number (splashtag)
     */
    public static function findBySplashtag(string $name, string $number): ?self
    {
        $playerInfo = (new Query())
            ->select(['name', 'number'])
            ->from('{{%battle_player3}}')
            ->innerJoin('{{%battle3}}', '{{%battle_player3}}.[[battle_id]] = {{%battle3}}.[[id]]')
            ->where([
                '{{%battle_player3}}.[[name]]' => $name,
                '{{%battle_player3}}.[[number]]' => $number,
                '{{%battle3}}.[[is_deleted]]' => false,
            ])
            ->limit(1)
            ->one();

        if (!$playerInfo) {
            return null;
        }

        $refIdResult = (new Query())
            ->select(['ref_id' => 'calc_played_with3_id(:name, :number)'])
            ->addParams([':name' => $name, ':number' => $number])
            ->one();

        $player = new self();
        $player->ref_id = $refIdResult['ref_id'];
        $player->name = $playerInfo['name'];
        $player->number = $playerInfo['number'];

        $player->loadAggregatedStats();

        return $player;
    }

    /**
     * Parse splashtag string and find player
     * Accepts formats: "username#1234" or "username #1234"
     */
    public static function findBySplashtagString(string $splashtag): ?self
    {
        $splashtag = trim($splashtag);
        $parts = explode('#', $splashtag, 2);
        
        if (count($parts) !== 2) {
            return null;
        }

        $name = trim($parts[0]);
        $number = trim($parts[1]);

        if (empty($name) || empty($number)) {
            return null;
        }

        if (!preg_match('/^\d+$/', $number)) {
            return null;
        }

        return self::findBySplashtag($name, $number);
    }

    /**
     * Load aggregated statistics for this unregistered player
     */
    private function loadAggregatedStats(): void
    {
        $battleStatsSubquery = (new Query())
            ->select([
                'client_uuid' => '{{%battle3}}.[[client_uuid]]',
                'is_win' => 'MAX(CASE WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1 ELSE 0 END)',
                'is_disconnect' => 'MAX(CASE WHEN {{%battle_player3}}.[[is_disconnected]] THEN 1 ELSE 0 END)',
            ])
            ->from('{{%battle_player3}}')
            ->innerJoin('{{%battle3}}', '{{%battle_player3}}.[[battle_id]] = {{%battle3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->andWhere([
                '{{%battle_player3}}.[[name]]' => $this->name,
                '{{%battle_player3}}.[[number]]' => $this->number,
                '{{%battle3}}.[[is_deleted]]' => false,
            ])
            ->andWhere(['not', ['{{%lobby3}}.[[key]]' => 'private']])
            ->groupBy('{{%battle3}}.[[client_uuid]]');

        $battleStats = (new Query())
            ->select([
                'battles' => 'COUNT(*)',
                'wins' => 'SUM(is_win)',
                'disconnects' => 'SUM(is_disconnect)',
            ])
            ->from(['dedupe' => $battleStatsSubquery])
            ->one();

        $this->total_battles = (int)($battleStats['battles'] ?? 0);
        $this->total_wins = (int)($battleStats['wins'] ?? 0);
        $this->total_disconnects = (int)($battleStats['disconnects'] ?? 0);

        $this->loadWeaponStats();
        $this->loadPerformanceStats();
        $this->loadLobbyStats();
    }

    /**
     * Load weapon usage statistics
     */
    private function loadWeaponStats(): void
    {
        $battlesSubquery = (new Query())
            ->select([
                'client_uuid' => '{{%battle3}}.[[client_uuid]]',
                'weapon_id' => 'MAX({{%battle_player3}}.[[weapon_id]])',
                'weapon_name' => 'MAX({{%weapon3}}.[[name]])',
                'weapon_key' => 'MAX({{%weapon3}}.[[key]])',
                'is_win' => 'MAX(CASE WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1 ELSE 0 END)',
                'kill_count' => 'MAX({{%battle_player3}}.[[kill]])',
                'death_count' => 'MAX({{%battle_player3}}.[[death]])',
                'assist_count' => 'MAX({{%battle_player3}}.[[assist]])',
                'special_count' => 'MAX({{%battle_player3}}.[[special]])',
                'inked_count' => 'MAX({{%battle_player3}}.[[inked]])',
            ])
            ->from('{{%battle_player3}}')
            ->innerJoin('{{%battle3}}', '{{%battle_player3}}.[[battle_id]] = {{%battle3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%weapon3}}', '{{%battle_player3}}.[[weapon_id]] = {{%weapon3}}.[[id]]')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->andWhere([
                '{{%battle_player3}}.[[name]]' => $this->name,
                '{{%battle_player3}}.[[number]]' => $this->number,
                '{{%battle3}}.[[is_deleted]]' => false,
            ])
            ->andWhere(['not', ['{{%lobby3}}.[[key]]' => 'private']])
            ->andWhere(['not', ['{{%battle_player3}}.[[weapon_id]]' => null]])
            ->groupBy('{{%battle3}}.[[client_uuid]]');

        $weaponQuery = (new Query())
            ->select([
                'weapon_id',
                'weapon_name',
                'weapon_key',
                'battles' => 'COUNT(*)',
                'wins' => 'SUM(is_win)',
                'avg_kill' => 'AVG(kill_count)',
                'avg_death' => 'AVG(death_count)',
                'avg_assist' => 'AVG(assist_count)',
                'avg_special' => 'AVG(special_count)',
                'avg_inked' => 'AVG(inked_count)',
            ])
            ->from(['dedupe' => $battlesSubquery])
            ->groupBy(['weapon_id', 'weapon_name', 'weapon_key'])
            ->orderBy(['battles' => SORT_DESC])
            ->all();

        $this->weapon_stats = $weaponQuery;
    }

    /**
     * Load overall performance statistics
     */
    private function loadPerformanceStats(): void
    {
        $battlesSubquery = (new Query())
            ->select([
                'client_uuid' => '{{%battle3}}.[[client_uuid]]',
                'kill_count' => 'MAX({{%battle_player3}}.[[kill]])',
                'death_count' => 'MAX({{%battle_player3}}.[[death]])',
                'assist_count' => 'MAX({{%battle_player3}}.[[assist]])',
                'special_count' => 'MAX({{%battle_player3}}.[[special]])',
                'inked_count' => 'MAX({{%battle_player3}}.[[inked]])',
            ])
            ->from('{{%battle_player3}}')
            ->innerJoin('{{%battle3}}', '{{%battle_player3}}.[[battle_id]] = {{%battle3}}.[[id]]')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->andWhere([
                '{{%battle_player3}}.[[name]]' => $this->name,
                '{{%battle_player3}}.[[number]]' => $this->number,
                '{{%battle3}}.[[is_deleted]]' => false,
            ])
            ->andWhere(['not', ['{{%lobby3}}.[[key]]' => 'private']])
            ->andWhere(['not', ['{{%battle_player3}}.[[kill]]' => null]])
            ->groupBy('{{%battle3}}.[[client_uuid]]');

        $performanceQuery = (new Query())
            ->select([
                'avg_kill' => 'AVG(kill_count)',
                'avg_death' => 'AVG(death_count)',
                'avg_assist' => 'AVG(assist_count)',
                'avg_special' => 'AVG(special_count)',
                'avg_inked' => 'AVG(inked_count)',
                'max_kill' => 'MAX(kill_count)',
                'max_assist' => 'MAX(assist_count)',
                'max_special' => 'MAX(special_count)',
                'max_inked' => 'MAX(inked_count)',
            ])
            ->from(['dedupe' => $battlesSubquery])
            ->one();

        $this->performance_stats = $performanceQuery ?: [];
    }

    /**
     * Load lobby/mode statistics
     */
    private function loadLobbyStats(): void
    {
        $battlesSubquery = (new Query())
            ->select([
                'client_uuid' => '{{%battle3}}.[[client_uuid]]',
                'lobby_id' => 'MAX({{%lobby3}}.[[id]])',
                'lobby_key' => 'MAX({{%lobby3}}.[[key]])',
                'lobby_name' => 'MAX({{%lobby3}}.[[name]])',
                'is_win' => 'MAX(CASE WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1 ELSE 0 END)',
            ])
            ->from('{{%battle_player3}}')
            ->innerJoin('{{%battle3}}', '{{%battle_player3}}.[[battle_id]] = {{%battle3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->andWhere([
                '{{%battle_player3}}.[[name]]' => $this->name,
                '{{%battle_player3}}.[[number]]' => $this->number,
                '{{%battle3}}.[[is_deleted]]' => false,
            ])
            ->andWhere(['not', ['{{%lobby3}}.[[key]]' => 'private']])
            ->groupBy('{{%battle3}}.[[client_uuid]]');

        $lobbyQuery = (new Query())
            ->select([
                'lobby_key',
                'lobby_name',
                'battles' => 'COUNT(*)',
                'wins' => 'SUM(is_win)',
            ])
            ->from(['dedupe' => $battlesSubquery])
            ->groupBy(['lobby_id', 'lobby_key', 'lobby_name'])
            ->orderBy(['battles' => SORT_DESC])
            ->all();

        $this->lobby_stats = $lobbyQuery;
    }

    /**
     * Get win rate as percentage
     */
    public function getWinRate(): float
    {
        return $this->total_battles > 0 
            ? ($this->total_wins / $this->total_battles) * 100 
            : 0.0;
    }

    /**
     * Get disconnect rate as percentage
     */
    public function getDisconnectRate(): float
    {
        return $this->total_battles > 0 
            ? ($this->total_disconnects / $this->total_battles) * 100 
            : 0.0;
    }

    /**
     * Get kill ratio (kills/deaths)
     */
    public function getKillRatio(): ?float
    {
        $avgKill = (float)($this->performance_stats['avg_kill'] ?? 0);
        $avgDeath = (float)($this->performance_stats['avg_death'] ?? 0);

        if ($avgDeath == 0) {
            return $avgKill > 0 ? null : 99.99;
        }

        return $avgKill / $avgDeath;
    }

    /**
     * Get total kills across all battles
     */
    public function getTotalKills(): int
    {
        $avgKill = (float)($this->performance_stats['avg_kill'] ?? 0);
        return (int)($avgKill * $this->total_battles);
    }

    /**
     * Get total deaths across all battles
     */
    public function getTotalDeaths(): int
    {
        $avgDeath = (float)($this->performance_stats['avg_death'] ?? 0);
        return (int)($avgDeath * $this->total_battles);
    }

    /**
     * Get most used weapon
     */
    public function getMostUsedWeapon(): ?array
    {
        return $this->weapon_stats[0] ?? null;
    }

    /**
     * Check if player has enough data to show meaningful stats
     */
    public function hasSignificantData(): bool
    {
        return $this->total_battles >= 5;
    }

    /**
     * Get splashtag representation
     */
    public function getSplashtag(): string
    {
        return vsprintf('%s #%s', [
            $this->name ?? '???',
            $this->number ?? '????',
        ]);
    }
}