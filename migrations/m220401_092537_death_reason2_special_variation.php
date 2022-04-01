<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m220401_092537_death_reason2_special_variation extends Migration
{
    private const TYPE_KEY_SPECIAL = 'special';

    private const SPECIAL_KEY_JETPACK = 'jetpack';
    private const SPECIAL_KEY_SPHERE = 'sphere';

    private const KEY_JETPACK_EXHAUST = 'jetpack_exhaust';
    private const KEY_SPHERE_SPLASH = 'sphere_splash';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $typeIdSpecial = $this->findIdByKey('death_reason_type2', self::TYPE_KEY_SPECIAL);

        $this->batchInsert('death_reason2', ['key', 'type_id', 'special_id', 'name'], [
            [
                self::KEY_JETPACK_EXHAUST,
                $typeIdSpecial,
                $this->findIdByKey('special2', self::SPECIAL_KEY_JETPACK),
                'Inkjet Exhaust',
            ],
            [
                self::KEY_SPHERE_SPLASH,
                $typeIdSpecial,
                $this->findIdByKey('special2', self::SPECIAL_KEY_SPHERE),
                'Baller Inksplosion',
            ],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete(
            'death_reason2',
            [
                'key' => [
                    self::KEY_JETPACK_EXHAUST,
                    self::KEY_SPHERE_SPLASH,
                ],
            ],
        );

        return true;
    }

    private function findIdByKey(string $tableName, string $key): int
    {
        $value = (new Query())
            ->select(['id'])
            ->from(['t' => $tableName])
            ->andWhere(['t.key' => $key])
            ->limit(1)
            ->scalar($this->getDb());

        $intVal = filter_var($value, FILTER_VALIDATE_INT);
        if (!is_int($intVal)) {
            throw new RuntimeException("Unknown key {$key} for table {$tableName}");
        }

        return $intVal;
    }
}
