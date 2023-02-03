<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\helpers\ArrayHelper;

final class m230203_005352_medal_canonical3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%medal_canonical3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'gold' => $this->boolean()->notNull(),
            'name' => $this->string(64)->notNull(),
        ]);

        $data = ArrayHelper::getColumn(
            $this->getData(),
            fn (string $text): array => [
                self::medalKey($text),
                self::isGold($text),
                $text,
            ],
        );
        $this->batchInsert('{{%medal_canonical3}}', ['key', 'gold', 'name'], $data);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%medal_canonical3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%medal_canonical3}}',
        ];
    }

    /**
     * @return string[]
     */
    private function getData(): array
    {
        return ArrayHelper::sort(
            [
                '#1 Base Defender',
                '#1 Big Bubbler User',
                '#1 Booyah Bomb User',
                '#1 Checkpoint Breaker',
                '#1 Clam Carrier',
                '#1 Clam Stopper',
                '#1 Crab Tank User',
                '#1 Damage Taker',
                '#1 Enemy-Base Inker',
                '#1 Enemy Splatter',
                '#1 Ground Traveler',
                '#1 Home-Base Inker',
                '#1 Ink Consumer',
                '#1 Inkjet User',
                '#1 Ink Storm User',
                '#1 Ink Vac User',
                '#1 Killer Wail 5.1 User',
                '#1 Overall Splatter',
                '#1 Popular Target',
                '#1 Rainmaker Carrier',
                '#1 Rainmaker Stopper',
                '#1 Reefslider User',
                '#1 Score Booster',
                '#1 Splat Assister',
                '#1 Splat Zone Guard',
                '#1 Splat Zone Hero',
                '#1 Splat Zone Inker',
                '#1 Super Jump Spot',
                '#1 Tacticooler User',
                '#1 Tenta Missiles User',
                '#1 Tower Stopper',
                '#1 Triple Inkstrike User',
                '#1 Trizooka User',
                '#1 Turf Inker',
                '#1 Ultra Stamp User',
                '#1 Wave Breaker User',
                '#1 Zipcaster User',
                '#2 Clam Carrier',
                '#2 Enemy-Base Inker',
                '#2 Enemy Splatter',
                '#2 Home-Base Inker',
                '#2 Overall Splatter',
                '#2 Popular Target',
                '#2 Score Booster',
                '#2 Splat Assister',
                '#2 Splat Zone Guard',
                '#2 Splat Zone Inker',
                '#2 Super Jump Spot',
                '#2 Turf Inker',
                'First Splat!',
                'Record-Score Setter',
            ],
            fn (string $a, string $b): int => strnatcmp(self::medalKey($a), self::medalKey($b)),
        );
    }

    private static function isGold(string $name): bool
    {
        return in_array(
            strict: true,
            needle: $name,
            haystack: [
                '#1 Clam Carrier',
                '#1 Enemy Splatter',
                '#1 Enemy-Base Inker',
                '#1 Home-Base Inker',
                '#1 Overall Splatter',
                '#1 Popular Target',
                '#1 Score Booster',
                '#1 Splat Assister',
                '#1 Splat Zone Guard',
                '#1 Splat Zone Inker',
                '#1 Super Jump Spot',
                '#1 Turf Inker',
                'Record-Score Setter',
            ],
        );
    }

    private static function medalKey(string $name): string
    {
        return substr(
            trim(
                (string)preg_replace(
                    '/[^a-z0-9]+/',
                    '_',
                    strtolower(
                        preg_replace('/^#(\d)\s+(.+)$/', '$2 No.$1', $name),
                    ),
                ),
                '_',
            ),
            0,
            32,
        );
    }
}
