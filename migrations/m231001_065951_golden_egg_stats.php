<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m231001_065951_golden_egg_stats extends Migration
{
    /**
     * @return array<string, string|Stringable>
     */
    private function getColumns(): array
    {
        return [
            'min_team' => $this->integer()->null(),
            'q1_team' => $this->decimal(5, 1)->null(),
            'q2_team' => $this->decimal(5, 1)->null(),
            'q3_team' => $this->decimal(5, 1)->null(),
            'max_team' => $this->integer()->null(),
            'mode_team' => $this->integer()->null(),
            'min_individual' => $this->integer()->null(),
            'q1_individual' => $this->decimal(5, 1)->null(),
            'q2_individual' => $this->decimal(5, 1)->null(),
            'q3_individual' => $this->decimal(5, 1)->null(),
            'max_individual' => $this->integer()->null(),
            'mode_individual' => $this->integer()->null(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumns(
            '{{%salmon3_user_stats_golden_egg}}',
            $this->getColumns(),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumns(
            '{{%salmon3_user_stats_golden_egg}}',
            array_keys($this->getColumns()),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon3_user_stats_golden_egg}}',
        ];
    }
}
