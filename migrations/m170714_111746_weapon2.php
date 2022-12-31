<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Expression;
use yii\db\Query;

class m170714_111746_weapon2 extends Migration
{
    public function safeUp()
    {
        $this->upSpecial();
        $this->upSushikora();
    }

    public function safeDown()
    {
        $this->downSpecial();
        $this->downSushikora();
    }

    private function upSpecial()
    {
        // {{{
        // 名前間違ってた
        $this->update(
            'special2',
            ['name' => 'Bomb Launcher'],
            ['key' => 'pitcher'],
        );

        // バブルランチャー
        $this->insert('special2', [
            'key' => 'bubble',
            'name' => 'Bubble Blower',
        ]);
        $this->insert('death_reason2', [
            'key' => 'bubble',
            'name' => 'Bubble Blower',
            'type_id' => (new Query())
                ->select('id')
                ->from('death_reason_type2')
                ->where(['key' => 'special'])
                ->scalar(),
            'special_id' => (new Query())
                ->select('id')
                ->from('special2')
                ->where(['key' => 'bubble'])
                ->scalar(),
        ]);
        // }}}
    }

    private function downSpecial()
    {
        // {{{
        $bubble = (new Query())
            ->select('id')
            ->from('special2')
            ->where(['key' => 'bubble'])
            ->scalar();
        $this->delete('death_reason2', ['special_id' => $bubble]);
        $this->delete('special2', ['id' => $bubble]);

        // 一応壊れていた名前も戻す
        $this->update(
            'special2',
            ['name' => 'Bubble Blower'],
            ['key' => 'pitcher'],
        );
        // }}}
    }

    private function upSushikora()
    {
        // {{{
        $shooter = (new Query())
            ->select('id')
            ->from('weapon_type2')
            ->where(['key' => 'shooter'])
            ->scalar();

        $bomb = (new Query())
            ->select('id')
            ->from('subweapon2')
            ->where(['key' => 'splashbomb'])
            ->scalar();

        $jetpack = (new Query())
            ->select('id')
            ->from('special2')
            ->where(['key' => 'jetpack'])
            ->scalar();

        $sshooter = (new Query())
            ->select('id')
            ->from('weapon2')
            ->where(['key' => 'sshooter'])
            ->scalar();

        $this->insert('weapon2', [
            'key'           => 'sshooter_collabo',
            'name'          => 'Tentatek Splattershot',
            'type_id'       => $shooter,
            'subweapon_id'  => $bomb,
            'special_id'    => $jetpack,
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => $sshooter,
        ]);

        $weapon = (new Query())
            ->select('id')
            ->from('weapon2')
            ->where(['key' => 'sshooter_collabo'])
            ->scalar();

        $this->insert('death_reason2', [
            'key' => 'sshooter_collabo',
            'name' => 'Tentatek Splattershot',
            'type_id' => (new Query())->select('id')->from('death_reason_type2')->where(['key' => 'main'])->scalar(),
            'weapon_id' => $weapon,
        ]);
        // }}}
    }

    private function downSushikora()
    {
        // {{{
        $weapon = (new Query())
            ->select('id')
            ->from('weapon2')
            ->where(['key' => 'sshooter_collabo'])
            ->scalar();
        $this->delete('death_reason2', ['weapon_id' => $weapon]);
        $this->delete('weapon2', ['id' => $weapon]);
        // }}}
    }
}
