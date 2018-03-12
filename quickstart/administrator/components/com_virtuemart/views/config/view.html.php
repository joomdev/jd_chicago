<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Config
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 9035 2015-11-03 10:37:57Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmViewAdmin'))require(VMPATH_ADMIN.DS.'helpers'.DS.'vmviewadmin.php');

/**
 * HTML View class for the configuration maintenance
 *
 * @package		VirtueMart
 * @subpackage 	Config
 * @author 		RickG
 */
class VirtuemartViewConfig extends VmViewAdmin {

	function display($tpl = null) {

		if (!class_exists('VmImage'))
			require(VMPATH_ADMIN . DS . 'helpers' . DS . 'image.php');

		if (!class_exists('VmHTML'))
			require(VMPATH_ADMIN . DS . 'helpers' . DS . 'html.php');

		$model = VmModel::getModel();
		$usermodel = VmModel::getModel('user');

		JToolBarHelper::title( vmText::_('COM_VIRTUEMART_CONFIG') , 'head vm_config_48');

		$this->addStandardEditViewCommands();

		$this->config = VmConfig::loadConfig();
		if(!empty($this->config->_params)){
			unset ($this->config->_params['pdf_invoice']); // parameter remove and replaced by inv_os
		}

		$this->userparams = JComponentHelper::getParams('com_users');

		$this->jTemplateList = ShopFunctions::renderTemplateList(vmText::_('COM_VIRTUEMART_ADMIN_CFG_JOOMLA_TEMPLATE_DEFAULT'));

		$this->vmLayoutList = $model->getLayoutList('virtuemart');

		$this->cartLayoutList = $model->getLayoutList('cart',array('padded.php','perror.php'));
		$this->categoryLayoutList = $model->getLayoutList('category');

		$this->productLayoutList = $model->getLayoutList('productdetails');

		$this->productsFieldList  = $model->getFieldList('products');

		$this->noimagelist = $model->getNoImageList();

		$this->orderStatusModel= VmModel::getModel('orderstatus');

		$this->os_Options = $this->osWoP_Options = $this->osDel_Options = $this->orderStatusModel->getOrderStatusNames();
		$emptyOption = JHtml::_ ('select.option', -1, vmText::_ ('COM_VIRTUEMART_NONE'), 'order_status_code', 'order_status_name');

		array_unshift ($this->os_Options, $emptyOption);

		unset($this->osWoP_Options['P']);
		array_unshift ($this->osWoP_Options, $emptyOption);

		$deldate_inv = JHtml::_ ('select.option', 'm', vmText::_ ('COM_VIRTUEMART_DELDATE_INV'), 'order_status_code', 'order_status_name');
		unset($this->osDel_Options['P']);
		array_unshift ($this->osDel_Options, $deldate_inv);
		array_unshift ($this->osDel_Options, $emptyOption);

		//vmdebug('my $this->os_Options',$this->osWoP_Options);

		$this->currConverterList = $model->getCurrencyConverterList();

		$this->activeLanguages = $model->getActiveLanguages( VmConfig::get('active_languages') );

		$this->orderByFieldsProduct = $model->getProductFilterFields('browse_orderby_fields');

		VmModel::getModel('category');

		foreach (VirtueMartModelCategory::$_validOrderingFields as $key => $field ) {
			if($field=='c.category_shared') continue;
			$fieldWithoutPrefix = $field;
			$dotps = strrpos($fieldWithoutPrefix, '.');
			if($dotps!==false){
				$prefix = substr($field, 0,$dotps+1);
				$fieldWithoutPrefix = substr($field, $dotps+1);
			}

			$text = vmText::_('COM_VIRTUEMART_'.strtoupper(str_replace(array(',',' '),array('_',''),$fieldWithoutPrefix))) ;
			$orderByFieldsCat[] =  JHtml::_('select.option', $field, $text) ;
		}

		$this->orderByFieldsCat = $orderByFieldsCat;

		$this->searchFields = $model->getProductFilterFields( 'browse_search_fields');

		$this->aclGroups = $usermodel->getAclGroupIndentedTree();

		if(!class_exists('VmTemplate')) require(VMPATH_SITE.DS.'helpers'.DS.'vmtemplate.php');
		$this->vmtemplate = VmTemplate::loadVmTemplateStyle();
		$this->imagePath = shopFunctions::getAvailabilityIconUrl($this->vmtemplate);

		$this->listShipment = $this -> listIt('shipment');
		$this->listPayment = $this -> listIt('payment');

		shopFunctions::checkSafePath();
		$this -> checkTCPDFinstalled();
		$this -> checkVmUserVendor();
		//$this -> checkClientIP();
		parent::display($tpl);
	}

	private function listIt($ps){
		$db = JFactory::getDBO();
		$q = 'SELECT virtuemart_'.$ps.'method_id,'.$ps.'_name
FROM #__virtuemart_'.$ps.'methods
INNER JOIN #__virtuemart_'.$ps.'methods_'.VmConfig::$vmlang.' USING (virtuemart_'.$ps.'method_id)
WHERE published="1"';
		$db->setQuery($q);

		try {
			$options = $db->loadAssocList();
		} catch (Exception $e){
			return array();
		}
		if(empty($options)) $options = array();
		$emptyOption = JHtml::_('select.option', '0', vmText::_('COM_VIRTUEMART_NOPREF'),'virtuemart_'.$ps.'method_id',$ps.'_name');
		array_unshift($options,$emptyOption);
		$emptyOption = JHtml::_('select.option', '-1', vmText::_('COM_VIRTUEMART_NONE'),'virtuemart_'.$ps.'method_id',$ps.'_name');
		array_unshift($options,$emptyOption);
		return $options;
	}

	private function checkVmUserVendor(){

		$db = JFactory::getDBO();
		$multix = Vmconfig::get('multix','none');

		$q = 'select * from #__virtuemart_vmusers where user_is_vendor = 1';// and virtuemart_vendor_id '.$vendorWhere.' limit 1';
		$db->setQuery($q);
		$r = $db->loadAssocList();

		if (empty($r)){
			vmWarn('Your Virtuemart installation contains an error: No user as marked as vendor. Please fix this in your phpMyAdmin and set #__virtuemart_vmusers.user_is_vendor = 1 and #__virtuemart_vmusers.virtuemart_vendor_id = 1 to one of your administrator users. Please update all users to be associated with virtuemart_vendor_id 1.');
		} else {
			if($multix=='none' and count($r)!=1){
				vmWarn('You are using single vendor mode, but it seems more than one user is set as vendor');
			}
			foreach($r as $entry){
				if(empty($entry['virtuemart_vendor_id'])){
					vmWarn('The user with virtuemart_user_id = '.$entry['virtuemart_user_id'].' is set as vendor, but has no referencing vendorId.');
				}
			}
		}
	}

	private function checkTCPDFinstalled(){

		if(!file_exists(VMPATH_LIBS.DS.'tcpdf'.DS.'tcpdf.php')){
			vmWarn('COM_VIRTUEMART_TCPDF_NINSTALLED');
		}
	}

	private function checkClientIP(){
		$revproxvar = VmConfig::get('revproxvar','');
		if(!empty($revproxvar)) vmdebug('My server variable ',$_SERVER);
	}
}
// pure php no closing tag
