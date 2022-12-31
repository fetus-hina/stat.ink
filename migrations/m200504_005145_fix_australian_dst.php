<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m200504_005145_fix_australian_dst extends Migration
{
    public function safeUp()
    {
        return $this->swapTimezones();
    }

    public function safeDown()
    {
        return $this->swapTimezones();
    }

    private function swapTimezones(): bool
    {
        $adelaide = $this->getInfo('Australia/Adelaide');
        $darwin = $this->getInfo('Australia/Darwin');

        // UNIQUE 制約回避のためにめちゃくちゃな値にする
        $this->update(
            'timezone',
            [
                'name' => 'TEMPORARILY',
                'order' => 0x7fffffff,
            ],
            ['id' => $adelaide->id],
        );

        $this->update(
            'timezone',
            [
                'name' => $adelaide->name,
                'order' => $adelaide->order,
            ],
            ['id' => $darwin->id],
        );

        $this->update(
            'timezone',
            [
                'name' => $darwin->name,
                'order' => $darwin->order,
            ],
            ['id' => $adelaide->id],
        );

        return true;
    }

    private function getInfo(string $identifier): stdClass
    {
        return (object)(new Query())
            ->select('*')
            ->from('timezone')
            ->where(['identifier' => $identifier])
            ->limit(1)
            ->one();
    }
}
