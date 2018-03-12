<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.form.element.text');

class N2ElementJoomlaComponentOptions extends N2ElementText
{

    function fetchElement() {
        JHTML::_('behavior.modal');
        $html = '<a class="nextend-configurator-button modal" rel="{handler: \'iframe\', size: {x: 875, y: 550}}" href="index.php?option=com_config&view=component&component=' . N2XmlHelper::getAttribute($this->_xml, 'component') . '&tmpl=component">Configure</a>';
        return $html;
    }
}
