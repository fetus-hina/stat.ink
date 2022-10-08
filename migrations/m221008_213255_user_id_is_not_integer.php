<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;

final class m221008_213255_user_id_is_not_integer extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->addColumn('{{%battle_player3}}', 'number_new', (string)$this->string(32)->null());
        $this->execute(vsprintf('UPDATE %1$s SET %2$s = %4$s WHERE %3$s IS NOT NULL', [
            $db->quoteTableName('{{%battle_player3}}'),
            $db->quoteColumnName('number_new'),
            $db->quoteColumnName('number'),
            vsprintf('(CASE WHEN %1$s < 1000 THEN TO_CHAR(%1$s, %2$s) ELSE %1$s::text END)', [
                $db->quoteColumnName('number'),
                $db->quoteValue('FM0000'),
            ]),
        ]));
        $this->dropColumn('{{%battle_player3}}', 'number');
        $this->renameColumn('{{%battle_player3}}', 'number_new', 'number');

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->addColumn('{{%battle_player3}}', 'number_int', (string)$this->integer()->null());
        $this->execute(vsprintf('UPDATE %1$s SET %2$s = %4$s WHERE %3$s IS NOT NULL', [
            $db->quoteTableName('{{%battle_player3}}'),
            $db->quoteColumnName('number_int'),
            $db->quoteColumnName('number'),
            vsprintf('(CASE WHEN %1$s ~ %2$s THEN %1$s::int ELSE NULL END)', [
                $db->quoteColumnName('number'),
                $db->quoteValue('^[0-9]+$'),
            ]),
        ]));
        $this->dropColumn('{{%battle_player3}}', 'number');
        $this->renameColumn('{{%battle_player3}}', 'number_int', 'number');

        return true;
    }
}
