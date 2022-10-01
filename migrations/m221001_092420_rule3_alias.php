<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

final class m221001_092420_rule3_alias extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%rule3_alias}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apikey3()->notNull()->unique(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
        ]);

        $idMap = ArrayHelper::map(
            (new Query())->select(['id', 'key'])->from('{{%rule3}}')->all(),
            'key',
            'id'
        );

        $this->batchInsert('{{%rule3_alias}}', ['key', 'rule_id'], [
            // SplatNet keys
            [strtolower('TURF_WAR'), $idMap['nawabari']],
            [strtolower('LOFT'), $idMap['yagura']],
            [strtolower('GOAL'), $idMap['hoko']],
            [strtolower('CLAM'), $idMap['asari']],
            // English names
            [self::name2key3('Splat Zones'), $idMap['area']],
            [self::name2key3('Tower Control'), $idMap['yagura']],
            [self::name2key3('Rainmaker'), $idMap['hoko']],
            [self::name2key3('Clam Blitz'), $idMap['asari']],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%rule3_alias}}');

        return true;
    }
}
