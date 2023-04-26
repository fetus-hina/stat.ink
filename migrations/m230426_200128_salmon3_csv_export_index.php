<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230426_200128_salmon3_csv_export_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute(
            vsprintf(
                'CREATE INDEX {{%%salmon3_csv_export_index}} ON {{%%salmon3}} ( [[created_at]] ) WHERE ((%s))',
                [
                    implode(') AND (', [
                        '[[clear_waves]] IS NOT NULL',
                        '[[has_broken_data]] = FALSE',
                        '[[has_disconnect]] = FALSE',
                        '[[is_automated]] = TRUE',
                        '[[is_deleted]] = FALSE',
                        '[[is_private]] = FALSE',
                        '[[start_at]] IS NOT NULL',
                        '[[version_id]] IS NOT NULL',
                        vsprintf('((%s))', [
                            implode(') OR (', [
                                '[[is_big_run]] = TRUE AND [[big_stage_id]] IS NOT NULL',
                                '[[is_big_run]] = FALSE AND [[stage_id]] IS NOT NULL',
                            ]),
                        ]),
                    ]),
                ],
            ),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('{{%salmon3_csv_export_index}}', '{{%salmon3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return ['{{%salmon3}}'];
    }
}
