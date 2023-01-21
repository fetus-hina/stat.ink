<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m230121_111509_drop_use_for_entire_itok extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $agentIds = ArrayHelper::getColumn(
            (new Query())
                ->select('*')
                ->from('{{agent}}')
                ->andWhere(['name' => 'itok.stat'])
                ->all($db),
            'id',
        );

        $this->update(
            '{{%battle3}}',
            ['use_for_entire' => false],
            ['agent_id' => $agentIds],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return true;
    }
}
