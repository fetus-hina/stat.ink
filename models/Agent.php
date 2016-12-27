<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\Version;

/**
 * This is the model class for table "agent".
 *
 * @property integer $id
 * @property string $name
 * @property string $version
 *
 * @property Battle[] $battles
 */
class Agent extends \yii\db\ActiveRecord
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
                'message' => 'The combination of Name and Version has already been taken.']
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

    public function getIsAutomatedByDefault()
    {
        $attr = AgentAttribute::findOne(['name' => (string)$this->name]);
        if ($attr && $attr->is_automated) {
            return true;
        }
        return false;
    }

    public function getProductUrl()
    {
        if ($this->getIsIkaRec()) {
            return $this->getIkaRecProductUrl();
        } elseif ($this->getIsIkalog()) {
            return $this->getIkaLogProductUrl();
        } elseif ($this->getIsStatinkWeb()) {
            return 'https://stat.ink/';
        } else {
            return null;
        }
    }

    protected function getIkaRecProductUrl() : string
    {
        $id = (function () {
            switch (substr(Yii::$app->language, 0, 2)) {
                case 'ja':
                    return 'com.syanari.merluza.ikarec';

                case 'en':
                default:
                    return 'ink.pocketgopher.ikarec';
            }
        })();
        return sprintf(
            'https://play.google.com/store/apps/details?%s',
            http_build_query(['id' => $id], '&', '')
        );
    }

    protected function getIkaLogProductUrl() : string
    {
        switch (substr(Yii::$app->language, 0, 2)) {
            case 'ja':
                return 'https://github.com/hasegaw/IkaLog/wiki/ja_WinIkaLog';

            case 'en':
            default:
                return 'https://github.com/hasegaw/IkaLog/wiki/en_Home';
        }
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

        if ($t === null) {
            $t = $_SERVER['REQUEST_TIME'] ?? time();
        } elseif (is_string($t)) {
            $t = strtotime($t);
        } else {
            $t = (int)$t;
        }
        if (!$this->getIsIkalog()) {
            return false;
        }
        if (preg_match('/^unknown\b/', $this->version)) {
            return false;
        }
        return preg_match('/_Win(?:Ika|Tako)Log$/', $this->version)
            ? $this->getIsOldWinIkalogAsAtTheTime($t)
            : $this->getIsOldCliIkalogAsAtTheTime($t);
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

        if (empty($ikalog->winikalogVersions)) {
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
        return ($diff >= 21 * 86400);
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
        return ($diff >= 21 * 86400);
    }
}
