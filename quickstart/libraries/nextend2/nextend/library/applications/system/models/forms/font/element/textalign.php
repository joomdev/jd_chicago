<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.element.radiotab');

class N2ElementTextAlign extends N2ElementRadioTab
{

    protected $class = 'n2-form-element-radio-tab n2-form-element-textalign';

    function generateOptions(&$xml) {
        $options = array(
            'left'    => 'n2-i n2-it n2-i-left',
            'center'  => 'n2-i n2-it n2-i-center',
            'right'   => 'n2-i n2-it n2-i-right',
            'justify' => 'n2-i n2-it n2-i-justify'
        );
        $length  = count($options) - 1;

        $this->values = array();
        $html         = '';
        $i            = 0;
        foreach ($options AS $value => $class) {
            $this->values[] = $value;

            $html .= NHtml::tag('div', array(
                'class' => 'n2-radio-option' . ($this->isSelected($value) ? ' n2-active' : '') . ($i == 0 ? ' n2-first' : '') . ($i == $length ? ' n2-last' : '')
            ), NHtml::tag('i', array('class' => $class)));
            $i++;
        }
        return $html;
    }
}