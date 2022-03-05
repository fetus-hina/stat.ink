<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160305_102010_splatoon_version_data extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('splatoon_version', ['tag', 'name', 'released_at'], [
            [ '1.0.0', '1.0.0', '2015-05-28T00:00:00+09:00' ],
            [ '1.2.0', '1.2.0', '2015-06-02T10:00:00+09:00' ],
            [ '1.3.0', '1.3.0', '2015-07-01T10:00:00+09:00' ],
            [ '2.0.0', '2.0.0', '2015-08-06T10:00:00+09:00' ],
            [ '2.1.0', '2.1.0', '2015-09-02T10:00:00+09:00' ],
            [ '2.2.0', '2.2.0', '2015-10-21T10:00:00+09:00' ],
            [ '2.3.0', '2.3.0', '2015-11-13T10:00:00+09:00' ],
            [ '2.4.0', '2.4.0', '2015-12-18T10:00:00+09:00' ],
            [ '2.5.0', '2.5.0', '2016-01-20T10:00:00+09:00' ],
            [ '2.6.0', '2.6.0', '2016-03-09T10:00:00+09:00' ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('splatoon_version');
    }
}
