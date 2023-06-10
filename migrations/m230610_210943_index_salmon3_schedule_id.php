<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230610_210943_index_salmon3_schedule_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = \app\components\helpers\TypeHelper::instanceOf($this->db, \yii\db\Connection::class);

        $this->execute(
            vsprintf('CREATE UNIQUE INDEX %s ON %s (%s) WHERE ((%s))', [
                $db->quoteColumnName('salmon3_user_id_schedule_id'),
                $db->quoteTableName('{{%salmon3}}'),
                implode(
                    ', ',
                    array_map(
                        fn (string $column): string => $db->quoteColumnName($column),
                        [
                            'user_id',
                            'schedule_id',
                            'id',
                        ],
                    ),
                ),
                implode(') AND (', [
                    '[[is_deleted]] = FALSE',
                    '[[is_private]] = FALSE',
                    '[[schedule_id]] IS NOT NULL',
                ]),
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('salmon3_user_id_schedule_id', '{{%salmon3}}');

        return true;
    }
}
