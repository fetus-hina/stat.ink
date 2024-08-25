<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;
use yii\db\Query;

final class m240823_165002_ground_arena extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $id = (new Query())
            ->select('MAX([[id]]) + 1')
            ->from('{{%bigrun_map3}}')
            ->scalar($this->db);

        $this->insert('{{%bigrun_map3}}', [
            'id' => $id,
            'key' => 'grand_arena',
            'name' => 'Grand Splatlands Bowl',
            'short_name' => 'Bowl',
            'release_at' => '2024-09-07T00:00:00+00:00',
            'bigrun' => true,
        ]);

        $this->batchInsert('{{%bigrun_map3_alias}}', ['key', 'map_id'], [
            [self::name2key3('Grand Splatlands Bowl'), $id],
            [self::name2key3('Bowl'), $id],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%bigrun_map3}}', 'grand_arena');

        $this->delete('{{%bigrun_map3_alias}}', ['map_id' => $id]);
        $this->delete('{{%bigrun_map3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%bigrun_map3}}',
            '{{%bigrun_map3_alias}}',
        ];
    }
}
