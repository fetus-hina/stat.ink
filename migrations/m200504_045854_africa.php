<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m200504_045854_africa extends Migration
{
    public function safeUp()
    {
        $regionId = $this->getRegionId();
        foreach ($this->getData() as $groupName => $groupData) {
            $tzGroupId = $this->upTzGroup($groupName);
            $tldIdMap = $this->upCountries($groupData);
            $order = 10 * ceil(
                (1 + (int)(new Query())->select('MAX([[order]])')->from('timezone')->scalar()) / 10,
            );

            foreach ($groupData as $cctld => $info) {
                foreach ($info['tz'] as $tzIdStr => $tzName) {
                    $this->insert('timezone', [
                        'identifier' => $tzIdStr,
                        'name' => $tzName,
                        'order' => $order++,
                        'region_id' => $regionId,
                        'group_id' => $tzGroupId,
                    ]);
                    $this->insert('timezone_country', [
                        'timezone_id' => (int)(new Query())
                            ->select('id')
                            ->from('timezone')
                            ->where(['identifier' => $tzIdStr])
                            ->scalar(),
                        'country_id' => $tldIdMap[$cctld],
                    ]);
                }
            }
        }
    }

    public function safeDown()
    {
        $tzIdentifiers = [];
        $cctlds = [];
        foreach ($this->getData() as $groupName => $groupData) {
            foreach ($groupData as $cctld => $info) {
                $cctlds[] = $cctld;
                foreach (array_keys($info['tz']) as $tmp) {
                    $tzIdentifiers[] = $tmp;
                }
            }
        }

        $tzIds = (new Query())
            ->select('id')
            ->from('timezone')
            ->where(['identifier' => $tzIdentifiers])
            ->column();

        $countryIds = (new Query())
            ->select('id')
            ->from('country')
            ->where(['key' => $cctlds])
            ->column();

        $this->delete('timezone_country', ['or',
            ['timezone_id' => $tzIds],
            ['country_id' => $countryIds],
        ]);

        $this->delete('timezone', ['id' => $tzIds]);
        $this->delete('country', ['id' => $countryIds]);
        foreach (array_keys($this->getData()) as $groupName) {
            $this->delete('timezone_group', ['id' => $this->getTzGroupId($groupName)]);
        }
    }

    private function upTzGroup(string $name): int
    {
        $order = (int)(new Query())
            ->select(['v' => 'MAX([[order]])'])
            ->from('timezone_group')
            ->where(['<>', 'name', 'Others'])
            ->scalar();

        $order = (int)(ceil($order / 10) * 10) + 10;

        $this->insert('timezone_group', ['order' => $order, 'name' => $name]);
        return $this->getTzGroupId($name);
    }

    private function getTzGroupId(string $name): int
    {
        return (int)(new Query())
            ->select('id')
            ->from('timezone_group')
            ->where(['name' => $name])
            ->scalar();
    }

    private function getRegionId(): int
    {
        return (int)(new Query())
            ->select('id')
            ->from('region')
            ->where(['key' => 'eu'])
            ->scalar();
    }

    private function upCountries(array $data): array
    {
        $this->batchInsert('country', ['key', 'name'], array_map(
            function (string $cctld, array $cInfo): array {
                return [$cctld, $cInfo['name']];
            },
            array_keys($data),
            array_values($data),
        ));

        return ArrayHelper::map(
            (new Query())->select('*')->from('country')->where(['key' => array_keys($data)])->all(),
            'key',
            'id',
        );
    }

    private function getData(): array
    {
        return [
            'North Africa' => [
                'dz' => [
                    'name' => 'Algeria',
                    'tz' => [
                        'Africa/Algiers' => 'Algeria',
                    ],
                ],
                'eg' => [
                    'name' => 'Egypt',
                    'tz' => [
                        'Africa/Cairo' => 'Egypt',
                    ],
                ],
                'ly' => [
                    'name' => 'Libya',
                    'tz' => [
                        'Africa/Tripoli' => 'Libya',
                    ],
                ],
                'ma' => [
                    'name' => 'Morocco',
                    'tz' => [
                        'Africa/Casablanca' => 'Morocco',
                    ],
                ],
                'sd' => [
                    'name' => 'Sudan',
                    'tz' => [
                        'Africa/Khartoum' => 'Sudan',
                    ],
                ],
                'tn' => [
                    'name' => 'Tunisia',
                    'tz' => [
                        'Africa/Tunis' => 'Tunisia',
                    ],
                ],
                'eh' => [
                    'name' => 'Western Sahara',
                    'tz' => [
                        'Africa/El_Aaiun' => 'Western Sahara',
                    ],
                ],
            ],
            'West Africa' => [
                'bj' => [
                    'name' => 'Benin',
                    'tz' => [
                        'Africa/Porto-Novo' => 'Benin',
                    ],
                ],
                'bf' => [
                    'name' => 'Burkina Faso',
                    'tz' => [
                        'Africa/Ouagadougou' => 'Burkina Faso',
                    ],
                ],
                'cv' => [
                    'name' => 'Cape Verde',
                    'tz' => [
                        'Atlantic/Cape_Verde' => 'Cape Verde',
                    ],
                ],
                'ci' => [
                    'name' => 'Côte d\'Ivoire',
                    'tz' => [
                        'Africa/Abidjan' => 'Côte d\'Ivoire',
                    ],
                ],
                'gm' => [
                    'name' => 'Gambia',
                    'tz' => [
                        'Africa/Banjul' => 'Gambia',
                    ],
                ],
                'gh' => [
                    'name' => 'Ghana',
                    'tz' => [
                        'Africa/Accra' => 'Ghana',
                    ],
                ],
                'gn' => [
                    'name' => 'Guinea',
                    'tz' => [
                        'Africa/Conakry' => 'Guinea',
                    ],
                ],
                'gw' => [
                    'name' => 'Guinea-Bissau',
                    'tz' => [
                        'Africa/Bissau' => 'Guinea-Bissau',
                    ],
                ],
                'lr' => [
                    'name' => 'Liberia',
                    'tz' => [
                        'Africa/Monrovia' => 'Liberia',
                    ],
                ],
                'ml' => [
                    'name' => 'Mali',
                    'tz' => [
                        'Africa/Bamako' => 'Mali',
                    ],
                ],
                'mr' => [
                    'name' => 'Mauritania',
                    'tz' => [
                        'Africa/Nouakchott' => 'Mauritania',
                    ],
                ],
                'ne' => [
                    'name' => 'Niger',
                    'tz' => [
                        'Africa/Niamey' => 'Niger',
                    ],
                ],
                'ng' => [
                    'name' => 'Nigeria',
                    'tz' => [
                        'Africa/Lagos' => 'Nigeria',
                    ],
                ],
                'sn' => [
                    'name' => 'Senegal',
                    'tz' => [
                        'Africa/Dakar' => 'Senegal',
                    ],
                ],
                'sl' => [
                    'name' => 'Sierra Leone',
                    'tz' => [
                        'Africa/Freetown' => 'Sierra Leone',
                    ],
                ],
                'tg' => [
                    'name' => 'Togo',
                    'tz' => [
                        'Africa/Lome' => 'Togo',
                    ],
                ],
            ],
            'Central Africa' => [
                'ao' => [
                    'name' => 'Angola',
                    'tz' => [
                        'Africa/Luanda' => 'Angola',
                    ],
                ],
                'cm' => [
                    'name' => 'Cameroon',
                    'tz' => [
                        'Africa/Douala' => 'Cameroon',
                    ],
                ],
                'cf' => [
                    'name' => 'Central African Republic',
                    'tz' => [
                        'Africa/Bangui' => 'Central African Republic',
                    ],
                ],
                'td' => [
                    'name' => 'Chad',
                    'tz' => [
                        'Africa/Ndjamena' => 'Chad',
                    ],
                ],
                'cd' => [
                    'name' => 'DR Congo',
                    'tz' => [
                        'Africa/Lubumbashi' => 'DR Congo (East)',
                        'Africa/Kinshasa' => 'DR Congo (West)',
                    ],
                ],
                'cg' => [
                    'name' => 'Congo Republic',
                    'tz' => [
                        'Africa/Brazzaville' => 'Congo Republic',
                    ],
                ],
                'gq' => [
                    'name' => 'Equatorial Guinea',
                    'tz' => [
                        'Africa/Malabo' => 'Equatorial Guinea',
                    ],
                ],
                'ga' => [
                    'name' => 'Gabon',
                    'tz' => [
                        'Africa/Libreville' => 'Gabon',
                    ],
                ],
                'st' => [
                    'name' => 'São Tomé and Príncipe',
                    'tz' => [
                        'Africa/Sao_Tome' => 'São Tomé and Príncipe',
                    ],
                ],
            ],
            'East Africa' => [
                'bi' => [
                    'name' => 'Burundi',
                    'tz' => [
                        'Africa/Bujumbura' => 'Burundi',
                    ],
                ],
                'km' => [
                    'name' => 'Comoros',
                    'tz' => [
                        'Indian/Comoro' => 'Comoros',
                    ],
                ],
                'dj' => [
                    'name' => 'Djibouti',
                    'tz' => [
                        'Africa/Djibouti' => 'Djibouti',
                    ],
                ],
                'er' => [
                    'name' => 'Eritrea',
                    'tz' => [
                        'Africa/Asmara' => 'Eritrea',
                    ],
                ],
                'et' => [
                    'name' => 'Ethiopia',
                    'tz' => [
                        'Africa/Addis_Ababa' => 'Ethiopia',
                    ],
                ],
                'ke' => [
                    'name' => 'Kenya',
                    'tz' => [
                        'Africa/Nairobi' => 'Kenya',
                    ],
                ],
                'mg' => [
                    'name' => 'Madagascar',
                    'tz' => [
                        'Indian/Antananarivo' => 'Madagascar',
                    ],
                ],
                'mw' => [
                    'name' => 'Malawi',
                    'tz' => [
                        'Africa/Blantyre' => 'Malawi',
                    ],
                ],
                'mu' => [
                    'name' => 'Mauritius',
                    'tz' => [
                        'Indian/Mauritius' => 'Mauritius',
                    ],
                ],
                'mz' => [
                    'name' => 'Mozambique',
                    'tz' => [
                        'Africa/Maputo' => 'Mozambique',
                    ],
                ],
                'rw' => [
                    'name' => 'Rwanda',
                    'tz' => [
                        'Africa/Kigali' => 'Rwanda',
                    ],
                ],
                'sc' => [
                    'name' => 'Seychelles',
                    'tz' => [
                        'Indian/Mahe' => 'Seychelles',
                    ],
                ],
                'so' => [
                    'name' => 'Somalia',
                    'tz' => [
                        'Africa/Mogadishu' => 'Somalia',
                    ],
                ],
                'ss' => [
                    'name' => 'South Sudan',
                    'tz' => [
                        'Africa/Juba' => 'South Sudan',
                    ],
                ],
                'tz' => [
                    'name' => 'Tanzania',
                    'tz' => [
                        'Africa/Dar_es_Salaam' => 'Tanzania',
                    ],
                ],
                'ug' => [
                    'name' => 'Uganda',
                    'tz' => [
                        'Africa/Kampala' => 'Uganda',
                    ],
                ],
                'zm' => [
                    'name' => 'Zambia',
                    'tz' => [
                        'Africa/Lusaka' => 'Zambia',
                    ],
                ],
                'zw' => [
                    'name' => 'Zimbabwe',
                    'tz' => [
                        'Africa/Harare' => 'Zimbabwe',
                    ],
                ],
            ],
            'Southern Africa' => [
                'sz' => [
                    'name' => 'Eswatini',
                    'tz' => [
                        'Africa/Mbabane' => 'Eswatini',
                    ],
                ],
                'ls' => [
                    'name' => 'Lesotho',
                    'tz' => [
                        'Africa/Maseru' => 'Lesotho',
                    ],
                ],
                'na' => [
                    'name' => 'Namibia',
                    'tz' => [
                        'Africa/Windhoek' => 'Namibia',
                    ],
                ],
                'bw' => [
                    'name' => 'Botswana',
                    'tz' => [
                        'Africa/Gaborone' => 'Botswana',
                    ],
                ],
                'za' => [
                    'name' => 'South Africa',
                    'tz' => [
                        'Africa/Johannesburg' => 'South Africa',
                    ],
                ],
            ],
        ];
    }
}
