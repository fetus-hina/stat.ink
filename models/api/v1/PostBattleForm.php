<?php
namespace app\models\api\v1;

use Yii;
use yii\base\Model;
use app\models\Map;
use app\models\Rank;
use app\models\Rule;
use app\models\User;
use app\models\Weapon;

class PostBattleForm extends Model
{
    // API
    public $apikey;
    // common
    public $rule;
    public $map;
    public $weapon;
    public $rank;
    public $level;
    public $result;
    public $rank_in_team;
    public $kill;
    public $death;
    public $image_judge;
    public $image_result;
    // nawabari
    public $my_point;
    public $my_team_final_point;
    public $his_team_final_point;
    public $my_team_final_percent;
    public $his_team_final_percent;
    // gachi
    public $knock_out;
    public $my_team_count;
    public $his_team_count;

    public function rules()
    {
        return [
            [['apikey'], 'required'],
            [['apikey'], 'exist',
                'targetClass' => User::className(),
                'targetAttribute' => 'api_key'],
            [['rule'], 'exist',
                'targetClass' => Rule::className(),
                'targetAttribute' => 'key'],
            [['map'], 'exist',
                'targetClass' => Map::className(),
                'targetAttribute' => 'key'],
            [['weapon'], 'exist',
                'targetClass' =>  Weapon::className(),
                'targetAttribute' => 'key'],
            [['rank'], 'exist',
                'targetClass' => Rank::className(),
                'targetAttribute' => 'key'],
            [['level'], 'integer', 'min' => 1, 'max' => 50],
            [['result'], 'boolean', 'trueValue' => 'win', 'falseValue' => 'lose'],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['kill', 'death'], 'integer', 'min' => 0],
            [['image_judge', 'image_result'], 'file',
                'maxSize' => 3 * 1024 * 1024,
                'when' => function ($model, $attr) {
                    return !is_string($model->$attr);
                }],
            [['image_judge', 'image_result'], 'validateImageFile',
                'when' => function ($model, $attr) {
                    return !is_string($model->$attr);
                }],
            [['image_judge', 'image_result'], 'validateImageString',
                'when' => function ($model, $attr) {
                    return is_string($model->$attr);
                }],
            [['my_point'], 'integer', 'min' => 0],
            [['my_team_final_point', 'his_team_final_point'], 'integer', 'min' => 0],
            [['my_team_final_percent', 'his_team_final_percent'], 'number',
                'min' => 0.0, 'max' => 100.0],
            [['knock_out'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['my_team_count', 'his_team_count'], 'integer', 'min' => 0, 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    public function validateImageFile($attribute, $params)
    {
        if ($this->hasError($attribute)) {
            return;
        }
        if (!($this->$attr instanceof UploadedFile)) {
            // 先に file バリデータを通すのでここは絶対通らないはず
            $this->addError($attribute, 'ファイルをアップロードしてください / [BUG?] $attributes is not an instance of UploadedFile');
            return;
        }
        return $this->validateImageStringImpl(
            file_get_contents($this->$attribute->tempName, false, null),
            $attribute
        );
    }

    public function validateImageString($attribute, $params)
    {
        return $this->validateImageStringImpl($this->$attribute, $attribute);
    }

    private function validateImageStringImpl($binary, $attribute)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        if (!$gd = @imagecreatefromstring($binary)) {
            $this->addError($attribute, '画像が読み込めません');
            return;
        }
        imagedestroy($gd);
    }

    public function getUser()
    {
        return User::findOne(['api_key' => $this->apikey]);
    }
}
