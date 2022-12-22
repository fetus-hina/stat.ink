<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221117_011848_remove_salmon3_weapon extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('{{%salmon3}}', 'weapon_id');

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->addColumn(
            '{{%salmon3}}',
            'weapon_id',
            (string)$this->pkRef('{{%weapon3}}')->null(),
        );

        return true;
    }

    public function vacuumTables(): array
    {
        return ['{{%salmon3}}'];
    }
}
