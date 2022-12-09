<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m221209_040348_s3_v201 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->upVersion3(
            '2.0',
            'v2.0.x',
            '2.0.1',
            'v2.0.1',
            new DateTimeImmutable('2022-12-09T10:00:00+09:00'),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downVersion3('2.0.1', '2.0.0');

        return true;
    }
}
