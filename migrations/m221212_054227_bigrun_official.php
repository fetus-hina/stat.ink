<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m221212_054227_bigrun_official extends Migration
{
    private const SKIP_REGISTER = false;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%bigrun_official_result3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'gold' => $this->bigInteger()->notNull(),
            'silver' => $this->bigInteger()->notNull(),
            'bronze' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[schedule_id]])',
        ]);

        $id = filter_var(
            (new Query())
                ->select(['id'])
                ->from('{{%salmon_schedule3}}')
                ->andWhere(['start_at' => '2022-12-10T09:00:00+09:00'])
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT,
        );
        if (\is_int($id)) {
            $this->insert('{{%bigrun_official_result3}}', [
                'schedule_id' => (int)$id,
                'gold' => 137,
                'silver' => 113,
                'bronze' => 88,
            ]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%bigrun_official_result3}}');

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%bigrun_official_result3}}',
        ];
    }
}
