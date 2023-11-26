<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m231126_182037_bigrun_abstract extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumns('{{%stat_bigrun_distrib_job_abstract3}}', $this->getColumns());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumns(
            '{{%stat_bigrun_distrib_job_abstract3}}',
            array_keys($this->getColumns()),
        );

        return true;
    }

    /**
     * @return array<string, string>
     */
    private function getColumns(): array
    {
        return [
            'w1_failed_jobs' => (string)$this->bigInteger()->null(),
            'w1_failed_average' => (string)$this->double()->null(),
            'w1_failed_stddev' => (string)$this->double()->null(),
            'w2_failed_jobs' => (string)$this->bigInteger()->null(),
            'w2_failed_average' => (string)$this->double()->null(),
            'w2_failed_stddev' => (string)$this->double()->null(),
            'w3_failed_jobs' => (string)$this->bigInteger()->null(),
            'w3_failed_average' => (string)$this->double()->null(),
            'w3_failed_stddev' => (string)$this->double()->null(),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_bigrun_distrib_job_abstract3}}',
        ];
    }
}
