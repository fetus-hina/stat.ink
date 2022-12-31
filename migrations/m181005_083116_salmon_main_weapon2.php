<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181005_083116_salmon_main_weapon2 extends Migration
{
    public function up()
    {
        // we can't use salmon_weapon2 because it already used for schedule...
        $this->createTable('salmon_main_weapon2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull(),
            'splatnet' => $this->integer()->null(),
            'weapon_id' => $this->pkRef('weapon2')->null(),
        ]);

        $this->db->transaction(function (): void {
            // copy weapons from {{weapon2}} table
            $this->execute(
                'INSERT INTO {{salmon_main_weapon2}}([[key]], [[name]], [[splatnet]], [[weapon_id]]) ' .
                'SELECT [[key]], [[name]], [[splatnet]], [[id]] ' .
                'FROM {{weapon2}} ' .
                'WHERE {{weapon2}}.[[id]] = {{weapon2}}.[[main_group_id]] ' .
                'ORDER BY {{weapon2}}.[[splatnet]] ASC',
            );

            $this->batchInsert('salmon_main_weapon2', ['key', 'name'], [
                ['kuma_blaster', 'Grizzco Blaster'],
                ['kuma_brella', 'Grizzco Brella'],
                ['kuma_charger', 'Grizzco Charger'],
                ['kuma_slosher', 'Grizzco Slosher'],
            ]);
        });
    }

    public function down()
    {
        $this->dropTable('salmon_main_weapon2');
    }
}
