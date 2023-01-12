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

final class m230112_041055_stat_ink_color3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->createTable('{{%stat_ink_color3}}', [
            'color1' => $this->char(8)->notNull(),
            'color2' => $this->char(8)->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'wins' => $this->bigInteger()->notNull(),

            'PRIMARY KEY ([[color1]], [[color2]])',
            'CHECK ([[color1]] < [[color2]])',
        ]);

        $color1 = 'LEAST({{%battle3}}.[[our_team_color]], {{%battle3}}.[[their_team_color]])';
        $color2 = 'GREATEST({{%battle3}}.[[our_team_color]], {{%battle3}}.[[their_team_color]])';

        $this->execute(
            vsprintf('INSERT INTO %s %s', [
                $db->quoteTableName('{{%stat_ink_color3}}'),
                (new Query())
                    ->select([
                        'color1' => $color1,
                        'color2' => $color2,
                        'battles' => 'COUNT(*)',
                        'wins' => vsprintf('SUM(%s)', [
                            vsprintf('CASE WHEN %s THEN 1 ELSE 0 END', [
                                "{{%result3}}.[[is_win]] = ($color1 = {{%battle3}}.[[our_team_color]])",
                            ]),
                        ]),
                    ])
                    ->from('{{%battle3}}')
                    ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
                    ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
                    ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
                    ->andWhere(['and',
                        [
                            '{{%battle3}}.[[has_disconnect]]' => false,
                            '{{%battle3}}.[[is_deleted]]' => false,
                            '{{%battle3}}.[[use_for_entire]]' => true,
                            '{{%result3}}.[[aggregatable]]' => true,
                        ],
                        ['<>', '{{%rule3}}.[[key]]', 'tricolor'],
                        ['not', ['{{%battle3}}.[[our_team_color]]' => null]],
                        ['not', ['{{%battle3}}.[[their_team_color]]' => null]],
                        ['not', ['{{%lobby3}}.[[key]]' => ['private', 'splatfest_challenge', 'splatfest_open']]],
                        '{{%battle3}}.[[our_team_color]] <> {{%battle3}}.[[their_team_color]]',
                    ])
                    ->groupBy([$color1, $color2])
                    ->createCommand($db)
                    ->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_ink_color3}}');

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%stat_ink_color3}}',
        ];
    }
}
