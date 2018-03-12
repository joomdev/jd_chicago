<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.element.list');

class N2ElementSkin extends N2ElementList
{

    protected $fixedMode = false;

    function fetchElement() {
        N2Localization::addJS('Done');
        if (N2XmlHelper::getAttribute($this->_xml, 'fixed')) {
            $this->fixedMode = true;
        }

        $html = parent::fetchElement();

        N2JS::addInline('new NextendElementSkin("' . $this->_id . '", "' . str_replace($this->_name, '', $this->_id) . '", ' . json_encode($this->skins) . ', ' . json_encode($this->fixedMode) . ');');

        return $html;
    }

    function generateOptions(&$xml) {
        $html = '';
        if (!$this->fixedMode) {
            $html .= '<option value="0" selected="selected">' . n2_('Choose') . '</option>';
        }
        $this->skins = array();
        foreach ($this->_xml->children() as $skin) {
            $v = $skin->getName();
            $html .= '<option ' . $this->isSelected($v) . ' value="' . $v . '">' . n2_(N2XmlHelper::getAttribute($skin, 'label')) . '</option>';
            $this->skins[$v] = array();
            foreach ($skin as $param) {
                $this->skins[$v][$param->getName()] = (string)$param;
            }
        }
        return $html;
    }
}
