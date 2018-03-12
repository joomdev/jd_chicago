<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
*
* @version $Id: admin.virtuemart.php 7256 2013-09-29 18:42:44Z Milbo $
* @package VirtueMart
* @subpackage core
* @copyright Copyright (C) VirtueMart Team - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();
//VmConfig::showDebug('all');

if (!class_exists( 'VmController' )) require(VMPATH_ADMIN.DS.'helpers'.DS.'vmcontroller.php');
if (!class_exists( 'VmModel' )) require(VMPATH_ADMIN.DS.'helpers'.DS.'vmmodel.php');

vmRam('Start');
vmSetStartTime('vmStart');

$_controller = vRequest::getCmd('view', vRequest::getCmd('controller', 'virtuemart'));

VmConfig::loadJLang('com_virtuemart');
$exe = true;
//VmConfig::$echoDebug=true;
// Require specific controller if requested
if($_controller) {
	if (file_exists(VMPATH_ADMIN.DS.'controllers'.DS.$_controller.'.php')) {
		// Only if the file exists, since it might be a Joomla view we're requesting...
		require (VMPATH_ADMIN.DS.'controllers'.DS.$_controller.'.php');
	} else {
		// try plugins

		JPluginHelper::importPlugin('vmextended');
		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger('onVmAdminController', array($_controller));

		if (empty($results)) {
			$app = JFactory::getApplication();
			$app->enqueueMessage('Fatal Error in maincontroller admin.virtuemart.php: Couldnt find file '.$_controller);
			$app->redirect('index.php?option=com_virtuemart');
		} else {
			foreach($results as $res){
				if($res){
					$exe = false;break;
				}
			}
		}
	}
} else {
	$app = JFactory::getApplication();
	$app->enqueueMessage('Fatal Error in maincontroller admin.virtuemart.php: No controller given '.$_controller);
	$app->redirect('index.php?option=com_virtuemart');
}

if($exe){
	vmJsApi::jQuery(0);

// Create the controller
	$_class = 'VirtueMartController'.ucfirst($_controller);
	if(!class_exists($_class)){
		vmError('Serious Error could not find controller '.$_class,'Serious error, could not find class');
		$app = JFactory::getApplication();
		$app->enqueueMessage('Fatal Error in maincontroller admin.virtuemart.php: No controller given '.$_controller);
		$app->redirect('index.php?option=com_virtuemart');
	}
	$controller = new $_class();

// Perform the Request task
	$controller->execute(vRequest::getCmd('task', $_controller));

	vmTime($_class.' Finished task '.$_controller,'vmStart');
	vmRam('End');
	vmRamPeak('Peak');
	$controller->redirect();

}


// pure php no closing tag