<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use Yii;
use yii\helpers\Url;
use yii\web\ViewAction as BaseAction;
use yii\web\NotFoundHttpException;
use app\models\Rule2;
use app\models\Weapon2;

class Weapon2Action extends BaseAction
{
    public $weapon;
    public $rule;

    public function init()
    {
        parent::init();

        $request = Yii::$app->request;
        $this->weapon = Weapon2::findOne(['key' => $request->get('weapon')]);
        $this->rule = Rule2::findOne(['key' => $request->get('rule')]);
        if (!$this->weapon || !$this->rule) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run()
    {
        return $this->controller->render('weapon2', [
            'weapon' => $this->weapon,
            'rule' => $this->rule,
        ]);
    }
}
