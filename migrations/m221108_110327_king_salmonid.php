<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m221108_110327_king_salmonid extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_king3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'name' => $this->string(64)->notNull(),
        ]);

        $this->createTable('{{%salmon_king3_alias}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'salmonid_id' => $this->pkRef('{{%salmon_king3}}')->notNull(),
        ]);

        $this->insert('{{%salmon_king3}}', [
            'key' => 'yokozuna',
            'name' => 'Cohozuna',
        ]);

        $this->insert('{{%salmon_king3_alias}}', [
            'key' => self::name2key3('Cohozuna'),
            'salmonid_id' => $this->key2id('{{%salmon_king3}}', 'yokozuna'),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%salmon_king3_alias}}',
            '{{%salmon_king3}}',
        ]);

        return true;
    }
}
