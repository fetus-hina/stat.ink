<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

// missing "main" key here
// https://github.com/fetus-hina/stat.ink/blob/534003a68cdcaadcc05f7dc0146f294655720ff5/migrations/m230823_112003_add_weapons.php#L87-L97
final class m231021_031845_fix_kugelschreiber_hue extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update(
            '{{%weapon3}}',
            ['mainweapon_id' => $this->key2id('mainweapon3', 'kugelschreiber')],
            ['key' => 'kugelschreiber_hue'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update(
            '{{%weapon3}}',
            ['mainweapon_id' => $this->key2id('mainweapon3', 'kugelschreiber_hue')],
            ['key' => 'kugelschreiber_hue'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%weapon3}}',
        ];
    }
}
