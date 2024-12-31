<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\Version;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function date;
use function in_array;
use function preg_match;
use function rawurlencode;
use function sprintf;
use function strtotime;
use function trim;

/**
 * This is the model class for table "agent".
 *
 * @property integer $id
 * @property string $name
 * @property string $version
 *
 * @property AgentAttribute $agentAttribute
 * @property Battle2[] $battle2s
 * @property Battle[] $battles
 * @property Salmon2[] $salmon2s
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

    public function getIsIkaRec()
    {
        return in_array($this->name, [
            'IkaRec',
            'IkaRecord',
            'IkaRec-en',
        ]);
    }

    public function getIsIkalog()
    {
        return $this->name === 'IkaLog' || $this->name === 'TakoLog';
    }

    public function getIsStatinkWeb()
    {
        return $this->name === 'stat.ink web client';
    }

    public function getIsOldIkalogAsAtTheTime($t = null)
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

        if (static::$latestWinIkaLog === null) {
            static::$latestWinIkaLog = WinikalogVersion::find()
                ->andWhere(['<=', '{{winikalog_version}}.[[build_at]]', date('Y-m-d H:i:sP', $t)])
                ->orderBy('{{winikalog_version}}.[[build_at]] DESC')
                ->limit(1)
                ->one();
        }

        if (static::$latestWinIkaLog->id === $thisWinIkaLog->id) {
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
        if (static::$latestIkaLog === null) {
            static::$latestIkaLog = IkalogVersion::find()
                ->andWhere(['<=', '{{ikalog_version}}.[[at]]', date('Y-m-d H:i:sP', $t)])
                ->orderBy('{{ikalog_version}}.[[at]] DESC')
                ->limit(1)
                ->one();
        }
        if (static::$latestIkaLog->id === $ikalog->id) {
            // これより新しいバージョンは存在しない
            return false;
        }

        $diff = $t - strtotime($ikalog->at);
        return $diff >= 21 * 86400;
    }
}
