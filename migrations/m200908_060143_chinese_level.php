<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m200908_060143_chinese_level extends Migration
{
    public function safeUp()
    {
        $this->update(
            'language',
            ['support_level_id' => 2], // ALMOST
            ['lang' => ['zh-CN', 'zh-TW']],
        );
    }

    public function safeDown()
    {
        $this->update(
            'language',
            ['support_level_id' => 5], // Machine-translated
            ['lang' => ['zh-CN', 'zh-TW']],
        );
    }
}
