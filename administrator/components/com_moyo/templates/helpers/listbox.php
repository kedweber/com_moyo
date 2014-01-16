<?php

class ComMoyoTemplateHelperListbox extends ComDefaultTemplateHelperListbox
{
    /**
     * @param array $config
     * @return mixed|string
     */
    public function categories($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'model'    => 'categories',
            'value'    => 'id',
            'text'     => 'title',
            'prompt'   => '',
            'required' => false,
            'attribs' => array('data-placeholder' => $this->translate('Select a category&hellip;'), 'class' => 'select2-listbox'),
            'behaviors' => array('select2' => array('element' => '.select2-listbox'))
        ));

        return $this->_treelistbox($config);
    }

    /**
     * @param array $config
     * @return mixed|string
     */
    protected function _treelistbox($config = array())
     {
         $config = new KConfig($config);
         $config->append(array(
                 'name'		   => '',
                 'attribs'	   => array(),
                 'model'		   => KInflector::pluralize($this->getIdentifier()->package),
                 'deselect'     => true,
                 'prompt'       => '- '.$this->translate('Select').' -',
                 'unique'	   => false, // Overridden since there can be categories in different levels with the same name
                 'check_access' => false,
                 'behaviors'   => array(), // Behaviors like select2,
         ))->append(array(
                'indent'    => '&nbsp;&nbsp;&nbsp;',
                 'ignore' 	 => array(),
                 'value'		 => $config->name,
                 'selected'   => $config->{$config->name},
                 'identifier' => 'com://'.$this->getIdentifier()->application.'/'.$this->getIdentifier()->package.'.model.'.KInflector::pluralize($config->model)
                 ))->append(array(
                         'text'		=> $config->value,
                 ))->append(array(
                         'filter' 	=> array('sort' => $config->text),
                 ));

         if ($config->required) {
             $config->attribs->class .= ' required';
         }

         if ($config->deselect) {
             if (in_array('select2', array_keys(KConfig::unbox($config->behaviors)))) {
                 $config->behaviors->select2->append(array('options' => array('allowClear' => true)));
             }
         }

         $list = $this->getService($config->identifier)->set($config->filter)->getList();

         //Get the list of items
         $items = $list->getColumn($config->value);
         if ($config->unique) {
             $items = array_unique($items);
         }

         //Compose the options array
         $options = array();
         if ($config->deselect) {
             $options[] = $this->option(array('text' => $config->prompt));
         }

         $ignore = KConfig::unbox($config->ignore);
         foreach ($items as $key => $value) {
             $item = $list->find($key);

             if (in_array($item->id, $ignore)) {
                 continue;
             }

             $options[] =  $this->option(array('text' => str_repeat($config->indent, $item->level) . $item->{$config->text}, 'value' => $item->{$config->value}));
         }

         //Add the options to the config object
         $config->options = $options;

         $html = $this->optionlist($config);

         if ($this->getTemplate()) {
             foreach ($config->behaviors as $behavior => $options) {
                 $html .= $this->getTemplate()->renderHelper('com://admin/moyo.template.helper.behavior.'.$behavior, KConfig::unbox($options));
             }
         }

         return $html;
     }

     /**
      * Overridden to fix the Bootstrap problem with size=1 select boxes
      *
      * @see KTemplateHelperSelect::optionlist()
      */
     public function optionlist($config = array())
     {
         $html = parent::optionlist($config);

         $html = preg_replace('#size="1"#', '', $html, 1);

         return $html;
     }
}
