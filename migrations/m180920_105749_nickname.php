<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m180920_105749_nickname extends Migration
{
    public function up()
    {
        $this->createTable('team_nickname2', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull()->unique(),
        ]);

        $this->addColumns('battle2', [
            'my_team_nickname_id' => $this->pkRef('team_nickname2')->null(),
            'his_team_nickname_id' => $this->pkRef('team_nickname2')->null(),
        ]);
    }

    public function down()
    {
        $this->dropColumns('battle2', ['my_team_nickname_id', 'his_team_nickname_id']);
        $this->dropTable('team_nickname2');
    }
}
