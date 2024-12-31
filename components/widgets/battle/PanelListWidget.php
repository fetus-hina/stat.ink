<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets\battle;

use Yii;
use app\components\widgets\Icon;
use yii\base\Widget;
use yii\bootstrap\Html;

use function call_user_func;
use function implode;
use function preg_replace_callback;

class PanelListWidget extends Widget
{
    public $panelClass = 'panel panel-default';
    public $template;
    public $models = [];
    public $title;
    public $titleLink;
    public $titleLinkText;
    public $emptyText;
    public $itemClass;

    public function init()
    {
        parent::init();
        if (!$this->template) {
            $this->template = Html::tag(
                'div',
                implode('', [
                    Html::tag('div', '{title}{titleLink}', ['class' => 'panel-heading']),
                    '{list}',
                ]),
                ['class' => '{panelClass}'],
            );
        }
        if (!$this->emptyText) {
            $this->emptyText = Html::tag(
                'div',
                Html::tag(
                    'p',
                    Html::encode(Yii::t('app', 'No Data')),
                ),
                ['class' => 'panel-body'],
            );
        }
        if (!$this->itemClass) {
            $this->itemClass = PanelListItemWidget::class;
        }
    }

    public function run()
    {
        $replace = [
            '{title}' => Html::encode($this->title),
            '{titleLink}' => $this->renderTitleLink(),
            '{panelClass}' => $this->panelClass,
            '{list}' => $this->renderList(),
        ];
        return preg_replace_callback(
            '/\{\w+\}/',
            fn (array $match): string => $replace[$match[0]] ?? $match[0],
            $this->template,
        );
    }

    protected function renderList(): string
    {
        $ret = [];
        foreach ($this->models as $model) {
            $tmp = call_user_func([$this->itemClass, 'widget'], ['model' => $model]);
            if ($tmp != '') {
                $ret[] = $tmp;
            }
        }
        if ($ret) {
            return Html::tag(
                'table',
                implode('', $ret),
                ['class' => 'table'],
            );
        }
        return $this->emptyText;
    }

    protected function renderTitleLink(): string
    {
        if (!$this->titleLink || !$this->titleLinkText) {
            return '';
        }
        return Html::a(
            implode(' ', [
                Icon::search(),
                Html::encode($this->titleLinkText),
            ]),
            $this->titleLink,
            [
                'class' => 'pull-right btn btn-default btn-xs',
                'data' => [
                    'pjax' => '0',
                ],
            ],
        );
    }
}
