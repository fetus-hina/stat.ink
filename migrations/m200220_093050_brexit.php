<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m200220_093050_brexit extends Migration
{
    public function safeUp()
    {
        $this->insert('country', [
            'key' => 'gb', // It's not UK, for historical reasons outside this project.
            'name' => 'United Kingdom',
        ]);

        $this->insert('timezone_country', [
            'timezone_id' => $this->getUKTimeZone(),
            'country_id' => $this->getUKId(),
        ]);
    }

    public function safeDown()
    {
        $uk = $this->getUKId();
        $this->delete('timezone_county', [
            'timezone_id' => $this->getUKTimeZone(),
            'country_id' => $uk,
        ]);
        $this->delete('country', ['id' => $uk]);
    }

    private function getUKTimeZone(): int
    {
        return filter_var(
            (new Query())
                ->select('id')
                ->from('timezone')
                ->andWhere(['timezone.identifier' => 'Europe/London'])
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT,
        );
    }

    private function getUKId(): int
    {
        return filter_var(
            (new Query())
                ->select('id')
                ->from('country')
                ->andWhere(['country.key' => 'gb'])
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT,
        );
    }
}
