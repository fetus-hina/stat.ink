<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m221109_133833_salmon_title3 extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_title3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'name' => $this->string(64)->notNull(),
            'rank' => $this->integer()->unique(),
        ]);

        $this->createTable('{{%salmon_title3_alias}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'title_id' => $this->pkRef('{{%salmon_title3}}')->notNull(),
        ]);

        $data = [
            'Apprentice', // 0? (unverified)
            'Part-Timer',
            'Go-Getter',
            'Overachiever',
            'Profreshional',
            'Profreshional +1',
            'Profreshional +2',
            'Profreshional +3', // 7
            'Eggsecutive VP', // 8
        ];

        $this->batchInsert('{{%salmon_title3}}', ['key', 'name', 'rank'], array_map(
            fn (string $name, int $i): array => [
                self::name2key3($name === 'Profreshional' ? 'Profreshional +0' : $name),
                $name,
                100 + $i * 10,
            ],
            $data,
            range(0, count($data) - 1),
        ));

        // でんせつが coopgrade-8, たつじん3が同7だったので$iをそのままaliasとする
        $this->batchInsert('{{%salmon_title3_alias}}', ['key', 'title_id'], array_map(
            fn (string $name, int $i): array => [
                (string)$i,
                $this->key2id(
                    '{{%salmon_title3}}',
                    self::name2key3($name === 'Profreshional' ? 'Profreshional +0' : $name),
                ),
            ],
            $data,
            range(0, count($data) - 1),
        ));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables(['{{%salmon_title3_alias}}', '{{%salmon_title3}}']);

        return true;
    }
}
