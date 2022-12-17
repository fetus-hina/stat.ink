<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221217_114013_tricolor extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%rule_group3}}', [
            'key' => 'tricolor',
            'name' => 'Tricolor Battle',
            'rank' => 300,
        ]);

        $this->insert('{{%rule3}}', [
            'key' => 'tricolor',
            'name' => 'Tricolor Turf War',
            'short_name' => 'Tri',
            'rank' => 310,
            'group_id' => $this->key2id('{{%rule_group3}}', 'tricolor'),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%rule3}}', ['key' => 'tricolor']);
        $this->delete('{{%rule_group3}}', ['key' => 'tricolor']);

        return true;
    }
}
