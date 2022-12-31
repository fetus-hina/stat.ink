<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use yii\db\Migration;

class m151219_093250_splatfest extends Migration
{
    public function up()
    {
        $this->createTable('splatfest', [
            'id'        => $this->primaryKey(),
            'region_id' => $this->integer()->notNull(),
            'name'      => $this->string(64)->notNull(),
            'start_at'  => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
            'end_at'    => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        ]);
        $this->addForeignKey('fk_splatfest_1', 'splatfest', 'region_id', 'region', 'id');

        $jp = Region::findOne(['key' => 'jp'])->id;
        $this->batchInsert('splatfest', ['region_id', 'name', 'start_at', 'end_at'], [
            [ $jp, 'ごはん vs パン',                '2015-06-13 18:00:00+09', '2015-06-14 18:00:00+09' ],
            [ $jp, '赤いきつね vs 緑のたぬき',      '2015-07-03 15:00:00+09', '2015-07-04 15:00:00+09' ],
            [ $jp, 'レモンティー vs ミルクティー',  '2015-07-25 15:00:00+09', '2015-07-26 15:00:00+09' ],
            [ $jp, 'キリギリス vs アリ',            '2015-08-22 12:00:00+09', '2015-08-23 12:00:00+09' ],
            [ $jp, 'ボケ vs ツッコミ',              '2015-09-12 12:00:00+09', '2015-09-13 12:00:00+09' ],
            [ $jp, 'イカ vs タコ',                  '2015-10-10 09:00:00+09', '2015-10-11 09:00:00+09' ],
            [ $jp, '愛 vs おカネ',                  '2015-10-31 09:00:00+09', '2015-11-01 09:00:00+09' ],
            [ $jp, '山の幸 vs 海の幸',              '2015-11-21 12:00:00+09', '2015-11-22 12:00:00+09' ],
            [ $jp, '赤いきつね vs 緑のたぬき',      '2015-12-26 09:00:00+09', '2015-12-27 09:00:00+09' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('splatfest');
    }
}
