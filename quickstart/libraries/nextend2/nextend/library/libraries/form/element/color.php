<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.element.text');

class N2ElementColor extends N2ElementText
{

    protected $alpha = 0;

    function fetchElement() {

        if (N2XmlHelper::getAttribute($this->_xml, 'alpha') == 1) {
            $this->alpha = 1;
        }

        $html = parent::fetchElement();
        N2JS::addInline('new NextendElementColor("' . $this->_id . '", ' . $this->alpha . ');');
        return $html;
    }

    protected function getClass() {
        return 'n2-form-element-color ' . ($this->alpha ? 'n2-form-element-color-alpha ' : '');
    }

    protected function pre() {
        return '<div class="sp-replacer"><div class="sp-preview"><div class="sp-preview-inner" style="background-color: rgb(62, 62, 62);"></div></div><div class="sp-dd">&#9650;</div></div>';
    }

    protected function post() {
        return '';
    }
}
