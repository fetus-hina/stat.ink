<?php
namespace app\models\api\v1;

use Yii;
use yii\base\Model;

class TeamColorForm extends Model
{
    public $hue;
    public $rgb;

    public function rules()
    {
        return [
            [['hue'], 'number', 'min' => 0, 'max' => 360],
            [['rgb'], 'validateRgb'],
            [['rgb'], 'makeHue'],
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

    public function validateRgb($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        $value = $this->$attribute;
        if (!is_array($value)) {
            $this->addError($attribute, "{$attribute} must be an array.");
            return;
        }
        if (count($value) !== 3) {
            $this->addError($attribute, "{$attribute} must have 3 values.");
            return;
        }

        $numberValidator = new \yii\validators\NumberValidator();
        $numberValidator->integerOnly = true;
        $numberValidator->min = 0;
        $numberValidator->max = 255;
        foreach ($value as $v) {
            $error = null;
            if (!$numberValidator->validate($v, $error)) {
                $this->addError($attribute, $error);
                return;
            }
        }
    }

    public function makeHue()
    {
        if ($this->hasErrors() || (string)$this->hue != '') {
            return;
        }
        if (!is_array($this->rgb) || count($this->rgb) !== 3) {
            return;
        }
        list ($r, $g, $b) = $this->rgb;
        $color = new \Color();
        $color->fromRgbInt($r, $g, $b);
        $hsv = $color->toHsvFloat();
        $this->hue = (int)round($hsv['hue']);
    }
}
