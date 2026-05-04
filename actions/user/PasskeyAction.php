<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\models\UserPasskey;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction as BaseAction;

use const SORT_ASC;

final class PasskeyAction extends BaseAction
{
    public function run()
    {
        $ident = Yii::$app->user->getIdentity();

        $passkeys = UserPasskey::find()
            ->andWhere(['user_id' => $ident->id])
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        return $this->controller->render('passkey', [
            'user' => $ident,
            'passkeys' => $passkeys,
            'aaguidInfo' => $this->buildAaguidInfo($passkeys),
        ]);
    }

    /**
     * @param UserPasskey[] $passkeys
     * @return array<string, array{name: string, mime_type: ?string, base64_data: ?string}>
     */
    private function buildAaguidInfo(array $passkeys): array
    {
        $aaguids = ArrayHelper::getColumn($passkeys, 'aaguid');
        if (!$aaguids) {
            return [];
        }

        $theme = Yii::$app->theme->isDarkTheme ? 'dark' : 'light';

        $rows = new Query()
            ->select([
                'aaguid' => '{{pa}}.[[aaguid]]',
                'name' => '{{pa}}.[[name]]',
                'mime_type' => '{{pai}}.[[mime_type]]',
                'base64_data' => "REPLACE(ENCODE({{pai}}.[[data]], 'base64'), E'\\n', '')",
            ])
            ->from(['pa' => '{{%passkey_aaguid}}'])
            ->leftJoin(
                ['pai' => '{{%passkey_aaguid_icon}}'],
                '{{pa}}.[[aaguid]] = {{pai}}.[[aaguid]] AND {{pai}}.[[theme]] = :theme',
                [':theme' => $theme],
            )
            ->andWhere(['{{pa}}.[[aaguid]]' => $aaguids])
            ->all();

        return ArrayHelper::index($rows, 'aaguid');
    }
}
