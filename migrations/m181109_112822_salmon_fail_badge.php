<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181109_112822_salmon_fail_badge extends Migration
{
    public function up()
    {
        $this->addColumns('salmon_fail_reason2', [
            'short_name' => $this->string(32)->null(),
            'color' => $this->string(32)->null(),
        ]);
        return $this->db->transaction(function (): bool {
            return $this->safeUp();
        });
    }

    public function safeUp()
    {
        $this->update(
            'salmon_fail_reason2',
            ['name' => 'Time is up', 'short_name' => 'Time', 'color' => 'warning'],
            ['key' => 'time_limit'],
        );
        $this->update(
            'salmon_fail_reason2',
            ['name' => 'Wipe out', 'short_name' => 'Wiped', 'color' => 'info'],
            ['key' => 'wipe_out'],
        );
        return true;
    }

    public function down()
    {
        $status = $this->db->transaction(function (): bool {
            return $this->safeDown();
        });
        if (!$status) {
            return $status;
        }
        $this->dropColumns('salmon_fail_reason2', [
            'short_name',
            'color',
        ]);
    }

    public function safeDown()
    {
        $this->update(
            'salmon_fail_reason2',
            ['name' => 'Time was up'],
            ['key' => 'time_limit'],
        );
        $this->update(
            'salmon_fail_reason2',
            ['name' => 'Dead all players'],
            ['key' => 'wipe_out'],
        );
    }
}
