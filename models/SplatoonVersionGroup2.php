<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models;

use DateTimeImmutable;
use app\components\helpers\TypeHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use const SORT_ASC;
use const SORT_DESC;

/**
 * This is the model class for table "splatoon_version_group2".
 *
 * @property integer $id
 * @property string|null $tag
 * @property string|null $name
 *
 * @property SplatoonVersion2[] $versions
 */
class SplatoonVersionGroup2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatoon_version_group2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['name'], 'unique'],
            [['tag'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag' => 'Tag',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getVersions()
    {
        return $this->hasMany(SplatoonVersion2::class, ['group_id' => 'id']);
    }

    /**
     * @return array{DateTimeImmutable, DateTimeImmutable|null}
     */
    public function getAvailableDateRange(): array
    {
        return [
            $this->getReleaseDate(),
            $this->getObsoleteDate(),
        ];
    }

    public function getReleaseDate(): DateTimeImmutable
    {
        $firstVersion = SplatoonVersion2::find()
            ->andWhere(['group_id' => $this->id])
            ->orderBy(['released_at' => SORT_ASC])
            ->limit(1)
            ->one();

        return new DateTimeImmutable(
            TypeHelper::instanceOf($firstVersion, SplatoonVersion2::class)->released_at,
        );
    }

    public function getObsoleteDate(): ?DateTimeImmutable
    {
        $lastVersionOfThisGroup = TypeHelper::instanceOf(
            SplatoonVersion2::find()
                ->andWhere(['group_id' => $this->id])
                ->orderBy(['released_at' => SORT_DESC])
                ->limit(1)
                ->one(),
            SplatoonVersion2::class,
        );

        $nextVersion = SplatoonVersion2::find()
            ->andWhere(['>', 'released_at', $lastVersionOfThisGroup->released_at])
            ->orderBy(['released_at' => SORT_ASC])
            ->limit(1)
            ->one();

        return $nextVersion
            ? new DateTimeImmutable($nextVersion->released_at)
            : null;
    }
}
