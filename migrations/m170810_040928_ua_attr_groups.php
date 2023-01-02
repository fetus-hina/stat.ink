<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170810_040928_ua_attr_groups extends Migration
{
    public function safeUp()
    {
        $this->insert('agent_group', ['name' => 'SquidTracks']);

        $ikaRec = $this->getGroupId('IkaRec');
        $squidTracks = $this->getGroupId('SquidTracks');

        $this->batchInsert('agent_group_map', ['group_id', 'agent_name'], [
            [$ikaRec, 'IkaRec2'],
            [$squidTracks, 'SquidTracks'],
            [$squidTracks, 'SplatTrack'],
        ]);
    }

    public function safeDown()
    {
        $this->delete('agent_group_map', ['name' => [
            'IkaRec2',
            'SquidTracks',
            'SplatTrack',
        ]]);
        $this->delete('agent_group', ['name' => 'SquidTracks']);
    }

    private function getGroupId(string $name): int
    {
        $ret = (new Query())
            ->select('id')
            ->from('agent_group')
            ->where(['name' => $name])
            ->scalar();
        if (!$ret) {
            throw new Exception('Invalid UA group name: ' . $name);
        }
        return (int)$ret;
    }
}
