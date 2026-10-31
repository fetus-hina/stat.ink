<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m261030_051301_unique_nulls_not_distinct extends Migration
{
    private const TARGETS = [
        // map_id と big_map_id はどちらか一方が必ず NULL (CHECK 制約) なので、
        // NULLS DISTINCT のままでは一意性が全く担保されていなかった
        'stat_salmon3_map_king' => [
            'stat_salmon3_map_king_map_id_big_map_id_king_id_key',
            ['map_id', 'big_map_id', 'king_id'],
        ],
        'stat_salmon3_map_king_tide' => [
            'stat_salmon3_map_king_tide_map_id_big_map_id_king_id_tide_i_key',
            ['map_id', 'big_map_id', 'king_id', 'tide_id'],
        ],
        // event_id IS NULL 用の部分一意インデックスを統合する
        'salmon3_user_stats_event' => [
            'salmon3_user_stats_event_user_id_map_id_tide_id_event_id_key',
            ['user_id', 'map_id', 'tide_id', 'event_id'],
        ],
    ];

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        foreach (self::TARGETS as $table => [$name, $columns]) {
            $this->replaceUnique($table, $name, $columns, nullsNotDistinct: true);
        }

        $this->dropIndex('ix_salmon3_user_stats_event_1', '{{%salmon3_user_stats_event}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->execute(
            vsprintf('CREATE UNIQUE INDEX %s ON %s (%s) WHERE %s IS NULL', [
                '[[ix_salmon3_user_stats_event_1]]',
                '{{%salmon3_user_stats_event}}',
                '[[user_id]], [[map_id]], [[tide_id]]',
                '[[event_id]]',
            ]),
        );

        foreach (self::TARGETS as $table => [$name, $columns]) {
            $this->replaceUnique($table, $name, $columns, nullsNotDistinct: false);
        }

        return true;
    }

    /**
     * @param string[] $columns
     */
    private function replaceUnique(
        string $table,
        string $name,
        array $columns,
        bool $nullsNotDistinct,
    ): void {
        $this->execute(
            vsprintf('ALTER TABLE %s DROP CONSTRAINT %s, ADD CONSTRAINT %2$s UNIQUE %s(%s)', [
                "{{%{$table}}}",
                "[[{$name}]]",
                $nullsNotDistinct ? 'NULLS NOT DISTINCT ' : '',
                implode(', ', array_map(fn (string $c): string => "[[{$c}]]", $columns)),
            ]),
        );
    }
}
