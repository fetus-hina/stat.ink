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

final class m220916_002751_spl3_1_1_1 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%splatoon_version3}}', [
            'tag' => '1.1.1',
            'group_id' => $this->getGroupId('1.1'),
            'name' => 'v1.1.1',
            'release_at' => '2022-09-16T11:00:00+09:00',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $versionId = $this->getVersionId('1.1.1');
        $this->update(
            '{{%battle3}}',
            ['version_id' => $this->getVersionId('1.1.0')],
            ['version_id' => $versionId],
        );

        $this->delete('{{%splatoon_version3}}', ['id' => $versionId]);

        return true;
    }

    private function getVersionId(string $tag): int
    {
        return $this->tag2id('{{%splatoon_version3}}', $tag);
    }

    private function getGroupId(string $tag): int
    {
        return $this->tag2id('{{%splatoon_version_group3}}', $tag);
    }

    private function tag2id(string $table, string $tag): int
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $id = (new Query())
            ->select(['id'])
            ->from($table)
            ->andWhere(['tag' => $tag])
            ->limit(1)
            ->scalar($db);

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!is_int($id)) {
            throw new Exception();
        }
        return $id;
    }
}
