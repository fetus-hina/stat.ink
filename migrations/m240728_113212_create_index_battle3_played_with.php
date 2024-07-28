<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;

final class m240728_113212_create_index_battle3_played_with extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);

        $this->execute(
            vsprintf('CREATE INDEX %s ON %s ( %s ) WHERE %s', [
                $db->quoteColumnName('battle3_played_with_user_id_count_name_number'),
                $db->quoteTableName('{{%battle3_played_with}}'),
                implode(', ', [
                    $db->quoteColumnName('user_id'),
                    $db->quoteColumnName('count') . ' DESC',
                    $db->quoteColumnName('name'),
                    $db->quoteColumnName('number'),
                ]),
                sprintf('%s > 1', $db->quoteColumnName('count')),
            ]),
        );

        $this->execute(
            vsprintf('CREATE INDEX %s ON %s ( %s ) WHERE %s', [
                $db->quoteColumnName('salmon3_played_with_user_id_count_name_number'),
                $db->quoteTableName('{{%salmon3_played_with}}'),
                implode(', ', [
                    $db->quoteColumnName('user_id'),
                    $db->quoteColumnName('count') . ' DESC',
                    $db->quoteColumnName('name'),
                    $db->quoteColumnName('number'),
                ]),
                sprintf('%s > 1', $db->quoteColumnName('count')),
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('battle3_played_with_user_id_count_name_number', '{{%battle3_played_with}}');
        $this->dropIndex('salmon3_played_with_user_id_count_name_number', '{{%salmon3_played_with}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%battle3_played_with}}',
            '{{%salmon3_played_with}}',
        ];
    }
}
