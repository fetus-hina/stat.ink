<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

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
        return $this->hasMany(Battle::className(), ['agent_id' => 'id']);
    }

    public function getIsIkalog()
    {
        return $this->name === 'IkaLog' || $this->name === 'TakoLog';
    }

    public function getIsOldIkalogAsAtTheTime($t = null)
    {
        if ($t === null) {
            $t = @$_SERVER['REQUEST_TIME'] ?: time();
        } elseif (is_string($t)) {
            $t = strtotime($t);
        }
        if (!$this->getIsIkalog()) {
            return false;
        }
        if ($this->version === 'unknown') {
            return false;
        }
        return preg_match('/_Win(?:Ika|Tako)Log$/', $this->version)
            ? $this->getIsOldWinIkalogAsAtTheTime($t)
            : $this->getIsOldCliIkalogAsAtTheTime($t);
    }

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

        $latestWinIkaLog = WinikalogVersion::find()
            ->andWhere(['<=', '{{winikalog_version}}.[[build_at]]', date('Y-m-d H:i:sP', $t)])
            ->orderBy('{{winikalog_version}}.[[build_at]] DESC')
            ->limit(1)
            ->one();

        $diff = strtotime($latestWinIkaLog->build_at) - strtotime($thisWinIkaLog->build_at);
        return ($diff >= 21 * 86400);
    }

    private function getIsOldCliIkalogAsAtTheTime($t)
    {
        if (!preg_match('/^[0-9a-f]{7,}$/', $this->version)) {
            // なんかおかしい
            return false;
        }

        $ikalog = IkalogVersion::findOneByRevision($this->version);
        if (!$ikalog) {
            // 知らない IkaLog だった
            return false;
        }

        return $this->getIsOldCliIkalogAsAtTheTimeImpl($ikalog, $t);
    }

    private function getIsOldCliIkalogAsAtTheTimeImpl(IkalogVersion $ikalog, $t)
    {
        $latest = IkalogVersion::find()
            ->andWhere(['<=', '{{ikalog_version}}.[[at]]', date('Y-m-d H:i:sP', $t)])
            ->orderBy('{{ikalog_version}}.[[at]] DESC')
            ->limit(1)
            ->one();

        $diff = strtotime($latest->at) - strtotime($ikalog->at);
        return ($diff >= 21 * 86400);
    }
}
