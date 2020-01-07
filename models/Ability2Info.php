<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class Ability2Info extends Model
{
    public $ability;
    public $primary = 0;
    public $secondary = 0;

    public function getIsPrimaryOnly(): bool
    {
        return $this->ability && $this->ability->primary_only;
    }

    public function get57Format(): int
    {
        return $this->primary * 10 + $this->secondary * 3;
    }
}
