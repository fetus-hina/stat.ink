<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m230301_082902_gloopsuits extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $data = $this->getData();

        $this->batchInsert(
            '{{%salmon_uniform3}}',
            ['key', 'name', 'rank'],
            array_map(
                fn (string $name, int $rank): array => [
                    self::name2key3($name),
                    $name,
                    $rank,
                ],
                array_keys($data),
                array_values($data),
            ),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%salmon_uniform3}}', [
            'key' => array_map(
                fn (string $name): string => self::name2key3($name),
                $this->getData(),
            ),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_uniform3}}',
        ];
    }

    /**
     * @return array<string, int>
     */
    private function getData(): array
    {
        return [
            'Orange Gloopsuit' => 200,
            'Black Gloopsuit' => 210,
            'Yellow Gloopsuit' => 220,
            'Brown Gloopsuit' => 230,
        ];
    }
}
