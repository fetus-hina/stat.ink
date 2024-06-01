<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m240601_055955_triumvirate extends Migration
{
    use AutoKey;

    private const KEY = 'rengo';
    private const NAME = 'Triumvirate';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%salmon_king3}}', [
            'key' => self::KEY,
            'name' => self::NAME,
        ]);

        $id = $this->key2id('{{%salmon_king3}}', self::KEY);
        $this->batchInsert('{{%salmon_king3_alias}}', ['salmonid_id', 'key'], [
            [$id, self::name2key3(self::NAME)],
            [$id, '26'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%salmon_king3}}', self::KEY);
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
