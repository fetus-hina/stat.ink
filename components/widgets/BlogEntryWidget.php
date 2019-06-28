<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
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
    public function run()
    {
        $entries = $this->getEntries();
        if (!$entries) {
            return '';
        }

        BlogEntryAsset::register($this->view);
        return Html::tag(
            'aside',
            Alert::widget([
                'options' => [
                    'class' => [
                        'alert-success',
                        'blog-entries',
                    ],
                ],
                'body' => Html::tag('nav', $this->renderEntries($entries)),
            ])
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
                $entry->url,
                ['class' => 'alert-link'],
            ),
            Yii::$app->formatter->asHtmlRelative($entry->at),
        );
    }
}
