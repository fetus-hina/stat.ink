<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221103_084007_gear_configuration3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%gear_configuration3}}', [
            'id' => $this->primaryKey(),
            'fingerprint' => 'UUID NOT NULL UNIQUE',
            'ability_id' => $this->pkRef('{{%ability3}}')->null(),
        ]);

        $this->createTable('{{%gear_configuration_secondary3}}', [
            'id' => $this->primaryKey(),
            'config_id' => $this->pkRef('{{%gear_configuration3}}')->notNull(),
            'ability_id' => $this->pkRef('{{%ability3}}')->null(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%gear_configuration_secondary3}}',
            '{{%gear_configuration3}}',
        ]);

        return true;
    }
}
