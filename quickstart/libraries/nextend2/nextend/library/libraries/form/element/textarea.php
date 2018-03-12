<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2ElementTextarea extends N2Element
{

    function fetchElement() {

        N2JS::addInline('new NextendElementText("' . $this->_id . '");');

        return NHtml::tag('div', array(
            'class' => 'n2-form-element-textarea n2-border-radius',
            'style' => N2XmlHelper::getAttribute($this->_xml, 'style')
        ), NHtml::tag('textarea', array(
            'id'           => $this->_id,
            'name'         => $this->_inputname,
            'class'        => 'n2-h5',
            'autocomplete' => 'off',
            'style'        => N2XmlHelper::getAttribute($this->_xml, 'style2')
        ), $this->_form->get($this->_name, $this->_default)));
    }
}
