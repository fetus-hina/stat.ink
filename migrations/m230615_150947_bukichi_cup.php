<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230615_150947_bukichi_cup extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $lobbyId = (int)$this->key2id('{{%lobby3}}', 'event');
        $this->execute(
            vsprintf('CREATE UNIQUE INDEX %s ON %s ( %s ) WHERE ((%s))', [
                '[[battle3_event_id]]',
                '{{%battle3}}',
                implode(', ', [
                    '[[event_id]]',
                    '[[period]]',
                    '[[id]]',
                ]),
                implode(') AND (', [
                    '[[has_disconnect]] = FALSE',
                    '[[is_automated]] = TRUE',
                    '[[is_deleted]] = FALSE',
                    '[[use_for_entire]] = TRUE',
                    "[[lobby_id]] = {$lobbyId}",
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
        $this->dropIndex('battle3_event_id', '{{%battle3}}');

        return true;
    }
}
