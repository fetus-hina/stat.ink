<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m190509_101024_accept_language_de extends Migration
{
    public function safeUp()
    {
        $this->insert('accept_language', [
            'rule' => 'de*',
            'language_id' => (new Query())
                ->select('id')
                ->from('language')
                ->where(['lang' => 'de-DE'])
                ->scalar(),
        ]);
    }

    public function safeDown()
    {
        $this->delete('accept_language', [
            'rule' => 'de*',
        ]);
    }
}
