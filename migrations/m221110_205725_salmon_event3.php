<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221110_205725_salmon_event3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_event3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'name' => $this->string(64)->notNull(),
        ]);

        $this->createTable('{{%salmon_event3_alias}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'event_id' => $this->pkRef('{{%salmon_event3}}')->notNull(),
        ]);

        $this->batchInsert('{{%salmon_event3}}', ['key', 'name'], [
            ['cohock_charge', 'Cohock Charge'],
            ['giant_tornado', 'Giant Tornado'],
            ['goldie_seeking', 'Goldie Seeking'],
            ['griller', 'The Griller'],
            ['mothership', 'The Mothership'],
            ['mudmouth_eruption', 'Mudmouth Eruption'],
            ['rog', 'Fog'],
            ['rush', 'Rush'],
        ]);

        $this->batchInsert('{{%salmon_event3_alias}}', ['key', 'event_id'], [
            ['the_griller', $this->key2id('{{%salmon_event3}}', 'griller')],
            ['the_mothership', $this->key2id('{{%salmon_event3}}', 'mothership')],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables(['{{%salmon_event3_alias}}', '{{%salmon_event3}}']);

        return true;
    }
}
