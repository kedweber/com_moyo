<?php

/**
 * Com_Moyo
 *
 * @author 		Jasper van Rijbroek <jasper@moyoweb.nl>
 * @category
 * @package     Date template helper
 * @subpackage
 */

defined('KOOWA') or die('Restricted Access');

class ComMoyoTemplateHelperDate extends ComDefaultTemplateHelperDate
{
    public function format($config = array())
    {
        // All we have to do is change the LC_TIME locale and the rest of the default behavior is fine.
        setlocale(LC_TIME, str_replace('-', '_', JFactory::getLanguage()->getTag()) . (strpos($_SERVER['HTTP_USER_AGENT'], 'OS X') ? '.UTF-8' : '.utf8'));

        return parent::format($config);
    }
}