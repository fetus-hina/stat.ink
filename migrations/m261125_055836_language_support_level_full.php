<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m261125_055836_language_support_level_full extends Migration
{
    private const FULL = 1;
    private const ALMOST = 2;
    private const PARTIAL = 3;
    private const FEW = 4;

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->update('{{%language}}', ['support_level_id' => self::FULL]);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->update('{{%language}}', ['support_level_id' => self::FEW]);
        $this->update(
            '{{%language}}',
            ['support_level_id' => self::FULL],
            ['lang' => ['ja-JP', 'ja-JP@calendar=japanese']],
        );
        $this->update(
            '{{%language}}',
            ['support_level_id' => self::ALMOST],
            ['lang' => ['en-US', 'en-GB', 'zh-CN', 'zh-TW']],
        );
        $this->update(
            '{{%language}}',
            ['support_level_id' => self::PARTIAL],
            ['lang' => ['es-ES', 'es-MX', 'pt-BR']],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%language}}',
        ];
    }
}
