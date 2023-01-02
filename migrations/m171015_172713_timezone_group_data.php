<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m171015_172713_timezone_group_data extends Migration
{
    public function up()
    {
        $this->addColumn(
            'timezone',
            'group_id',
            $this->pkRef('timezone_group')->null(),
        );
        $this->upData();
        $this->execute('ALTER TABLE {{timezone}} ALTER COLUMN [[group_id]] SET NOT NULL');
    }

    public function down()
    {
        $this->dropColumn('timezone', 'group_id');
    }

    private function upData(): void
    {
        $list = [
            // {{{
            'East Asia' => [
                'Asia/Tokyo',
            ],
            'Australia/Oceania' => [
                'Pacific/Honolulu',
                'Australia/Brisbane',
                'Australia/Sydney',
                'Australia/Adelaide',
                'Australia/Darwin',
                'Australia/Perth',
            ],
            'Russia' => [
                'Europe/Kaliningrad',
                'Europe/Moscow',
                'Europe/Samara',
                'Asia/Yekaterinburg',
                'Asia/Omsk',
                'Asia/Krasnoyarsk',
                'Asia/Irkutsk',
                'Asia/Yakutsk',
                'Asia/Vladivostok',
                'Asia/Magadan',
                'Asia/Kamchatka',
            ],
            'Europe' => [
                'Europe/Athens',
                'Europe/Paris',
                'Europe/London',
            ],
            'North America' => [
                'America/New_York',
                'America/Chicago',
                'America/Denver',
                'America/Phoenix',
                'America/Los_Angeles',
                'America/Anchorage',
                'America/Adak',
                'America/St_Johns',
                'America/Halifax',
                'America/Regina',
            ],
            'Latin America' => [
            ],
            'Others' => [
                'Etc/UTC',
            ],
            // }}}
        ];
        $groups = $this->getGroups();
        foreach ($list as $groupName => $timeZones) {
            if (!$timeZones) {
                continue;
            }
            $this->update(
                'timezone',
                ['group_id' => $groups[$groupName]],
                ['identifier' => $timeZones],
            );
        }
    }

    private function getGroups(): array
    {
        return ArrayHelper::map(
            (new Query())
                ->select(['id', 'name'])
                ->from('timezone_group')
                ->all(),
            'name',
            'id',
        );
    }
}
