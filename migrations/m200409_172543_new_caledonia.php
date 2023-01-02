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

// ニューカレドニア（フランス領）の国旗が出ない (flag-icon に fc がない) ので、
// フランスを登録の上、国をフランスに書き換える
class m200409_172543_new_caledonia extends Migration
{
    public function safeUp()
    {
        $this->insert('country', [
            'key' => 'fr',
            'name' => 'France',
        ]);
        $cc = $this->getCountryIds();
        $this->update(
            'timezone_country',
            ['country_id' => $cc['fr']],
            ['timezone_id' => $this->getTimezoneId()],
        );
    }

    public function safeDown()
    {
        $cc = $this->getCountryIds();
        $this->update(
            'timezone_country',
            ['country_id' => $cc['fc']],
            ['timezone_id' => $this->getTimezoneId()],
        );
        $this->delete('country', ['key' => 'fr']);
    }

    private function getTimezoneId(): int
    {
        return (int)(new Query())
            ->select('id')
            ->from('timezone')
            ->where(['identifier' => 'Pacific/Noumea'])
            ->scalar();
    }

    private function getCountryIds(): array
    {
        return ArrayHelper::map(
            (new Query())
                ->select(['id', 'key'])
                ->from('country')
                ->where(['key' => ['fc', 'fr']])
                ->all(),
            'key',
            'id',
        );
    }
}
