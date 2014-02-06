<?php

class ComMoyoTemplateHelperBehavior extends ComExtmanTemplateHelperBehavior
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        // Load language file.
        JFactory::getLanguage()->load('com_moyo', JPATH_ADMINISTRATOR . '/components/com_moyo', null, true);
    }

    public function select2($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'element' => '.select2-listbox',
            'options' => array(
                'width' => 'resolve',
                'dropdownCssClass' => 'com_moyo'
             )
        ));

        $html ='';

        if (!isset(self::$_loaded['jquery'])) {
            $html .= $this->jquery();
        }

        if (!isset(self::$_loaded['select2'])) {

            $html .= '<script src="media/com_moyo/js/select2.js"></script>';

            $html .= '<script>jQuery(function($){
                $("'.$config->element.'").select2('.$config->options.');
            });</script>';

            if(isset(self::$_loaded['validator']))
            {
                $html .= '<script src="media://com_moyo/js/select2.validator.js" />';

                $html .= '<script>jQuery(function($){
                    $("'.$config->element.'").select2(\'container\').removeClass(\'required\');
                });</script>';
            }


            $html .= '<link rel="stylesheet" type="text/css" href="media/com_moyo/css/select2.css">';

            self::$_loaded['select2'] = true;
        }

        return $html;
    }

    /*
     * Overriden to make the validator support Select2
     */
    public function validator($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'options'  => array(
                'fieldSelectors' => 'input, select, textarea, .select2-container'
            )
        ));

        return parent::validator($config);
    }

    /**
     * Renders an input html tag or returns the config if it is not an array
     *
     * @param   array   $config
     * @return  string  rendered html
     */
    private function getInput($config = array())
    {
        if (is_array($config)) {
            $config = new KConfig($config);
            return '<input class="'.$config->class.'" type="'.$config->type.'" name="'.$config->name.'" value="" placeholder="'.$this->translate($config->placeholder).'" />';
        } else {
            return $config;
        }
    }

    /**
     * Returns a rendered control-group
     *
     * @param   array   $config
     * @return  string  rendered control-group
     */
    public function controlGroup($config = array())
    {
        $config = new KConfig($config);

        $config->append(array(
            'label' => array(
                'text' => ''
            ),
            'input' => array(
                'type'          => 'text',
                'class'         => '',
                'name'          => '',
                'placeholder'   => ''
            ),
            'controls' => array(
                'style' => ''
            )
        ));

        return '<div class="control-group">
                    <label class="control-label">'.$this->translate($config->label->text).'</label>
                    <div class="controls" style="'.$config->controls->style.'">'
                        .$this->getInput($config->input).'
                    </div>
                </div>';
    }

    /**
     * Returns 2 rendered control-groups with created_on and created_by
     *
     * @param   array   $config
     * @return  string  rendered control-groups
     * @usage   @helper('com://admin/moyo.template.helper.behavior.creatable');
     */
    public function creatable($config = array())
    {
        $config = new KConfig($config);

        return $this->controlGroup(array(
            'label' => array(
                'text' => 'CREATED_ON'
            ),
            'input' => $this->getService('com://admin/moyo.template.helper.date')->humanize(array('date', $config->created_on)),
            'controls' => array(
                'style' => 'padding-top: 5px;'
            )
        )) . $this->controlGroup(array(
            'label' => array(
                'text' => 'CREATED_BY'
            ),
            'input' => JFactory::getUser($config->created_by)->username,
            'controls' => array(
                'style' => 'padding-top: 5px;'
            )
        ));
    }

    /**
     * Returns 2 rendered control-groups with modified_on and modified_by
     *
     * @param   array   $config
     * @return  string  rendered control-groups
     * @usage   @helper('com://admin/moyo.template.helper.behavior.modifiable');
     */
    public function modifiable($config = array())
    {
        $config = new KConfig($config);

        return $this->controlGroup(array(
            'label' => array(
                'text' => 'MODIFIED_ON'
            ),
            'input' => $this->getService('com://admin/moyo.template.helper.date')->humanize(array('date', $config->modified_on)),
            'controls' => array(
                'style' => 'padding-top: 5px;'
            )
        )) . $this->controlGroup(array(
            'label' => array(
                'text' => 'MODIFIED_BY'
            ),
            'input' => JFactory::getUser($config->created_by)->username,
            'controls' => array(
                'style' => 'padding-top: 5px;'
            )
        ));
    }
}