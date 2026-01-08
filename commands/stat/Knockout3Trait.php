<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat;

use Yii;
use app\commands\stat\knockout3\BasicTrait;
use app\commands\stat\knockout3\HistogramTrait;
use yii\db\Connection;
use yii\db\Transaction;

use function fwrite;

use const STDERR;

trait Knockout3Trait
{
    use BasicTrait;
    use HistogramTrait;

    protected function updateKnockout3(): void
    {
        fwrite(STDERR, "Updating knockout3...\n");
        Yii::$app->db->transaction(
            function (Connection $db): void {
                $this->updateKnockout3Basic($db);
                $this->updateKnockout3Histogram($db);
                fwrite(STDERR, "Committing...\n");
            },
            Transaction::READ_COMMITTED,
        );
        fwrite(STDERR, "Updated knockout3.\n");
    }
}
