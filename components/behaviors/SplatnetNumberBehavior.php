<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\behaviors;

use yii\base\Behavior;
use yii\base\Model;
use yii\db\Query;

class SplatnetNumberBehavior extends Behavior
{
    public $trigger = Model::EVENT_BEFORE_VALIDATE;

    public $attribute; // e.g. "weapon" $model->$attribute
    public $prefix = '#';
    public $tableName; // '{{weapon2}}'
    public $tableAttribute = '[[splatnet]]';
    public $keyAttribute = '[[key]]';

    public $modifier = null; // function (Query $query) : void {}
    public $notFound = null; // value || function (int $value) : ?string {}

    public function events()
    {
        return [
            $this->trigger => [$this, 'doExec'],
        ];
    }

    public function doExec(): void
    {
        $attrName = $this->attribute;
        $value = trim((string)($this->owner->{$attrName}));

        if ($value === '') {
            return;
        }

        if (substr($value, 0, strlen($this->prefix)) !== $this->prefix) {
            return;
        }

        $value = substr($value, strlen($this->prefix));
        $value = filter_var($value, FILTER_VALIDATE_INT);
        if ($value === false) {
            return;
        }

        $query = (new Query())
            ->select([
                'key' => "{$this->tableName}.{$this->keyAttribute}",
            ])
            ->from($this->tableName)
            ->andWhere([
                "{$this->tableName}.{$this->tableAttribute}" => $value,
            ])
            ->limit(1);
        if ($this->modifier) {
            call_user_func($this->modifier, $query);
        }

        $key = $query->scalar();
        if (!$key) {
            if (is_callable($this->notFound)) {
                $key = call_user_func($this->notFound, $value);
            } else {
                $key = $this->notFound;
            }
        }

        $this->owner->{$attrName} = $key;
    }
}
