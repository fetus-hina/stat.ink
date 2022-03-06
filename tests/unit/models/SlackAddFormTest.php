<?php

declare(strict_types=1);

namespace tests\models;

use Codeception\Test\Unit;
use Yii;
use app\models\Language;
use app\models\SlackAddForm;

class SlackAddFormTest extends Unit
{
    /**
     * @dataProvider webhookUrlData
     */
    public function testWebhookUrl(bool $expect, string $url): void
    {
        $model = Yii::createObject(array_merge(
            ['class' => SlackAddForm::class],
            $this->getValidAttributes(),
            ['webhook_url' => $url],
        ));
        $model->validate();
        $this->assertEquals($expect, !$model->hasErrors('webhook_url'));
    }

    public function webhookUrlData(): array
    {
        return [
            'example.com' => [
                false,
                'https://example.com/',
            ],
            'slack.com' => [
                true,
                'https://hooks.slack.com/services/Txxxxxxxxxxxxxxxxxx/xxxxxxxxxxxxxxxxxxxxxxxx',
            ],
            'discordapp.com' => [
                true,
                'https://discordapp.com/api/webhooks/1234/00aaAA/slack',
            ],
            'discord.com' => [
                true,
                'https://discord.com/api/webhooks/1234/00aaAA/slack',
            ],
        ];
    }

    private function getValidAttributes(): array
    {
        return [
            'webhook_url' => 'https://hooks.slack.com/services/Txxxxxxxxxxxxxxxxxx/xxxxxxxxxxxxxxxxxxxxxxxx',
            'username' => 'UserName',
            'icon' => ':oden:',
            'channel' => '#general',
            'language_id' => Language::findOne(['lang' => 'ja-JP'])->id,
        ];
    }
}
