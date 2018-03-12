<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2ElementToken extends N2Element
{

    public $_mode = 'hidden';

    public $_tooltip = false;

    function fetchTooltip() {
        return $this->fetchNoTooltip();
    }

    function fetchElement() {
        $this->_xml->addAttribute('class', 'n2-hidden');
        return N2Form::tokenize();
    }
}
