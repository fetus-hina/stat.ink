<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181003_170140_salmon_boss2 extends Migration
{
    public function up()
    {
        $this->createTable('salmon_boss2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull(),
            'splatnet' => $this->integer(),
            'splatnet_str' => $this->string(64),
        ]);
        $this->batchInsert('salmon_boss2', ['key', 'name', 'splatnet', 'splatnet_str'], [
            ['goldie', 'Goldie', 3, 'sakelien-golden'],
            ['steelhead', 'Steelhead', 6, 'sakelien-bomber'],
            ['flyfish', 'Flyfish', 9, 'sakelien-cup-twins'],
            ['scrapper', 'Scrapper', 12, 'sakelien-shield'],
            ['steel_eel', 'Steel Eel', 13, 'sakelien-snake'],
            ['stinger', 'Stinger', 14, 'sakelien-tower'],
            ['maws', 'Maws', 15, 'sakediver'],
            ['griller', 'Griller', 16, 'sakedozer'],
            ['drizzler', 'Drizzler', 21, 'sakerocket'],
        ]);
    }

    public function down()
    {
        $this->dropTable('salmon_boss2');
    }
}
