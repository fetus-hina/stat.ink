<?php
namespace app\models;

use Yii;
use yii\base\Model;

class ProfileForm extends Model
{
    public $name;
    public $nnid;
    public $twitter;
    public $ikanakama;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 15],
            [['nnid'], 'string', 'min' => 6, 'max' => 16],
            [['nnid'], 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/'],
            [['twitter'], 'string', 'max' => 15],
            [['twitter'], 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/'],
            [['ikanakama'], 'integer', 'min' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'screen_name'       => Yii::t('app', 'Screen Name (Login Name)'),
            'name'              => Yii::t('app', 'Name (for Display)'),
            'nnid'              => Yii::t('app', 'Nintendo Network ID'),
            'twitter'           => Yii::t('app', 'Twitter @name'),
            'ikanakama'         => Yii::t('app', 'IKANAKAMA User ID'),
        ];
    }
}
