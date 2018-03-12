<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.form.element.image');
N2Loader::import('libraries.image.manager');

class N2ElementImageManager extends N2ElementImage
{

    protected $attributes = array();

    function fetchElement() {
        $html = parent::fetchElement();

        $html .= '<a id="' . $this->_id . '_manage" class="n2-button n2-button-medium n2-button-grey n2-h5 n2-uc n2-expert" href="#">'.n2_('Manage').'</a>';

        N2JS::addInline('new NextendElementImageManager("' . $this->_id . '", {});');

        return $html;
    }

    protected function getClass() {
        return 'n2-form-element-img ';
    }
}
