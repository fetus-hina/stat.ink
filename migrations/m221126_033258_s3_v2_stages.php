<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m221126_033258_s3_v2_stages extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $at = '2022-12-01T00:00:00+00:00';

        $this->batchInsert('{{%map3}}', ['key', 'name', 'short_name', 'release_at'], [
            ['hirame', 'Flounder Heights', 'Heights', $at],
            ['kusaya', 'Brinewater Springs', 'Springs', $at],
        ]);

        $hirame = $this->key2id('{{%map3}}', 'hirame');
        $kusaya = $this->key2id('{{%map3}}', 'kusaya');

        $this->batchInsert('{{%map3_alias}}', ['map_id', 'key'], [
            [$hirame, self::name2key3('Flounder Heights')],
            [$hirame, self::name2key3('Heights')],
            [$kusaya, self::name2key3('Brinewater Springs')],
            [$kusaya, self::name2key3('Springs')],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $ids = [
            $this->key2id('{{%map3}}', 'hirame'),
            $this->key2id('{{%map3}}', 'kusaya'),
        ];

        $this->delete('{{%map3_alias}}', ['map_id' => $ids]);
        $this->delete('{{%map3}}', ['id' => $ids]);

        return true;
    }

    public function vacuumTables(): array
    {
        return [
            '{{%map3}}',
            '{{%map3_alias}}',
        ];
    }
}
