<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Expression as DbExpr;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m171219_110233_fest_theme_table extends Migration
{
    public function up()
    {
        $this->createTable('splatfest2_theme', [
            'id' => $this->primaryKey(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('splatfest2_theme', ['name'], $this->createData());
    }

    public function down()
    {
        $this->dropTable('splatfest2_theme');
    }

    private function createData(): array
    {
        return array_map( // [ [name], [name], [name] ... ] ( for batchInsert )
            fn (string $name): array => [$name],
            array_keys( // [ name, name, name ... ]
                $this->mergeData( // [ name => first_seen ]
                    $this->queryData('my_team_fes_theme'), // [ name => first_seen ]
                    $this->queryData('other_team_fes_theme')
                )
            )
        );
    }

    private function queryData(string $field): array
    {
        $db = Yii::$app->db;
        $qField = new DbExpr(sprintf(
            '%s.%s->%s->>%s',
            $db->quoteTableName('battle2_splatnet'),
            $db->quoteColumnName('json'),
            $db->quoteValue($field),
            $db->quoteValue('name')
        ));
        $query = (new Query())
            ->select([
                'first_seen' => 'MIN({{battle2}}.[[id]])',
                'name' => $qField,
            ])
            ->from('battle2')
            ->innerJoin('battle2_splatnet', '{{battle2}}.[[id]] = {{battle2_splatnet}}.[[id]]')
            ->innerJoin('mode2', '{{battle2}}.[[mode_id]] = {{mode2}}.[[id]]')
            ->innerJoin('lobby2', '{{battle2}}.[[lobby_id]] = {{lobby2}}.[[id]]')
            ->andWhere(['and',
                [
                    '{{mode2}}.[[key]]' => 'fest',
                    '{{lobby2}}.[[key]]' => 'standard',
                ],
                sprintf('%s IS NOT NULL', $qField),
            ])
            ->groupBy([$qField]);
        return ArrayHelper::map(
            $query->all(),
            'name',
            'first_seen'
        );
    }

    private function mergeData(): array
    {
        $args = func_get_args();
        $result = [];
        foreach ($args as $arg) {
            foreach ($arg as $name => $firstSeen) {
                $result[$name] = (int)min(($result[$name] ?? PHP_INT_MAX), $firstSeen);
            }
        }
        asort($result);
        return $result;
    }
}
