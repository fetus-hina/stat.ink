<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230124_171524_stat_weapon3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        foreach (['season', 'version'] as $period) {
            foreach ($this->getTargetAttributes() as $attr) {
                $this->createTable(
                    $this->getStatsTableName($period, $attr),
                    array_filter(
                        [
                            'season_id' => $period === 'season'
                                ? $this->pkRef('{{%season3}}')->notNull()
                                : null,
                            'version_id' => $period === 'version'
                                ? $this->pkRef('{{%splatoon_version3}}')->notNull()
                                : null,
                            'lobby_id' => $this->pkRef('{{%lobby3}}')->notNull(),
                            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
                            'weapon_id' => $this->pkRef('{{%weapon3}}')->notNull(),
                            $attr => $this->integer()->notNull(),
                            'battles' => $this->bigInteger()->notNull(),
                            'wins' => $this->bigInteger()->notNull(),

                            "PRIMARY KEY ([[{$period}_id]], [[lobby_id]], [[rule_id]], [[weapon_id]], [[{$attr}]])",
                        ],
                        fn ($v): bool => $v !== null,
                    ),
                );
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $tables = [];
        foreach (['season', 'version'] as $period) {
            foreach ($this->getTargetAttributes() as $attr) {
                $tables[] = $this->getStatsTableName($period, $attr);
            }
        }

        return true;
    }

    /**
     * @param 'season'|'version' $period
     */
    private function getStatsTableName(string $period, string $attr): string
    {
        return $period === 'season'
            ? "{{%stat_weapon3_{$attr}}}"
            : "{{%stat_weapon3_{$attr}_per_{$period}}}";
    }

    /**
     * @return string[]
     */
    private function getTargetAttributes(): array
    {
        return [
            'assist',
            'death',
            'inked',
            'kill',
            'kill_or_assist',
            'special',
        ];
    }
}
