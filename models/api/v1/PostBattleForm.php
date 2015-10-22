<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models\api\v1;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\components\helpers\db\Now;
use app\models\Battle;
use app\models\BattleDeathReason;
use app\models\BattleImage;
use app\models\BattleImageType;
use app\models\DeathReason;
use app\models\FestTitle;
use app\models\Lobby;
use app\models\Map;
use app\models\Rank;
use app\models\Rule;
use app\models\User;
use app\models\Weapon;

class PostBattleForm extends Model
{
    // API
    public $apikey;
    public $test;
    // common
    public $lobby;
    public $rule;
    public $map;
    public $weapon;
    public $rank;
    public $rank_after;
    public $rank_exp;
    public $rank_exp_after;
    public $level;
    public $level_after;
    public $cash;
    public $cash_after;
    public $result;
    public $rank_in_team;
    public $kill;
    public $death;
    public $death_reasons;
    public $gender;
    public $fest_title;
    public $my_team_color;
    public $his_team_color;
    public $image_judge;
    public $image_result;
    public $start_at;
    public $end_at;
    public $agent;
    public $agent_version;
    public $my_point;
    public $my_team_final_point;
    public $his_team_final_point;
    public $my_team_final_percent;
    public $his_team_final_percent;
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
            [['test'], 'in', 'range' => ['validate', 'dry_run']],
            [['lobby'], 'exist',
                'targetClass' => Lobby::className(),
                'targetAttribute' => 'key'],
            [['rule'], 'exist',
                'targetClass' => Rule::className(),
                'targetAttribute' => 'key'],
            [['map'], 'exist',
                'targetClass' => Map::className(),
                'targetAttribute' => 'key'],
            [['weapon'], 'exist',
                'targetClass' =>  Weapon::className(),
                'targetAttribute' => 'key'],
            [['rank', 'rank_after'], 'exist',
                'targetClass' => Rank::className(),
                'targetAttribute' => 'key'],
            [['rank_exp', 'rank_exp_after'], 'integer', 'min' => 0, 'max' => 99],
            [['level', 'level_after'], 'integer', 'min' => 1, 'max' => 50],
            [['result'], 'boolean', 'trueValue' => 'win', 'falseValue' => 'lose'],
            [['cash', 'cash_after'], 'integer', 'min' => 0, 'max' => 9999999],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['kill', 'death'], 'integer', 'min' => 0],
            [['death_reasons'], 'validateDeathReasons'],
            [['gender'], 'in', 'range' => [ 'boy', 'girl']],
            [['fest_title'], 'filter', 'filter' => 'strtolower'],
            [['fest_title'], 'exist',
                'targetClass' => FestTitle::className(),
                'targetAttribute' => 'key'],
            [['my_team_color', 'his_team_color'], 'validateTeamColor'],
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
            [['agent'], 'string', 'max' => 64],
            [['agent_version'], 'string', 'max' => 255],
            [['agent', 'agent_version'], 'required',
                'when' => function ($model, $attr) {
                    return (string)$this->agent !== '' || (string)$this->agent_version !== '';
                }],
            [['my_point'], 'integer', 'min' => 0],
            [['my_team_final_point', 'his_team_final_point'], 'integer', 'min' => 0],
            [['my_team_final_percent', 'his_team_final_percent'], 'number',
                'min' => 0.0, 'max' => 100.0],
            [['knock_out'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['my_team_count', 'his_team_count'], 'integer', 'min' => 0, 'max' => 100],

            [['lobby'], 'fixLobby'],
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

    public function validateDeathReasons($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        $value = $this->$attribute;
        if ($value == '') {
            $this->$attribute = [];
            return;
        }
        if (!is_array($value) && !$value instanceof \stdClass) {
            $this->addError($attribute, "{$attribute} should be a map.");
            return;
        }
        foreach ($value as $k => $v) {
            $tmp = filter_var($v, FILTER_VALIDATE_INT);
            if ($tmp === false || $tmp < 1 || $tmp > 99) {
                $this->addError($attribute, "Value of {$attribute}[{$k}] (= {$v}) looks broken.");
            }
        }
    }

    public function validateTeamColor($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        if (!is_array($this->$attribute)) {
            $this->addError($attribute, "{$attribute} must be a map.");
            return;
        }
        $subForm = new TeamColorForm();
        $subForm->attributes = $this->$attribute;
        if (!$subForm->validate()) {
            foreach ($subForm->getErrors() as $k => $v) {
                foreach ($v as $v2) {
                    $this->addError($attribute, "{$k}: {$v2}");
                }
            }
            return;
        }
        $this->$attribute = $subForm->attributes;
    }

    public function validateImageFile($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
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

    public function getIsTest()
    {
        return $this->test != '';
    }

    public function toBattle()
    {
        $o = new Battle();
        $o->user_id         = $this->getUser()->id;
        $o->lobby_id        = $this->lobby ? Lobby::findOne(['key' => $this->lobby])->id : null;
        $o->rule_id         = $this->rule ? Rule::findOne(['key' => $this->rule])->id : null;
        $o->map_id          = $this->map ? Map::findOne(['key' => $this->map])->id : null;
        $o->weapon_id       = $this->weapon ? Weapon::findOne(['key' => $this->weapon])->id : null;
        $o->level           = $this->level ? (int)$this->level : null;
        $o->level_after     = $this->level_after ? (int)$this->level_after : null;
        $o->rank_id         = $this->rank ? Rank::findOne(['key' => $this->rank])->id : null;
        $o->rank_after_id   = $this->rank_after ? Rank::findOne(['key' => $this->rank_after])->id : null;
        $o->rank_exp        = (string)$this->rank_exp != '' ? (int)$this->rank_exp : null;
        $o->rank_exp_after  = (string)$this->rank_exp_after != '' ? (int)$this->rank_exp_after : null;
        $o->cash            = (string)$this->cash != '' ? (int)$this->cash : null;
        $o->cash_after      = (string)$this->cash_after != '' ? (int)$this->cash_after : null;
        $o->is_win          = $this->result === 'win' ? true : ($this->result === 'lose' ? false : null);
        $o->rank_in_team    = $this->rank_in_team ? (int)$this->rank_in_team : null;
        $o->kill            = (string)$this->kill != '' ? (int)$this->kill : null;
        $o->death           = (string)$this->death != '' ? (int)$this->death : null;
        $o->gender_id       = $this->gender === 'boy' ? 1 : ($this->gender === 'girl' ? 2 : null);
        $o->fest_title_id   = $this->fest_title ? FestTitle::findOne(['key' => $this->fest_title])->id : null;
        $o->my_team_color_hue = $this->my_team_color ? $this->my_team_color['hue'] : null;
        $o->my_team_color_rgb = $this->my_team_color ? vsprintf('%02x%02x%02x', $this->my_team_color['rgb']) : null;
        $o->his_team_color_hue = $this->his_team_color ? $this->his_team_color['hue'] : null;
        $o->his_team_color_rgb = $this->his_team_color ? vsprintf('%02x%02x%02x', $this->his_team_color['rgb']) : null;
        $o->start_at        = $this->start_at != ''
            ? gmdate('Y-m-d H:i:sP', (int)$this->start_at)
            : null;
        $o->end_at          = $this->end_at != ''
            ? gmdate('Y-m-d H:i:sP', (int)$this->end_at)
            : new Now();
        $o->agent_id        = null;
        $o->at              = new Now();

        $o->my_point                = (string)$this->my_point != '' ? (int)$this->my_point : null;
        $o->my_team_final_point     = (string)$this->my_team_final_point != ''
            ? (int)$this->my_team_final_point
            : null;
        $o->his_team_final_point    = (string)$this->his_team_final_point != ''
            ? (int)$this->his_team_final_point
            : null;
        $o->my_team_final_percent   = (string)$this->my_team_final_percent != ''
            ? sprintf('%.1f', (float)$this->my_team_final_percent)
            : null;
        $o->his_team_final_percent   = (string)$this->his_team_final_percent != ''
            ? sprintf('%.1f', (float)$this->his_team_final_percent)
            : null;
        $o->is_knock_out    = $this->knock_out === 'yes' ? true : ($this->knock_out === 'no' ? false : null);
        $o->my_team_count   = (string)$this->my_team_count != '' ? (int)$this->my_team_count : null;
        $o->his_team_count  = (string)$this->his_team_count != '' ? (int)$this->his_team_count : null;

        if ($this->isTest) {
            $now = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
            $o->id = 0;
            foreach ($o->attributes as $k => $v) {
                if ($v instanceof Now) {
                    $o->$k = gmdate('Y-m-d H:i:sP', $now);
                }
            }
        }

        return $o;
    }

    public function toDeathReasons(Battle $battle)
    {
        if (is_array($this->death_reasons) || $this->death_reasons instanceof \stdClass) {
            $unknownCount = 0;
            foreach ($this->death_reasons as $key => $count) {
                $reason = DeathReason::findOne(['key' => $key]);
                if ($key === 'unknown' || !$reason) {
                    $unknownCount += (int)$count;
                } else {
                    $o = new BattleDeathReason();
                    $o->battle_id = $battle->id;
                    $o->reason_id = $reason->id;
                    $o->count = (int)$count;
                    yield $o;
                }
            }
            if ($unknownCount > 0) {
                $reason = DeathReason::findOne(['key' => 'unknown']);
                if ($reason) {
                    $o = new BattleDeathReason();
                    $o->battle_id = $battle->id;
                    $o->reason_id = $reason->id;
                    $o->count = (int)$unknownCount;
                    yield $o;
                }
            }
        }
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
        if ($this->isTest) {
            return null;
        }
        if ($this->$attr == '' && !$this->$attr instanceof UploadedFile) {
            return null;
        }
        $o = new BattleImage();
        $o->battle_id = $battle->id;
        $o->type_id = $imageTypeId;
        $o->filename = BattleImage::generateFilename();
        return $o;
    }

    // 特定のバージョンの IkaLog が lobby を誤るのでわかる範囲で書き換える
    public function fixLobby()
    {
        if ($this->hasErrors()) {
            return;
        }
        if ($this->agent !== 'IkaLog' && $this->agent !== 'TakoLog') {
            return;
        }
        if ($this->lobby === 'fest' && !$this->gender && !$this->fest_title) {
            $this->lobby = 'standard';
        }
        if ($this->lobby === 'standard' && $this->gender && $this->fest_title) {
            $this->lobby = 'fest';
        }
    }
}
