<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220920_145515_medals extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%medal3}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull()->unique(),
        ]);

        $this->createTable('{{%battle_medal3}}', [
            'id' => $this->bigPrimaryKey(),
            'battle_id' => $this->bigPkRef('{{%battle3}}')->notNull(),
            'medal_id' => $this->pkRef('{{%medal3}}')->notNull(),
            'UNIQUE ([[battle_id]], [[medal_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%battle_medal3}}');
        $this->dropTable('{{%medal3}}');

        return true;
    }
}
