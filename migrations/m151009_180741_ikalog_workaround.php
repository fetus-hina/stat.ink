<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Lobby;
use yii\db\Migration;

class m151009_180741_ikalog_workaround extends Migration
{
    public function safeUp()
    {
        $lobbyStandard = Lobby::findOne(['key' => 'standard'])->id;
        $lobbyFest = Lobby::findOne(['key' => 'fest'])->id;

        $this->update('battle', ['lobby_id' => $lobbyStandard], [
            'lobby_id' => $lobbyFest,
            'gender_id' => null,
            'fest_title_id' => null,
        ]);
    }

    public function safeDown()
    {
    }
}
