<?php
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
if (file_exists(dirname(__FILE__) . '/defines.php')) {
	include_once dirname(__FILE__) . '/defines.php';
}
if (!defined('_JDEFINES')) {
	define('JPATH_BASE', dirname(__FILE__).DS.'..'.DS.'..'.DS.'..' );
	require_once JPATH_BASE.'/includes/defines.php';
}//echo JPATH_BASE;
require_once JPATH_BASE.'/includes/framework.php';
 jimport( 'joomla.html.editor' );
$addItem = JRequest::getVar('addItem',0);

if($addItem)
{
	$item = JRequest::getVar('item');
	$db = JFactory::getDbo();
	$obj = new stdClass();
	$obj->id = null;
	$obj->params = json_encode($item);
	$db->insertObject('#__bds',$obj);
	$return = array();
	$return['id'] = $db->insertid();
	echo json_encode($return);
}
$updateItem = JRequest::getVar('updateItem',0);
if($updateItem)
{
	//$item = JRequest::getVar('item');
	$post = JRequest::get('item["description"]', '', 'item', 'string', JREQUEST_ALLOWRAW);
    $itemid = JRequest::getVar('itemid');
	$db = JFactory::getDbo();
	$obj = new stdClass();
	$obj->id = $itemid;
	$obj->params = json_encode($post['item']);
	$db->updateObject('#__bds',$obj,'id');
	$return = array();
	$return['id'] = $itemid;
	echo json_encode($return);
}
$deleteItem = JRequest::getVar('deleteItem',0);

if($deleteItem)
{
	$id = JRequest::getVar('id');
	$db = JFactory::getDbo();
	$query = "DELETE FROM `#__bds` WHERE `id`='$id'";
	$db->setQuery($query);
	$db->query();
	$return = array();
	$return['id'] = $id;
	echo json_encode($return);
}
?>