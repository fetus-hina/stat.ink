<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151016_080823_gachi extends Migration
{
    public function up()
    {
        $sql = 'ALTER TABLE {{battle}} ' . implode(', ', [
            'ADD COLUMN [[is_knock_out]] BOOLEAN',
            'ADD COLUMN [[my_team_count]] INTEGER',
            'ADD COLUMN [[his_team_count]] INTEGER',
        ]);
        $this->execute($sql);

        $update = sprintf(
            'UPDATE {{battle}} SET %s FROM {{battle_gachi}} AS {{t}} WHERE {{battle}}.[[id]] = {{t}}.[[id]]',
            implode(', ', array_map(
                function ($col) {
                    return sprintf('[[%1$s]] = {{t}}.[[%1$s]]', $col);
                },
                [
                    'is_knock_out',
                    'my_team_count',
                    'his_team_count',
                ],
            )),
        );
        $this->execute($update);
        $this->dropTable('battle_gachi');
    }

    public function down()
    {
        echo "m151016_080823_gachi cannot be reverted.\n";
        return false;
    }
}
