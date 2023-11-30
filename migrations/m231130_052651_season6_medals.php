<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;

final class m231130_052651_season6_medals extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);

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
        foreach (array_keys($this->getData()) as $canonicalName) {
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
            '#1 Splattercolor Screen User' => [
                '#1 Splattercolor Screen User',
                '1. Platz: Unsichtbarriere',
                'Cortina ingannevole: asso nº 1',
                'N.º 1 en muro marmoleado',
                'N.º 1 en pantintalla',
                'PLED-scherm gebruikt: nr. 1',
                'Мастер заливного барьера',
                '№ 1 de la barrière barbouillée',
                'スミナガシート No.1',
                '浮墨幕墙 No.1',
                '浮墨幕牆 No.1',
                '스플래터컬러 스크린 No.1',
            ],
            '#1 Triple Splashdown User' => [
                '#1 Triple Splashdown User',
                '1. Platz: Tri-Tintenschock',
                'N.º 1 en clavado triple',
                'N.º 1 en puñetazos explosivos',
                'Triplo vernischianto: asso nº 1',
                'Ultralanding gebruikt: nr. 1',
                'Мастер тройного мегаплюха',
                '№ 1 du triple choc chromatique',
                'ウルトラチャクチ No.1',
                '終極著陸 No.1',
                '终极着陆 No.1',
                '울트라 착지 No.1',
            ],
        ];
    }
}
