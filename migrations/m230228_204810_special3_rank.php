<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230228_204810_special3_rank extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('{{%special3}}', ['rank' => 1250], ['key' => 'decoy']);
        $this->update('{{%special3}}', ['rank' => 260], ['key' => 'teioika']);
        $this->update('{{%special3}}', ['rank' => 250], ['key' => 'decoy']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('{{%special3}}', ['rank' => 1250], ['key' => 'teioika']);
        $this->update('{{%special3}}', ['rank' => 260], ['key' => 'decoy']);
        $this->update('{{%special3}}', ['rank' => 250], ['key' => 'teioika']);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%special3}}',
        ];
    }
}
