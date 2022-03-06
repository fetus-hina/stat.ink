<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151016_073134_nawabari extends Migration
{
    public function up()
    {
        $sql = 'ALTER TABLE {{battle}} ' . implode(', ', [
            'ADD COLUMN [[my_point]] INTEGER',
            'ADD COLUMN [[my_team_final_point]] INTEGER',
            'ADD COLUMN [[his_team_final_point]] INTEGER',
            'ADD COLUMN [[my_team_final_percent]] NUMERIC(4,1)',
            'ADD COLUMN [[his_team_final_percent]] NUMERIC(4,1)',
        ]);
        $this->execute($sql);

        $update = sprintf(
            'UPDATE {{battle}} SET %s FROM {{battle_nawabari}} AS {{t}} WHERE {{battle}}.[[id]] = {{t}}.[[id]]',
            implode(', ', array_map(
                fn ($col) => sprintf('[[%1$s]] = {{t}}.[[%1$s]]', $col),
                [
                    'my_point',
                    'my_team_final_point',
                    'his_team_final_point',
                    'my_team_final_percent',
                    'his_team_final_percent',
                ]
            ))
        );
        $this->execute($update);
        $this->dropTable('battle_nawabari');
    }

    public function down()
    {
        echo "m151016_073134_nawabari cannot be reverted.\n";
        return false;
    }
}
