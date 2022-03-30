<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170720_112422_fix_weapon2 extends Migration
{
    public function safeUp()
    {
        $this->fix('manueuver', 'quickbomb', 'missile');
        $this->fix('sshooter', null, 'chakuchi');
        $this->fix('splatroller', 'curlingbomb', null);
    }

    public function safeDown()
    {
        $this->fix('manueuver', 'curlingbomb', 'jetpack');
        $this->fix('sshooter', null, 'missile');
        $this->fix('splatroller', 'kyubanbomb', null);
    }

    private function fix(
        string $weapon,
        ?string $sub = null,
        ?string $special = null
    ): void {
        $update = array_filter(
            [
                'subweapon_id' => $this->findId('subweapon2', $sub),
                'special_id' => $this->findId('special2', $special),
            ],
            fn ($v): bool => $v !== null,
        );
        if (!$update) {
            throw new Exception('No update field');
        }
        $this->update('weapon2', $update, ['key' => $weapon]);
    }

    private function findId(string $table, ?string $key): ?int
    {
        if ($key === null) {
            return null;
        }

        $id = (new Query())
            ->select('id')
            ->from($table)
            ->where(['key' => $key])
            ->limit(1)
            ->scalar();
        if ($id === false) {
            throw new Exception('Unknown ' . $table . ' ' . $key);
        }
        return (int)$id;
    }
}
