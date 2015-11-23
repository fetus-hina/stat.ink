<?php
use yii\db\Migration;

class m151123_181256_fix30 extends Migration
{
    public function safeUp()
    {
        // https://github.com/fetus-hina/stat.ink/issues/30
        // "Bamboozler 14 Mk II" が "mboozler 14 Mk II" になってしまっている問題
        $this->update(
            'death_reason',
            [ 'name' => 'Bamboozler 14 Mk II' ],
            [ 'key' => 'bamboo14mk2' ]
        );
    }

    // このコードは絶対に必要ない
    public function safeDown()
    {
        $this->update(
            'death_reason',
            [ 'name' => 'mboozler 14 Mk II' ],
            [ 'key' => 'bamboo14mk2' ]
        );
    }
}
