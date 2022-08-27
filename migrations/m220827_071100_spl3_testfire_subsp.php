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

final class m220827_071100_spl3_testfire_subsp extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $subs = $this->getIdMap('{{%subweapon3}}');
        $sps = $this->getIdMap('{{%special3}}');

        foreach ($this->getWeaponData() as $mainKey => $keys) {
            [$subKey, $spKey] = $keys;
            $this->update(
                '{{%weapon3}}',
                [
                    'subweapon_id' => $subs[$subKey],
                    'special_id' => $sps[$spKey],
                ],
                [
                    'key' => $mainKey,
                ],
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('{{%weapon3}}', ['subweapon_id' => null, 'special_id' => null], '1 = 1');

        return true;
    }

    private function getWeaponData(): array
    {
        return [
            // shooters
            '52gal' => ['linemarker', 'megaphone51'],
            'nzap85' => ['kyubanbomb', 'energystand'],
            'prime' => ['linemarker', 'kanitank'],
            'promodeler_mg' => ['tansanbomb', 'sameride'],
            'sshooter' => ['kyubanbomb', 'ultrashot'],
            'wakaba' => ['splashbomb', 'greatbarrier'],
            // rollers
            'dynamo' => ['sprinkler', 'energystand'],
            'splatroller' => ['curlingbomb', 'greatbarrier'],
            // chargers
            'splatcharger' => ['splashbomb', 'kyuinki'],
            'splatscope' => ['splashbomb', 'kyuinki'],
            'liter4k' => ['trap', 'hopsonar'],
            'liter4k_scope' => ['trap', 'hopsonar'],
            // sloshers
            'bucketslosher' => ['poisonmist', 'tripletornado'],
            'hissen' => ['splashbomb', 'jetpack'],
            // spinner/splatlings
            'barrelspinner' => ['sprinkler', 'hopsonar'],
            'hydra' => ['robotbomb', 'nicedama'],
            // maneuvers/dualies
            'maneuver' => ['kyubanbomb', 'kanitank'],
            'quadhopper_black' => ['robotbomb', 'sameride'],
            // shelters/brellas
            'parashelter' => ['sprinkler', 'tripletornado'],
            'campingshelter' => ['jumpbeacon', 'kyuinki'],
            // blasters
            'nova' => ['splashbomb', 'shokuwander'],
            'hotblaster' => ['robotbomb', 'greatbarrier'],
            // brushes
            'pablo' => ['splashbomb', 'megaphone51'],
            'hokusai' => ['kyubanbomb', 'shokuwander'],
            // stringers
            'tristringer' => ['poisonmist', 'megaphone51'],
            // wipers/splatanas
            'drivewiper' => ['torpedo', 'ultrahanko'],
        ];
    }

    /**
     * @phpstan-return array{string, int}
     */
    private function getIdMap(string $tableName): array
    {
        return ArrayHelper::map(
            (new Query())->select(['id', 'key'])->from($tableName)->all(),
            'key',
            fn (array $row): int => filter_var($row['id'], FILTER_VALIDATE_INT),
        );
    }
}
