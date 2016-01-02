<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use jp3cki\yii2\twitter\widget\TweetButton;

class SnsWidget extends Widget
{
    public static $autoIdPrefix = 'sns-';
    public $template = '<div id="{id}" class="sns">{tweet}</div>';

    public $tweetButton;

    public function init()
    {
        $this->tweetButton = Yii::createObject([
            'class' => TweetButton::class
        ]);
        return parent::init();
    }

    public function run()
    {
        $replace = [
            'id' => $this->id,
            'tweet' => $this->tweetButton->run(),
        ];
        return preg_replace_callback(
            '/\{(\w+)\}/',
            function ($match) use ($replace) {
                return @$replace[$match[1]] ?: $match[0];
            },
            $this->template
        );
    }
}
