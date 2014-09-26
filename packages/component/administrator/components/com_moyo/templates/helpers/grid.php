<?php

class ComMoyoTemplateHelperGrid extends ComDefaultTemplateHelperGrid
{
    public function defaultable($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'row'  		=> null,
            'field'		=> 'default'
        ))->append(array(
            'data'		=> array($config->field => $config->row->{$config->field})
        ));

        $class  = $config->row->{$config->field} ? 'icon-star' : 'icon-star-empty';
        $alt 	= $config->row->{$config->field} ? $this->translate('Default') : $this->translate('Not default');
        $config->data->{$config->field} = $config->row->{$config->field} ? 0 : 1;
        $data = str_replace('"', '&quot;', $config->data);

        $html = '<i  data-action="edit" data-data="'.$data.'" class="' . $class . '" title="' . $alt . '"></i>';

        return $html;
    }
}