<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m221015_041843_has_disconnect extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(
            '{{%battle3}}',
            'has_disconnect',
            (string)$this->boolean()->notNull()->defaultValue(false),
        );

        $selectIds = (new Query())
            ->select(['id' => '{{%battle_player3}}.[[battle_id]]'])
            ->from('{{%battle_player3}}')
            ->where(['is_disconnected' => true])
            ->groupBy(['{{%battle_player3}}.[[battle_id]]'])
            ->orderBy(['{{%battle_player3}}.[[battle_id]]' => SORT_ASC]);

        $this->update('{{%battle3}}', ['has_disconnect' => true], [
            'id' => $selectIds,
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%battle3}}', 'has_disconnect');

        return true;
    }
}
