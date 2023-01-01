<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160211_092602_batttle_events extends Migration
{
    public function up()
    {
        $this->createTable('battle_events', [
            'id' => 'BIGINT NOT NULL PRIMARY KEY',
            'events' => 'TEXT NOT NULL',
        ]);
        $this->execute(
            'INSERT INTO {{battle_events}} ( [[id]], [[events]] ) ' .
            'SELECT {{battle}}.[[id]], {{battle}}.[[events]]::text ' .
            'FROM {{battle}} ' .
            'WHERE {{battle}}.[[events]] IS NOT NULL',
        );
        $this->execute(
            'ALTER TABLE {{battle}} DROP COLUMN [[events]]',
        );
    }

    public function down()
    {
        $this->execute(
            'ALTER TABLE {{battle}} ADD COLUMN [[events]] JSONB',
        );
        $this->execute(
            'UPDATE {{battle}} ' .
            'SET {{battle}}.[[events]] = {{ev}}.[[events]] ' .
            'FROM {{battle_events}} {{ev}} ' .
            'WHERE {{battle}}.[[id]] = {{ev}}.[[id]] ' .
            'AND {{ev}}.[[events]] LIKE :json',
            [':json' => '[{%'],
        );
        $this->execute(
            'DROP TABLE {{battle_events}}',
        );
    }
}
