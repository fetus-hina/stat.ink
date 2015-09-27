<?php
namespace app\models\api\v1;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\components\helpers\db\Now;
use app\models\Battle;
use app\models\BattleGachi;
use app\models\BattleImage;
use app\models\BattleImageType;
use app\models\BattleNawabari;
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
    public $start_at;
    public $end_at;
    public $agent;
    public $agent_version;
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
            [['image_judge', 'image_result'], 'safe'],
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
            [['start_at', 'end_at'], 'integer'],
            [['agent', 'agent_version'], 'string', 'max' => 16],
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

    public function toBattle()
    {
        $o = new Battle();
        $o->user_id         = $this->getUser()->id;
        $o->rule_id         = $this->rule ? Rule::findOne(['key' => $this->rule])->id : null;
        $o->map_id          = $this->map ? Map::findOne(['key' => $this->map])->id : null;
        $o->weapon_id       = $this->weapon ? Weapon::findOne(['key' => $this->weapon])->id : null;
        $o->level           = $this->level ? (int)$this->level : null;
        $o->rank_id         = $this->rank ? Rank::findOne(['key' => $this->rank])->id : null;
        $o->is_win          = $this->result === 'win' ? true : ($this->result === 'lose' ? false : null);
        $o->rank_in_team    = $this->rank_in_team ? (int)$this->rank_in_team : null;
        $o->kill            = $this->kill != '' ? (int)$this->kill : null;
        $o->death           = $this->death != '' ? (int)$this->death : null;
        $o->start_at        = $this->start_at != ''
            ? gmdate('Y-m-d H:i:sP', (int)$this->start_at)
            : null;
        $o->end_at          = $this->end_at != ''
            ? gmdate('Y-m-d H:i:sP', (int)$this->end_at)
            : null;
        $o->agent           = $this->agent != '' ? $this->agent : null;
        $o->agent_version   = $this->agent_version != '' ? $this->agent_version : null;
        $o->at              = new Now();
        return $o;
    }

    public function toBattleNawabari(Battle $battle)
    {
        $o = new BattleNawabari();
        $o->id                      = $battle->id;
        $o->my_point                = $this->my_point != '' ? (int)$this->my_point : null;
        $o->my_team_final_point     = $this->my_team_final_point != '' ? (int)$this->my_team_final_point : null;
        $o->his_team_final_point    = $this->his_team_final_point != '' ? (int)$this->his_team_final_point : null;
        $o->my_team_final_percent   = $this->my_team_final_percent != ''
            ? sprintf('%.1f', (float)$this->my_team_final_percent)
            : null;
        $o->his_team_final_percent   = $this->his_team_final_percent != ''
            ? sprintf('%.1f', (float)$this->his_team_final_percent)
            : null;
        return $o;
    }

    public function toBattleGachi(Battle $battle)
    {
        $o = new BattleGachi();
        $o->id              = $battle->id;
        $o->is_knock_out    = $this->knock_out === 'win' ? true : ($this->knock_out === 'lose' ? false : null);
        $o->my_team_count   = $this->my_team_count != '' ? (int)$this->my_team_count : null;
        $o->his_team_count  = $this->his_team_count != '' ? (int)$this->his_team_count : null;
        return $o;
    }

    public function toImageJudge(Battle $battle)
    {
        return $this->toImage($battle, BattleImageType::ID_JUDGE, 'image_judge');
    }

    public function toImageResult(Battle $battle)
    {
        return $this->toImage($battle, BattleImageType::ID_RESULT, 'image_result');
    }

    protected function toImage(Battle $battle, $imageTypeId, $attr)
    {
        if ($this->$attr == '' && !$this->$attr instanceof UploadedFile) {
            return null;
        }
        $o = new BattleImage();
        $o->battle_id = $battle->id;
        $o->type_id = $imageTypeId;
        $o->filename = BattleImage::generateFilename();
        return $o;
    }
}
