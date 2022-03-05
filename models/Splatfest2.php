<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use DateTimeImmutable;
use DateTimeZone;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest2".
 *
 * @property int $id
 * @property string $name_a
 * @property string $name_b
 * @property string $term
 * @property string $query_term
 *
 * @property Region2[] $regions
 * @property Splatfest2Region[] $splatfest2Regions
 *
 * @property-read DateTimeImmutable $beginTime
 * @property-read DateTimeImmutable $endTime
 * @property-read DateTimeImmutable $queryBeginTime
 * @property-read DateTimeImmutable $queryEndTime
 * @property-read string $permaID
 */
class Splatfest2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatfest2';
    }

    public function rules()
    {
        return [
            [['name_a', 'name_b', 'term', 'query_term'], 'required'],
            [['term', 'query_term'], 'string'],
            [['name_a', 'name_b'], 'string', 'max' => 63],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_a' => 'Name A',
            'name_b' => 'Name B',
            'term' => 'Term',
            'query_term' => 'Query Term',
        ];
    }

    private ?array $termCache = null;
    private ?array $queryTermCache = null;

    private function splitTerm(): ?array
    {
        if ($this->termCache === null) {
            $this->termCache = $this->splitTermImpl($this->term);
        }
        return $this->termCache;
    }

    private function splitQueryTerm(): ?array
    {
        if ($this->queryTermCache === null) {
            $this->queryTermCache = $this->splitTermImpl($this->query_term);
        }
        return $this->queryTermCache;
    }

    private function splitTermImpl($term): ?array
    {
        // 将来ネイティブに range に対応が入ればたぶんこうなるんじゃないかな…
        if (is_array($term)) {
            return $term;
        } elseif (preg_match('/^[\[(](.*?)\s*,\s*(.*?)[)\]]$/', trim((string)$term), $match)) {
            return [
                new DateTimeImmutable(trim($match[1], ' "\'')),
                new DateTimeImmutable(trim($match[2], ' "\'')),
            ];
        }
        return null;
    }

    public function getBeginTime(): DateTimeImmutable
    {
        return $this->splitTerm()[0];
    }

    public function getEndTime(): DateTimeImmutable
    {
        return $this->splitTerm()[1];
    }

    public function getQueryBeginTime(): DateTimeImmutable
    {
        return $this->splitQueryTerm()[0];
    }

    public function getQueryEndTime(): DateTimeImmutable
    {
        return $this->splitQueryTerm()[1];
    }

    public function getPermaID(): string
    {
        return vsprintf('fest-%s-%s-%s', [
            $this->beginTime->setTimezone(new DateTimeZone('Etc/UTC'))->format('Y.m.d'),
            substr(preg_replace('/[^\w]+/', '_', trim(strtolower($this->name_a))), 0, 15),
            substr(preg_replace('/[^\w]+/', '_', trim(strtolower($this->name_b))), 0, 15),
        ]);
    }

    public function getRegions(): ActiveQuery
    {
        return $this->hasMany(Region2::class, ['id' => 'region_id'])
            ->viaTable('splatfest2_region', ['fest_id' => 'id']);
    }

    public function getSplatfest2Regions(): ActiveQuery
    {
        return $this->hasMany(Splatfest2Region::class, ['fest_id' => 'id']);
    }
}
