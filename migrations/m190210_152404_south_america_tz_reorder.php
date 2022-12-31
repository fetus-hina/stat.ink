<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190210_152404_south_america_tz_reorder extends Migration
{
    public function safeUp()
    {
        foreach ($this->getData() as $ident => $_) {
            [$order,] = $_;
            $this->update(
                'timezone',
                ['order' => $order + 100000],
                ['identifier' => $ident],
            );
        }
        foreach ($this->getData() as $ident => $_) {
            [$order,] = $_;
            $this->update(
                'timezone',
                ['order' => $order],
                ['identifier' => $ident],
            );
        }
    }

    public function safeDown()
    {
        foreach ($this->getData() as $ident => $_) {
            [, $order] = $_;
            $this->update(
                'timezone',
                ['order' => $order + 100000],
                ['identifier' => $ident],
            );
        }
        foreach ($this->getData() as $ident => $_) {
            [, $order] = $_;
            $this->update(
                'timezone',
                ['order' => $order],
                ['identifier' => $ident],
            );
        }
    }

    private function getData(): array
    {
        return [
            // id => [new, old]
            'America/Argentina/Buenos_Aires' => [71, 77],
            'America/La_Paz' => [72, 78],
            'America/Noronha' => [73, 71],
            'America/Sao_Paulo' => [74, 72],
            'America/Fortaleza' => [75, 73],
            'America/Cuiaba' => [76, 74],
            'America/Manaus' => [77, 75],
            'America/Eirunepe' => [78, 76],
            // 'America/Santiago' => [79, 79],
            // 'America/Punta_Arenas' => [80, 80],
            // 'Pacific/Easter' => [81, 81],
            // 'America/Bogota' => [82, 82],
            // 'America/Guayaquil' => [83, 83],
            // 'America/Guyana' => [84, 84],
            // 'America/Asuncion' => [85, 85],
            // 'America/Lima' => [86, 86],
            // 'America/Paramaribo' => [87, 87],
            // 'America/Montevideo' => [88, 88],
            // 'America/Caracas' => [89, 89],
        ];
    }
}
