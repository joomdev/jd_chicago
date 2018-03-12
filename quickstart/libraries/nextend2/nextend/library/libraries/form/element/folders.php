<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.form.element.text');
N2Loader::import('libraries.browse.browse');

class N2ElementFolders extends N2ElementText
{

    function fetchElement() {

        N2ImageHelper::initLightbox();

        N2JS::addInline("new NextendElementFolders('" . $this->_id . "' );");

        return parent::fetchElement();
    }

    protected function post() {
        return NHtml::tag('a', array(
            'href'  => '#',
            'class' => 'n2-form-element-clear'
        ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-empty n2-i-grey-opacity'), '')) . '<a id="' . $this->_id . '_button" class="n2-form-element-button n2-h5 n2-uc" href="#">' . n2_('Folders') . '</a>';
    }
}
