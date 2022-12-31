<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v2;

use Yii;
use yii\base\Model;
use app\components\helpers\Color;

class TeamColorForm extends Model
{
    public $hue;
    public $rgb;

    public function rules()
    {
        return [
            [['hue'], 'number', 'min' => 0, 'max' => 360],
            [['hue'], 'filter',
                'filter' => fn ($value) => $value % 360,
            ],
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
        list($r, $g, $b) = $this->rgb;
        $this->hue = (int)Color::getHueFromRGB($r, $g, $b);
    }
}
