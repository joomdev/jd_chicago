<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2ElementGroup extends N2Element
{

    var $_translateable = true;

    function fetchTooltip() {
        if ($this->_label) {
            return parent::fetchTooltip();
        } else {
            return '';
        }
    }

    function fetchElement() {
        $this->_translateable = N2XmlHelper::getAttribute($this->_xml, 'translateable');
        $this->_translateable = ($this->_translateable === '0' ? false : true);

        $html = '';
        foreach ($this->_xml->param AS $element) {

            $class = N2Form::importElement(N2XmlHelper::getAttribute($element, 'type'));

            $el = new $class($this->_form, $this, $element);

            list($label, $field) = $el->render($this->control_name, $this->_translateable);

            $html .= NHtml::tag('div', array(
                'class' => 'n2-mixed-group ' . N2XmlHelper::getAttribute($element, 'class')
            ), NHtml::tag('div', array('class' => 'n2-mixed-label'), $label) . NHtml::tag('div', array('class' => 'n2-mixed-element'), $field));

            if (N2XmlHelper::getAttribute($element, 'post') == 'break') {
                $html .= '<br class="' . N2XmlHelper::getAttribute($element, 'class') . '" />';
            }
        }

        return NHtml::tag('div', array(
            'class' => 'n2-form-element-mixed',
            'style' => N2XmlHelper::getAttribute($this->_xml, 'style')
        ), $html);
    }
}
