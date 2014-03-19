<?php

/**
 * Com
 *
 * @author 		Joep van der Heijden <joep.van.der.heijden@moyoweb.nl>
 * @category	
 * @package 	
 * @subpackage	
 */
 
defined('KOOWA') or die('Restricted Access');

class ComMoyoTemplateHelperParser extends KTemplateHelperAbstract
{
    public function link($config = array())
    {
        $config = new KConfig($config);
        $url = $config->url;

        if ($url && substr($url, 0, 7) !== 'http://' && substr($url, 0, 8) !== 'https://') {
            $url = 'http://' . $url;
        }

        return $url;
    }
}