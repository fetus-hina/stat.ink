<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m261118_062116_junior_slopsuit extends Migration
{
    use AutoKey;

    private const string NAME = 'Junior Slopsuit';
    private const string SPLATNET_ID = '22';
    private const int RANK = 540;

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->insert('{{%salmon_uniform3}}', [
            'key' => self::name2key3(self::NAME),
            'name' => self::NAME,
            'rank' => self::RANK,
        ]);

        $this->insert('{{%salmon_uniform3_alias}}', [
            'uniform_id' => $this->key2id('{{%salmon_uniform3}}', self::name2key3(self::NAME)),
            'key' => self::SPLATNET_ID,
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $id = $this->key2id('{{%salmon_uniform3}}', self::name2key3(self::NAME));
        $this->delete('{{%salmon_uniform3_alias}}', ['uniform_id' => $id]);
        $this->delete('{{%salmon_uniform3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_uniform3}}',
            '{{%salmon_uniform3_alias}}',
        ];
    }
}
