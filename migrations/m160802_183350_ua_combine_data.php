<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\models\AgentGroup;
use yii\db\Migration;

class m160802_183350_ua_combine_data extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('agent_group', ['name'], [
            ['IkaLog'],
            ['IkaRec'],
        ]);

        $ikalog = $this->findGroup('IkaLog');
        $ikarec = $this->findGroup('IkaRec');

        $this->batchInsert('agent_group_map', ['group_id', 'agent_name'], [
            [ $ikalog, 'IkaLog' ],
            [ $ikalog, 'TakoLog' ],
            [ $ikarec, 'IkaRec' ],
            [ $ikarec, 'IkaRecord' ],
            [ $ikarec, 'IkaRec-en' ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('agent_group_map');
        $this->delete('agent_group');
    }

    private function findGroup(string $name) : int
    {
        return (int)AgentGroup::findOne(['name' => $name])->id;
    }
}
