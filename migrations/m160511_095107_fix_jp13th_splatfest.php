<?php
use yii\db\Migration;
use app\models\Region;

class m160511_095107_fix_jp13th_splatfest extends Migration
{
    public function safeUp()
    {
        $this->update(
            'splatfest',
            ['name' => 'ツナマヨネーズ vs 紅しゃけ'],
            [
                'region_id' => Region::findOne(['key' => 'jp'])->id,
                'order' => 13,
            ]
        );
    }

    public function safeDown()
    {
    }
}
