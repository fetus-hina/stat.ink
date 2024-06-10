<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240610_022108_salmon_weapon3_sort extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // fix data
        // 何故動いていたんだろう…
        $this->batchInsert('{{%salmon_weapon3_alias}}', ['weapon_id', 'key'], [
            [$this->key2id('{{%salmon_weapon3}}', 'spaceshooter'), '100'],
            [$this->key2id('{{%salmon_weapon3}}', 'wideroller'), '1040'],
            [$this->key2id('{{%salmon_weapon3}}', 'rpen_5h'), '2070'],
        ]);

        $this->addColumn(
            '{{%salmon_weapon3}}',
            'rank',
            (string)$this->integer()->null(),
        );

        $sql = 'UPDATE {{%salmon_weapon3}} ' .
            'SET [[rank]] = CAST({{%salmon_weapon3_alias}}.[[key]] AS INTEGER) ' .
            'FROM {{%salmon_weapon3_alias}} ' .
            'WHERE {{%salmon_weapon3}}.[[id]] = {{%salmon_weapon3_alias}}.[[weapon_id]] ' .
            'AND {{%salmon_weapon3_alias}}.[[key]] ~ \'^[0-9]+$\'';
        $this->execute($sql);

        $this->alterColumn(
            '{{%salmon_weapon3}}',
            'rank',
            (string)$this->integer()->notNull(),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%salmon_weapon3}}', 'rank');

        $this->delete('{{%salmon_weapon3_alias}}', [
            'key' => ['100', '1040', '2070'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_weapon3}}',
        ];
    }
}
