<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m220910_091032_update_weapon extends Migration
{
    /**
     * @inheritdoc
     */
    public function vacuumTables(): array
    {
        return [
            '{{%weapon3}}',
        ];
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%subweapon3}}', [
            'key' => 'pointsensor',
            'name' => 'Point Sensor',
        ]);

        foreach ($this->getData() as $wKey => $info) {
            $this->update(
                '{{%weapon3}}',
                [
                    'subweapon_id' => $this->key2id('{{%subweapon3}}', $info[0]),
                    'special_id' => $this->key2id('{{%special3}}', $info[1]),
                ],
                [
                    'key' => $wKey,
                ]
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update(
            '{{%weapon3}}',
            ['subweapon_id' => null, 'special_id' => null],
            ['key' => array_keys($this->getData())]
        );

        $this->delete('{{%subweapon3}}', [
            'key' => 'pointsensor',
        ]);

        return true;
    }

    public function getData(): array
    {
        return [
            'bold' => ['curlingbomb', 'ultrahanko'],
            'sharp' => ['quickbomb', 'kanitank'],
            '96gal' => ['sprinkler', 'kyuinki'],
            'jetsweeper' => ['linemarker', 'kyuinki'],
            'bottlegeyser' => ['splashshield', 'ultrashot'],
            'longblaster' => ['kyubanbomb', 'hopsonar'],
            'clashblaster' => ['splashbomb', 'ultrashot'],
            'rapid' => ['trap', 'tripletornado'],
            'rapid_elite' => ['poisonmist', 'kyuinki'],
            'l3reelgun' => ['curlingbomb', 'kanitank'],
            'h3reelgun' => ['pointsensor', 'energystand'],
            'sputtery' => ['jumpbeacon', 'energystand'],
            'kelvin525' => ['splashshield', 'nicedama'],
            'dualsweeper' => ['splashbomb', 'hopsonar'],
            'carbon' => ['robotbomb', 'shokuwander'],
            'variableroller' => ['trap', 'missile'],
            'squiclean_a' => ['pointsensor', 'greatbarrier'],
            'bamboo14mk1' => ['robotbomb', 'megaphone51'],
            'soytuber' => ['torpedo', 'missile'],
            'screwslosher' => ['tansanbomb', 'nicedama'],
            'furo' => ['sprinkler', 'amefurashi'],
            'explosher' => ['pointsensor', 'amefurashi'],
            'splatspinner' => ['quickbomb', 'ultrahanko'],
            'kugelschreiber' => ['tansanbomb', 'jetpack'],
            'nautilus47' => ['pointsensor', 'amefurashi'],
            'spygadget' => ['trap', 'sameride'],
            'jimuwiper' => ['quickbomb', 'shokuwander'],
            'lact450' => ['curlingbomb', 'missile'],
        ];
    }
}
