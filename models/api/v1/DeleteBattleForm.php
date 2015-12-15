<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models\api\v1;

use Yii;
use yii\base\Model;
use app\components\helpers\db\Now;
use app\models\Battle;
use app\models\User;

class DeleteBattleForm extends Model
{
    // API
    public $apikey;
    public $test;

    // target
    public $id;

    // read-only properties
    public $deletedIdList = [];
    public $errorIdList = [];

    public function rules()
    {
        return [
            [['apikey', 'id'], 'required'],
            [['apikey'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'api_key'],
            [['test'], 'in', 'range' => ['validate', 'dry_run']],
            [['id'], 'validateBattleId'],
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

    public function validateBattleId($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        if (is_scalar($this->$attribute)) {
            $this->$attribute = [$this->$attribute];
        }

        if (!is_array($this->$attribute)) {
            $this->addError($attribute, "{$attribute} should be an array of or scalar ID.");
            return;
        }

        if (count($this->$attribute) > 100) {
            $this->addError($attribute, "too many values.");
            return;
        }

        $valueErrors = [];
        foreach ($this->$attribute as $id) {
            if (!is_scalar($id)) {
                $this->addError($attribute, "{$attribute} should be an array of or scalar ID.");
                return;
            }
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                $valueErrors[] = (string)$id;
            }
        }

        if (!empty($valueErrors)) {
            $this->addError($attribute, "{$attribute} has non-integer value(s): " . implode(', ', $valueError));
            return;
        }
    }

    public function save()
    {
        $this->deletedIdList = [];
        $this->errorIdList = [];

        if ($this->hasErrors()) {
            return false;
        }

        if (!$user = User::findOne(['api_key' => $this->apikey])) {
            $this->addError('apikey', 'User does not exist.');
            return false;
        }

        foreach ($this->id as $id) {
            $battle = Battle::findOne(['id' => (int)(string)$id]);
            if (!$battle) {
                $this->errorIdList[] = [
                    'id'    => $id,
                    'error' => 'not found'
                ];
                continue;
            }

            if ($battle->user_id != $user->id) {
                $this->errorIdList[] = [
                    'id'    => $id,
                    'error' => 'user not match'
                ];
                continue;
            }

            if ($battle->is_automated) {
                $this->errorIdList[] = [
                    'id'    => $id,
                    'error' => 'automated result'
                ];
                continue;
            }

            if (!$this->test) {
                $battle->delete();
            }

            $this->deletedIdList[] = [
                'id'    => $id,
                'error' => null,
            ];
        }

        return true;
    }
}
