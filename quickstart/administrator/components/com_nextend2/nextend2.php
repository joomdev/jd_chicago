<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
if (JFactory::getUser()->authorise('core.manage', 'com_nextend2')) {
    jimport("nextend2.nextend.joomla.library");
    N2Base::getApplication("system")->getApplicationType('backend')->render(array(
        "controller" => "dashboard",
        "action"     => "index"
    ));
    n2_exit();
} else {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
