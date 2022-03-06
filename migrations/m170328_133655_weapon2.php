<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m170328_133655_weapon2 extends Migration
{
    public function up()
    {
        $this->createTable('weapon2', [
            'id'            => $this->primaryKey(),
            'key'           => $this->apiKey(),
            'type_id'       => $this->pkRef('weapon_type2'),
            'subweapon_id'  => $this->pkRef('subweapon2'),
            'special_id'    => $this->pkRef('special2'),
            'name'          => $this->string(32)->notNull()->unique(),
            'canonical_id'  => $this->pkRef('weapon2'),
            'main_group_id' => $this->pkRef('weapon2'),
        ]);
        $transaction = Yii::$app->db->beginTransaction();
        $this->safeUp();
        $transaction->commit();
    }

    public function safeUp()
    {
        $type = $this->getTypes();
        $sub = $this->getSubweapons();
        $special = $this->getSpecials();
        $this->insert('weapon2', [
            'type_id'       => $type['shooter'],
            'key'           => 'sshooter',
            'name'          => 'Splattershot',
            'subweapon_id'  => $sub['quickbomb'],
            'special_id'    => $special['missile'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
        $this->insert('weapon2', [
            'type_id'       => $type['shooter'],
            'key'           => 'manueuver',
            'name'          => 'Splat Dualies',
            'subweapon_id'  => $sub['curlingbomb'],
            'special_id'    => $special['jetpack'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
        $this->insert('weapon2', [
            'type_id'       => $type['roller'],
            'key'           => 'splatroller',
            'name'          => 'Splat Roller',
            'subweapon_id'  => $sub['kyubanbomb'],
            'special_id'    => $special['chakuchi'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
        $this->insert('weapon2', [
            'type_id'       => $type['charger'],
            'key'           => 'splatcharger',
            'name'          => 'Splat Charger',
            'subweapon_id'  => $sub['splashbomb'],
            'special_id'    => $special['presser'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
    }

    public function down()
    {
        $this->dropTable('weapon2');
    }

    protected function getTypes(): array
    {
        return $this->getData('weapon_type2');
    }

    protected function getSubweapons(): array
    {
        return $this->getData('subweapon2');
    }

    protected function getSpecials(): array
    {
        return $this->getData('special2');
    }

    protected function getData(string $table, string $key = 'key', string $id = 'id'): array
    {
        return ArrayHelper::map(
            (new Query())->select([$key, $id])->from($table)->all(),
            $key,
            $id
        );
    }
}
