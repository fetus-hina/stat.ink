<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160107_101048_fest_title_i18n extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{fest_title}} ADD COLUMN [[name]] VARCHAR(32)');
        $t = Yii::$app->db->beginTransaction();
        $this->update('fest_title', ['name' => 'Fanboy/Fangirl'], ['id' => 1]);
        $this->update('fest_title', ['name' => 'Fiend'], ['id' => 2]);
        $this->update('fest_title', ['name' => 'Defender'], ['id' => 3]);
        $this->update('fest_title', ['name' => 'Champion'], ['id' => 4]);
        $this->update('fest_title', ['name' => 'King/Queen'], ['id' => 5]);
        $t->commit();
        $this->execute('ALTER TABLE {{fest_title}} ALTER COLUMN [[name]] SET NOT NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{fest_title}} DROP COLUMN [[name]]');
    }
}
