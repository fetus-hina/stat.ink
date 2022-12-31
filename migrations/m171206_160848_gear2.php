<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171206_160848_gear2 extends Migration
{
    public function safeUp()
    {
        $data = $this->getUpdateData();
        $updateCase = new \yii\db\Expression(sprintf(
            '(CASE %s %s END)',
            $this->db->quoteColumnName('key'),
            implode(' ', array_map(
                function (string $key, int $value): string {
                    return sprintf(
                        'WHEN %s THEN %s',
                        $this->db->quoteValue($key),
                        $this->db->quoteValue($value),
                    );
                },
                array_keys($data),
                array_values($data),
            )),
        ));
        $this->update(
            'gear2',
            ['splatnet' => $updateCase],
            ['key' => array_keys($data)],
        );
    }

    public function safeDown()
    {
        $this->update(
            'gear2',
            ['splatnet' => null],
            ['key' => array_keys($this->getUpdateData())],
        );
    }

    public function getUpdateData(): array
    {
        return [
            'red_slip_ons' => 7001,
            'squid_stitch_slip_ons' => 7002,
            'tulip_parasol' => 4010,
            'varsity_baseball_ls' => 2005,
        ];
    }
}
