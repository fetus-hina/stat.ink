<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author ShingoMisawa <33051481+shngmsw@users.noreply.github.com>
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m181106_083644_fix_jetsweeper_special extends Migration
{
    public function safeUp()
    {
        $this->update(
            'weapon2',
            ['special_id' => $this->getSpecialId('missile')],
            ['key' => 'jetsweeper'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'weapon2',
            ['special_id' => $this->getSpecialId('jetpack')],
            ['key' => 'jetsweeper'],
        );
    }

    private function getSpecialId(string $key): int
    {
        $query = (new Query())
            ->select(['id'])
            ->from('special2')
            ->where(['key' => $key])
            ->limit(1);

        if (!$value = $query->scalar()) {
            throw new \Exception("Unknown special: {$key}");
        }

        return (int)$value;
    }
}
