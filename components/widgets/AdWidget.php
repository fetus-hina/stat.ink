<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets;

use Yii;
use app\components\helpers\Resource;
use jp3cki\yii2\googleadsense\GoogleAdSense;
use yii\base\Widget;

class AdWidget extends Widget
{
    public static $autoIdPrefix = 'ad-';
    public $template = '<div id="{id}" class="adsense">{adsense}</div>';

    public $adSense;

    public function init()
    {
        parent::init();

        $params = Yii::$app->params['googleAdsense'];
        $slot = @$params['slot'] ?: '';
        $client = @$params['client'] ?: '';
        if ($slot == '' || $client == '') {
            $this->adSense = null;
        } else {
            $this->adSense = Yii::createObject([
                'class' => GoogleAdSense::class,
                'slot' => $slot,
                'client' => $client,
                'responsive' => true,
            ]);
        }
    }

    public function run()
    {
        if (!$this->adSense) {
            return '';
        }

        $replace = [
            'id' => $this->id,
            'adsense' => $this->adCode,
        ];
        return preg_replace_callback(
            '/\{(\w+)\}/',
            fn ($match) => @$replace[$match[1]] ?: $match[0],
            $this->template
        );
    }

    public function getAdCode()
    {
        $ob = new Resource(
            ob_start(),
            function ($unused) {
                ob_end_clean();
            }
        );
        try {
            return $this->adSense->run() . ob_get_contents();
        } finally {
            unset($ob);
        }
    }
}
