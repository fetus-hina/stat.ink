<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170810_033541_agent_attribute_link extends Migration
{
    public function up()
    {
        $this->addColumn('agent_attribute', 'link_url', 'VARCHAR(256)');
        $this->update(
            'agent_attribute',
            ['link_url' => 'https://github.com/hasegaw/IkaLog/wiki/en_Home'],
            ['name' => ['IkaLog', 'TakoLog']],
        );
        $this->update(
            'agent_attribute',
            ['link_url' => 'https://play.google.com/store/apps/details?id=ink.pocketgopher.ikarec'],
            ['name' => ['IkaRec', 'IkaRecord']],
        );
    }

    public function down()
    {
        $this->dropColumn('agent_attribute', 'link_url');
    }
}
