<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181126_131112_new_abilities extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{ability2}} ALTER COLUMN [[splatnet]] DROP NOT NULL');
        $this->batchInsert('ability2', ['key', 'name'], [
            ['bomb_defense_up_dx', 'Bomb Defense Up DX'],
            ['main_power_up', 'Main Power Up'],
        ]);
    }

    public function down()
    {
        $this->delete('ability2', ['key' => ['bomb_defense_up_dx', 'main_power_up']]);
        $this->execute('ALTER TABLE {{ability2}} ALTER COLUMN [[splatnet]] SET NOT NULL');
    }
}
