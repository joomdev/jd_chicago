<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.element.hidden');

class N2ElementLayerPicker extends N2ElementHidden
{

    public $_tooltip = true;

    function fetchElement() {

        N2JS::addInline('new NextendElementLayerPicker("' . $this->_id . '");');

        return parent::fetchElement() . NHtml::tag('div', array('class' => 'n2-ss-layer-picker'), '<i class="n2-i n2-it n2-i-16 n2-i-layerlink"></i>');
    }
}
