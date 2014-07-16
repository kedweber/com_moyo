<?php

defined('KOOWA') or die('Protected resource');

class ComMoyoTemplateHelperString extends KTemplateHelperAbstract
{
    public function truncate($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
                'text' => '',
                'offset' => 0,
                'length' => 100,
                'path' => ' ...',
                'allowed_tags' => '')
        );

        $text = strip_tags($config->text, $config->allowed_tags);

        if(KHelperString::strlen($config->text) > $config->length) {
            $text = KHelperString::substr($text, $config->offset, $config->length) . $config->path;
        }

        return $text;
    }
}