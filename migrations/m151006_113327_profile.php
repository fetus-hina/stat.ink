<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151006_113327_profile extends Migration
{
    public function up()
    {
        $sql = sprintf(
            'ALTER TABLE {{user}} %s',
            implode(', ', [
                'ADD COLUMN [[nnid]] VARCHAR(16)',
                'ADD COLUMN [[twitter]] VARCHAR(15)',
                'ADD COLUMN [[ikanakama]] INTEGER',
            ]),
        );
        $this->execute($sql);
    }

    public function down()
    {
        $sql = sprintf(
            'ALTER TABLE {{user}} %s',
            implode(', ', [
                'DROP COLUMN [[nnid]]',
                'DROP COLUMN [[twitter]]',
                'DROP COLUMN [[ikanakama]]',
            ]),
        );
        $this->execute($sql);
    }
}
