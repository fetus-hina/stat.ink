<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m230831_105325_uniform3 extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%salmon_uniform3}}', ['key', 'name', 'rank'], [
            [
                self::name2key3('Polka-Dot Slopsuit'),
                'Polka-Dot Slopsuit',
                300,
            ],
            [
                self::name2key3('Camo Slopsuit'),
                'Camo Slopsuit',
                310,
            ],
            [
                self::name2key3('Koi Slopsuit'),
                'Koi Slopsuit',
                320,
            ],
        ]);

        $this->batchInsert('{{%salmon_uniform3_alias}}', ['uniform_id', 'key'], [
            [
                $this->key2id('{{%salmon_uniform3}}', self::name2key3('Polka-Dot Slopsuit')),
                '12',
            ],
            [
                $this->key2id('{{%salmon_uniform3}}', self::name2key3('Camo Slopsuit')),
                '13',
            ],
            [
                $this->key2id('{{%salmon_uniform3}}', self::name2key3('Koi Slopsuit')),
                '14',
            ],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $ids = [
            $this->key2id('{{%salmon_uniform3}}', self::name2key3('Polka-Dot Slopsuit')),
            $this->key2id('{{%salmon_uniform3}}', self::name2key3('Camo Slopsuit')),
            $this->key2id('{{%salmon_uniform3}}', self::name2key3('Koi Slopsuit')),
        ];

        $this->delete('{{%salmon_uniform3_alias}}', ['uniform_id' => $ids]);
        $this->delete('{{%salmon_uniform3}}', ['id' => $ids]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_uniform3}}',
            '{{%salmon_uniform3_alias}}',
        ];
    }
}
