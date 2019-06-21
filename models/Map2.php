<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use DateTimeZone;
use Yii;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\Translator;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "map2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $short_name
 * @property integer $area
 * @property string $release_at
 * @property integer $splatnet
 */
class Map2 extends ActiveRecord
{
    public static function find() : ActiveQuery
    {
        return new class(static::class) extends ActiveQuery {
            public function excludeMystery() : self
            {
                return $this->andWhere(['and',
                    ['not', ['like', 'key', 'mystery%', false]],
                ]);
            }
        };
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map2';
    }

    public static function getSortedMap($callback = null) : array
    {
        $query = static::find()->asArray();
        if ($callback && is_callable($callback)) {
            call_user_func($callback, $query);
        }
        $list = ArrayHelper::map(
            $query->all(),
            'key',
            function (array $row) : string {
                return Yii::t('app-map2', $row['name']);
            }
        );
        uksort($list, function ($a, $b) use ($list) {
            if ($a === $b) {
                return 0;
            } elseif ($a === 'mystery') {
                return 1;
            } elseif ($b === 'mystery') {
                return -1;
            } else {
                return strcmp($list[$a], $list[$b]);
            }
        });
        return $list;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name', 'short_name'], 'required'],
            [['area', 'splatnet'], 'integer'],
            [['release_at'], 'safe'],
            [['key', 'short_name'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['short_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'short_name' => 'Short Name',
            'area' => 'Area',
            'release_at' => 'Release At',
            'splatnet' => 'SplatNet ID',
        ];
    }

    public function toJsonArray() : array
    {
        $t = $this->release_at ? strtotime($this->release_at) : null;
        return [
            'key' => $this->key,
            'splatnet' => $this->splatnet,
            'name' => Translator::translateToAll('app-map2', $this->name),
            'short_name' => Translator::translateToAll('app-map2', $this->short_name),
            'area' => $this->area,
            'release_at' => $t
                ? DateTimeFormatter::unixTimeToJsonArray(
                    $t,
                    new DateTimeZone('Etc/UTC')
                )
                : null,
        ];
    }
}
