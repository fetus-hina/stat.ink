<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\web;

use Symfony\Component\Yaml\Yaml;
use Yii;
use yii\base\Component;
use yii\web\ResponseFormatterInterface;

class YamlResponseFormatter extends Component implements ResponseFormatterInterface
{
    public $contentType; // e.g., 'text/yaml', 'application/x-yaml'
    public $encodeOptions;
    public $inline = 2;
    public $indent = 4;

    public function init()
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
                function (int $carry, int $item): int {
                    return $carry | $item;
                },
                0,
            );
        }
    }

    public function format($response)
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
