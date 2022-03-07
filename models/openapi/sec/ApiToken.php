<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\openapi\sec;

use Yii;
use app\components\helpers\Html;
use app\models\openapi\SecurityInterface;
use yii\base\Component;

class ApiToken extends Component implements SecurityInterface
{
    public static function oapiSecUse(array $options = []): array
    {
        return [
            static::oapiSecName() => $options,
        ];
    }

    public static function oapiSecName(): string
    {
        return 'APITokenAuth';
    }

    public static function oapiSecurity(): array
    {
        return [
            'type' => 'http',
            'scheme' => 'bearer',
            'description' => implode("\n", [
                Html::encode(Yii::t(
                    'app-apidoc2',
                    'An API Token with bearer auth format.'
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
                        'Authorization: Bearer sD093VHLHW41b9xdaM7zVpyIX2TbIornR0h47RaUNGA'
                    )),
                ]),
            ]),
        ];
    }
}
