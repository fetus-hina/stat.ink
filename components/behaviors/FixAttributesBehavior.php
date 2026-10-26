<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\behaviors;

use Override;
use Yii;
use yii\base\Behavior;
use yii\base\Model;

use function trim;
use function vsprintf;

class FixAttributesBehavior extends Behavior
{
    // $attributes = [
    //   'attrName' => [
    //     'wrong' => 'fixed',
    //     'wrong2' => 'fixed2',
    //   ],
    // ]
    /**
     * @var array<string, array<string, string>>
     */
    public array $attributes;
    public string $trigger = Model::EVENT_BEFORE_VALIDATE;

    /**
     * @return array<string, callable|string>
     */
    #[Override]
    public function events(): array
    {
        return [
            $this->trigger => $this->doFix(...),
        ];
    }

    public function doFix(): void
    {
        foreach ($this->attributes as $attrName => $fixData) {
            $value = $this->owner->{$attrName};
            if (isset($fixData[trim($value)])) {
                $this->owner->{$attrName} = $fixData[trim($value)];
                Yii::info(
                    vsprintf('%s fixed %s to %s', [
                        $attrName,
                        $value,
                        $fixData[trim($value)],
                    ]),
                    __METHOD__,
                );
            }
        }
    }
}
