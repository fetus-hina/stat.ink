<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170720_202004_weapon2_key extends Migration
{
    public function up()
    {
        $this->execute(
            'ALTER TABLE {{weapon2}} ' .
            'ALTER COLUMN [[key]] SET DATA TYPE VARCHAR(32)',
        );
        $this->execute(
            'ALTER TABLE {{death_reason2}} ' .
            'ALTER COLUMN [[key]] SET DATA TYPE VARCHAR(32)',
        );
    }

    public function down()
    {
        $this->execute(
            'ALTER TABLE {{weapon2}} ' .
            'ALTER COLUMN [[key]] SET DATA TYPE VARCHAR(16)',
        );
        $this->execute(
            'ALTER TABLE {{death_reason2}} ' .
            'ALTER COLUMN [[key]] SET DATA TYPE VARCHAR(16)',
        );
    }
}
