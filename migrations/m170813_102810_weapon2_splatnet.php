<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170813_102810_weapon2_splatnet extends Migration
{
    public function up()
    {
        $this->addColumn('weapon2', 'splatnet', 'INTEGER NULL');
        $map = [
            0 => 'bold',
            10 => 'wakaba',
            20 => 'sharp',
            30 => 'promodeler_mg',
            31 => 'promodeler_rg',
            40 => 'sshooter',
            41 => 'sshooter_collabo',
            45 => 'heroshooter_replica',
            50 => '52gal',
            60 => 'nzap85',
            70 => 'prime',
            80 => '96gal',
            90 => 'jetsweeper',
            200 => 'nova',
            210 => 'hotblaster',
            211 => 'hotblaster_custom',
            215 => 'heroblaster_replica',
            230 => 'clashblaster',
            240 => 'rapid',
            300 => 'l3reelgun',
            310 => 'h3reelgun',
            1000 => 'carbon',
            1010 => 'splatroller',
            1011 => 'splatroller_collabo',
            1015 => 'heroroller_replica',
            1020 => 'dynamo',
            1030 => 'variableroller',
            1100 => 'pablo',
            1110 => 'hokusai',
            1115 => 'herobrush_replica',
            2010 => 'splatcharger',
            2011 => 'splatcharger_collabo',
            2015 => 'herocharger_replica',
            2020 => 'splatscope',
            2021 => 'splatscope_collabo',
            2030 => 'liter4k',
            2040 => 'liter4k_scope',
            2060 => 'soytuber',
            3000 => 'bucketslosher',
            3005 => 'heroslosher_replica',
            3010 => 'hissen',
            4000 => 'splatspinner',
            4010 => 'barrelspinner',
            4015 => 'herospinner_replica',
            5000 => 'sputtery',
            5010 => 'maneuver',
            5011 => 'maneuver_collabo',
            5015 => 'heromaneuver_replica',
            5030 => 'dualsweeper',
            6000 => 'parashelter',
            6005 => 'heroshelter_replica',
        ];
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($map as $id => $key) {
            $this->update('weapon2', ['splatnet' => $id], ['key' => $key]);
        }
        $transaction->commit();
    }

    public function down()
    {
        $this->dropColumn('weapon2', 'splatnet');
    }
}
