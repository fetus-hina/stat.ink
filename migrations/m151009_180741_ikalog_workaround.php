<?php
use yii\db\Migration;
use app\models\Lobby;

class m151009_180741_ikalog_workaround extends Migration
{
    public function safeUp()
    {
        $lobbyStandard = Lobby::findOne(['key' => 'standard'])->id;
        $lobbyFest = Lobby::findOne(['key' => 'fest'])->id;

        $this->update('battle', [ 'lobby_id' => $lobbyStandard ], [
            'lobby_id' => $lobbyFest,
            'gender_id' => null,
            'fest_title_id' => null,
        ]);
    }

    public function safeDown()
    {
    }
}
