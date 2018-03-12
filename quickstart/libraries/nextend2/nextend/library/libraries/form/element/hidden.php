<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2ElementHidden extends N2Element
{

    public $_mode = 'hidden';

    public $_tooltip = false;

    function fetchTooltip() {
        if ($this->_tooltip) {
            return parent::fetchTooltip();
        } else {
            return $this->fetchNoTooltip();
        }
    }

    function fetchElement() {

        return NHtml::tag('input', array(
            'id'           => $this->_id,
            'name'         => $this->_inputname,
            'value'        => $this->getValue(),
            'type'         => $this->_mode,
            'autocomplete' => 'off'
        ), false);
    }
}
