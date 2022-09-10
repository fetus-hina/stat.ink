<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220910_184610_weapon3_splatnet extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%weapon3_alias}}', ['weapon_id', 'key'], [
            [$this->key2id('{{%weapon3}}', 'tristringer'), '7010'],
            [$this->key2id('{{%weapon3}}', 'lact450'), '7020'],
            [$this->key2id('{{%weapon3}}', 'drivewiper'), '8010'],
            [$this->key2id('{{%weapon3}}', 'jimuwiper'), '8000'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%weapon3_alias}}', ['key' => ['7010', '7020', '8000', '8010']]);

        return true;
    }
}
