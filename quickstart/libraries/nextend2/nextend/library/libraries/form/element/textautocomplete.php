<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.element.text');

class N2ElementTextAutocomplete extends N2ElementText
{

    function fetchElement() {
        $html = parent::fetchElement();
        N2JS::addInline('new NextendElementAutocompleteSimple("' . $this->_id . '", ' . json_encode(explode(',', N2XmlHelper::getAttribute($this->_xml, 'values'))) . ');');
        return $html;
    }

    protected function getClass() {
        return 'n2-form-element-autocomplete ui-front ' . parent::getClass();
    }
}