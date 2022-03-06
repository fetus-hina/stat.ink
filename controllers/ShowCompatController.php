<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\controllers;

use app\components\web\Controller;

class ShowCompatController extends Controller
{
    public $layout = 'main';

    public function redirect($url, $unused = 302)
    {
        return parent::redirect($url, 308);
    }

    public function actionBattle($screen_name, $battle)
    {
        return $this->redirect(['/show/battle', 'screen_name' => $screen_name, 'battle' => $battle]);
    }

    public function actionEditBattle($screen_name, $battle)
    {
        return $this->redirect(['/show/edit-battle', 'screen_name' => $screen_name, 'battle' => $battle]);
    }

    public function actionUserFromto($screen_name, $id_from, $id_to)
    {
        return $this->redirect(array_merge($_GET, ['/show/user']));
    }

    public function actionUser($screen_name)
    {
        return $this->redirect(array_merge($_GET, ['/show/user']));
    }

    public function actionUserStatReportY(string $screen_name, int $year)
    {
        return $this->redirect([
            '/show/user-stat-report',
            'screen_name' => $screen_name,
            'year' => $year,
        ]);
    }

    public function actionUserStatReportYM(string $screen_name, int $year, int $month)
    {
        return $this->redirect([
            '/show/user-stat-report',
            'screen_name' => $screen_name,
            'year' => $year,
            'month' => $month,
        ]);
    }

    public function actionUserStatBy(string $screen_name, string $by)
    {
        $params = array_merge($_GET, ["/show/user-stat-${by}"]);
        unset($params['by']);
        return $this->redirect($params);
    }
}
