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

final class m230930_180652_boss_salmonid_order extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $data = [
            110 => 'bakudan',
            120 => 'katapad',
            130 => 'teppan',
            140 => 'hebi',
            150 => 'tower',
            160 => 'mogura',
            170 => 'koumori',
            180 => 'hashira',
            190 => 'diver',
            200 => 'tekkyu',
            210 => 'nabebuta',
            220 => 'kin_shake',
            230 => 'grill',
            240 => 'doro_shake',
            900 => 'shake_copter',
            910 => 'hakobiya',
        ];

        $this->addColumn(
            'salmon_boss3',
            'rank',
            (string)$this->integer(),
        );

        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $this->execute(
            vsprintf('UPDATE %s SET %s = %s', [
                $db->quoteTableName('{{%salmon_boss3}}'),
                $db->quoteColumnName('rank'),
                vsprintf('(CASE %s %s END)', [
                    $db->quoteColumnName('key'),
                    implode(
                        ' ',
                        array_map(
                            fn (int $rank, string $key): string => vsprintf('WHEN %s THEN %d', [
                                $db->quoteValue($key),
                                $rank,
                            ]),
                            array_keys($data),
                            array_values($data),
                        ),
                    ),
                ]),
            ]),
        );

        $this->alterColumn(
            '{{%salmon_boss3}}',
            'rank',
            (string)$this->integer()->notNull(),
        );

        $this->createIndex(
            'salmon_boss3_rank',
            'salmon_boss3',
            ['rank'],
            true,
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('salmon_boss3', 'rank');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            'salmon_boss3',
        ];
    }
}
