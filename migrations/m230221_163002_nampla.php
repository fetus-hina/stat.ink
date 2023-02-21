<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m230221_163002_nampla extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%map3}}', [
            'key' => 'nampla',
            'name' => "Um'ami Ruins",
            'short_name' => 'Ruins',
            'area' => null,
            'release_at' => '2023-03-01T00:00:00+00:00',
        ]);

        $this->insert('{{%map3_alias}}', [
            'map_id' => $this->key2id('{{%map3}}', 'nampla'),
            'key' => self::name2key3("Um'ami Ruins"),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%map3}}', 'nampla');
        $this->delete('{{%map3_alias}}', ['map_id' => $id]);
        $this->delete('{{%map3}}', ['id' => $id]);

        return true;
    }
}
