<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230220_191321_horrorboros extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%salmon_king3}}', [
            'key' => 'tatsu',
            'name' => 'Horrorboros',
        ]);

        $this->insert('{{%salmon_king3_alias}}', [
            'key' => 'horrorboros',
            'salmonid_id' => $this->key2id('{{%salmon_king3}}', 'tatsu'),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%salmon_king3}}', 'tatsu');
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
            '{{%salmon_king3}}',
            '{{%salmon_king3_alias}}',
        ];
    }
}
