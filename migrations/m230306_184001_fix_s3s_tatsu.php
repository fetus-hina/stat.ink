<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;

final class m230306_184001_fix_s3s_tatsu extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $params = [
            ':tatsu' => $this->key2id('{{%salmon_king3}}', 'tatsu'),
        ];

        $whereSql = $db->getQueryBuilder()->buildWhere(
            ['and',
                '{{%salmon3}}.[[agent_id]] = {{agent}}.[[id]]', // JOIN cond
                [
                    '{{%agent}}.[[name]]' => 's3s',
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[king_salmonid_id]]' => null, // king salmonid not known
                ],
                ['>=', '{{%salmon3}}.[[clear_waves]]', 3], // cleared
                ['not', ['{{%salmon3}}.[[clear_extra]]' => null]], // Xtrawave happened
                ['between', '{{%salmon3}}.[[start_at]]',
                    '2023-03-04T00:00:00+00:00',
                    '2023-03-06T00:00:00+00:00',
                ],
            ],
            $params,
        );

        $sql = 'UPDATE {{%salmon3}} ' .
            'SET [[king_salmonid_id]] = :tatsu ' .
            'FROM {{agent}} ' .
            $whereSql; // including 'WHERE '

        $this->execute(
            $db->createCommand($sql, $params)->rawSql,
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        // can't reverted the changes

        return true;
    }
}
