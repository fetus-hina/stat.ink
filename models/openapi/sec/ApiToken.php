<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models\openapi\sec;

use Override;
use Yii;
use app\models\openapi\SecurityInterface;
use yii\base\Component;
use yii\helpers\Html;

use function implode;
use function vsprintf;

class ApiToken extends Component implements SecurityInterface
{
    /**
     * @param string[] $options
     * @return array<string, string[]>
     */
    #[Override]
    public static function oapiSecUse(array $options = []): array
    {
        return [
            static::oapiSecName() => $options,
        ];
    }

    #[Override]
    public static function oapiSecName(): string
    {
        return 'APITokenAuth';
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public static function oapiSecurity(): array
    {
        return [
            'type' => 'http',
            'scheme' => 'bearer',
            'description' => implode("\n", [
                Html::encode(Yii::t(
                    'app-apidoc2',
                    'An API Token with bearer auth format.',
                )),
                '',
                Html::encode(Yii::t(
                    'app-apidoc2',
                    'The API Token is issued for each user by stat.ink system.',
                )),
                Html::encode(Yii::t(
                    'app-apidoc2',
                    'It can be obtained from the [user settings page](https://stat.ink/profile).',
                )),
                '',
                vsprintf('%s %s', [
                    Html::encode(Yii::t('app-apidoc2', 'Example:')),
                    Html::tag('code', Html::encode(
                        'Authorization: Bearer sD093VHLHW41b9xdaM7zVpyIX2TbIornR0h47RaUNGA',
                    )),
                ]),
            ]),
        ];
    }
}
