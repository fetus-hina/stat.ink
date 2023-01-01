<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\base\Model;

use function sprintf;

class Weapon2StageFilterForm extends Model
{
    public $result;
    public $rank;
    public $version;

    public function formName()
    {
        return 'stages';
    }

    public function rules()
    {
        return [
            [['result', 'rank', 'version'], 'string'],
            [['result'], 'in', 'range' => ['win', 'lose']],
            [['rank'], 'exist', 'skipOnError' => true,
                'targetClass' => RankGroup2::class,
                'targetAttribute' => 'key',
            ],
            [['version'], 'exist', 'skipOnError' => true,
                'targetClass' => SplatoonVersion2::class,
                'targetAttribute' => 'tag',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'result' => 'Result',
            'rank' => 'Rank',
            'version' => 'Version',
        ];
    }

    public function toQueryParams($formName = false)
    {
        if ($formName === false) {
            $formName = $this->formName();
        }

        $ret = [];
        $push = function ($key, $value) use ($formName, &$ret) {
            if ($formName != '') {
                $key = sprintf('%s[%s]', $formName, $key);
            }
            $ret[$key] = $value;
        };
        foreach ($this->attributes as $key => $value) {
            $push($key, $value);
        }
        return $ret;
    }
}
