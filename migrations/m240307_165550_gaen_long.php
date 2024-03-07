<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240307_165550_gaen_long extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update(
            '{{%x_matching_group_weapon3}}',
            ['group_id' => $this->key2id('{{%x_matching_group3}}', 'L', 'short_name')],
            ['weapon_id' => $this->key2id('{{%weapon3}}', 'gaen_ff')],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update(
            '{{%x_matching_group_weapon3}}',
            ['group_id' => $this->key2id('{{%x_matching_group3}}', 'M', 'short_name')],
            ['weapon_id' => $this->key2id('{{%weapon3}}', 'gaen_ff')],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%x_matching_group_weapon3}}',
        ];
    }
}
