<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\behaviors;

use Override;
use app\components\helpers\TypeHelper;
use yii\base\Model;

use function array_keys;

class AutoTrimAttributesBehavior extends TrimAttributesBehavior
{
    /**
     * @inheritdoc
     * @return array<string, callable|string>
     */
    #[Override]
    public function events(): array
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => $this->trim(...),
        ];
    }

    public function trim(): void
    {
        $owner = TypeHelper::instanceOf($this->owner, Model::class);
        foreach (array_keys($owner->attributes) as $attrName) {
            $owner->{$attrName} = $this->doTrim($owner->{$attrName});
        }
    }
}
