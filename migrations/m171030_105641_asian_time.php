<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m171030_105641_asian_time extends Migration
{
    public function safeUp()
    {
        $this->upKorea();
        $this->upChina();
    }

    public function safeDown()
    {
        $this->downChina();
        $this->downKorea();
    }

    // korea {{{
    private function upKorea(): void
    {
        $this->update('timezone', ['name' => 'Japan & Korea Time'], ['identifier' => 'Asia/Tokyo']);
        $this->insert('country', ['key' => 'kr', 'name' => 'Korea']);
        $this->insert('timezone_country', [
            'timezone_id' => $this->timezone('Asia/Tokyo'),
            'country_id' => $this->country('kr'),
        ]);
    }

    private function downKorea(): void
    {
        $this->delete('timezone_country', [
            'timezone_id' => $this->timezone('Asia/Tokyo'),
            'country_id' => $this->country('kr'),
        ]);
        $this->delete('country', ['key' => 'kr']);
        $this->update('timezone', ['name' => 'Japan Time'], ['identifier' => 'Asia/Tokyo']);
    }

    // }}}

    // china and taiwan {{{
    private function upChina(): void
    {
        $this->batchInsert('country', ['key', 'name'], [['cn', 'China'], ['tw', 'Taiwan']]);
        $this->batchInsert('timezone', ['identifier', 'name', 'order', 'region_id', 'group_id'], [
            ['Asia/Shanghai', 'China & Taiwan Time', 3, $this->region('jp'), $this->group('East Asia')],
            ['Asia/Urumqi', 'Xinjiang Time', 4, $this->region('jp'), $this->group('East Asia')],
        ]);
        $this->batchInsert('timezone_country', ['timezone_id', 'country_id'], [
            [$this->timezone('Asia/Shanghai'), $this->country('cn')],
            [$this->timezone('Asia/Shanghai'), $this->country('tw')],
            [$this->timezone('Asia/Urumqi'), $this->country('cn')],
        ]);
    }

    private function downChina(): void
    {
        $this->delete('timezone_country', [
            'country_id' => [
                $this->country('cn'),
                $this->country('tw'),
            ],
        ]);
        $this->delete('timezone', ['identifier' => ['Asia/Shanghai', 'Asia/Urumqi']]);
        $this->delete('country', ['key' => ['cn', 'tw']]);
    }

    // }}}

    // get id {{{
    private function timezone(string $key): int
    {
        return $this->getIdByKey('timezone', $key, 'identifier');
    }

    private function country(string $key): int
    {
        return $this->getIdByKey('country', $key);
    }

    private function region(string $key): int
    {
        return $this->getIdByKey('region', $key);
    }

    private function group(string $name): int
    {
        return $this->getIdByKey('timezone_group', $name, 'name');
    }

    private function getIdByKey(
        string $table,
        string $value,
        string $key = 'key',
        string $id = 'id',
    ): int {
        $ret = (new Query())
            ->select(['id' => $id])
            ->from($table)
            ->where([$key => $value])
            ->limit(1)
            ->scalar();
        if ($ret === null) {
            throw new Exception('Could not find ID');
        }
        return (int)$ret;
    }

    // }}}
}
