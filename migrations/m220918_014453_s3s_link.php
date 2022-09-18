<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220918_014453_s3s_link extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('agent_attribute', [
            'name' => 's3s',
            'is_automated' => true,
            'link_url' => 'https://github.com/frozenpandaman/s3s',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('agent_attribute', ['name' => 's3s']);

        return true;
    }
}
