<?php

namespace yii\helpers;

class BaseHtml {
    /**
     * @param string|null $name
     * @param string|null $value
     * @param array<string, mixed> $options
     */
    public static function textInput(?string $name, ?string $value, array $options = []): string
    {
    }

    /**
     * @param string|false|null $name
     * @param string|string[]|null $selection
     * @param array<mixed> $items
     * @param array<array-key, mixed> $options
     * @return string
     */
    public static function dropDownList($name, $selection = null, array $items = [], array $options = []): string
    {
    }
}
