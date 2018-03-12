<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.tab');

class N2TabPlaceholder extends N2Tab
{

    function decorateTitle() {
        $id = N2XmlHelper::getAttribute($this->_xml, 'id');
        echo "<div id='" . $id . "' class='nextend-tab " . N2XmlHelper::getAttribute($this->_xml, 'class') . "'>";
        if (isset($GLOBALS[$id])) {
            echo $GLOBALS[$id];
        }
    }

    function decorateGroupStart() {

    }

    function decorateGroupEnd() {
        echo "</div>";
    }
}