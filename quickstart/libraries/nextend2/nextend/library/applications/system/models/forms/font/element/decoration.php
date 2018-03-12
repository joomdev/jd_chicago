<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.element.checkbox');

class N2ElementDecoration extends N2ElementCheckbox
{

    function fetchElement() {

        return NHtml::tag('div', array(
            'class' => 'n2-form-element-decoration',
            'style' => N2XmlHelper::getAttribute($this->_xml, 'style')
        ), parent::fetchElement());
    }

    function generateOptions(&$xml) {
        $options = array(
            'bold'      => 'n2-i n2-it n2-i-bold',
            'italic'    => 'n2-i n2-it n2-i-italic',
            'underline' => 'n2-i n2-it n2-i-underline'
        );

        $length = count($options) - 1;

        $html = '';
        $i    = 0;
        foreach ($options AS $value => $class) {
            $this->values[] = $value;

            $html .= NHtml::tag('div', array(
                'class' => 'n2-checkbox-option n2-decoration-' . $value . ($this->isSelected($value) ? ' n2-active' : '') . ($i == 0 ? ' n2-first' : '') . ($i == $length ? ' n2-last' : '')
            ), NHtml::tag('i', array('class' => $class)));
            $i++;
        }
        return $html;
    }
}