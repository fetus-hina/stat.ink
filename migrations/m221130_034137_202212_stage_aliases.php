<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221130_034137_202212_stage_aliases extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert(
            '{{%map3_alias}}',
            ['map_id', 'key'],
            [
                [
                    $this->key2id('{{%map3}}', 'hirame'),
                    '9',
                ],
                [
                    $this->key2id('{{%map3}}', 'kusaya'),
                    '7',
                ],
            ],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%map3_alias}}', ['key' => ['7', '9']]);

        return true;
    }

    public function vacuumTables(): array
    {
        return [
            '{{%map3_alias}}',
        ];
    }
}
