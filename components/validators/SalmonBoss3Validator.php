<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\validators;

use Yii;
use app\models\SalmonBoss3;
use app\models\SalmonBoss3Alias;
use app\models\api\v3\postSalmon\BossForm;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;

use function array_shift;
use function in_array;
use function is_array;
use function vsprintf;

final class SalmonBoss3Validator extends Validator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $errors = $this->validateValueImpl($model->$attribute);
        if (!$errors) {
            return;
        }

        foreach ($errors as $error) {
            $this->addError($model, $attribute, $error);
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $errors = $this->validateValueImpl($value);
        if (!$errors) {
            return;
        }

        return [array_shift($errors), []];
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    private function validateValueImpl($value): array
    {
        if (
            $value === null ||
            (is_array($value) && !$value) // empty array
        ) {
            return [];
        }

        if (!is_array($value)) {
            return ['{attribute} is invalid. (must be map<string, struct>)'];
        }

        $exists = [];

        foreach ($value as $k => $v) {
            // {"bosses": {"bakudan": null}} のような null の指定を無視する
            if ($v === null) {
                continue;
            }

            if (!is_array($v) || !ArrayHelper::isAssociative($v)) {
                return ['{attribute} is invalid. (must be map<string, struct>)'];
            }

            $boss = self::getBossByKey((string)$k);
            if (!$boss) {
                return ["{attribute} is invalid. unknown boss {$boss}"];
            }

            if (in_array($boss->key, $exists, true)) {
                return [
                    vsprintf('{attribute} is invalid. duplicate entry for %s', [
                        $boss->name,
                    ]),
                ];
            }
            $exists[] = $boss->key;

            $subForm = Yii::createObject(BossForm::class);
            $subForm->attributes = $v;
            if (!$subForm->validate()) {
                foreach ($subForm->getFirstErrors() as $subKey => $subError) {
                    return [
                        vsprintf('{attribute}.%s.%s is invalid. %s', [
                            $k,
                            $subKey,
                            $subError,
                        ]),
                    ];
                }
            }
        }

        return [];
    }

    private static function getBossByKey(string $key): ?SalmonBoss3
    {
        $model = SalmonBoss3::find()
            ->andWhere(['key' => $key])
            ->limit(1)
            ->one();
        if ($model) {
            return $model;
        }

        $model = SalmonBoss3Alias::find()
            ->andWhere(['key' => $key])
            ->limit(1)
            ->one();
        if ($model) {
            return $model->salmonid;
        }

        return null;
    }
}
