<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151007_094737_api_update extends Migration
{
    public function up()
    {
        $sql = sprintf(
            'ALTER TABLE {{battle}} %s',
            implode(', ', [
                'ADD COLUMN [[level_after]] INTEGER',
                'ADD COLUMN [[rank_after_id]] INTEGER',
                'ADD COLUMN [[rank_exp]] INTEGER',
                'ADD COLUMN [[rank_exp_after]] INTEGER',
                'ADD COLUMN [[cash]] INTEGER',
                'ADD COLUMN [[cash_after]] INTEGER',
            ]),
        );
        $this->execute($sql);
        $this->addForeignKey('fk_battle_7', 'battle', 'rank_after_id', 'rank', 'id', 'RESTRICT');
    }

    public function down()
    {
        $sql = sprintf(
            'ALTER TABLE {{battle}} %s',
            implode(', ', [
                'DROP COLUMN [[level_after]]',
                'DROP COLUMN [[rank_after_id]]',
                'DROP COLUMN [[rank_exp]]',
                'DROP COLUMN [[rank_exp_after]]',
                'DROP COLUMN [[cash]]',
                'DROP COLUMN [[cash_after]]',
            ]),
        );
        $this->execute($sql);
    }
}
