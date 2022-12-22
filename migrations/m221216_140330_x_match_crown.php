<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221216_140330_x_match_crown extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumns('{{%battle_player3}}', [
            'is_crowned' => $this->boolean()->null(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumns('{{%battle_player3}}', [
            'is_crowned',
        ]);

        return true;
    }
}
