<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m200107_085417_weapon_main_power_up extends Migration
{
    use AutoKey;

    public function up()
    {
        $status = parent::up();
        if ($status !== false) {
            $this->analyze('weapon2');
        }
        return $status;
    }

    public function safeUp()
    {
        $this->addColumns('weapon2', [
            'main_power_up_id' => $this->pkRef('main_power_up2')->null(),
        ]);
        if (!$this->updateWeaponData()) {
            return false;
        }
        $this->alterColumn('weapon2', 'main_power_up_id', $this->integer()->notNull());
    }

    public function safeDown()
    {
        $this->dropColumns('weapon2', [
            'main_power_up_id',
        ]);
    }

    public function updateWeaponData(): bool
    {
        $mainPowerMap = ArrayHelper::map(
            (new Query())->select('*')->from('main_power_up2')->all(),
            'key',
            'id'
        );
        foreach ($this->getUpdateTasks() as $mainPowerKey => $weapons) {
            $this->update(
                'weapon2',
                [
                    'main_power_up_id' => $mainPowerMap[$mainPowerKey],
                ],
                [
                    'main_group_id' => (new Query())
                        ->select('id')
                        ->from('weapon2')
                        ->andWhere(['key' => $weapons]),
                ]
            );
        }

        $nullWeapons = (new Query())
            ->select('*')
            ->from('weapon2')
            ->where(['main_power_up_id' => null])
            ->orderBy(['key' => SORT_ASC])
            ->all();
        if ($nullWeapons) {
            fwrite(STDERR, "BUG! main_power_up_id IS NULL. weapons:\n");
            foreach ($nullWeapons as $weapon) {
                fwrite(STDERR, "  - {$weapon['key']}\n");
            }

            return false;
        }

        return true;
    }

    public function getUpdateTasks(): array
    {
        return [
            $this->getKeyFromName('Increase damage') => [
                '96gal',
                'bamboo14mk1',
                'bold',
                'bottlegeyser',
                'carbon',
                'dualsweeper',
                'dynamo',
                'h3reelgun',
                'hydra',
                'kelvin525',
                'kugelschreiber',
                'l3reelgun',
                'maneuver',
                'prime',
                'quadhopper_black',
                'sharp',
                'soytuber',
                'splatcharger',
                'splatroller',
                'sputtery',
                'variableroller',
            ],
            $this->getKeyFromName('Increase shot accuracy') => [
                '52gal',
                'clashblaster',
                'hotblaster',
                'longblaster',
                'rapid',
                'rapid_elite',
                'sshooter',
            ],
            $this->getKeyFromName('Increase bullet velocity') => [
                'jetsweeper',
            ],
            $this->getKeyFromName('Increase range') => [
                'liter4k',
                'squiclean_a',
            ],
            $this->getKeyFromName('Increase duration of firing') => [
                'barrelspinner',
                'nautilus47',
                'splatspinner',
            ],
            $this->getKeyFromName('Increase ink coverage') => [
                'explosher',
                'furo',
                'hissen',
                'nzap85',
                'promodeler_mg',
                'screwslosher',
                'wakaba',
            ],
            $this->getKeyFromName('Increase high-damage radius of explosions') => [
                'nova',
            ],
            $this->getKeyFromName('Increase damage from higher grounds') => [
                'bucketslosher',
            ],
            $this->getKeyFromName('Increase movement speed') => [
                'hokusai',
                'pablo',
            ],
            $this->getKeyFromName('Speed up brella canopy regeneration') => [
                'parashelter',
                'spygadget',
            ],
            $this->getKeyFromName('Increase brella canopy durability') => [
                'campingshelter',
            ],
        ];
    }

    // See m200107_074759_main_power_up
    private function getKeyFromName(string $name): string
    {
        $name = preg_replace('/^Increases?\b/i', ' ', $name);
        $name = preg_replace('/\bbrella\b/i', ' ', $name);
        return static::name2key($name);
    }
}
