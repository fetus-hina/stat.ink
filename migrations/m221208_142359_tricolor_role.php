<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221208_142359_tricolor_role extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%tricolor_role3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apikey3()->notNull(),
            'name' => $this->string(32)->notNull(),
        ]);

        $this->batchInsert(
            '{{%tricolor_role3}}',
            ['key', 'name'],
            [
                ['attacker', 'Attackers'],
                ['defender', 'Defenders'],
            ],
        );

        $this->addColumns('{{%battle3}}', [
            'our_team_role_id' => $this->pkRef('{{%tricolor_role3}}')->null(),
            'their_team_role_id' => $this->pkRef('{{%tricolor_role3}}')->null(),
            'third_team_role_id' => $this->pkRef('{{%tricolor_role3}}')->null(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumns('{{%battle3}}', [
            'our_team_role_id',
            'their_team_role_id',
            'third_team_role_id',
        ]);

        $this->dropTable('{{%tricolor_role3}}');

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%tricolor_role3}}',
        ];
    }
}
