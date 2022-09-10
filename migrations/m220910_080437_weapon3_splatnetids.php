<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;

final class m220910_080437_weapon3_splatnetids extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        if (!$db instanceof Connection) {
            throw new LogicException();
        }

        // Copy splatnet2's ID as weapon3's alias
        $sql = vsprintf('INSERT INTO %1$s ( %2$s, %3$s ) %4$s', [
            $db->quoteTableName('{{%weapon3_alias}}'),
            $db->quoteColumnName('weapon_id'),
            $db->quoteColumnName('key'),
            (new Query())
                ->select([
                    'weapon_id' => '{{%weapon3}}.[[id]]',
                    'key' => new Expression(vsprintf('%s.%s::text', [
                        $db->quoteTableName('{{%weapon2}}'),
                        $db->quoteColumnName('splatnet'),
                    ])),
                ])
                ->from('{{%weapon2}}')
                ->innerJoin('{{%weapon3}}', '{{%weapon2}}.[[key]] = {{%weapon3}}.[[key]]')
                ->orderBy([
                    '{{weapon3}}.[[id]]' => SORT_ASC,
                ])
                ->createCommand($db)
                ->rawSql,
        ]);
        $this->execute($sql);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%weapon3_alias}}', ['and',
            ['~', '{{%weapon3_alias}}.key', '^[0-9]+$'],
        ]);

        return true;
    }
}
