<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m230306_020623_bigrun_official_result extends Migration
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
                ->andWhere(['start_at' => '2023-03-04T09:00:00+09:00'])
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT,
        );

        if (is_int($id)) {
            $this->insert('{{%bigrun_official_result3}}', [
                'schedule_id' => (int)$id,
                'gold' => 141,
                'silver' => 117,
                'bronze' => 90,
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
                ->andWhere(['start_at' => '2023-03-04T09:00:00+09:00'])
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
