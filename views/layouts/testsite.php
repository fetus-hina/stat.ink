<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\bootstrap\NavBar;
use yii\helpers\Html;

if (YII_ENV === 'prod') {
    return;
}
?>
<aside>
  <div class="navbar bg-warning mb-0">
    <div class="container-fluid">
      <div class="container">
        <div class="navbar-header">
          <p class="navbar-text ml-0 mr-0 p-0 w-100"><?=
            (YII_ENV === 'test')
              ? 'This is a staging environment. Database will be reset daily.'
              : vsprintf('This is a development environment. Database: %s', [
                Html::tag('code', Html::encode(preg_replace_callback(
                  '/^.*?dbname=([\w-]+)\b.*$/',
                  function (array $match): string {
                    return $match[1];
                  },
                  Yii::$app->db->dsn
                ))),
              ])
          ?></p>
        </div>
      </div>
    </div>
  </div>
</aside>
