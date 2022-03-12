<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\base\Model;

final class BattleSummary extends Model
{
    public int $battle_count = 0;
    public ?float $wp = null;
    public ?float $wp_short = null;
    public int $total_kill = 0;
    public int $total_death = 0;
    public int $kd_present = 0;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['battle_count', 'wp', 'wp_short', 'total_kill', 'total_death', 'kd_present'], 'safe'],
        ];
    }
}
