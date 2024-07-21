<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240721_161019_conch_clash extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%conch_clash3}}', [
            'id' => $this->primaryKey()->notNull(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'name' => $this->string(32)->notNull(),
        ]);

        $this->batchInsert('{{%conch_clash3}}', ['key', 'name'], [
            ['1x', 'Conch Clash'],
            ['10x', '10x Conch Clash'],
            ['33x', '33x Conch Clash'],
        ]);

        $this->addColumn(
            '{{%battle3}}',
            'conch_clash_id',
            (string)$this->pkRef('{{%conch_clash3}}')->null(),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%battle3}}', 'conch_clash_id');
        $this->dropTable('{{%conch_clash3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%conch_clash3}}',
        ];
    }
}
