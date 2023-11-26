<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m231126_163934_jaw extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%salmon_king3}}', [
            'key' => 'jaw',
            'name' => 'Megalodontia',
        ]);

        $this->batchInsert(
            '{{%salmon_king3_alias}}',
            ['salmonid_id', 'key'],
            [
                [
                    $this->key2id('{{%salmon_king3}}', 'jaw'),
                    'megalodontia',
                ],
                [
                    $this->key2id('{{%salmon_king3}}', 'jaw'),
                    '25',
                ],
            ],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%salmon_king3}}', 'jaw');
        $this->delete('{{%salmon_king3_alias}}', ['salmonid_id' => $id]);
        $this->delete('{{%salmon_king3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_king3_alias}}',
            '{{%salmon_king3}}',
        ];
    }
}
