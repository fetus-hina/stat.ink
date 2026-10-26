<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\controllers;

use Override;
use app\components\web\Controller;
use yii\web\Response;

use function array_merge;

class ShowCompatController extends Controller
{
    public $layout = 'main';

    /**
     * @param string|array<int|string, mixed> $url
     */
    #[Override]
    public function redirect($url, $unused = 302): Response
    {
        return parent::redirect($url, 308);
    }

    public function actionBattle(string $screen_name, string $battle): Response
    {
        return $this->redirect(['/show/battle', 'screen_name' => $screen_name, 'battle' => $battle]);
    }

    public function actionEditBattle(string $screen_name, string $battle): Response
    {
        return $this->redirect(['/show/edit-battle', 'screen_name' => $screen_name, 'battle' => $battle]);
    }

    public function actionUserFromto(string $screen_name, string $id_from, string $id_to): Response
    {
        return $this->redirect(array_merge($_GET, ['/show/user']));
    }

    public function actionUser(string $screen_name): Response
    {
        return $this->redirect(array_merge($_GET, ['/show/user']));
    }

    public function actionUserStatReportY(string $screen_name, int $year): Response
    {
        return $this->redirect([
            '/show/user-stat-report',
            'screen_name' => $screen_name,
            'year' => $year,
        ]);
    }

    public function actionUserStatReportYM(string $screen_name, int $year, int $month): Response
    {
        return $this->redirect([
            '/show/user-stat-report',
            'screen_name' => $screen_name,
            'year' => $year,
            'month' => $month,
        ]);
    }

    public function actionUserStatBy(string $screen_name, string $by): Response
    {
        $params = array_merge($_GET, ["/show/user-stat-${by}"]);
        unset($params['by']);
        return $this->redirect($params);
    }
}
