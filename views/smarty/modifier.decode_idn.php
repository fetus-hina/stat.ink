<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use Zend\Uri\UriFactory;
use Zend\Uri\Http;

function smarty_modifier_decode_idn($url)
{
    try {
        $uri = UriFactory::factory($url);
        if (!$uri->isValid() || !$uri instanceof Http) {
            return $url;
        }
        
        $host = $uri->getHost();
        if (strpos($host, 'xn--') !== false) {
            $host = idn_to_utf8($host);
            if ($host === false) {
                return $url;
            }
            $uri->sethost($host);
        }

        return $uri->__toString();
    } catch (\Exception $e) {
    }
    return $url;
}
