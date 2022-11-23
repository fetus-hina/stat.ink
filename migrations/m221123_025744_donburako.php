<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m221123_025744_donburako extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%salmon_map3}}', [
            'key' => 'donburako',
            'name' => "Marooner's Bay",
            'short_name' => 'Bay',
        ]);

        $this->insert('{{%salmon_map3_alias}}', [
            'map_id' => $this->key2id('{{%salmon_map3}}', 'donburako'),
            'key' => self::name2key3("Marooner's Bay"),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%salmon_map3}}', 'donburako');

        $this->delete('{{%salmon_map3_alias}}', ['map_id' => $id]);
        $this->delete('{{%salmon_map3}}', ['id' => $id]);

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_map3}}',
            '{{%salmon_map3_alias}}',
        ];
    }
}
