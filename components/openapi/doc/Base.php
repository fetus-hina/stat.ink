<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\openapi\doc;

use Yii;
use app\components\openapi\OpenApiSpec;
use yii\base\Component;
use LogicException;

abstract class Base extends Component
{
    private ?OpenApiSpec $renderer = null;

    abstract public function getTitle(): string;

    /**
     * @return array<string, mixed>
     */
    abstract public function getPaths(): array;

    public function render(): string
    {
        $obj = Yii::createObject([
            'class' => OpenApiSpec::class,
            'title' => $this->getTitle(),
        ]);
        $this->renderer = $obj;
        $obj->paths = $this->getPaths();
        return $obj->renderJson();
    }

    protected function registerSecurityScheme(string $className): self
    {
        ($this->renderer ?? throw new LogicException('Not rendering'))->registerSecurityScheme($className);
        return $this;
    }

    protected function registerSchema(string $className): self
    {
        ($this->renderer ?? throw new LogicException('Not rendering'))->registerSchema($className);
        return $this;
    }

    protected function registerTag(string $key, ?string $description = null): self
    {
        ($this->renderer ?? throw new LogicException('Not rendering'))->registerTag($key, $description);
        return $this;
    }
}
