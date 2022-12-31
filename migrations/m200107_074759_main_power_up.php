<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

class m200107_074759_main_power_up extends Migration
{
    use AutoKey;

    public function up()
    {
        $status = parent::up();
        if ($status !== false) {
            $this->analyze('main_power_up2');
        }
    }

    public function safeUp()
    {
        $this->createTable('main_power_up2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(32)->notNull(),
            'name' => $this->string(64)->notNull(),
        ]);

        $data = $this->getInsertData();
        usort($data, fn (array $a, array $b): int => strcmp($a[0], $b[0]));
        $this->batchInsert('main_power_up2', ['key', 'name'], $data);
    }

    public function safeDown()
    {
        $this->dropTable('main_power_up2');
    }

    public function getInsertData(): array
    {
        return array_map(
            function (string $name): array {
                $name4key = $name;
                $name4key = preg_replace('/^Increases?\b/i', ' ', $name4key);
                $name4key = preg_replace('/\bbrella\b/i', ' ', $name4key);
                return [
                    static::name2key($name4key),
                    $name,
                ];
            },
            [
                'Increase brella canopy durability',
                'Increase bullet velocity',
                'Increase damage from higher grounds',
                'Increase damage',
                'Increase duration of firing',
                'Increase high-damage radius of explosions',
                'Increase ink coverage',
                'Increase movement speed',
                'Increase range',
                'Increase shot accuracy',
                'Speed up brella canopy regeneration',
            ],
        );
    }
}
