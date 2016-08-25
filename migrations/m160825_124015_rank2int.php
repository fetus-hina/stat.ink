<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160825_124015_rank2int extends Migration
{
    public function up()
    {
        $list = ['c-', 'c', 'c+', 'b-', 'b', 'b+', 'a-', 'a', 'a+', 's', 's+'];
        $this->execute('ALTER TABLE {{rank}} ADD COLUMN [[int_base]] INTEGER');
        foreach ($list as $i => $key) {
            $this->update('rank', ['int_base' => $i * 100], ['key' => $key]);
        }
        $this->execute('ALTER TABLE {{rank}} ALTER COLUMN [[int_base]] SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{rank}} DROP COLUMN [[int_base]]');
    }
}
