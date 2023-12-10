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

final class m231210_114616_x_matching_group_version3_season extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = TypeHelper::instanceOf($this->db, Connection::class);

        $this->addColumns('{{%x_matching_group_version3}}', [
            'minimum_season_id' => (string)$this->pkRef('{{%season3}}')->null(),
        ]);

        $this->update(
            '{{%x_matching_group_version3}}',
            [
                'minimum_season_id' => new Expression(
                    vsprintf('(CASE %s %s END)', [
                        $db->quoteColumnName('minimum_version'),
                        implode(' ', [
                            vsprintf('WHEN %s THEN %s', [
                                $db->quoteValue('2.0.0'),
                                $db->quoteValue(
                                    $this->key2id('{{%season3}}', 'season202212'),
                                ),
                            ]),
                            vsprintf('WHEN %s THEN %s', [
                                $db->quoteValue('6.0.0'),
                                $db->quoteValue(
                                    $this->key2id('{{%season3}}', 'season202312'),
                                ),
                            ]),
                        ]),
                    ]),
                ),
            ],
        );

        $this->alterColumn(
            '{{%x_matching_group_version3}}',
            'minimum_season_id',
            (string)$this->integer()->notNull(),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%x_matching_group_version3}}', 'minimum_season_id');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%x_matching_group_version3}}',
        ];
    }
}
