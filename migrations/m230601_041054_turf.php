<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;

final class m230601_041054_turf extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $this->execute(
            vsprintf('UPDATE %s SET area = %s', [
                $db->quoteTableName('{{%map3}}'),
                $this->createUpdateCases($db, [
                    'amabi' => 2591,
                    'chozame' => 2880,
                    'gonzui' => 2773,
                    'hirame' => 2591,
                    'kinmedai' => 2332,
                    'kombu' => 2326,
                    'kusaya' => 2045,
                    'mahimahi' => 1693,
                    'manta' => 2765,
                    'masaba' => 2501,
                    'mategai' => 2440,
                    'namero' => 2177,
                    'nampla' => 2020,
                    'sumeshi' => 3044,
                    'taraport' => 2274,
                    'yagara' => 2631,
                    'yunohana' => 2144,
                    'zatou' => 2275,
                ]),
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $this->execute(
            vsprintf('UPDATE %s SET area = %s', [
                $db->quoteTableName('{{%map3}}'),
                $this->createUpdateCases($db, [
                    'amabi' => 2610,
                    'chozame' => 2970,
                    'gonzui' => 2746,
                    'kinmedai' => 2363,
                    'mahimahi' => 1690,
                    'masaba' => 2477,
                    'mategai' => 2622,
                    'namero' => 2186,
                    'sumeshi' => 3045,
                    'yagara' => 2621,
                    'yunohana' => 2142,
                    'zatou' => 2265,
                ]),
            ]),
        );

        return true;
    }

    /**
     * @param array<string, positive-int|null> $data
     */
    private function createUpdateCases(Connection $db, array $data): string
    {
        return vsprintf('(CASE %s %s ELSE NULL END)', [
            $db->quoteColumnName('key'),
            implode(
                ' ',
                array_map(
                    fn (string $key, ?int $value): string => vsprintf('WHEN %s THEN %s', [
                        $db->quoteValue($key),
                        $value === null ? 'NULL' : (string)$value,
                    ]),
                    array_keys($data),
                    array_values($data),
                ),
            ),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3}}',
        ];
    }
}
