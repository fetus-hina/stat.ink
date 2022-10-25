<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m221025_020033_s3_v1_2_0 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // https://twitter.com/SplatoonJP/status/1584712596913270784
        $this->upVersion3(
            '1.2',
            'v1.2.x',
            '1.2.0',
            'v1.2.0',
            new DateTimeImmutable('2022-10-26T10:00:00+09:00')
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downVersion3('1.2.0', '1.1.2');
    }
}
