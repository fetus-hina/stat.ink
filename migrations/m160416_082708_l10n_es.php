<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160416_082708_l10n_es extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('language', ['lang', 'name', 'name_en'], [
            [ 'es-ES', 'Español (EU)', 'Spanish (EU)' ],
            [ 'es-MX', 'Español (LA)', 'Spanish (LA)' ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('language', ['lang' => ['es-ES', 'es-MX']]);
    }
}
