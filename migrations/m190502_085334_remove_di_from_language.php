<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190502_085334_remove_di_from_language extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE {{language}} DROP COLUMN [[di]]');
    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE {{language}} ADD COLUMN [[di]] JSONB NULL');
    }
}
