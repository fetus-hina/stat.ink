<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\SplatoonVersion;
use yii\db\Migration;

class m160411_135803_weapon_attack extends Migration
{
    public function up()
    {
        $this->createTable('weapon_attack', [
            'id' => $this->primaryKey(),
            'main_weapon_id' => $this->integer()->notNull(),
            'version_id' => $this->integer()->notNull()->defaultValue(
                SplatoonVersion::findOne(['tag' => '1.0.0'])->id,
            ),
            'damage' => $this->decimal(4, 1)->notNull(), // 999.9
        ]);
        $this->createIndex('ix_weapon_attack_1', 'weapon_attack', ['main_weapon_id', 'version_id'], true);
        $this->addForeignKey('fk_weapon_attack_1', 'weapon_attack', 'main_weapon_id', 'weapon', 'id');
        $this->addForeignKey('fk_weapon_attack_2', 'weapon_attack', 'version_id', 'splatoon_version', 'id');
    }

    public function down()
    {
        $this->dropTable('weapon_attack');
    }
}
