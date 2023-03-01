<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230301_000000_splatnet_ids extends Migration
{
    private const ID_MAP_MANTA = '18';
    private const ID_MAP_NAMPLA = '5';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [
                $this->key2id('{{%map3}}', 'manta'),
                self::ID_MAP_MANTA,
            ],
            [
                $this->key2id('{{%map3}}', 'nampla'),
                self::ID_MAP_NAMPLA,
            ],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete(
            '{{%map3_alias}}',
            [
                'key' => [self::ID_MAP_MANTA, self::ID_MAP_NAMPLA],
            ],
        );

        return true;
    }
}
