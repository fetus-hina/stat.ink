<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221217_072253_fest_theme_and_color extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%splatfest3_theme}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(64)->notNull()->unique(),
        ]);

        $this->addColumns('{{%battle3}}', [
            'our_team_color' => $this->char(8)->null(),
            'their_team_color' => $this->char(8)->null(),
            'third_team_color' => $this->char(8)->null(),
            'our_team_theme_id' => $this->pkRef('{{%splatfest3_theme}}')->null(),
            'their_team_theme_id' => $this->pkRef('{{%splatfest3_theme}}')->null(),
            'third_team_theme_id' => $this->pkRef('{{%splatfest3_theme}}')->null(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumns('{{%battle3}}', [
            'our_team_color',
            'their_team_color',
            'third_team_color',
            'our_team_theme_id',
            'their_team_theme_id',
            'third_team_theme_id',
        ]);

        $this->dropTable('{{%splatfest3_theme}}');

        return true;
    }
}
