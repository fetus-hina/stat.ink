<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240823_152743_switch_bigrun_map3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute(
            'ALTER TABLE {{%salmon3}} ' .
            'DROP CONSTRAINT {{%salmon3_big_stage_id_fkey}}, ' .
            'ADD FOREIGN KEY ([[big_stage_id]]) REFERENCES {{%bigrun_map3}} ([[id]])',
        );

        $this->execute(
            'ALTER TABLE {{%salmon_schedule3}} ' .
            'DROP CONSTRAINT {{%salmon_schedule3_big_map_id_fkey}}, ' .
            'ADD FOREIGN KEY ([[big_map_id]]) REFERENCES {{%bigrun_map3}} ([[id]])',
        );

        $this->execute(
            'ALTER TABLE {{%stat_salmon3_tide_event}} ' .
            'DROP CONSTRAINT {{%stat_salmon3_tide_event_big_stage_id_fkey}}, ' .
            'ADD FOREIGN KEY ([[big_stage_id]]) REFERENCES {{%bigrun_map3}} ([[id]])',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute(
            'ALTER TABLE {{%salmon3}} ' .
            'DROP CONSTRAINT {{%salmon3_big_stage_id_fkey}}, ' .
            'ADD FOREIGN KEY ([[big_stage_id]]) REFERENCES {{%map3}} ([[id]])',
        );

        $this->execute(
            'ALTER TABLE {{%salmon_schedule3}} ' .
            'DROP CONSTRAINT {{%salmon_schedule3_big_map_id_fkey}}, ' .
            'ADD FOREIGN KEY ([[big_map_id]]) REFERENCES {{%map3}} ([[id]])',
        );

        $this->execute(
            'ALTER TABLE {{%stat_salmon3_tide_event}} ' .
            'DROP CONSTRAINT {{%stat_salmon3_tide_event_big_stage_id_fkey}}, ' .
            'ADD FOREIGN KEY ([[big_stage_id]]) REFERENCES {{%map3}} ([[id]])',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon3}}',
            '{{%salmon_schedule3}}',
            '{{%stat_salmon3_tide_event}}',
        ];
    }
}
