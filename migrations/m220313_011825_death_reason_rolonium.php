<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;

final class m220313_011825_death_reason_rolonium extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        if (parent::up() === false) {
            return false;
        }

        $this->execute('VACUUM ( ANALYZE ) {{death_reason2}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->getConnection();
        $this->insert('death_reason2', $this->createData($db));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $db = $this->getConnection();
        $this->delete('death_reason2', [
            'key' => $this->createData($db)['key'],
        ]);

        return true;
    }

    private function getConnection(): Connection
    {
        $db = $this->db;
        assert($db instanceof Connection);
        return $db;
    }

    /**
     * @return array{key: string, type_id: int, name: string}
     */
    private function createData(Connection $db): array
    {
        return [
            'key' => 'korogarium',
            'type_id' => $this->getTypeId($db),
            'name' => 'Rolonium',
        ];
    }

    private function getTypeId(Connection $db): int
    {
        $query = (new Query())
            ->select(['id'])
            ->from('death_reason_type2')
            ->andWhere(['key' => 'gadget'])
            ->limit(1);

        return (int)$query->scalar($db);
    }
}
