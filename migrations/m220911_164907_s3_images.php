<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220911_164907_s3_images extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tables = [
            '{{%battle_image_judge3}}',
            '{{%battle_image_result3}}',
            '{{%battle_image_gear3}}',
        ];
        foreach ($tables as $table) {
            $this->createTable($table, [
                'battle_id' => $this->bigPkRef('{{%battle3}}')->notNull(),
                'bucket_id' => $this->pkRef('{{%image_bucket}}')->notNull()->defaultValue(1),
                'filename' => $this->string(64)->notNull()->unique(),
                'PRIMARY KEY ([[battle_id]])',
            ]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%battle_image_judge3}}',
            '{{%battle_image_result3}}',
            '{{%battle_image_gear3}}',
        ]);

        return true;
    }
}
