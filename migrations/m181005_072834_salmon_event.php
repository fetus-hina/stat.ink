<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181005_072834_salmon_event extends Migration
{
    public function up()
    {
        $this->createTable('salmon_event2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(64)->notNull(),
            'splatnet' => $this->string(32),
        ]);
        $this->batchInsert('salmon_event2', ['key', 'name', 'splatnet'], [
            ['cohock_charge', 'Cohock Charge', 'cohock-charge'],
            ['fog', 'Fog', 'fog'],
            ['goldie_seeking', 'Goldie Seeking', 'goldie-seeking'],
            ['griller', 'The Griller', 'griller'],
            ['mothership', 'The Mothership', 'the-mothership'],
            ['rush', 'Rush', 'rush'],
        ]);
    }

    public function down()
    {
        $this->dropTable('salmon_event2');
    }
}
