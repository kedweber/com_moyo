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

    /**
     * Encapsulates links with a tags
     *
     * @param array $config
     */
    public function urlcloaking($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'text' => ''
        ));

        preg_match_all("/https?\:\/\/[^\" ]+/i", $config->text, $result);

        foreach($result as $string) {
            if(isset($string[0])) {
                $config->text = str_replace($string[0], '<a href="'.$string[0].'" target="_blank">'.$string[0].'</a>', $config->text);
            }
        }

        return $config->text;
    }
}