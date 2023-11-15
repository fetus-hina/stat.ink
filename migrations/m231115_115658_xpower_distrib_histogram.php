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
use yii\db\Expression;

final class m231115_115658_xpower_distrib_histogram extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);

        $this->addColumns('{{%stat_x_power_distrib_abstract3}}', [
            'histogram_width' => $this->integer()->null(),
        ]);

        $this->update(
            '{{%stat_x_power_distrib_abstract3}}',
            [
                'histogram_width' => new Expression(
                    vsprintf('HISTOGRAM_WIDTH(%s, %s::NUMERIC)', [
                        $db->quoteColumnName('users'),
                        $db->quoteColumnName('stddev'),
                    ]),
                ),
            ],
        );

        $this->createTable('{{%stat_x_power_distrib_histogram3}}', [
            'season_id' => $this->pkRef('{{%season3}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
            'class_value' => $this->integer()->notNull(),
            'users' => $this->bigInteger()->notNull(),
            'PRIMARY KEY ([[season_id]], [[rule_id]], [[class_value]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_x_power_distrib_histogram3}}');
        $this->dropColumn('{{%stat_x_power_distrib_abstract3}}', 'histogram_width');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_x_power_distrib_abstract3}}',
            '{{%stat_x_power_distrib_histogram3}}',
        ];
    }
}
