<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230324_113106_boss3_badge extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%salmon_boss3}}', 'has_badge', (string)$this->boolean());
        $this->update('{{%salmon_boss3}}', ['has_badge' => false]);
        $this->update('{{%salmon_boss3}}', ['has_badge' => true], [
            'key' => [
                'bakudan',
                'diver',
                'hashira',
                'hebi',
                'katapad',
                'koumori',
                'mogura',
                'nabebuta',
                'tekkyu',
                'teppan',
                'tower',
            ],
        ]);
        $this->alterColumn('{{%salmon_boss3}}', 'has_badge', (string)$this->boolean()->notNull());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%salmon_boss3}}', 'has_badge');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_boss3}}',
        ];
    }
}
