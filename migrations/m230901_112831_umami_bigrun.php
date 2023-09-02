<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230901_112831_umami_bigrun extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%map3_alias}}', [
            'map_id' => $this->key2id('{{%map3}}', 'nampla'),
            'key' => '104',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%map3_alias}}', [
            'key' => '104',
        ]);

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
