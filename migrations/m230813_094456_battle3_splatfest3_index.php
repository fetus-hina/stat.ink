<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230813_094456_battle3_splatfest3_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute(
            vsprintf('CREATE INDEX %s ON %s ( %s ) WHERE (%s)', [
                'battle3_entire_splatfest3',
                '{{%battle3}}',
                implode(', ', [
                    '[[start_at]]',
                ]),
                implode(') AND (', [
                    '[[end_at]] IS NOT NULL',
                    '[[has_disconnect]] = FALSE',
                    '[[is_automated]] = TRUE',
                    '[[is_deleted]] = FALSE',
                    '[[our_team_color]] IS NOT NULL',
                    '[[our_team_theme_id]] IS NOT NULL',
                    '[[start_at]] IS NOT NULL',
                    '[[their_team_color]] IS NOT NULL',
                    '[[their_team_theme_id]] IS NOT NULL',
                    '[[use_for_entire]] = TRUE',
                    '[[start_at]] < [[end_at]]',
                    vsprintf('[[lobby_id]] IN (%d, %d)', [
                        $this->key2id('{{%lobby3}}', 'splatfest_open'),
                        $this->key2id('{{%lobby3}}', 'splatfest_challenge'),
                    ]),
                    vsprintf('[[rule_id]] = %d', [
                        $this->key2id('{{%rule3}}', 'nawabari'),
                    ]),
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
        $this->dropIndex('battle3_entire_splatfest3', '{{%battle3}}');

        return true;
    }
}
