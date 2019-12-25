<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m191225_114755_current_rank extends Migration
{
    public function safeUp()
    {
        $this->addColumns('user_stat2', $this->getColumns());
    }

    public function safeDown()
    {
        $this->dropColumns('user_stat2', array_keys($this->getColumns()));
    }

    private function getColumns(): array
    {
        $rules = ['area', 'yagura', 'hoko', 'asari'];
        $patterns = [
            '_current_rank' => (string)$this->integer()->null(),
            '_current_x_power' => (string)$this->decimal(6, 1)->null(),
            '_x_power_peak' => (string)$this->decimal(6, 1)->null(),
        ];
        $results = [];
        foreach ($patterns as $keySuffix => $columnType) {
            foreach ($rules as $rule) {
                $results[$rule . $keySuffix] = $columnType;
            }
        }
        return $results;
    }
}
