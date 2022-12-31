<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151024_080455_battle_period extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ADD COLUMN [[period]] INTEGER');

        $guessBattleStart = sprintf('(CASE %s END)', implode(' ', [
            "WHEN [[start_at]] IS NOT NULL THEN [[start_at]] - '5 seconds'::interval",
            "WHEN [[end_at]] IS NOT NULL THEN [[end_at]] - '195 seconds'::interval",
            "ELSE [[at]] - '210 seconds'::interval",
        ]));
        $calcBattlePeriod = sprintf(
            'FLOOR((EXTRACT(epoch from %s) - %d) / %d)',
            $guessBattleStart,
            2 * 3600, // 02:00UTC に切り替わるのを基準にする
            4 * 3600,  // 4 時間ごとに切り替わる
        );
        $this->execute(
            sprintf('UPDATE {{battle}} SET period = %s', $calcBattlePeriod),
        );
        $this->execute('ALTER TABLE {{battle}} ALTER COLUMN [[period]] SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN [[period]]');
    }
}
