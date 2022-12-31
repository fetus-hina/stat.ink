<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

final class m221028_051233_s3_v1_2_1 extends Migration
{
    use VersionMigration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // https://twitter.com/SplatoonJP/status/1585860158961262593
        // https://www.nintendo.co.jp/support/switch/software_support/av5ja/121.html
        $this->upVersion3(
            '1.2',
            'v1.2.x',
            '1.2.1',
            'v1.2.1',
            new DateTimeImmutable('2022-10-28T15:00:00+09:00'),
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->downVersion3('1.2.1', '1.2.0');
    }
}
