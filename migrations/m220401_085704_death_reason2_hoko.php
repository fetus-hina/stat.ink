<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m220401_085704_death_reason2_hoko extends Migration
{
    private const TYPE_KEY_HOKO = 'hoko';

    private const REASON_KEY_BARRIER = 'hoko_barrier';
    private const REASON_KEY_INKSPLODE = 'hoko_inksplode';
    private const REASON_KEY_SHOT = 'hoko_shot';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('death_reason_type2', [
            'key' => self::TYPE_KEY_HOKO,
            'name' => 'Rainmaker',
        ]);

        $typeHoko = $this->getHokoTypeId();

        $this->batchInsert('death_reason2', ['key', 'type_id', 'name'], [
            [self::REASON_KEY_BARRIER, $typeHoko, 'Rainmaker Shield'],
            [self::REASON_KEY_INKSPLODE, $typeHoko, 'Rainmaker Inksplosion'],
            [self::REASON_KEY_SHOT, $typeHoko, 'Rainmaker Shot'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $typeHoko = $this->getHokoTypeId();

        $this->delete('death_reason2', ['type_id' => $typeHoko]);
        $this->delete('death_reason_type2', ['id' => $typeHoko]);

        return true;
    }

    private function getHokoTypeId(): int
    {
        $value = (new Query())
            ->select(['id'])
            ->from('death_reason_type2')
            ->where(['key' => self::TYPE_KEY_HOKO])
            ->limit(1)
            ->scalar($this->getDb());

        if (!is_int($intVal = filter_var($value, FILTER_VALIDATE_INT))) {
            throw new RuntimeException();
        }

        return $intVal;
    }
}
