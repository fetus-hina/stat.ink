<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\Weapon3Migration;
use app\components\helpers\TypeHelper;
use yii\db\Query;

final class m240528_133844_season8_weapons extends Migration
{
    use Weapon3Migration;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        foreach ($this->getData() as $row) {
            [$type, $key, $mainKey, $name, $splatnet, $sub, $special, $xGroup2] = $row;

            if ($xGroup2 === '!') {
                $xGroup2 = TypeHelper::string(
                    (new Query())
                        ->select(['value' => '{{%x_matching_group3}}.[[short_name]]'])
                        ->from('{{%x_matching_group3}}')
                        ->innerJoin(
                            '{{%x_matching_group_weapon3}}',
                            '{{%x_matching_group3}}.[[id]] = {{%x_matching_group_weapon3}}.[[group_id]]',
                        )
                        ->innerJoin(
                            '{{%x_matching_group_version3}}',
                            '{{%x_matching_group_weapon3}}.[[version_id]] = {{%x_matching_group_version3}}.[[id]]',
                        )
                        ->innerJoin(
                            '{{%weapon3}}',
                            '{{%x_matching_group_weapon3}}.[[weapon_id]] = {{%weapon3}}.[[id]]',
                        )
                        ->andWhere([
                            '{{%weapon3}}.[[key]]' => $mainKey,
                            '{{%x_matching_group_version3}}.[[minimum_version]]' => '6.0.0',
                        ])
                        ->limit(1)
                        ->scalar(),
                );
            }

            $this->upWeapon3(
                key: $key,
                name: $name,
                type: $type,
                sub: $sub,
                special: $special,
                main: $key === $mainKey ? null : $mainKey,
                salmon: $key === $mainKey,
                aliases: [$splatnet],
                xGroup: null,
                xGroup2: $xGroup2 === '!'
                    ?
                    : $xGroup2,
                releaseAt: '2024-06-01T00:00:00+00:00',
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        foreach (array_reverse($this->getData()) as $row) {
            $this->downWeapon3(
                $row[1], // key
                salmon: $row[1] === $row[2],
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @return string[][]
     */
    private function getData(): array
    {
        $locale = TypeHelper::string(setlocale(LC_CTYPE, '0'));
        try {
            setlocale(LC_CTYPE, 'en_US.UTF-8');

            return array_map(
                fn (string $line): array => str_getcsv($line, ',', '"', '\\'),
                [
                    'blaster,longblaster_custom,longblaster,Custom Range Blaster,221,splashbomb,teioika,!',
                    'charger,bamboo14mk2,bamboo14mk1,Bamboozler 14 Mk II,2051,tansanbomb,decoy,!',
                    'spinner,hydra_custom,hydra,Custom Hydra Splatling,4021,trap,suminagasheet,!',
                    'spinner,examiner_hue,examiner,Heavy Edit Splatling Nouveau,4051,splashbomb,kanitank,!',
                    'maneuver,gaen_ff_custom,gaen_ff,Custom Douser Dualies FF,5051,quickbomb,tripletornado,!',
                    'brella,brella24mk2,brella24mk1,Recycled Brella 24 Mk II,6031,poisonmist,ultra_chakuchi,!',
                    'stringer,furuido,furuido,Wellstring V,7030,robotbomb,ultrahanko,L',
                    'stringer,furuido_custom,furuido,Custom Wellstring V,7031,pointsensor,hopsonar,!',
                    'wiper,dentalwiper_mint,dentalwiper_mint,Mint Decavitator,8020,kyubanbomb,greatbarrier,L',
                    'wiper,dentalwiper_sumi,dentalwiper_mint,Charcoal Decavitator,8021,splashshield,jetpack,!',
                ],
            );
        } finally {
            setlocale(LC_CTYPE, $locale);
        }
    }
}
