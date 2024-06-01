<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m240601_070119_slopsuits extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        foreach ($this->getData() as [$name, $splatnet, $rank]) {
            $key = $this->name2key3($name);

            $this->insert('{{%salmon_uniform3}}', [
                'key' => $key,
                'name' => $name,
                'rank' => $rank,
            ]);

            $this->insert('{{%salmon_uniform3_alias}}', [
                'uniform_id' => $this->key2id('{{%salmon_uniform3}}', $key),
                'key' => $splatnet,
            ]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        foreach ($this->getData() as [$name]) {
            $id = $this->key2id('{{%salmon_uniform3}}', $this->name2key3($name));

            $this->delete('{{%salmon_uniform3_alias}}', ['uniform_id' => $id]);
            $this->delete('{{%salmon_uniform3}}', ['id' => $id]);
        }

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

    /**
     * @return array{string, string, int}[]
     */
    private function getData(): array
    {
        return [
            ['Silver Slopsuit', '18', 500],
            ['Gold Slopsuit', '19', 510],
            ['Iridescent Slopsuit', '20', 520],
        ];
    }
}
