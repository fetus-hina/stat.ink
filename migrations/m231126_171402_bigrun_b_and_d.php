<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m231126_171402_bigrun_b_and_d extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $id = $this->key2id('{{%map3}}', 'taraport');

        $this->update('{{%map3}}', ['bigrun' => true], ['id' => $id]);
        $this->insert('{{%map3_alias}}', [
            'map_id' => $id,
            'key' => '105',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%map3}}', 'taraport');

        $this->update('{{%map3}}', ['bigrun' => false], ['id' => $id]);
        $this->delete('{{%map3_alias}}', [
            'map_id' => $id,
            'key' => '105',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3}}',
            '{{%map3_alias}}',
        ];
    }
}
