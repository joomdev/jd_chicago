<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.element.hidden');

class N2ElementSliderWidgetArea extends N2ElementHidden
{

    function fetchElement() {

        $areas = '';
        for ($i = 1; $i <= 12; $i++) {
            $areas .= NHtml::tag('div', array(
                'class'     => 'n2-area n2-area-' . $i . $this->isSelected($i),
                'data-area' => $i
            ));
        }

        $html = NHtml::tag('div', array(
            'id'    => $this->_id . '_area',
            'class' => 'n2-widget-area'
        ), NHtml::tag('div', array(
                'class' => 'n2-widget-area-inner'
            )) . $areas);
        $html .= parent::fetchElement();

        N2JS::addInline('new NextendElementSliderWidgetArea("' . $this->_id . '");');

        return $html;
    }

    function isSelected($i) {
        if ($i == $this->getValue()) {
            return ' n2-active';
        }
        return '';
    }
}
