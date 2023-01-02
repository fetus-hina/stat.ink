<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use app\assets\JqueryTwemojiAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

use function mb_chr;
use function vsprintf;

final class Emoji extends Widget
{
    public const CP_CROSS_MARK = 0x274c;
    public const CP_PARTY_POPPER = 0x1f389;

    public string $text = '';

    public static function text(string $text): string
    {
        return static::widget(['text' => $text]);
    }

    public static function cp(int $codepoint): string
    {
        return self::widget([
            'text' => mb_chr($codepoint, 'UTF-8'),
        ]);
    }

    public function run(): string
    {
        $id = $this->id;

        $view = $this->view;
        if ($view instanceof View) {
            JqueryTwemojiAsset::register($view);
            $view->registerJs(
                vsprintf('jQuery(%s).twemoji();', [
                    Json::encode("#{$id}"),
                ]),
            );
        }

        return Html::tag('span', Html::encode($this->text), ['id' => $id]);
    }
}
