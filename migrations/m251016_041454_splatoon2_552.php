<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m251016_041454_splatoon2_552 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->upVersion2(
            '5.5',
            '5.5.x',
            '5.5.2',
            '5.5.2',
            new DateTimeImmutable('2025-10-16T09:00:00+09:00'),
        );
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->downVersion2('5.5.2', '5.5.1');
    }
}
