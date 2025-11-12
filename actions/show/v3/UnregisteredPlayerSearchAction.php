<?php

/**
 * @copyright Copyright (C) 2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v3;

use Yii;
use app\models\UnregisteredPlayer3;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\Response;

use function count;
use function explode;
use function preg_match;
use function trim;
use function urlencode;

final class UnregisteredPlayerSearchAction extends Action
{
    public function run(): string|Response
    {
        $request = Yii::$app->request;
        $splashtag = trim((string)$request->get('splashtag'));

        if ($splashtag) {
            $parts = explode('#', trim($splashtag), 2);
            if (count($parts) === 2) {
                $name = trim($parts[0]);
                $number = trim($parts[1]);

                if (!empty($name) && !empty($number) && preg_match('/^\d+$/', $number)) {
                    $registeredUsername = UnregisteredPlayer3::getRegisteredUsername($name, $number);
                    if ($registeredUsername) {
                        return $this->controller->redirect(['/@' . $registeredUsername . '/spl3/']);
                    }
                }
            }

            $player = UnregisteredPlayer3::findBySplashtagString($splashtag);
            
            if ($player) {
                if ($player->hasSignificantData()) {
                    return $this->controller->redirect([
                        '/unregistered-player-v3/by-splashtag/' . urlencode($splashtag)
                    ]);
                } else {
                    $errorMsg = Yii::t('app', 'Player found but has insufficient data (less than 5 battles). Found {battles} battles.', [
                        'battles' => $player->total_battles,
                    ]);
                    Yii::$app->session->setFlash('error', $errorMsg);
                }
            } else {
                Yii::$app->session->setFlash('error',
                    Yii::t('app', 'Player not found. Please check the exact format: Username#1234.')
                );
            }
        }

        return $this->controller->render('unregistered-player-search', [
            'searchValue' => $splashtag,
        ]);
    }
}