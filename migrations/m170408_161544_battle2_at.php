<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m170408_161544_battle2_at extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle2}} RENAME COLUMN [[at]] TO [[created_at]]');
        $this->execute('ALTER TABLE {{battle2}} ADD COLUMN [[updated_at]] TIMESTAMP(0) WITH TIME ZONE NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle2}} DROP COLUMN [[updated_at]]');
        $this->execute('ALTER TABLE {{battle2}} RENAME COLUMN [[created_at]] TO [[at]]');
    }
}
