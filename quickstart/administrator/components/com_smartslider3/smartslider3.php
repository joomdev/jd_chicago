<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
if (JFactory::getUser()->authorise('core.manage', 'com_smartslider3')) {

    if (!class_exists('plgSystemNextendSmartslider3')) {
        require_once(JPATH_PLUGINS . '/system/nextendsmartslider3/nextendsmartslider3.php');
        if (class_exists('JEventDispatcher', false)) {
            $dispatcher = JEventDispatcher::getInstance();
        } else {
            $dispatcher = JDispatcher::getInstance();
        }
        $plugin = JPluginHelper::getPlugin('system', 'nextendsmartslider3');
        new plgSystemNextendSmartslider3($dispatcher, (array)($plugin));
    }

    jimport("nextend2.nextend.joomla.library");
    N2Base::getApplication("smartslider")
          ->getApplicationType('backend')
          ->render(array(
              "controller" => "sliders",
              "action"     => "index"
          ));
    n2_exit();
} else {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
