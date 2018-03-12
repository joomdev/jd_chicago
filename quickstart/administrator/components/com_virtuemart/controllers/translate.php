<?php
/**
*
* Translate controller
*
* @package	VirtueMart
* @subpackage Translate
* @author Patrick Kohl
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL 2, see COPYRIGHT.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: translate.php
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


if(!class_exists('VmController'))require(VMPATH_ADMIN.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Translate Controller
 *
 * @package    VirtueMart
 * @subpackage Translate
 * @author Patrick Kohl
 */
class VirtuemartControllerTranslate extends VmController {

	var $check 	= null;
	var $fields = null;


	function __construct() {
		parent::__construct();

	}


	/**
	 * Paste the table  in json format
	 *
	 */
	public function paste() {

		// TODO Test user ?
		$json= array();
		$json['fields'] = 'error' ;
		$json['msg'] = 'Invalid Token';
		$json['structure'] = 'empty' ;
		if (!vRequest::vmCheckToken(-1)) {
			echo json_encode($json) ;
			jexit(  );
		}

		$lang = vRequest::getvar('lg');
		$langs = VmConfig::get('active_languages',array()) ;
		$language=JFactory::getLanguage();

		if (!in_array($lang, $langs) ) {
			$json['msg'] = 'Invalid language ! '.$lang;
			$json['langs'] = $langs ;
			echo json_encode($json) ;
			jexit( );
		}
		$lang = strtolower( $lang);
		// Remove tag if defaut or
		// if ($language->getDefault() == $lang ) $dblang ='';

		$dblang= strtr($lang,'-','_');
		VmConfig::$vmlang = $dblang;
		$id = vRequest::getInt('id',0);

		$viewKey = vRequest::getCmd('editView');
		// TODO temp trick for vendor
		if ($viewKey == 'vendor') $id = 1 ;

		$tables = array ('category' =>'categories','product' =>'products','manufacturer' =>'manufacturers','manufacturercategories' =>'manufacturercategories','vendor' =>'vendors', 'paymentmethod' =>'paymentmethods', 'shipmentmethod' =>'shipmentmethods');

		if ( !array_key_exists($viewKey, $tables) ) {
			$json['msg'] ="Invalid view ". $viewKey;
			echo json_encode($json);
			jExit();
		}
		$tableName = '#__virtuemart_'.$tables[$viewKey].'_'.$dblang;

		$m = VmModel::getModel('coupon');
		$table = $m->getTable($tables[$viewKey]);

		//Todo create method to load lang fields only
		$table->load($id);
		$vs = $table->loadFieldValues();
		$lf = $table->getTranslatableFields();

		$json['fields'] = array();
		foreach($lf as $v){
			if(isset($vs[$v])){
				$json['fields'][$v] = $vs[$v];
			}
		}

		//if ($json['fields'] = $db->loadAssoc()) {
		if ($table->getLoaded()) {
			$json['structure'] = 'filled' ;
			$json['msg'] = vmText::_('COM_VIRTUEMART_SELECTED_LANG').':'.$lang;

		} else {
			$db =JFactory::getDBO();

			$json['structure'] = 'empty' ;
			$db->setQuery('SHOW COLUMNS FROM '.$tableName);
			$tableDescribe = $db->loadAssocList();
			array_shift($tableDescribe);
			$fields=array();
			foreach ($tableDescribe as $key =>$val) $fields[$val['Field']] = $val['Field'] ;
			$json['fields'] = $fields;
			$json['msg'] = vmText::sprintf('COM_VIRTUEMART_LANG_IS_EMPTY',$lang ,vmText::_('COM_VIRTUEMART_'.strtoupper( $viewKey)) ) ;
		}
		echo vmJsApi::safe_json_encode($json);
		jExit();

	}


}

//pure php no tag
