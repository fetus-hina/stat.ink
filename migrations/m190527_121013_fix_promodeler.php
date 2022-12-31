<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m190527_121013_fix_promodeler extends Migration
{
    public function safeUp()
    {
        $w = static::getWeapons();
        $this->update(
            'weapon2',
            ['main_group_id' => $w['promodeler_mg']],
            ['id' => [$w['promodeler_mg'], $w['promodeler_rg']]],
        );
    }

    public function safeDown()
    {
        $w = static::getWeapons();
        $this->update(
            'weapon2',
            ['main_group_id' => $w['promodeler_rg']],
            ['id' => [$w['promodeler_mg'], $w['promodeler_rg']]],
        );
    }

    public static function getWeapons(): array
    {
        return ArrayHelper::map(
            (new Query())
                ->select(['id', 'key'])
                ->from('weapon2')
                ->andWhere(['like', 'key', 'promodeler_%', false])
                ->all(),
            'key',
            'id',
        );
    }
}
