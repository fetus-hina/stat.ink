<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m220402_103529_death_reason2_propeller extends Migration
{
    const KEY_STRING = 'propeller';
    const TYPE_GADGET = 'gadget';

    public function safeUp()
    {
        $this->insert('death_reason2', [
            'key' => self::KEY_STRING,
            'type_id' => $this->getTypeId(),
            'name' => 'Ink from a propeller',
        ]);

        return true;
    }

    public function safeDown()
    {
        $this->delete('death_reason2', ['key' => self::KEY_STRING]);

        return true;
    }

    private function getTypeId(): int
    {
        $value = filter_var(
            (new Query())
                ->select(['id'])
                ->from('death_reason_type2')
                ->where(['key' => self::TYPE_GADGET])
                ->limit(1)
                ->scalar($this->getDb()),
            FILTER_VALIDATE_INT,
        );
        if (!is_int($value)) {
            throw new RuntimeException();
        }
        return $value;
    }
}
