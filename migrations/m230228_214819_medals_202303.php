<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;
use yii\db\Connection;

final class m230228_214819_medals_202303 extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        foreach ($this->getData() as $canonicalName => $names) {
            $key = self::canonicalName2Key($canonicalName);
            $this->insert('{{%medal_canonical3}}', [
                'key' => $key,
                'gold' => false,
                'name' => $canonicalName,
            ]);

            $id = $this->key2id('{{%medal_canonical3}}', $key);
            $this->execute(
                vsprintf(
                    'INSERT INTO %s ( %s ) VALUES %s ON CONFLICT ON CONSTRAINT medal3_name_key DO UPDATE SET %s',
                    [
                        $db->quoteTableName('{{%medal3}}'),
                        implode(
                            ', ',
                            array_map(
                                fn (string $c): string => $db->quoteColumnName($c),
                                ['name', 'canonical_id'],
                            ),
                        ),
                        implode(
                            ', ',
                            array_map(
                                fn (string $name): string => vsprintf('(%s, %d)', [
                                    $db->quoteValue($name),
                                    $id,
                                ]),
                                $names,
                            ),
                        ),
                        vsprintf('%2$s = %1$s.%2$s', [
                            $db->quoteTableName('excluded'),
                            $db->quoteColumnName('canonical_id'),
                        ]),
                    ],
                ),
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        foreach ($this->getData() as $canonicalName => $names) {
            $id = $this->key2id('{{%medal_canonical3}}', self::canonicalName2Key($canonicalName));

            // should not delete from "medal3"
            $this->update(
                '{{%medal3}}',
                ['canonical_id' => null],
                ['canonical_id' => $id],
            );

            $this->delete('{{%medal_canonical3}}', ['id' => $id]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%medal_canonical3}}',
            '{{%medal3}}',
        ];
    }

    private static function canonicalName2Key(string $name): string
    {
        return self::name2key3(
            preg_replace(
                '/^#(\d) (.+)$/',
                '$2 No.$1',
                $name,
            ),
        );
    }

    /**
     * @return array<string, string[]>
     */
    private function getData(): array
    {
        return [
            '#1 Super Chump User' => [
                '#1 Super Chump User',
                '1. Platz: Bluffbomber',
                'Diversivo esplosivo: asso nº 1',
                'N.º 1 en multiseñuelos',
                'Voorschutter gebruikt: nr. 1',
                'Мастер суперпрыжатора',
                '№ 1 du Multi-leurres',
                '№ 1 du multi-leurres',
                'デコイチラシ No.1',
                '誘餌煙火 No.1',
                '诱饵烟花 No.1',
                '디코이 캐넌 No.1',
            ],
            '#1 Kraken Royale User' => [
                '#1 Kraken Royale User',
                '1. Platz: Tintentyrann',
                'Kraken reale: asso nº 1',
                'Monstermorfose gebruikt: nr. 1',
                'N.º 1 en calamar imperial',
                'Мастер тираннокракена',
                '№ 1 du Kraken royal',
                '№ 1 du kraken royal',
                'テイオウイカ No.1',
                '帝王魷魚 No.1',
                '帝王鱿鱼 No.1',
                '로열 크라켄 No.1',
            ],
        ];
    }
}
