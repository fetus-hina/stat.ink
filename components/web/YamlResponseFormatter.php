<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\web;

use Override;
use Symfony\Component\Yaml\Yaml;
use yii\base\Component;
use yii\web\ResponseFormatterInterface;

use function array_reduce;
use function is_int;

class YamlResponseFormatter extends Component implements ResponseFormatterInterface
{
    public ?string $contentType = null; // e.g., 'text/yaml', 'application/x-yaml'
    public ?int $encodeOptions = null;
    public int $inline = 2;
    public int $indent = 4;

    #[Override]
    public function init(): void
    {
        parent::init();

        if (!is_int($this->encodeOptions)) {
            $this->encodeOptions = array_reduce(
                [
                    Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE,
                    Yaml::DUMP_OBJECT_AS_MAP,
                    Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK,
                    Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE,
                ],
                fn (int $carry, int $item): int => $carry | $item,
                0,
            );
        }
    }

    #[Override]
    public function format($response): void
    {
        $response->getHeaders()->set('Content-Type', (string)($this->contentType ?: 'text/yaml'));
        $response->content = Yaml::dump(
            $response->data,
            (int)$this->inline,
            (int)$this->indent,
            (int)$this->encodeOptions,
        );
    }
}
