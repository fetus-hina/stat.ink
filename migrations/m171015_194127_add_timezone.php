<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;
use yii\db\Query;

class m171015_194127_add_timezone extends Migration
{
    public function safeUp()
    {
        $regionNA = (new Query())
            ->select(['id'])
            ->from('region')
            ->where(['key' => 'na'])
            ->scalar();

        $groupOceania = (new Query())
            ->select(['id'])
            ->from('timezone_group')
            ->where(['name' => 'Australia/Oceania'])
            ->scalar();

        $countryUS = (new Query())
            ->select(['id'])
            ->from('country')
            ->where(['key' => 'us'])
            ->scalar();

        $this->insert('timezone', [
            'identifier' => 'Pacific/Guam',
            'name' => 'Guam Time',
            'order' => 32,
            'region_id' => $regionNA,
            'group_id' => $groupOceania,
        ]);
        $tz = (new Query())
            ->select(['id'])
            ->from('timezone')
            ->where(['identifier' => 'Pacific/Guam'])
            ->scalar();
        $this->insert('timezone_country', [
            'timezone_id' => $tz,
            'country_id' => $countryUS,
        ]);
    }

    public function safeDown()
    {
        $tz = (new Query())
            ->select(['id'])
            ->from('timezone')
            ->where(['identifier' => 'Pacific/Guam'])
            ->scalar();

        $this->delete('timezone_country', ['timezone_id' => $tz]);
        $this->delete('timezone', ['id' => $tz]);
    }
}
