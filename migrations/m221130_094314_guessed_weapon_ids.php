<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221130_094314_guessed_weapon_ids extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%weapon3_alias}}', ['weapon_id', 'key'], [
            [$this->key2id('{{%weapon3}}', 'spaceshooter'), '100'],
            [$this->key2id('{{%weapon3}}', 'wideroller'), '1040'],
            [$this->key2id('{{%weapon3}}', 'rpen_5h'), '2070'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%weapon3_alias}}', ['key' => ['100', '1040', '2070']]);

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%weapon3_alias}}',
        ];
    }
}
