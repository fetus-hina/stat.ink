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
use yii\db\Query;

final class m230902_102400_salmon3_salmometer extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%stat_salmon3_salmometer}}', [
            'king_smell' => $this->integer()->notNull(),
            'jobs' => $this->bigInteger()->notNull(),
            'cleared' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[king_smell]])',
        ]);

        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $this->execute(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%stat_salmon3_salmometer}}'),
                implode(', ', [
                    $db->quoteColumnName('king_smell'),
                    $db->quoteColumnName('jobs'),
                    $db->quoteColumnName('cleared'),
                ]),
                (new Query())
                    ->select([
                        'king_smell' => '{{%salmon3}}.[[king_smell]]',
                        'jobs' => 'COUNT(*)',
                        'cleared' => vsprintf('SUM(%s)', [
                            vsprintf('CASE %s END', [
                                implode(' ', [
                                    vsprintf('WHEN %s.%s = 3 THEN 1', [
                                        $db->quoteTableName('{{%salmon3}}'),
                                        $db->quoteColumnName('clear_waves'),
                                    ]),
                                    'ELSE 0',
                                ]),
                            ]),
                        ]),
                    ])
                    ->from('{{%salmon3}}')
                    ->andWhere(['and',
                        [
                            '{{%salmon3}}.[[has_broken_data]]' => false,
                            '{{%salmon3}}.[[has_disconnect]]' => false,
                            '{{%salmon3}}.[[is_automated]]' => true,
                            '{{%salmon3}}.[[is_big_run]]' => false,
                            '{{%salmon3}}.[[is_deleted]]' => false,
                            '{{%salmon3}}.[[is_eggstra_work]]' => false,
                            '{{%salmon3}}.[[is_private]]' => false,
                            '{{%salmon3}}.[[title_before_id]]' => $this->key2id('{{%salmon_title3}}', 'eggsecutive_vp'),
                        ],
                        ['between', '{{%salmon3}}.[[clear_waves]]', 0, 3],
                        ['not', ['{{%salmon3}}.[[king_smell]]' => null]],
                    ])
                    ->groupBy(['{{%salmon3}}.[[king_smell]]'])
                    ->createCommand($db)
                    ->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_salmon3_salmometer}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_salmon3_salmometer}}',
        ];
    }
}
