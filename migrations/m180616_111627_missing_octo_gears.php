<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;
use yii\db\Query;

class m180616_111627_missing_octo_gears extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $this->upGear2(
            static::name2key('Octoleet Goggles'),
            'Octoleet Goggles',
            'headgear',
            static::name2key('Grizzco'),
            null,
            21004,
        );
        $this->upGear2(
            static::name2key('Fresh Octo Tee'),
            'Fresh Octo Tee',
            'clothing',
            static::name2key('Cuttlegear'),
            static::name2key('Ink Saver (Sub)'),
            3,
        );

        $brand = (new Query())
            ->select(['id'])
            ->from('brand2')
            ->where(['key' => static::name2key('Grizzco')])
            ->scalar();

        $this->update(
            'gear2',
            ['brand_id' => $brand],
            [
                'key' => [
                    static::name2key('Octoleet Armor'),
                    static::name2key('Octoleet Boots'),
                ],
            ],
        );
    }

    public function safeDown()
    {
        $this->downGear2(static::name2key('Octoleet Goggles'));
        $this->downGear2(static::name2key('Fresh Octo Tee'));

        $brand = (new Query())
            ->select(['id'])
            ->from('brand2')
            ->where(['key' => static::name2key('Cuttlegear')])
            ->scalar();

        $this->update(
            'gear2',
            ['brand_id' => $brand],
            [
                'key' => [
                    static::name2key('Octoleet Armor'),
                    static::name2key('Octoleet Boots'),
                ],
            ],
        );
    }
}
