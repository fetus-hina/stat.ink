<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170425_165119_user_lang extends Migration
{
    public function up()
    {
        $langId = (new Query())
            ->select(['id'])
            ->from('language')
            ->where(['lang' => 'ja-JP'])
            ->scalar();

        $this->execute(
            'ALTER TABLE {{user}} ' .
            sprintf('ADD COLUMN [[default_language_id]] INTEGER NOT NULL DEFAULT %d ', (int)$langId) .
            'REFERENCES {{language}}([[id]])',
        );
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{user}} ' . implode(', ', [
            'DROP COLUMN [[default_language_id]]',
        ]));
    }
}
