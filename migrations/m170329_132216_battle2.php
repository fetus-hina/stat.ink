<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170329_132216_battle2 extends Migration
{
    public function up()
    {
        $this->execute(
            Yii::$app->db
                ->createCommand(
                    'CREATE DOMAIN {{rgb}} CHAR(6) NOT NULL CHECK ( VALUE ~ :regex )',
                )
                ->bindValue(':regex', '^\x{6}$')
                ->rawSql,
        );
        $this->execute(
            Yii::$app->db
                ->createCommand(
                    'CREATE DOMAIN {{hue}} SMALLINT NOT NULL CHECK ( VALUE BETWEEN 0 AND 359 )',
                )
                ->rawSql,
        );
        $this->execute(
            Yii::$app->db
                ->createCommand(
                    'CREATE DOMAIN {{httpurl}} TEXT NOT NULL CHECK ( VALUE ~ :regex )',
                )
                ->bindValue(
                    ':regex',
                    '^https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+$',
                )
                ->rawSql,
        );
        $this->createTable('battle2', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigPkRef('user'),
            'lobby_id' => $this->pkRef('lobby2')->null(),
            'mode_id' => $this->pkRef('mode2')->null(),
            'rule_id' => $this->pkRef('rule2')->null(),
            'map_id' => $this->pkRef('map2')->null(),
            'weapon_id' => $this->pkRef('weapon2')->null(),
            'is_win' => $this->boolean(),
            'is_knockout' => $this->boolean(),
            'level' => $this->integer(),
            'level_after' => $this->integer(),
            'rank_id' => $this->pkRef('rank2')->null(),
            'rank_exp' => $this->integer(),
            'rank_after_id' => $this->pkRef('rank2')->null(),
            'rank_after_exp' => $this->integer(),
            'rank_in_team' => $this->integer(),
            'kill' => $this->integer(),
            'death' => $this->integer(),
            'kill_ratio' => $this->decimal(4, 2),
            'kill_rate' => $this->decimal(5, 2),
            'max_kill_combo' => $this->integer(),
            'max_kill_streak' => $this->integer(),
            'my_point' => $this->integer(),
            'my_team_point' => $this->integer(),
            'his_team_point' => $this->integer(),
            'my_team_percent' => $this->decimal(4, 1),
            'his_team_percent' => $this->decimal(4, 1),
            'my_team_count' => $this->integer(),
            'his_team_count' => $this->integer(),
            'my_team_color_hue' => 'hue NULL',
            'his_team_color_hue' => 'hue NULL',
            'my_team_color_rgb' => 'rgb NULL',
            'his_team_color_rgb' => 'rgb NULL',
            'cash' => $this->integer(),
            'cash_after' => $this->integer(),
            'note' => $this->text(),
            'private_note' => $this->text(),
            'link_url' => 'httpurl NULL',
            'period' => $this->integer(),
            'version_id' => $this->pkRef('splatoon_version2')->null(),
            'bonus_id' => $this->pkRef('turfwar_win_bonus2')->null(),
            'env_id' => $this->pkRef('environment')->null(),
            'client_uuid' => 'UUID NOT NULL',
            'ua_variables' => 'JSONB NULL DEFAULT NULL',
            'ua_custom' => $this->text(),
            'agent_game_version_id' => $this->pkRef('splatoon_version2')->null(),
            'agent_game_version_date' => $this->string(32),
            'agent_id' => $this->pkRef('agent')->null(),
            'is_automated' => $this->boolean()->notNull()->defaultValue(false),
            'use_for_entire' => $this->boolean()->notNull()->defaultValue(false),
            'remote_addr' => 'INET NULL DEFAULT NULL',
            'remote_port' => $this->integer()->check('([[remote_port]] BETWEEN 0 AND 65535)'),
            'start_at' => $this->timestampTZ(),
            'end_at' => $this->timestampTZ(),
            'at' => $this->timestampTZ()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('battle2');
        $this->execute('DROP DOMAIN {{rgb}}, {{hue}}, {{httpurl}}');
    }
}
