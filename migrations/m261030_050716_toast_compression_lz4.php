<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m261030_050716_toast_compression_lz4 extends Migration
{
    /**
     * Only newly stored values are compressed with LZ4.
     * Existing values are kept as they are (pglz) and remain readable.
     */
    private const TARGETS = [
        '{{%battle2_splatnet}}' => 'json',
        '{{%battle_events}}' => 'events',
        '{{%battle_events2}}' => 'events',
    ];

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        foreach (self::TARGETS as $table => $column) {
            $this->alterColumn($table, $column, 'SET COMPRESSION lz4');
        }
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        foreach (self::TARGETS as $table => $column) {
            $this->alterColumn($table, $column, 'SET COMPRESSION default');
        }
    }
}
