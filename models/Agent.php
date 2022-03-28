<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\Version;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "agent".
 *
 * @property int $id
 * @property string $name
 * @property string $version
 *
 * @property AgentAttribute|null $agentAttribute
 * @property Battle2[] $battle2s
 * @property Battle[] $battles
 * @property Salmon2[] $salmon2s
 *
 * @property-read bool $isIkaRec
 * @property-read bool $isIkalog
 * @property-read bool $isStatinkWeb
 * @property-read bool $isOldIkalogAsAtTheTime
 */
final class Agent extends ActiveRecord
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
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::class, ['agent_id' => 'id']);
    }

    public function getAgentAttribute()
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

    public function getVersionUrl()
    {
        if ($this->getIsIkalog() && preg_match('/^[0-9a-f]{7,}/', $this->version, $match)) {
            $ikalog = IkalogVersion::findOneByRevision($match[0]);
            if ($ikalog) {
                return sprintf(
                    'https://github.com/hasegaw/IkaLog/tree/%s',
                    rawurlencode($ikalog->revision)
                );
            }
        }
        if ($this->getIsStatinkWeb()) {
            if (preg_match('/\(([0-9a-f]{7,}\b)/', $this->version, $match)) {
                $version = Version::getFullHash($match[1]);
                if ($version) {
                    return sprintf(
                        'https://github.com/fetus-hina/stat.ink/tree/%s',
                        rawurlencode($version)
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

    public function getIsOldIkalogAsAtTheTime($t = null): bool
    {
        return false;
    }

    private static $latestWinIkaLog;

    private function getIsOldWinIkalogAsAtTheTime($t)
    {
        if (!preg_match('/^([0-9a-f]{7,})_/', $this->version, $match)) {
            // なんかおかしい
            return false;
        }

        $ikalog = IkalogVersion::findOneByRevision($match[1]);
        if (!$ikalog) {
            // 知らない WinIkaLog だった
            return false;
        }

        if (!$ikalog->winikalogVersions) {
            // なぜか WinIkaLog のリリースされてないリビジョンっぽい（たぶん新しすぎて認識できてない）
            return $this->getIsOldCliIkalogAsAtTheTimeImpl($ikalog, $t);
        }
        $thisWinIkaLog = $ikalog->winikalogVersions[0];

        if (self::$latestWinIkaLog === null) {
            self::$latestWinIkaLog = WinikalogVersion::find()
                ->andWhere(['<=', '{{winikalog_version}}.[[build_at]]', date('Y-m-d H:i:sP', $t)])
                ->orderBy('{{winikalog_version}}.[[build_at]] DESC')
                ->limit(1)
                ->one();
        }

        if (self::$latestWinIkaLog->id === $thisWinIkaLog->id) {
            // これより新しいバージョンは存在しない
            return false;
        }

        $diff = $t - strtotime($thisWinIkaLog->build_at);
        return $diff >= 21 * 86400;
    }

    private function getIsOldCliIkalogAsAtTheTime($t)
    {
        if (!preg_match('/^[0-9a-f]{7,}/', $this->version, $match)) {
            // なんかおかしい
            return false;
        }

        $ikalog = IkalogVersion::findOneByRevision($match[0]);
        if (!$ikalog) {
            // 知らない IkaLog だった
            return false;
        }

        return $this->getIsOldCliIkalogAsAtTheTimeImpl($ikalog, $t);
    }

    private static $latestIkaLog;

    private function getIsOldCliIkalogAsAtTheTimeImpl(IkalogVersion $ikalog, $t)
    {
        if (self::$latestIkaLog === null) {
            self::$latestIkaLog = IkalogVersion::find()
                ->andWhere(['<=', '{{ikalog_version}}.[[at]]', date('Y-m-d H:i:sP', $t)])
                ->orderBy('{{ikalog_version}}.[[at]] DESC')
                ->limit(1)
                ->one();
        }
        if (self::$latestIkaLog->id === $ikalog->id) {
            // これより新しいバージョンは存在しない
            return false;
        }

        $diff = $t - strtotime($ikalog->at);
        return $diff >= 21 * 86400;
    }
}
