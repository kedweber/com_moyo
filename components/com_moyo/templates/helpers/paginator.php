<?php
/**
 * Com
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Socialhub
 * @subpackage  ...
 * @uses        Com_
 */

defined('KOOWA') or die('Protected resource');

class ComMoyoTemplateHelperPaginator extends KTemplateHelperPaginator
{

    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        JFactory::getLanguage()->load('com_moyo', JPATH_SITE . '/components/com_moyo', null, true);
    }

    /**
     * Render item pagination
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     * @see     http://developer.yahoo.com/ypatterns/navigation/pagination/
     */
    public function pagination($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'total'      => 0,
            'display'    => 4,
            'ajax'       => false,
            'url'        => '',
            'container'  => 'container',
            'offset'     => 0,
            'limit'      => 0,
            'show_count' => true
        ));

        $html = '';
        $jsContainer = str_replace('-', '_', $config->container);

        if($config->ajax) {
            $html = '<script src="media://com_moyo/js/bootstrap-paginator.js" />';
            $html .= '<script>
                        function setPaginator'.$jsContainer.'() {

                            jQuery.noConflict()(function($) {
                                var options = {
                                    totalPages: '. ceil($config->total / $config->limit) .',
                                    onPageClicked: function(e, originalEvent, type, page){
                                        var height = '. ($config->height ? $config->height : '$("#'. $config->container.'").height();').'
                                        $("#'. $config->container.'").html('. '\'<div class="loading" style="height: \' + height + \'px;"></div>' . '\');
                                        $.ajax({
                                            url: "'. $config->url. '&limit='.$config->limit.'&offset=" + (page * '.$config->limit.' - '.$config->limit.')
                                        }).success(function(data) {
                                            $("#'. $config->container.'").html(data);
                                        });
                                    },
                                    itemTexts: function (type, page, current) {
                                        switch (type) {
                                            case "first":
                                                return window.innerWidth > 768 ? "'. $this->translate('FIRST') .'" : "<<";
                                            case "prev":
                                                return window.innerWidth > 768 ? "'. $this->translate('PREV') .'" : "<";
                                            case "next":
                                                return window.innerWidth > 768 ? "'. $this->translate('NEXT') .'" : ">";
                                            case "last":
                                                return window.innerWidth > 768 ? "'. $this->translate('LAST') .'" : ">>";
                                            case "page":
                                                return page;
                                        }
                                    },
                                    pageUrl: "#'. ($config->pageUrl ? $config->pageUrl : $config->container) .'"
                                }

                                $("#pagination-'. $config->container .'").bootstrapPaginator(options);
                            });
                        }
                        setPaginator'.$jsContainer.'();
                        window.addEventListener("resize", setPaginator'.$jsContainer.');
                    </script>';
        }

        if(!$config->ajax) {
            $this->_initialize($config);

            $html .= '<ul class="pagination">';
            $html .=  $this->_bootstrap_pages($this->_items($config));
            $html .= '</ul>';

            if($config->show_count) {
                $html .= '<div class="count pull-right"> '.$this->translate('Page').' '.$config->current.' '.$this->translate('of').' '.$config->count.'</div>';
            }
        } else {
            $html.= '<div id="pagination-'. $config->container .'"></div>';
        }

        return $html;
    }

    /**
     * Render a list of pages links
     *
     * This function is overriddes the default behavior to render the links in the khepri template
     * backend style.
     *
     * @param   araay   An array of page data
     * @return  string  Html
     */
    protected function _pages($pages)
    {
        $class = $pages['first']->active ? '' : 'off';
        $html  = '<div class="button2-right '.$class.'"><div class="start">'.$this->_link($pages['first'], 'Start').'</div></div>';

        $class = $pages['previous']->active ? '' : 'off';
        $html  .= '<div class="button2-right '.$class.'"><div class="prev">'.$this->_link($pages['previous'], 'Prev', array('class' => array('prev'))).'</div></div>';

        $html  .= '<div class="button2-left"><div class="page">';
        foreach($pages['pages'] as $page) {
            $html .= $this->_link($page, $page->page);
        }
        $html .= '</div></div>';

        $class = $pages['next']->active ? '' : 'off';
        $html  .= '<div class="button2-left '.$class.'"><div class="next">'.$this->_link($pages['next'], 'Next', array('class' => array('next'))).'</div></div>';

        $class = $pages['last']->active ? '' : 'off';
        $html  .= '<div class="button2-left '.$class.'"><div class="end">'.$this->_link($pages['last'], 'End').'</div></div>';

        return $html;
    }

    protected function _link($page, $title, $attribs = array())
    {
        $url   = clone KRequest::url();
        $query = $url->getQuery(true);

        //For compatibility with Joomla use limitstart instead of offset
        $query['limit']      = $page->limit;
        $query['limitstart'] = $page->offset;

        $url->setQuery($query);

        print_r(KConfig::unbox($attribs));

        exit;

        $class = $page->current ? 'class="active"' : '';

        if($page->active && !$page->current) {
            $html = '<a href="'.$url.'" '.$class.'>'.$this->translate($title).'</a>';
        } else {
            $html = '<span '.$class.'>'.$this->translate($title).'</span>';
        }

        return $html;
    }

    protected function _bootstrap_pages($pages)
    {
        $html  = $pages['first'] ? '<li class="first">'.$this->_bootstrap_link($pages['first'], $this->translate('First'), array('class' => array('prev'))).'</li>' : '';
        $html .= $pages['previous'] ? '<li class="previous">'.$this->_bootstrap_link($pages['previous'], $this->translate('Previous')).'</li>' : '';

        /* @TODO should be a better way to do this than iterating the array to find the current page */
        $current = 0;
        foreach ($pages['pages'] as $i => $page) {
            if($page->current) $current = $i;
        }

        /* @TODO move this into the $config initialize */
        $padding = 2;

        $total = count($pages['pages']);
        $hellip = false;
        foreach ($pages['pages'] as $i => $page) {
            $in_range = $i > ($current - $padding) && $i < ($current + $padding);

            if ($i < $padding || $in_range || $i >= ($total - $padding)) {
                $html .= '<li class="'.($page->active && !$page->current ? '' : 'active').'">';
                $html .= $this->_bootstrap_link($page, $page->page);

                $hellip = false;
            } else {
                if($hellip == true) continue;

                $html .= '<li class="disabled">';
                $html .= '<a href="#">&hellip;</a>';

                $hellip = true;
            }

            $html .= '</li>';
        }
        die('etst');

        $html  .= $pages['next'] ? '<li class="next">'.$this->_bootstrap_link($pages['next'], $this->translate('Next'), array('class' => array('next'))).'</li>' : '';
        $html  .= $pages['last'] ? '<li class="last">'.$this->_bootstrap_link($pages['last'], $this->translate('Last')).'</li>' : '';

        return $html;
    }

    protected function _bootstrap_link($page, $title, $attribs = array())
    {
        $url   = clone KRequest::url();
        $query = $url->getQuery(true);

        //For compatibility with Joomla use limitstart instead of offset
        $query['limit']      = $page->limit;
        $query['limitstart'] = $page->offset;

        $url->setQuery($query);

        print_r(KConfig::unbox($attribs));

        $html = '<a class="" href="'.$url.'"  data-query="'.http_build_query(array('limit' => $page->limit, 'offset' => $page->offset)).'">'.$this->translate($title).'</a>';

        return $html;
    }
}