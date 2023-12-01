<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m231201_004735_stage_ids extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [
                $this->key2id('{{%map3}}', 'negitoro'),
                '22',
            ],
            [
                $this->key2id('{{%map3}}', 'baigai'),
                '21',
            ],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%map3_alias}}', ['key' => ['21', '22']]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3_alias}}',
        ];
    }
}
