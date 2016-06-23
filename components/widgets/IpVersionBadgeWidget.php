<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use app\assets\IpVersionBadgeAsset;

class IpVersionBadgeWidget extends Widget
{
    public function run()
    {
        $version = $this->getIpVersion();
        if ($version === null) {
            return '';
        }
        
        IpVersionBadgeAsset::register($this->view);

        return sprintf(
            '<span id="%1$s" class="via-badge via-badge-ipv%2$d">via %3$s</span>',
            $this->id,
            $version,
            sprintf(
                '<span class="via-ip-version">IPv%2$d</span>',
                $this->id,
                $version
            )
        );
    }

    public function getIpVersion()
    {
        $ipAddr = (string)(Yii::$app->request->userIP ?? '');
        if (preg_match('/^[0-9.]+$/', $ipAddr)) {
            return 4;
        } elseif (preg_match('/^[0-9a-fA-F:]+$/', $ipAddr)) {
            return 6;
        } else {
            return null;
        }
    }
}
