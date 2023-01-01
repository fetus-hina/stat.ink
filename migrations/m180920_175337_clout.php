<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m180920_175337_clout extends Migration
{
    public function up()
    {
        $this->addColumns('battle2', $this->getColumns());
    }

    public function down()
    {
        $this->dropColumns('battle2', array_keys($this->getColumns()));
    }

    public function getColumns(): array
    {
        return [
            'clout' => $this->integer()->null(),
            'total_clout' => $this->integer()->null(),
            'total_clout_after' => $this->integer()->null(),
            'synergy_bonus' => $this->decimal(2, 1)->null(),
            'my_team_win_streak' => $this->integer()->null(),
            'his_team_win_streak' => $this->integer()->null(),
        ];
    }
}
