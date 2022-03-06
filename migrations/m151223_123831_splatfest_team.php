<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use app\models\Splatfest;
use yii\db\Migration;

class m151223_123831_splatfest_team extends Migration
{
    public function up()
    {
        $this->createTable('team', [
            'id'        => 'INTEGER NOT NULL PRIMARY KEY',
            'name'      => 'VARCHAR(8) NOT NULL',
            'leader'    => 'VARCHAR(8) NOT NULL',
        ]);
        $this->batchInsert('team', ['id', 'name', 'leader'], [
            [ 1, 'Alpha', 'Callie' ],
            [ 2, 'Bravo', 'Marie' ],
        ]);

        $this->createTable('splatfest_team', [
            'fest_id'   => 'INTEGER NOT NULL',
            'team_id'   => 'INTEGER NOT NULL',
            'name'      => 'VARCHAR(32) NOT NULL',
            'color_hue' => 'INTEGER NULL',
        ]);
        $this->addPrimaryKey('pk_splatfest_team', 'splatfest_team', ['fest_id', 'team_id']);
        $this->addForeignKey('fk_splatfest_team_1', 'splatfest_team', 'fest_id', 'splatfest', 'id');
        $this->addForeignKey('fk_splatfest_team_2', 'splatfest_team', 'team_id', 'team', 'id');

        $jp = Region::findOne(['key' => 'jp'])->id;
        $this->batchInsert(
            'splatfest_team',
            ['fest_id', 'team_id', 'name', 'color_hue'],
            [
                // 色は実績に基づく
                [$this->fest($jp, 7), 1, '愛',         332],
                [$this->fest($jp, 7), 2, 'おカネ',      34],
                [$this->fest($jp, 8), 1, '山の幸',     346],
                [$this->fest($jp, 8), 2, '海の幸',     166],
                // 色は推定
                [$this->fest($jp, 9), 1, '赤いきつね', 335],
                [$this->fest($jp, 9), 2, '緑のたぬき', 155],
            ]
        );
    }

    public function down()
    {
        $this->dropTable('splatfest_team');
        $this->dropTable('team');
    }

    private function fest($regionId, $festOrder)
    {
        $cond = [
            'region_id' => $regionId,
            'order' => $festOrder,
        ];
        return Splatfest::findOne($cond)->id;
    }
}
