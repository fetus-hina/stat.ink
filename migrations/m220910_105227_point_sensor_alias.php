<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m220910_105227_point_sensor_alias extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%subweapon3_alias}}', [
            'subweapon_id' => $this->key2id('{{%subweapon3}}', 'pointsensor'),
            'key' => self::name2key3('Point Sensor'),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%subweapon3_alias}}', [
            'key' => self::name2key3('Point Sensor'),
        ]);

        return true;
    }
}
