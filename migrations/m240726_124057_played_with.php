<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240726_124057_played_with extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        foreach (['battle3', 'salmon3'] as $baseTable) {
            $this->createTable("{{%{$baseTable}_played_with}}", [
                'user_id' => $this->pkRef('user')->notNull(),
                'name' => $this->string(10)->notNull(),
                'number' => $this->string(32)->notNull(),
                'ref_id' => $this->string(20)->notNull()->unique(),
                'count' => $this->bigInteger()->notNull(),
                'disconnect' => $this->bigInteger()->notNull(),

                'PRIMARY KEY ([[user_id]], [[name]], [[number]])',
            ]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%battle3_played_with}}');
        $this->dropTable('{{%salmon3_played_with}}');

        return true;
    }
}
