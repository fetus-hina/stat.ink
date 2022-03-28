<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190729_120625_user_email extends Migration
{
    public function safeUp()
    {
        $this->addColumns('user', [
            'email' => (string)$this->string(254)
                ->check(vsprintf('%s ~ %s', [
                    $this->db->quoteColumnName('email'),
                    $this->db->quoteValue(sprintf('^%s$', implode('', [
                        '[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+',
                        '@',
                        '[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?',
                        '(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*',
                    ]))),
                ])),
            'email_lang_id' => (string)$this->pkRef('language')->null(),
        ]);
    }

    public function safeDown()
    {
        $this->dropColumns('user', [
            'email',
            'email_lang_id',
        ]);
    }
}
