<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m191216_111834_update_disconnect extends Migration
{
    public function safeUp()
    {
        $select = (new Query())
            ->select([
                'id' => '{{battle_player2}}.[[battle_id]]',
            ])
            ->from('battle_player2')
            ->andWhere(['{{battle_player2}}.[[point]]' => 0])
            ->groupBy(['{{battle_player2}}.[[battle_id]]'])
            ->having(['>', 'COUNT({{battle_player2}}.*)', 0]);
        $this->update('battle2', ['has_disconnect' => true], ['id' => $select]);
    }

    public function safeDown()
    {
        $this->update('battle2', ['has_disconnect' => false]);
    }
}
