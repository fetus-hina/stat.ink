<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230831_234209_stage_ids extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [
                $this->key2id('{{%map3}}', 'takaashi'),
                '19',
            ],
            [
                $this->key2id('{{%map3}}', 'ohyo'),
                '20',
            ],
        ]);

        $this->batchInsert('{{%salmon_map3_alias}}', ['map_id', 'key'], [
            [
                $this->key2id('{{%salmon_map3}}', 'tokishirazu'),
                '4',
            ],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%salmon_map3_alias}}', ['key' => '4']);
        $this->delete('{{%map3_alias}}', ['key' => ['19', '20']]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3_alias}}',
            '{{%salmon_map3_alias}}',
        ];
    }
}
