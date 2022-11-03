<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;

final class m221103_093433_battle_player3_gear extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->execute(vsprintf('ALTER TABLE %s %s', [
            $db->quoteTableName('{{%battle_player3}}'),
            implode(', ', array_map(
                fn (string $columnName): string => vsprintf('ADD COLUMN %s %s', [
                    $db->quoteColumnName($columnName),
                    (string)$this->pkRef('{{%gear_configuration3}}')->null(),
                ]),
                ['headgear_id', 'clothing_id', 'shoes_id'],
            )),
        ]));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->execute(vsprintf('ALTER TABLE %s %s', [
            $db->quoteTableName('{{%battle_player3}}'),
            implode(', ', array_map(
                fn (string $columnName): string => vsprintf('DROP COLUMN %s', [
                    $db->quoteColumnName($columnName),
                ]),
                ['headgear_id', 'clothing_id', 'shoes_id'],
            )),
        ]));

        return true;
    }
}
