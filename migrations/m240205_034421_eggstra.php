<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m240205_034421_eggstra extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $id = filter_var(
            (new Query())
                ->select(['id'])
                ->from('{{%salmon_schedule3}}')
                ->andWhere([
                    'is_eggstra_work' => true,
                    'start_at' => '2024-02-03T00:00:00+00:00',
                ])
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT,
        );

        if (is_int($id)) {
            $this->insert('{{%eggstra_work_official_result3}}', [
                'schedule_id' => (int)$id,
                'gold' => 218,
                'silver' => 172,
                'bronze' => 133,
            ]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = filter_var(
            (new Query())
                ->select(['id'])
                ->from('{{%salmon_schedule3}}')
                ->andWhere([
                    'is_eggstra_work' => true,
                    'start_at' => '2024-02-03T00:00:00+00:00',
                ])
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT,
        );

        if (is_int($id)) {
            $this->delete('{{%bigrun_official_result3}}', ['schedule_id' => $id]);
        }

        return true;
    }
}
