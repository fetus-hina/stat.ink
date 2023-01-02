<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m181109_071133_amiibo extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        foreach ($this->getGears() as $gearData) {
            call_user_func_array([$this, 'upGear2'], $gearData);
        }
    }

    public function safeDown()
    {
        foreach ($this->getGears() as $gearData) {
            $this->downGear2($gearData[0]);
        }
    }

    public function getGears(): array
    {
        return iterator_to_array($this->getGearsImpl());
    }

    private function getGearsImpl() // : generator
    {
        $fh = fopen(__FILE__, 'rt');
        fseek($fh, __COMPILER_HALT_OFFSET__, SEEK_SET);
        while (!feof($fh)) {
            $line = trim((string)fgets($fh));
            if ($line === '') {
                continue;
            }
            $row = $this->parseCsvRow($line);
            if (count($row) !== 4) {
                throw new Exception("CSV format error in {$line}");
            }
            yield [
                static::name2key(trim($row[0])),
                trim($row[0]),
                $row[1],
                'amiibo',
                $row[2],
                (int)$row[3],
            ];
        }
        fclose($fh);
    }

    private function parseCsvRow(string $line): array
    {
        // 本当は真面目にやる必要があるが、ここでは入力されるデータが既知のものなので
        // 特に何も考えなくても安全。
        // このコードをたまたま見つけてしまった第三者は絶対にこれをコピペして利用しないこと。
        return explode(',', $line);
    }
}

// phpcs:disable
__halt_compiler();
Enchanted Hat,headgear,ink_saver_main,25008
Steel Helm,headgear,special_charge_up,25009
Fresh Fish Head,headgear,comeback,25010
Enchanted Robe,clothing,thermal_ink,25008
Steel Platemail,clothing,ink_saver_sub,25009
Fresh Fish Gloves,clothing,quick_super_jump,25010
Enchanted Boots,shoes,run_speed_up,25008
Steel Greaves,shoes,object_shredder,25009
Fresh Fish Feet,shoes,quick_respawn,25010
