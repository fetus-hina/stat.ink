<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;
use app\models\DeathReasonType;

class m151007_123045_death_reason extends Migration
{
    public function up()
    {
        $this->createTable('death_reason_type', [
            'id'    => $this->primaryKey(),
            'key'   => $this->string(32)->notNull()->unique(),
            'name'  => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('death_reason_type', ['key', 'name'], [
            [ 'main',       'Main Weapon' ],
            [ 'sub',        'Sub Weapon' ],
            [ 'special',    'Special' ],
            [ 'suicide',    'Out of Bounds' ],
            [ 'hoko',       'Rainmaker' ],
        ]);

        $this->createTable('death_reason', [
            'id'        => $this->primaryKey(),
            'type_id'   => $this->integer(),
            'key'       => $this->string(32)->notNull()->unique(),
            'name'      => $this->string(64)->notNull()->unique(),
        ]);
        $this->addForeignKey('fk_death_reson_1', 'death_reason', 'type_id', 'death_reason_type', 'id');

        $this->insert('death_reason', [
            'type_id' => null,
            'key' => 'unknown',
            'name' => 'Unknown',
        ]);
        $this->makeDeathReason('main', 'weapon');
        $this->makeDeathReason('sub', 'subweapon');
        $this->makeDeathReason('special', 'special');

        $suicide = DeathReasonType::findOne(['key' => 'suicide'])->id;
        $hoko = DeathReasonType::findOne(['key' => 'hoko'])->id;
        $this->batchInsert('death_reason', ['type_id', 'key', 'name'], [
            [ $suicide, 'oob', 'Out of Bounds' ],
            [ $suicide, 'drown', 'Drowning' ],
            [ $suicide, 'fall', 'Fall' ],
            [ $hoko, 'hoko_shot', 'Rainmaker Shot' ],
            [ $hoko, 'hoko_barrier', 'Rainmaker Shield' ],
            [ $hoko, 'hoko_inksplode', 'Rainmaker Inksplode' ],
        ]);

        $this->createTable('battle_death_reason', [
            'battle_id' => $this->integer()->notNull(),
            'reason_id' => $this->integer()->notNull(),
            'count' => $this->integer()->notNull(),
        ]);
        $this->addPrimaryKey('pk_battle_death_reason', 'battle_death_reason', ['battle_id', 'reason_id']);
        $this->addForeignKey('fk_battle_death_reason_1', 'battle_death_reason', 'battle_id', 'battle', 'id');
        $this->addForeignKey('fk_battle_death_reason_2', 'battle_death_reason', 'reason_id', 'death_reason', 'id');
    }

    public function down()
    {
        $this->dropTable('battle_death_reason');
        $this->dropTable('death_reason');
        $this->dropTable('death_reason_type');
    }

    private function makeDeathReason($type_key, $table)
    {
        $typeId = DeathReasonType::findOne(['key' => $type_key])->id;
        $select = "SELECT {$typeId} AS [[type_id]], [[key]], [[name]] FROM {{{$table}}} ORDER BY [[id]]";
        $sql = "INSERT INTO {{death_reason}} ( [[type_id]], [[key]], [[name]] ) " . $select;
        $this->execute($sql);
    }
}
