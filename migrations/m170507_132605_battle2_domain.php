<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170507_132605_battle2_domain extends Migration
{
    public function up()
    {
        $this->execute('ALTER DOMAIN {{rgb}} DROP NOT NULL');
        $this->execute('ALTER DOMAIN {{hue}} DROP NOT NULL');
        $this->execute('ALTER DOMAIN {{httpurl}} DROP NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER DOMAIN {{rgb}} SET NOT NULL');
        $this->execute('ALTER DOMAIN {{hue}} SET NOT NULL');
        $this->execute('ALTER DOMAIN {{httpurl}} SET NOT NULL');
    }
}
