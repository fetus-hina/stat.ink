<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m200131_080911_glicko2 extends Migration
{
    public function safeUp()
    {
        $this->createTable('weapon2_rating_glicko2', [
            'rule_id' => $this->pkRef('rule2')->notNull(),
            'weapon_id' => $this->pkRef('weapon2')->notNull(),
            'period' => $this->integer()->notNull(),
            'rating' => $this->double()->notNull(),
            'deviation' => $this->double()->notNull(),
            'PRIMARY KEY ([[rule_id]], [[weapon_id]], [[period]])',
        ]);

        $index = [
            'rule_id' => SORT_ASC,
            'period' => SORT_DESC,
            'rating' => SORT_DESC,
            'deviation' => SORT_ASC,
        ];

        $this->execute(vsprintf('CREATE INDEX [[ix_%1$s_%3$s]] ON {{%1$s}} (%2$s)', [
            'weapon2_rating_glicko2',
            implode(', ', array_map(
                function (string $column, int $order): string {
                    return vsprintf('[[%s]] %s', [
                        $column,
                        $order === SORT_DESC ? 'DESC' : 'ASC',
                    ]);
                },
                array_keys($index),
                array_values($index),
            )),
            substr(
                implode('_', array_map(
                    function (string $column): string {
                        return preg_replace('/[^a-z0-9]+/', '', $column);
                    },
                    array_keys($index),
                )),
                0,
                63 - (4 + strlen('weapon2_rating_glicko2'))
            ),
        ]));
        $this->createIndex('ix_battle2_period', 'battle2', 'period');
    }

    public function safeDown()
    {
        $this->dropIndex('ix_battle2_period', 'battle2');
        $this->dropTable('weapon2_rating_glicko2');
    }
}
