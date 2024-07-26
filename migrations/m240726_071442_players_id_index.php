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

final class m240726_071442_players_id_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);
        foreach (['battle_player3', 'salmon_player3'] as $tableName) {
            $this->execute(
                vsprintf('CREATE INDEX CONCURRENTLY %s ON %s (%s) WHERE ((%s))', [
                    $db->quoteColumnName("{$tableName}_name_number"),
                    $db->quoteTableName("{{%{$tableName}}}"),
                    implode(
                        ', ',
                        array_map(
                            $db->quoteColumnName(...),
                            ['name', 'number'],
                        ),
                    ),
                    implode(') AND (', [
                        sprintf('%s IS NOT NULL', $db->quoteColumnName('name')),
                        sprintf('%s IS NOT NULL', $db->quoteColumnName('number')),
                        sprintf('%s = FALSE', $db->quoteColumnName('is_me')),
                    ]),
                ]),
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute('DROP INDEX CONCURRENTLY [[battle_player3_name_number]]');
        $this->execute('DROP INDEX CONCURRENTLY [[salmon_player3_name_number]]');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%battle_player3}}',
            '{{%salmon_player3}}',
        ];
    }
}
