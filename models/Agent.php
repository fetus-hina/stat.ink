<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use Yii;
use app\components\Version;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function in_array;
use function preg_match;
use function rawurlencode;
use function sprintf;
use function trim;

/**
 * This is the model class for table "agent".
 *
 * @property integer $id
 * @property string $name
 * @property string $version
 *
 * @property AgentAttribute|null $agentAttribute
 * @property Battle2[] $battle2s
 * @property Battle[] $battles
 * @property Salmon2[] $salmon2s
 * @property-read string|null $productUrl
 * @property-read bool $isIkaLog
 * @property-read string|null $versionUrl
 */
class Agent extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'version'], 'required'],
            [['name'], 'string', 'max' => 64],
            [['version'], 'string', 'max' => 255],
            [['name', 'version'], 'unique',
                'targetAttribute' => ['name', 'version'],
                'message' => 'The combination of Name and Version has already been taken.',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'version' => 'Version',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::class, ['agent_id' => 'id']);
    }

    public function getAgentAttribute(): ActiveQuery
    {
        return $this->hasOne(AgentAttribute::class, ['name' => 'name']);
    }

    public function getIsAutomatedByDefault(): bool
    {
        $attr = $this->agentAttribute;
        return $attr && $attr->is_automated;
    }

    public function getProductUrl(): ?string
    {
        if (!$attr = $this->agentAttribute) {
            return null;
        }
        if (trim((string)$attr->link_url) === '') {
            return null;
        }
        return Yii::t('app-link', $attr->link_url);
    }

    public function getVersionUrl(): ?string
    {
        if ($this->getIsIkalog() && preg_match('/^[0-9a-f]{7,}/', $this->version, $match)) {
            $ikalog = IkalogVersion::findOneByRevision($match[0]);
            if ($ikalog) {
                return sprintf(
                    'https://github.com/hasegaw/IkaLog/tree/%s',
                    rawurlencode($ikalog->revision),
                );
            }
        }
        if ($this->getIsStatinkWeb()) {
            if (preg_match('/\(([0-9a-f]{7,}\b)/', $this->version, $match)) {
                $version = Version::getFullHash($match[1]);
                if ($version) {
                    return sprintf(
                        'https://github.com/fetus-hina/stat.ink/tree/%s',
                        rawurlencode($version),
                    );
                }
            }
        }
        return null;
    }

    public function getIsIkaRec(): bool
    {
        return in_array($this->name, [
            'IkaRec',
            'IkaRecord',
            'IkaRec-en',
        ]);
    }

    public function getIsIkalog(): bool
    {
        return $this->name === 'IkaLog' || $this->name === 'TakoLog';
    }

    public function getIsStatinkWeb(): bool
    {
        return $this->name === 'stat.ink web client';
    }

    public function getIsOldIkalogAsAtTheTime(mixed $t = null): bool
    {
        return false;
    }
}
