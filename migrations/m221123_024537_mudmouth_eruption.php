<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m221123_024537_mudmouth_eruption extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update(
            '{{%salmon_event3}}',
            ['name' => 'Mudmouth Eruptions'],
            ['key' => 'mudmouth_eruption'],
        );

        $this->insert(
            '{{%salmon_event3_alias}}',
            [
                'key' => self::name2key3('Mudmouth Eruptions'),
                'event_id' => $this->key2id('{{%salmon_event3}}', 'mudmouth_eruption'),
            ],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%salmon_event3_alias}}', [
            'key' => self::name2key3('Mudmouth Eruptions'),
        ]);

        $this->update(
            '{{%salmon_event3}}',
            ['name' => 'Mudmouth Eruption'],
            ['key' => 'mudmouth_eruption'],
        );

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_event3}}',
            '{{%salmon_event3_alias}}',
        ];
    }
}
