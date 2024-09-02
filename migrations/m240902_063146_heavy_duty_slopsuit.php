<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m240902_063146_heavy_duty_slopsuit extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%salmon_uniform3}}', [
            'key' => self::name2key3('Heavy-Duty Slopsuit'),
            'name' => 'Heavy-Duty Slopsuit',
            'rank' => 530,
        ]);

        $this->insert('{{%salmon_uniform3_alias}}', [
            'uniform_id' => $this->key2id(
                '{{%salmon_uniform3}}',
                self::name2key3('Heavy-Duty Slopsuit'),
            ),
            'key' => '21',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%salmon_uniform3}}', self::name2key3('Heavy-Duty Slopsuit'));
        $this->delete('{{%salmon_uniform3_alias}}', ['uniform_id' => $id]);
        $this->delete('{{%salmon_uniform3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_uniform3}}',
            '{{%salmon_uniform3_alias}}',
        ];
    }
}
