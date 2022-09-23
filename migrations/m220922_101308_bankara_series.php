<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;

final class m220922_101308_bankara_series extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->execute(vsprintf('ALTER TABLE %s %s', [
            $db->quoteTableName('{{%battle3}}'),
            implode(', ', [
                vsprintf('ADD COLUMN %s %s', [
                    $db->quoteColumnName('challenge_win'),
                    (string)$this->integer()->null(),
                ]),
                vsprintf('ADD COLUMN %s %s', [
                    $db->quoteColumnName('challenge_lose'),
                    (string)$this->integer()->null(),
                ]),
            ]),
        ]));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%battle3}}', 'challenge_lose');
        $this->dropColumn('{{%battle3}}', 'challenge_win');

        return true;
    }
}
