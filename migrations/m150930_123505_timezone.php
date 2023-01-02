<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m150930_123505_timezone extends Migration
{
    public function up()
    {
        $this->createTable('timezone', [
            'id' => $this->primaryKey(),
            'identifier' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(32)->notNull()->unique(),
            'order' => $this->integer()->unique(),
        ]);
        $this->batchInsert('timezone', ['identifier', 'name', 'order'], [
            [ 'Asia/Tokyo', 'Japan Time', 1 ],
            [ 'Europe/Athens', 'European Time (East)', 11 ],
            [ 'Europe/Paris', 'European Time (Central)', 12 ],
            [ 'Europe/London', 'European Time (West)', 13 ],
            [ 'America/New_York', 'North America (ET)', 21 ],
            [ 'America/Chicago', 'North America (CT)', 22 ],
            [ 'America/Denver', 'North America (MT)', 23 ],
            [ 'America/Phoenix', 'North America (Arizona)', 24 ],
            [ 'America/Los_Angeles', 'North America (PT)', 25 ],
            [ 'America/Anchorage', 'North America (Alaska)', 26 ],
            [ 'America/Adak', 'North America (Adak)', 27 ],
            [ 'Pacific/Honolulu', 'Hawaii', 28 ],
            [ 'Etc/UTC', 'UTC', 99 ],
        ]);
    }

    public function down()
    {
        $this->dropTable('timezone');
    }
}
