<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Lobby;
use yii\db\Migration;

class m151010_131258_ikalog_workaround2 extends Migration
{
    public function safeUp()
    {
        $lobbyStandard = Lobby::findOne(['key' => 'standard'])->id;
        $lobbyFest = Lobby::findOne(['key' => 'fest'])->id;

        $this->update(
            'battle',
            ['lobby_id' => $lobbyFest],
            implode(' AND ', [
                "lobby_id = {$lobbyStandard}",
                'gender_id IS NOT NULL',
                'fest_title_id IS NOT NULL',
            ])
        );
    }

    public function safeDown()
    {
    }
}
