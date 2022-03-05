<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170810_035817_add_ua_attrs extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('agent_attribute', ['name', 'is_automated', 'link_url'], [
            ['IkaRec-en', false, 'https://play.google.com/store/apps/details?id=ink.pocketgopher.ikarec'],
            ['stat.ink web client', false, 'https://stat.ink/'],
            ['IkaRec2', false, 'https://play.google.com/store/apps/details?id=com.syanari.merluza.ikarec2'],
            ['splatnet2statink', true, 'https://github.com/frozenpandaman/splatnet2statink'],
            ['SquidTracks', true, 'https://github.com/hymm/squid-tracks/'],
            ['SplatTrack', true, 'https://github.com/hymm/squid-tracks/'],
        ]);
    }

    public function safeDown()
    {
        $this->delete('agent_attribute', [
            'name' => [
                'IkaRec-en',
                'stat.ink web client',
                'IkaRec2',
                'splatnet2statink',
                'SquidTracks',
                'SplatTrack',
            ],
        ]);
    }
}
