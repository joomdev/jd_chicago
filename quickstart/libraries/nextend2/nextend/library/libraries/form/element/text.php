<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2ElementText extends N2Element
{

    protected $attributes = array();

    public $fieldType = 'text';

    function fetchElement() {

        N2JS::addInline('new NextendElementText("' . $this->_id . '");');

        $html = NHtml::openTag('div', array(
            'class' => 'n2-form-element-text ' . $this->getClass() . ($this->_xml->unit ? 'n2-text-has-unit ' : '') . 'n2-border-radius',
            'style' => ($this->fieldType == 'hidden' ? 'display: none;' : '')
        ));

        $subLabel = N2XmlHelper::getAttribute($this->_xml, 'sublabel');
        if ($subLabel) {
            $html .= NHtml::tag('div', array(
                'class' => 'n2-text-sub-label n2-h5 n2-uc'
            ), n2_($subLabel));
        }

        $html .= $this->pre();

        $html .= NHtml::tag('input', $this->attributes + array(
                'type'         => $this->fieldType,
                'id'           => $this->_id,
                'name'         => $this->_inputname,
                'value'        => $this->_form->get($this->_name, $this->_default),
                'class'        => 'n2-h5',
                'style'        => $this->getStyle(),
                'autocomplete' => 'off'
            ), false);

        $html .= $this->post();

        if ($this->_xml->unit) {
            $html .= NHtml::tag('div', array(
                'class' => 'n2-text-unit n2-h5 n2-uc'
            ), n2_((string)$this->_xml->unit));
        }
        $html .= "</div>";
        return $html;
    }

    protected function getClass() {
        return '';
    }

    protected function getStyle() {
        return N2XmlHelper::getAttribute($this->_xml, 'style');
    }

    protected function pre() {
        return '';
    }

    protected function post() {
        return '';
    }
}