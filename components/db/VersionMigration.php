<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\db;

use DateTime;
use DateTimeInterface;
use app\components\helpers\Battle as BattleHelper;
use yii\db\Query;

trait VersionMigration
{
    protected function upVersion2(
        string $groupTag,
        string $groupName,
        string $versionTag,
        string $versionName,
        DateTimeInterface $releasedAt
    ): int {
        $this->insert('splatoon_version2', [
            'tag' => $versionTag,
            'name' => $versionName,
            'released_at' => $releasedAt->format(DateTime::ATOM),
            'group_id' => $this->upVersionGroup2($groupTag, $groupName),
        ]);
        $this->update(
            'battle2',
            ['version_id' => $this->findId('splatoon_version2', $versionTag)],
            ['>=', 'period', BattleHelper::calcPeriod2($releasedAt->getTimestamp())]
        );
        return $this->findId('splatoon_version2', $versionTag);
    }

    protected function downVersion2(string $versionTag, string $oldVersionTag): void
    {
        // グループを消すかどうか決めるために最初に探しておく
        $groupId = (new Query())
            ->select('group_id')
            ->from('splatoon_version2')
            ->where(['tag' => $versionTag])
            ->limit(1)
            ->scalar();
        if (!$groupId) {
            throw new \Exception('Could not find version ' . $versionTag);
        }

        $this->update(
            'battle2',
            ['version_id' => $this->findId('splatoon_version2', $oldVersionTag)],
            ['version_id' => $this->findId('splatoon_version2', $versionTag)]
        );

        $this->delete('splatoon_version2', ['tag' => $versionTag]);

        // グループに所属する数を数えて存在しなければ消す
        $count = (new Query())
            ->select('COUNT(*)')
            ->from('splatoon_version2')
            ->where(['group_id' => $groupId])
            ->scalar();
        if ($count == 0) {
            $this->delete('splatoon_version_group2', ['id' => $groupId]);
        }
    }

    private function upVersionGroup2(string $tag, string $name): int
    {
        $id = $this->findId('splatoon_version_group2', $tag);
        if ($id === null) {
            $this->insert('splatoon_version_group2', [
                'tag' => $tag,
                'name' => $name,
            ]);
            $id = $this->findId('splatoon_version_group2', $tag);
        }
        if ($id === null) {
            throw new \Exception('Could not create new version group');
        }
        return $id;
    }

    private function findId(string $table, string $tag): ?int
    {
        $id = (new Query())
            ->select('id')
            ->from($table)
            ->where(['tag' => $tag])
            ->limit(1)
            ->scalar();
        return $id ? (int)$id : null;
    }
}
