<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\openapi\doc;

use Yii;
use app\components\openapi\OpenApiSpec;
use yii\base\Component;

abstract class Base extends Component
{
    private $renderer;

    abstract public function getTitle(): string;
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
        $this->renderer->registerSecurityScheme($className);
        return $this;
    }

    protected function registerSchema(string $className): self
    {
        $this->renderer->registerSchema($className);
        return $this;
    }

    protected function registerTag(string $key, ?string $description = null): self
    {
        $this->renderer->registerTag($key, $description);
        return $this;
    }
}
