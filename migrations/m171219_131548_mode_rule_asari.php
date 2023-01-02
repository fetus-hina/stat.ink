<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m171219_131548_mode_rule_asari extends Migration
{
    public function safeUp()
    {
        $modes = $this->getModes();
        $rules = $this->getRules();

        $this->batchInsert('mode_rule2', ['mode_id', 'rule_id'], [
            [$modes['gachi'], $rules['asari']],
            [$modes['private'], $rules['asari']],
        ]);
    }

    public function safeDown()
    {
        $rules = $this->getRules();
        $this->delete('mode_rule2', ['rule_id' => $rules['asari']]);
    }

    private function getModes(): array
    {
        return ArrayHelper::map(
            (new Query())->select(['key', 'id'])->from('mode2')->all(),
            'key',
            'id',
        );
    }

    private function getRules(): array
    {
        return ArrayHelper::map(
            (new Query())->select(['key', 'id'])->from('rule2')->all(),
            'key',
            'id',
        );
    }
}
