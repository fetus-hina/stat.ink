<?php
use yii\db\Migration;

class m160416_082708_l10n_es extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('language', [ 'lang', 'name', 'name_en' ], [
            [ 'es-ES', 'Español (EU)', 'Spanish (EU)' ],
            [ 'es-MX', 'Español (LA)', 'Spanish (LA)' ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('language', ['lang' => ['es-ES', 'es-MX']]);
    }
}
