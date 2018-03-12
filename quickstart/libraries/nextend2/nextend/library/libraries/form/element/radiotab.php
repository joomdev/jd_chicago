<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.element.radio');

class N2ElementRadioTab extends N2ElementRadio
{

    protected $class = 'n2-form-element-radio-tab';

    function generateOptions(&$xml) {

        $length = count($xml->option) - 1;

        $html = '';
        $i    = 0;
        foreach ($xml->option AS $option) {
            $value          = N2XmlHelper::getAttribute($option, 'value');
            $this->values[] = $value;
            $html .= NHtml::tag('div', array(
                'class' => 'n2-radio-option n2-h4' . ($this->isSelected($value) ? ' n2-active' : '') . ($i == 0 ? ' n2-first' : '') . ($i == $length ? ' n2-last' : '')
            ), n2_((string)$option));
            $i++;
        }
        return $html;
    }
}
