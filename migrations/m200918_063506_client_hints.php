<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m200918_063506_client_hints extends Migration
{
    public function safeUp()
    {
        $hintExample = rtrim(base64_encode(hash('sha3-256', '', true)), '=');

        $this->createTable('http_client_hint', [
            'id' => $this->primaryKey(),
            'hash' => $this->char(strlen($hintExample))
                ->notNull()
                ->check(vsprintf('%s ~ %s', [
                    $this->db->quoteColumnName('hash'),
                    $this->db->quoteValue('\A[A-Za-z0-9+/]+\Z'),
                ]))
                ->unique(),
            'value' => 'JSONB NOT NULL',
        ]);
        $this->addColumn(
            'user_login_history',
            'client_hint_id',
            $this->pkRef('http_client_hint')->null(),
        );
    }

    public function safeDown()
    {
        $this->dropColumn('user_login_history', 'client_hint_id');
        $this->dropTable('http_client_hint');
    }
}
