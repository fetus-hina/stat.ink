<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230615_012948_salmon3_played_with extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute(
            vsprintf('CREATE INDEX %s ON %s (%s) WHERE ((%s))', [
                '[[salmon_player3_played_with]]',
                '{{%salmon_player3}}',
                implode(', ', [
                    '[[salmon_id]]',
                    '[[name]]',
                    '[[number]]',
                ]),
                implode(') AND (', [
                    '[[is_me]] = FALSE',
                    '[[name]] IS NOT NULL',
                    '[[number]] IS NOT NULL',
                ]),
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('salmon_player3_played_with', '{{%salmon_player3}}');

        return true;
    }
}
