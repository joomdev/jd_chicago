<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.tab');
N2Loader::import('libraries.form.tabs.tabbedsidebar');

class N2TabGrouppedSidebar extends N2TabTabbedSidebar
{

    var $_tabs;

    function render($control_name) {
        $this->initTabs();
        foreach ($this->_tabs AS $tabname => $tab) {
            $tab->render($control_name);
        }
    }
}