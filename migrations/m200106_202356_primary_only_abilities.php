<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m200106_202356_primary_only_abilities extends Migration
{
    public function safeUp()
    {
        $this->addColumns('ability2', [
            'primary_only' => $this->boolean()->notNull()->defaultValue(false),
        ]);
        $this->update(
            'ability2',
            ['primary_only' => true],
            ['key' => [
                'ability_doubler',
                'comeback',
                'drop_roller',
                'haunt',
                'last_ditch_effort',
                'ninja_squid',
                'object_shredder',
                'opening_gambit',
                'respawn_punisher',
                'stealth_jump',
                'tenacity',
                'thermal_ink',
            ]],
        );
    }

    public function safeDown()
    {
        $this->dropColumns('ability2', ['primary_only']);
    }
}
