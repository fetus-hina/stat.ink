<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m171108_192208_version_group extends Migration
{
    public function up()
    {
        $this->execute(
            'ALTER TABLE {{splatoon_version2}} ' .
            'ADD COLUMN [[group_id]] INTEGER NULL ' .
            'REFERENCES {{splatoon_version_group2}}([[id]])',
        );
        $transaction = $this->db->beginTransaction();
        $g = $this->getGroups();
        foreach ($this->getData() as $gTag => $vTags) {
            $this->update(
                '{{splatoon_version2}}',
                ['group_id' => $g[$gTag]],
                ['tag' => $vTags],
            );
        }
        $transaction->commit();
        $this->execute(
            'ALTER TABLE {{splatoon_version2}} ' .
            'ALTER COLUMN [[group_id]] SET NOT NULL',
        );
    }

    public function down()
    {
        $this->dropColumn('splatoon_version2', 'group_id');
    }

    private function getData(): array
    {
        return [
            '0.0' => [
                '0.0.1',
                '0.1.0',
            ],
            '1.0' => [
                '1.0.0',
                '1.1.2',
            ],
            '1.2' => [
                '1.2.0',
            ],
            '1.3' => [
                '1.3.0',
            ],
            '1.4' => [
                '1.4.0',
                '1.4.1',
                '1.4.2',
            ],
        ];
    }

    private function getGroups(): array
    {
        return ArrayHelper::map(
            (new Query())
                ->select(['id', 'tag'])
                ->from('splatoon_version_group2')
                ->all(),
            'tag',
            'id',
        );
    }
}
