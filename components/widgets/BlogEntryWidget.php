<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\BlogEntryAsset;
use app\models\BlogEntry;
use yii\base\Widget;
use yii\helpers\Html;

class BlogEntryWidget extends Widget
{
    public function init()
    {
        parent::init();
        $this->id = 'blog-entries';
    }

    public function run()
    {
        $entries = $this->getEntries();
        if (!$entries) {
            return '';
        }

        BlogEntryAsset::register($this->view);
        return Html::tag(
            'aside',
            Html::tag('nav', $this->renderEntries($entries)),
            [
                'id' => $this->id,
                'class' => 'bg-success',
            ]
        );
    }

    private function getEntries(): array
    {
        return BlogEntry::find()
            ->orderBy(['at' => SORT_DESC])
            ->limit(3)
            ->all();
    }

    private function renderEntries(array $entries): string
    {
        return Html::tag('ul', implode('', array_map(
            function (BlogEntry $entry): string {
                return Html::tag('li', $this->renderEntry($entry));
            },
            $entries
        )));
    }

    private function renderEntry(BlogEntry $entry): string
    {
        return sprintf(
            '%s (%s)',
            Html::a(
                Html::encode($entry->title),
                $entry->url
            ),
            Yii::$app->formatter->asHtmlRelative($entry->at),
        );
    }
}
