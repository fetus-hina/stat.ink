<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230131_014437_battle_replay extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%battle3}}',
            'replay_code',
            (string)$this->char(16)->null(),
        );

        $this->addColumn(
            '{{%salmon3}}',
            'scenario_code',
            (string)$this->char(16)->null(),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%battle3}}', 'replay_code');
        $this->dropColumn('{{%salmon3}}', 'scenario_code');

        return true;
    }
}
