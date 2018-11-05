<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class ChangeLangDropdown extends Widget
{
    public $dialog = '#language-dialog';

    public function run()
    {
        $this->view->registerJs(sprintf(
            'jQuery("#%s").click(function(){$("%s").modal()});',
            $this->id,
            $this->dialog
        ));
        return Html::tag(
            'button',
            implode('', [
                FA::fas('language')->fw()->__toString(),
                implode(' / ', [
                    'Switch Language',
                    '言語切替',
                ]),
                ' ',
                Html::tag('span', '', ['class' => 'caret']),
            ]),
            [
                'id' => $this->id,
                'class' => 'btn btn-default',
            ]
        );
    }
}
