<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m230213_124516_s2_v5_5_1 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upVersion2(
            '5.5',
            '5.5.x',
            '5.5.1',
            '5.5.1',
            new DateTimeImmutable('2022-11-15T11:00:00+09:00'),
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downVersion2('5.5.1', '5.5.0');
    }
}
