<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2Element
{

    /**
     * @var N2Form
     */
    public $_form;

    var $_tab;
    var $_xml;
    var $_default;
    var $_name;
    var $_label;
    var $_description;
    var $_id;
    var $_inputname;
    var $_editableName = false;

    function N2Element(&$form, &$tab, &$xml) {

        $this->_form = $form;
        $this->_tab  = $tab;
        $this->_xml  = $xml;
        $this->_name = N2XmlHelper::getAttribute($xml, 'name');
    }

    function render($control_name = 'params', $tooltip = true) {
        $this->control_name = $control_name;
        $this->_default     = N2XmlHelper::getAttribute($this->_xml, 'default');
        $this->_id          = $this->generateId($control_name . $this->_name);
        $this->_inputname   = (N2XmlHelper::getAttribute($this->_xml, 'hidename') ? '' : $control_name . '[' . $this->_name . ']');
        $this->_label       = N2XmlHelper::getAttribute($this->_xml, 'label');
        if ($this->_label == '') $this->_label = $this->_name;
        return array(
            $tooltip ? $this->fetchTooltip() : '',
            $this->fetchElement()
        );
    }

    function fetchTooltip() {
        if ($this->_label == '-') {
            $this->_label = '';
        } else {
            $this->_label = n2_($this->_label);
        }
        $html = NHtml::tag('label', array(
            'for' => $this->_id
        ), $this->_label);
        return $html;
    }

    function fetchNoTooltip() {
        return "";
    }

    function fetchElement() {

    }

    function getValue() {
        return $this->_form->get($this->_name, $this->_default);
    }

    function setValue($value) {
        return $this->_form->set($this->_name, $value);
    }

    function generateId($name) {

        return str_replace(array(
            '[',
            ']',
            ' '
        ), array(
            '',
            '',
            ''
        ), $name);
    }

}
