<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m231204_065936_bigrun_official_results extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $id = (new Query())
            ->select('id')
            ->from('{{%salmon_schedule3}}')
            ->andWhere(['start_at' => '2023-12-02T00:00:00+00:00'])
            ->andWhere(['not', ['big_map_id' => null]])
            ->limit(1)
            ->scalar();

        if ($id) {
            $this->insert('{{%bigrun_official_result3}}', [
                'schedule_id' => $id,
                'gold' => 165,
                'silver' => 144,
                'bronze' => 116,
            ]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = (new Query())
            ->select('id')
            ->from('{{%salmon_schedule3}}')
            ->andWhere(['start_at' => '2023-12-02T00:00:00+00:00'])
            ->andWhere(['not', ['big_map_id' => null]])
            ->limit(1)
            ->scalar();

        if ($id) {
            $this->delete('{{%bigrun_official_result3}}', ['schedule_id' => $id]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%bigrun_official_result3}}',
        ];
    }
}
