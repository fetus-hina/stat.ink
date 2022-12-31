<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151009_100616_kill_ratio extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ADD COLUMN [[kill_ratio]] NUMERIC(4,2)');
        $selectRatio = implode(' ', [
            'CASE',
                'WHEN {{battle}}.[[kill]] = 0 AND {{battle}}.[[death]] = 0 THEN 1.00',
                'WHEN {{battle}}.[[kill]] > 0 AND {{battle}}.[[death]] = 0 THEN 99.99',
                'ELSE ({{battle}}.[[kill]]::float / {{battle}}.[[death]]::float)::numeric(4,2)',
            'END',
        ]);
        $select = "SELECT {{battle}}.[[id]], {$selectRatio} AS [[kill_ratio]] " .
            "FROM {{battle}} " .
            "WHERE ({{battle}}.[[kill]] IS NOT NULL AND {{battle}}.[[death]] IS NOT NULL)";
        $update = "UPDATE {{battle}} " .
            "SET [[kill_ratio]] = {{tmp}}.[[kill_ratio]] " .
            "FROM ( {$select} ) AS [[tmp]] " .
            "WHERE {{battle}}.[[id]] = [[tmp]].[[id]]";
        $this->execute($update);
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN [[kill_ratio]]');
    }
}
