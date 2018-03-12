<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */
defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or define("GCORE_SITE", "front");
jimport('cegcore.joomla_gcloader');
if(!class_exists('JoomlaGCLoader')){
	JError::raiseWarning(100, "Please download the CEGCore framework from www.chronoengine.com then install it using the 'Extensions Manager'");
	return;
}

$chronoforms5_setup = function() use($params){
	$mainframe = \JFactory::getApplication();
	$formname = $params->get('chronoform', '');
	$chronoform = GCore\Libs\Request::data('chronoform', '');
	$event = GCore\Libs\Request::data('event', '');
	if(!empty($event)){
		if($formname != $chronoform){
			$event = 'load';
		}
	}
	return array('chronoform' => $formname, 'event' => $event);
};

$output = new JoomlaGCLoader('front', 'chronoforms5', 'chronoforms', $chronoforms5_setup, array('controller' => '', 'action' => ''));