<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220928_090911_exempted_lose extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%result3}}', [
            'id' => 4,
            'key' => 'exempted_lose',
            'name' => 'Defeat (Exempted)',
            'is_win' => false,
            'aggregatable' => false,
            'label_color' => 'danger',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%result3}}', ['key' => 'exempted_lose']);

        return true;
    }
}
