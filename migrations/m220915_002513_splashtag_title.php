<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220915_002513_splashtag_title extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%splashtag_title3}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->unique(),
        ]);
        $this->addColumn(
            '{{%battle_player3}}',
            'splashtag_title_id',
            (string)$this->pkRef('{{%splashtag_title3}}')->null()
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%battle_player3}}', 'splashtag_title_id');
        $this->dropTable('{{%splashtag_title3}}');

        return true;
    }
}
