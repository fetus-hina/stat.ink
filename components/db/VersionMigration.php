<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\db;

use DateTime;
use DateTimeInterface;
use Exception;
use app\components\helpers\Battle as BattleHelper;
use yii\db\Query;

trait VersionMigration
{
    protected function upVersion3(
        string $groupTag,
        string $groupName,
        string $versionTag,
        string $versionName,
        DateTimeInterface $releasedAt,
    ): int {
        return $this->upVersion2Impl(
            '{{%battle3}}',
            '{{%splatoon_version3}}',
            'release_at',
            $this->upVersionGroup3($groupTag, $groupName),
            $versionTag,
            $versionName,
            $releasedAt,
            true,
        );
    }

    protected function upVersion2(
        string $groupTag,
        string $groupName,
        string $versionTag,
        string $versionName,
        DateTimeInterface $releasedAt,
    ): int {
        return $this->upVersion2Impl(
            'battle2',
            'splatoon_version2',
            'released_at',
            $this->upVersionGroup2($groupTag, $groupName),
            $versionTag,
            $versionName,
            $releasedAt,
            false,
        );
    }

    private function upVersion2Impl(
        string $tableBattle,
        string $tableVersion,
        string $releasedAtColumn,
        int $groupId,
        string $versionTag,
        string $versionName,
        DateTimeInterface $releasedAt,
        bool $byDate = false,
    ): int {
        $this->insert($tableVersion, [
            'tag' => $versionTag,
            'name' => $versionName,
            $releasedAtColumn => $releasedAt->format(DateTime::ATOM),
            'group_id' => $groupId,
        ]);
        $this->update(
            $tableBattle,
            ['version_id' => $this->findId($tableVersion, $versionTag)],
            $byDate
                ? ['>=', 'start_at', $releasedAt->format(DateTime::ATOM)]
                : ['>=', 'period', BattleHelper::calcPeriod2($releasedAt->getTimestamp())],
        );
        return $this->findId($tableVersion, $versionTag);
    }

    protected function downVersion3(string $versionTag, string $oldVersionTag): void
    {
        $this->downVersion2Impl(
            '{{%battle3}}',
            '{{%splatoon_version3}}',
            '{{%splatoon_version_group3}}',
            $versionTag,
            $oldVersionTag,
        );
    }

    protected function downVersion2(string $versionTag, string $oldVersionTag): void
    {
        $this->downVersion2Impl(
            'battle2',
            'splatoon_version2',
            'splatoon_version_group2',
            $versionTag,
            $oldVersionTag,
        );
    }

    private function downVersion2Impl(
        string $tableBattle,
        string $tableVersion,
        string $tableGroup,
        string $versionTag,
        string $oldVersionTag,
    ): void {
        // グループを消すかどうか決めるために最初に探しておく
        $groupId = (new Query())
            ->select('group_id')
            ->from($tableVersion)
            ->where(['tag' => $versionTag])
            ->limit(1)
            ->scalar();
        if (!$groupId) {
            throw new Exception('Could not find version ' . $versionTag);
        }

        $this->update(
            $tableBattle,
            ['version_id' => $this->findId($tableVersion, $oldVersionTag)],
            ['version_id' => $this->findId($tableVersion, $versionTag)],
        );

        $this->delete($tableVersion, ['tag' => $versionTag]);

        // グループに所属する数を数えて存在しなければ消す
        $count = (new Query())
            ->select('COUNT(*)')
            ->from($tableVersion)
            ->where(['group_id' => $groupId])
            ->scalar();
        if ($count == 0) {
            $this->delete($tableGroup, ['id' => $groupId]);
        }
    }

    private function upVersionGroup3(string $tag, string $name): int
    {
        return $this->upVersionGroup2Impl(
            '{{%splatoon_version_group3}}',
            $tag,
            $name,
        );
    }

    private function upVersionGroup2(string $tag, string $name): int
    {
        return $this->upVersionGroup2Impl(
            'splatoon_version_group2',
            $tag,
            $name,
        );
    }

    private function upVersionGroup2Impl(string $table, string $tag, string $name): int
    {
        $id = $this->findId($table, $tag);
        if ($id === null) {
            $this->insert($table, [
                'tag' => $tag,
                'name' => $name,
            ]);
            $id = $this->findId($table, $tag);
        }
        if ($id === null) {
            throw new Exception('Could not create new version group');
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
