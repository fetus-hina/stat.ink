<?php
use yii\db\Migration;

class m160113_113141_en_gb extends Migration
{
    public function safeUp()
    {
        $this->update('language', [
            'name'      => 'English (NA)',
            'name_en'   => 'English (NA)',
        ], [
            'lang'      => 'en-US',
        ]);
        $this->insert('language', [
            'lang'      => 'en-GB',
            'name'      => 'English (EU/OC)',
            'name_en'   => 'English (EU/OC)',
        ]);
    }

    public function safeDown()
    {
        $this->delete('language', ['lang' => 'en-GB']);
        $this->update('language', [
            'name'      => 'English(US)',
            'name_en'   => 'English(US)',
        ], [
            'lang'      => 'en-US',
        ]);
    }
}
