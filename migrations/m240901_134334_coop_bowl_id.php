<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m240901_134334_coop_bowl_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%bigrun_map3_alias}}', [
            'id' => (new Query())
                ->select('MAX([[id]]) + 1')
                ->from('{{%bigrun_map3_alias}}')
                ->scalar(),
            'map_id' => $this->key2id('{{%bigrun_map3}}', 'grand_arena'),
            'key' => '107',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%bigrun_map3_alias}}', ['key' => '107']);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%bigrun_map3_alias}}',
        ];
    }
}
