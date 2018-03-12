<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.form.element.text');

N2Localization::addJS(array(
    'Link',
    'Lightbox',
    'Create lightbox from image, video or iframe.',
    'Content list',
    'One per line',
    'Autoplay duration',
    'Examples',
    'Image',
    'Insert',
    'Keyword',
    'No search term specified. Showing recent items.',
    'Showing items match for "%s"',
    'Select'
));

class N2ElementUrlAbstract extends N2ElementText
{

    function fetchElement() {
        $html               = parent::fetchElement();
        $params             = array(
            'hasPosts' => N2Platform::$hasPosts
        );
        $params['imageUrl'] = N2Uri::pathToUri(N2LIBRARYASSETS . "/images");
        $params['url']      = N2Base::getApplication('system')->getApplicationType('backend')->router->createUrl("link/search");

        N2JS::addInline("new NextendElementUrl('" . $this->_id . "', " . json_encode($this->extendParams($params)) . " );");
        return $html;
    }

    protected function post() {
        if (!N2Platform::$hasPosts && !N2PRO) {
            return '';
        }
        return NHtml::tag('a', array(
            'href'  => '#',
            'class' => 'n2-form-element-clear'
        ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-empty n2-i-grey-opacity'), '')) . '<a id="' . $this->_id . '_button" class="n2-form-element-button n2-h5 n2-uc" href="#">' . n2_('Link') . '</a>';
    }

    protected function extendParams($params) {

        return $params;
    }
}

N2Loader::import('libraries.form.element.url', 'platform');