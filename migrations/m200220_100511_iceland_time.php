<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m200220_100511_iceland_time extends Migration
{
    private const ICELAND_TLD = 'is';
    private const ICELAND_TZ = 'Atlantic/Reykjavik';

    public function safeUp()
    {
        $uktz = $this->getUKTimeZone();
        $this->insert('country', [
            'key' => static::ICELAND_TLD,
            'name' => 'Iceland',
        ]);
        $this->insert('timezone', [
            'identifier' => static::ICELAND_TZ,
            'name' => 'Iceland',
            'order' => $this->createTimezoneOrderNear($uktz->order),
            'region_id' => $uktz->region_id,
            'group_id' => $uktz->group_id,
        ]);
        $this->insert('timezone_country', [
            'timezone_id' => $this->getIcelandicTZID(),
            'country_id' => $this->getIcelandID(),
        ]);
    }

    public function safeDown()
    {
        $country = $this->getIcelandID();
        $timezone = $this->getIcelandicTZID();

        $this->delete('timezone_country', [
            'timezone_id' => $timezone,
            'country_id' => $country,
        ]);
        $this->delete('timezone', ['id' => $timezone]);
        $this->delete('country', ['id' => $country]);
    }

    private function getIcelandicTZID(): int
    {
        return filter_var(
            (new Query())
                ->select('id')
                ->from('timezone')
                ->andWhere(['timezone.identifier' => static::ICELAND_TZ])
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT,
        );
    }

    private function getIcelandID(): int
    {
        return filter_var(
            (new Query())
                ->select('id')
                ->from('country')
                ->andWhere(['country.key' => static::ICELAND_TLD])
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT,
        );
    }

    private function getUKTimeZone(): stdClass
    {
        return (object)(
            (new Query())
                ->select('*')
                ->from('timezone')
                ->andWhere(['timezone.identifier' => 'Europe/London'])
                ->limit(1)
                ->one()
        );
    }

    private function createTimezoneOrderNear(int $refId): int
    {
        $exists = array_map(
            fn ($row): int => filter_var($row, FILTER_VALIDATE_INT),
            (new Query())
                ->select('order')
                ->from('timezone')
                ->orderBy(['order' => SORT_ASC])
                ->column(),
        );

        for ($v = $refId + 1;; ++$v) {
            if (!in_array($v, $exists, true)) {
                return $v;
            }
        }
    }
}
