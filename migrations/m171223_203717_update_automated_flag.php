<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171223_203717_update_automated_flag extends Migration
{
    public function safeUp()
    {
        $sql = implode(' ', [
            'UPDATE {{battle2}}',
            'SET [[is_automated]] = TRUE',
            'FROM {{agent}}',
            'WHERE ( {{battle2}}.[[agent_id]] = {{agent}}.[[id]] )',
            'AND ( {{battle2}}.[[is_automated]] = FALSE )',
            sprintf(
                'AND ( {{agent}}.[[name]] IN ( %s ) )',
                implode(
                    ', ',
                    array_map(
                        fn (string $name): string => $this->db->quoteValue($name),
                        [
                            'splatnet2statink',
                            'SquidTracks',
                            'SplatTrack',
                        ],
                    ),
                ),
            ),
        ]);
        $this->execute($sql);
    }

    public function safeDown()
    {
    }
}
