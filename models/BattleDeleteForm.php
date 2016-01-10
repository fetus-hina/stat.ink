<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;

class BattleDeleteForm extends Model
{
    public $agree;

    public function rules()
    {
        $agreeErrorMessage = Yii::t('app', 'You must agree to the above to delete this battle.');
        return [
            [['agree'], 'required',
                'message' => $agreeErrorMessage],
            [['agree'], 'compare', 'compareValue' => 'yes', 'operator' => '===',
                'message' => $agreeErrorMessage],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'agree' => Yii::t('app', 'Agreement'),
        ];
    }
}
