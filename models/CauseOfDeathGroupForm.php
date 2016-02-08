<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;

class CauseOfDeathGroupForm extends Model
{
    public $level;

    public function formName()
    {
        return 'group';
    }

    public function rules()
    {
        return [
            [['level'], 'in', 'range' => ['canonical', 'main-weapon', 'type']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'level' => Yii::t('app', 'Grouping Level'),
        ];
    }
}
