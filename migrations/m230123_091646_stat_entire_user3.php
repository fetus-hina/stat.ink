<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Command;
use yii\db\Query;

final class m230123_091646_stat_entire_user3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Command);

        $this->execute(
            vsprintf('SET LOCAL timezone TO %s', [
                $db->quoteValue('Etc/UTC'),
            ]),
        );

        $this->execute(
            'CREATE INDEX battle3_created_at ON {{%battle3}} ([[created_at]]) ' .
            'WHERE [[is_deleted]] = FALSE',
        );

        $today = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
            ->setTimestamp($_SERVER['REQUEST_TIME'])
            ->setTime(0, 0, 0);

        $this->createTable('{{%stat_entire_user3}}', [
            'date' => $this->date()->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'users' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[date]])',
        ]);

        $this->execute(
            vsprintf('INSERT INTO %s %s', [
                $db->quoteTableName('{{%stat_entire_user3}}'),
                (new Query())
                    ->select([
                        'date' => '([[created_at]]::DATE)',
                        'battles' => 'COUNT(*)',
                        'users' => 'COUNT(DISTINCT [[user_id]])',
                    ])
                    ->from('{{%battle3}}')
                    ->andWhere(['is_deleted' => false])
                    ->andWhere(['<', 'created_at', $today->format(DateTimeInterface::ATOM)])
                    ->groupBy(['([[created_at]]::DATE)'])
                    ->createCommand($db)
                    ->rawSql,
            ]),
        );

        $this->createTable('{{%stat_entire_salmon3}}', [
            'date' => $this->date()->notNull(),
            'jobs' => $this->bigInteger()->notNull(),
            'users' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[date]])',
        ]);

        $this->execute(
            vsprintf('INSERT INTO %s %s', [
                $db->quoteTableName('{{%stat_entire_salmon3}}'),
                (new Query())
                    ->select([
                        'date' => '([[created_at]]::DATE)',
                        'jobs' => 'COUNT(*)',
                        'users' => 'COUNT(DISTINCT [[user_id]])',
                    ])
                    ->from('{{%salmon3}}')
                    ->andWhere(['is_deleted' => false])
                    ->andWhere(['<', 'created_at', $today->format(DateTimeInterface::ATOM)])
                    ->groupBy(['([[created_at]]::DATE)'])
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
        $this->dropTables([
            '{{%stat_entire_user3}}',
            '{{%stat_entire_salmon3}}',
        ]);

        $this->dropIndex('battle3_created_at', '{{%battle3}}');

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%battle3}}',
            '{{%stat_entire_salmon3}}',
            '{{%stat_entire_user3}}',
        ];
    }
}
