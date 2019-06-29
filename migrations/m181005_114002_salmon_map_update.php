<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181005_114002_salmon_map_update extends Migration
{
    public function up()
    {
        $data = [
            'damu' => '/images/coop_stage/65c68c6f0641cc5654434b78a6f10b0ad32ccdee.png',
            'donburako' => '/images/coop_stage/e07d73b7d9f0c64e552b34a2e6c29b8564c63388.png',
            'shaketoba' => '/images/coop_stage/6d68f5baa75f3a94e5e9bfb89b82e7377e3ecd2c.png',
            'tokishirazu' => '/images/coop_stage/e9f7c7b35e6d46778cd3cbc0d89bd7e1bc3be493.png',
        ];

        $this->addColumn('salmon_map2', 'splatnet_hint', $this->string(255)->null());
        $this->db->transaction(function () use ($data): void {
            foreach ($data as $key => $path) {
                $this->update('salmon_map2', ['splatnet_hint' => $path], ['key' => $key]);
            }
            $this->update('salmon_map2', ['key' => 'dam'], ['key' => 'damu']);
        });
    }

    public function down()
    {
        $this->dropColumn('salmon_map2', 'splatnet_hint');
        $this->update('salmon_map2', ['key' => 'damu'], ['key' => 'dam']);
    }
}
