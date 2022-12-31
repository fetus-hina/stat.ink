<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m210316_031242_image_bucket extends Migration
{
    public function safeUp()
    {
        $this->createTable('image_bucket', [
            'id' => $this->primaryKey(),
            'name' => $this->string(63)->notNull(),
        ]);
        $this->insert('image_bucket', [
            'id' => 1,
            'name' => 'default',
        ]);

        $this->addColumn(
            'battle_image',
            'bucket_id',
            (string)$this->pkRef('image_bucket')->notNull()->defaultValue(1),
        );

        $this->addColumn(
            'battle_image2',
            'bucket_id',
            (string)$this->pkRef('image_bucket')->notNull()->defaultValue(1),
        );

        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('battle_image2', 'bucket_id');
        $this->dropColumn('battle_image2', 'bucket_id');
        $this->dropTable('image_bucket');
        return true;
    }
}
