<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;

class m190403_064045_spring_fest_gear_ids extends Migration
{
    public function safeUp()
    {
        $data = $this->getData();
        $case = vsprintf('CASE %s %s END', [
            $this->db->quoteColumnName('key'),
            implode(' ', array_map(
                fn (string $key, int $id): string => vsprintf('WHEN %s THEN %d', [
                        $this->db->quoteValue($key),
                        $id,
                    ]),
                array_keys($data),
                array_values($data),
            )),
        ]);
        $this->update(
            'gear2',
            ['splatnet' => new Expression($case)],
            ['key' => array_keys($data)],
        );
    }

    public function safeDown()
    {
        $this->update(
            'gear2',
            ['splatnet' => null],
            ['key' => array_keys($this->getData())],
        );
    }

    public function getData(): array
    {
        return [
            'purple_novelty_visor' => 24008,
            'green_novelty_visor' => 24009,
            'orange_novelty_visor' => 24010,
            'pink_novelty_visor' => 24011,
            'pearl_scout_lace_ups' => 23000,
            'pearlescent_squidkid_iv' => 23001,
            'pearl_punk_crowns' => 23002,
            'new_day_arrows' => 23003,
            'marination_lace_ups' => 23004,
            'rina_squidkid_iv' => 23005,
            'trooper_power_stripes' => 23006,
            'midnight_slip_ons' => 23007,
        ];
    }
}
