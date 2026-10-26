<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\behaviors;

use Override;
use yii\base\Behavior;
use yii\base\Model;
use yii\db\Query;

use function call_user_func;
use function filter_var;
use function is_callable;
use function str_starts_with;
use function strlen;
use function substr;
use function trim;

use const FILTER_VALIDATE_INT;

class SplatnetNumberBehavior extends Behavior
{
    public string $trigger = Model::EVENT_BEFORE_VALIDATE;

    public ?string $attribute = null; // e.g. "weapon" $model->$attribute
    public string $prefix = '#';
    public string $tableName; // '{{weapon2}}'
    public string $tableAttribute = '[[splatnet]]';
    public string $keyAttribute = '[[key]]';

    /**
     * @var (callable(Query): void)|null
     */
    public $modifier = null; // function (Query $query) : void {}
    public mixed $notFound = null; // value || function (int $value) : ?string {}

    /**
     * @return array<string, callable|string>
     */
    #[Override]
    public function events(): array
    {
        return [
            $this->trigger => $this->doExec(...),
        ];
    }

    public function doExec(): void
    {
        $attrName = $this->attribute;
        $value = trim((string)($this->owner->{$attrName}));

        if ($value === '') {
            return;
        }

        if (!str_starts_with($value, $this->prefix)) {
            return;
        }

        $value = substr($value, strlen($this->prefix));
        $value = filter_var($value, FILTER_VALIDATE_INT);
        if ($value === false) {
            return;
        }

        $query = new Query()
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
