<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230711_190248_xgroup_sblast extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->updateGroup('D+');

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->updateGroup('D-');

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

    private function updateGroup(string $shortName): void
    {
        $this->update(
            '{{%x_matching_group_weapon3}}',
            ['group_id' => $this->key2id('{{%x_matching_group3}}', $shortName, 'short_name')],
            ['weapon_id' => $this->key2id('{{%weapon3}}', 'sblast92')],
        );
    }
}
