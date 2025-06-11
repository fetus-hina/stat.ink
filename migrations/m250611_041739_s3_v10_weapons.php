<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m250611_041739_s3_v10_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $weaponGroups = ArrayHelper::map(
            (new Query())
                ->select([
                    'key' => '{{%weapon3}}.[[key]]',
                    'short_name' => '{{%x_matching_group3}}.[[short_name]]',
                ])
                ->from('{{%x_matching_group3}}')
                ->innerJoin('{{%x_matching_group_weapon3}}', '{{%x_matching_group3}}.[[id]] = {{%x_matching_group_weapon3}}.[[group_id]]')
                ->innerJoin('{{%x_matching_group_version3}}', '{{%x_matching_group_weapon3}}.[[version_id]] = {{%x_matching_group_version3}}.[[id]]')
                ->innerJoin('{{%weapon3}}', '{{%x_matching_group_weapon3}}.[[weapon_id]] = {{%weapon3}}.[[id]]')
                ->andWhere([
                    '{{%x_matching_group_version3}}.[[minimum_version]]' => '6.0.0',
                ])
                ->all(),
            'key',
            'short_name',
        );

        foreach ($this->getData() as $row) {
            [$type, $key, $main, $id, $name, $sub, $special] = $row;
            $this->upWeapon3(
                key: $key,
                name: $name,
                type: $type,
                sub: $sub,
                special: $special,
                main: $main,
                salmon: false, // skip-salmon
                enableAutoKey: true,
                aliases: [(string)$id],
                xGroup: null,
                xGroup2: $weaponGroups[$main],
                releaseAt: '2025-06-12T10:00:00+09:00',
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        foreach ($this->getData() as $row) {
            $this->downWeapon3($row[1], salmon: false);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%mainweapon3}}',
            '{{%weapon3}}',
            '{{%weapon3_alias}}',
            '{{%salmon_weapon3}}',
            '{{%salmon_weapon3_alias}}',
        ];
    }

    private function getData(): array
    {
        return [
            ['shooter', 'sharp_geck', 'sharp', 22, 'Splash-o-matic GCK-O', 'poisonmist', 'amefurashi'],
            ['shooter', 'promodeler_sai', 'promodeler_mg', 32, 'Colorz Aerospray', 'quickbomb', 'suminagasheet'],
            ['shooter', 'sshooter_kou', 'sshooter', 42, 'Glamorz Splattershot', 'quickbomb', 'teioika'],
            ['shooter', 'prime_frzn', 'prime', 72, 'Splattershot Pro FRZ-N', 'splashbomb', 'missile'],
            ['shooter', '96gal_sou', '96gal', 82, 'Clawz .96 Gal', 'linemarker', 'energystand'],
            ['shooter', 'jetsweeper_cobr', 'jetsweeper', 92, 'Jet Squelcher COB-R', 'quickbomb', 'ultra_chakuchi'],
            ['blaster', 'hotblaster_en', 'hotblaster', 212, 'Gleamz Blaster', 'jumpbeacon', 'kanitank'],
            ['blaster', 'rapid_elite_wntr', 'rapid_elite', 252, 'Rapid Blaster Pro WNT-R', 'kyubanbomb', 'energystand'],
            ['reelgun', 'l3reelgun_haku', 'l3reelgun', 302, 'Glitterz L-3 Nozzlenose', 'splashbomb', 'jetpack'],
            ['reelgun', 'h3reelgun_snak', 'h3reelgun', 312, 'H-3 Nozzlenose VIP-R', 'kyubanbomb', 'tripletornado'],
            ['roller', 'carbon_angl', 'carbon', 1002, 'Carbon Roller ANG-L', 'tansanbomb', 'decoy'],
            ['roller', 'dynamo_mei', 'dynamo', 1022, 'Starz Dynamo Roller', 'pointsensor', 'megaphone51'],
            ['roller', 'wideroller_waku', 'wideroller', 1042, 'Planetz Big Swig Roller', 'torpedo', 'ultra_chakuchi'],
            ['brush', 'hokusai_sui', 'hokusai', 1112, 'Cometz Octobrush', 'robotbomb', 'teioika'],
            ['brush', 'fincent_brnz', 'fincent', 1122, 'Painbrush BRN-Z', 'splashshield', 'ultrashot'],
            ['charger', 'splatcharger_frst', 'splatcharger', 2012, 'Splat Charger CAM-O', 'sprinkler', 'kanitank'],
            ['charger', 'splatscope_frst', 'splatscope', 2022, 'Splatterscope CAM-O', 'sprinkler', 'kanitank'],
            ['slosher', 'hissen_ash', 'hissen', 3012, 'Tri-Slosher ASH-N', 'splashbomb', 'suminagasheet'],
            ['slosher', 'moprin_kaku', 'moprin', 3052, 'Hornz Dread Wringer', 'curlingbomb', 'kanitank'],
            ['spinner', 'splatspinner_pytn', 'splatspinner', 4002, 'Mini Splatling RTL-R', 'jumpbeacon', 'ultrashot'],
            ['spinner', 'hydra_atsu', 'hydra', 4022, 'Torrentz Hydra Splatling', 'sprinkler', 'greatbarrier'],
            ['maneuver', 'sputtery_owl', 'sputtery', 5002, 'Dapple Dualies NOC-T', 'splashbomb', 'megaphone51'],
            ['maneuver', 'maneuver_you', 'maneuver', 5012, 'Twinklez Splat Dualies', 'tansanbomb', 'greatbarrier'],
            ['maneuver', 'dualsweeper_tei', 'dualsweeper', 5032, 'Hoofz Dualie Squelchers', 'pointsensor', 'suminagasheet'],
            ['brella', 'campingshelter_crem', 'campingshelter', 6012, 'Tenta Brella CRE-M', 'poisonmist', 'decoy'],
            ['brella', 'spygadget_ryo', 'spygadget', 6022, 'Patternz Undercover Brella', 'curlingbomb', 'megaphone51'],
            ['stringer', 'tristringer_tou', 'tristringer', 7012, 'Bulbz Tri-Stringer', 'linemarker', 'jetpack'],
            ['stringer', 'lact450_milk', 'lact450', 7022, 'REEF-LUX 450 MIL-K', 'torpedo', 'nicedama'],
            ['wiper', 'drivewiper_rust', 'drivewiper', 8012, 'Splatana Wiper RUS-T', 'curlingbomb', 'ultrashot'],
            ['wiper', 'jimuwiper_fuu', 'jimuwiper', 8002, 'Stickerz Splatana Stamper', 'robotbomb', 'nicedama'],
        ];
    }
}
