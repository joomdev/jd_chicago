<?php
/**
 *
 * Handle the orders view
 *
 * @package	VirtueMart
 * @subpackage Orders
 * @author Oscar van Eijk, Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2015 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 9075 2015-12-02 13:56:15Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(VMPATH_SITE.DS.'helpers'.DS.'vmview.php');


/**
 * Handle the orders view
 */
class VirtuemartViewOrders extends VmView {

	public function display($tpl = null)
	{

		$mainframe = JFactory::getApplication();
		$pathway = $mainframe->getPathway();
		$task = vRequest::getCmd('task', 'list');

		$layoutName = vRequest::getCmd('layout', 'list');

		$this->setLayout($layoutName);

		$_currentUser = JFactory::getUser();
		$document = JFactory::getDocument();

		if(!empty($tpl)){
			$format = $tpl;
		} else {
			$format = vRequest::getCmd('format', 'html');
		}
		$this->assignRef('format', $format);

		if($format=='pdf'){
			$document->setTitle( vmText::_('COM_VIRTUEMART_INVOICE') );

			//PDF needs more RAM than usual
			VmConfig::ensureMemoryLimit(96);

		} else {
		    if ($layoutName == 'details') {
			$document->setTitle( vmText::_('COM_VIRTUEMART_ACC_ORDER_INFO') );
			$pathway->additem(vmText::_('COM_VIRTUEMART_ACC_ORDER_INFO'));
		    } else {
			$document->setTitle( vmText::_('COM_VIRTUEMART_ORDERS_VIEW_DEFAULT_TITLE') );
			$pathway->additem(vmText::_('COM_VIRTUEMART_ORDERS_VIEW_DEFAULT_TITLE'));
		    }
		}

		$orderModel = VmModel::getModel('orders');

		if ($layoutName == 'details') {

			$this->order_list_link = JRoute::_('index.php?option=com_virtuemart&view=orders&layout=list', FALSE);

			$orderDetails = $orderModel ->getMyOrderDetails();

			if(!$orderDetails or empty($orderDetails['details'])){
				echo vmText::_('COM_VIRTUEMART_ORDER_NOTFOUND');
				return;
			}

			$userFieldsModel = VmModel::getModel('userfields');
			$_userFields = $userFieldsModel->getUserFields(
				 'account'
			, array('captcha' => true, 'delimiters' => true) // Ignore these types
			, array('delimiter_userinfo','user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type') // Skips
			);
			$orderbt = $orderDetails['details']['BT'];
			$orderst = (array_key_exists('ST', $orderDetails['details'])) ? $orderDetails['details']['ST'] : $orderbt;
			$this->userfields = $userFieldsModel->getUserFieldsFilled(
			$_userFields
			,$orderbt
			);
			$_userFields = $userFieldsModel->getUserFields(
				 'shipment'
			, array() // Default switches
			, array('delimiter_userinfo', 'username', 'email', 'password', 'password2', 'agreed', 'address_type') // Skips
			);

			$this->shipmentfields = $userFieldsModel->getUserFieldsFilled(
			$_userFields
			,$orderst
			);

			$this->shipment_name='';
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			$dispatcher = JDispatcher::getInstance();
			$returnValues = $dispatcher->trigger('plgVmOnShowOrderFEShipment',array(  $orderDetails['details']['BT']->virtuemart_order_id, $orderDetails['details']['BT']->virtuemart_shipmentmethod_id, &$this->shipment_name));

			$this->payment_name='';
			if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
			JPluginHelper::importPlugin('vmpayment');
			$dispatcher = JDispatcher::getInstance();
			$returnValues = $dispatcher->trigger('plgVmOnShowOrderFEPayment',array( $orderDetails['details']['BT']->virtuemart_order_id, $orderDetails['details']['BT']->virtuemart_paymentmethod_id,  &$this->payment_name));

			if($format=='pdf'){
				$invoiceNumberDate = array();
				$return = $orderModel->createInvoiceNumber($orderDetails['details']['BT'], $invoiceNumberDate );
				if(empty($invoiceNumberDate)){
					$invoiceNumberDate[0] = 'no invoice number accessible';
					$invoiceNumberDate[1] = 'no invoice date accessible';
				}
				$this->assignRef('invoiceNumber', $invoiceNumberDate[0]);
				$this->assignRef('invoiceDate', $invoiceNumberDate[1]);
			}

			$this->assignRef('orderdetails', $orderDetails);

			if($_currentUser->guest){
				$details_url = juri::root().'index.php?option=com_virtuemart&view=orders&layout=details&tmpl=component&order_pass=' . vRequest::getString('order_pass',false) .'&order_number='.vRequest::getString('order_number',false);
			} else {
				$details_url = juri::root().'index.php?option=com_virtuemart&view=orders&layout=details&tmpl=component&virtuemart_order_id=' . $this->orderdetails['details']['BT']->virtuemart_order_id;
			}
			$this->assignRef('details_url', $details_url);

			$tmpl = vRequest::getCmd('tmpl');
			$this->print = false;
			if($tmpl){
				$this->print = true;
			}
			$this->prepareVendor();


			$emailCurrencyId = $orderDetails['details']['BT']->user_currency_id;
			$exchangeRate = FALSE;
			if (!class_exists ('vmPSPlugin')) {
				require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			}
			JPluginHelper::importPlugin ('vmpayment');
			$dispatcher = JDispatcher::getInstance ();
			$dispatcher->trigger ('plgVmgetEmailCurrency', array($orderDetails['details']['BT']->virtuemart_paymentmethod_id, $orderDetails['details']['BT']->virtuemart_order_id, &$emailCurrencyId));
			if (!class_exists ('CurrencyDisplay')) {
				require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
			}

			$currency = CurrencyDisplay::getInstance ($emailCurrencyId, $orderDetails['details']['BT']->virtuemart_vendor_id);
			if ($emailCurrencyId) {
				$currency->exchangeRateShopper = $orderDetails['details']['BT']->user_currency_rate;
			}
			$this->assignRef ('currency', $currency);

		} else { // 'list' -. default
			$this->useSSL = VmConfig::get('useSSL',0);
			$this->useXHTML = false;
			if ($_currentUser->get('id') == 0) {
				// getOrdersList() returns all orders when no userID is set (admin function),
				// so explicetly define an empty array when not logged in.
				$this->orderlist = array();
			} else {
				$this->orderlist = $orderModel->getOrdersList($_currentUser->get('id'), TRUE);
				foreach ($this->orderlist as $k =>$order) {
					$vendorId = 1;
					$emailCurrencyId = $order->user_currency_id;
					$exchangeRate = FALSE;
					if (!class_exists ('vmPSPlugin')) {
						require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
					}
					JPluginHelper::importPlugin ('vmpayment');
					$dispatcher = JDispatcher::getInstance ();
					$dispatcher->trigger ('plgVmgetEmailCurrency', array($order->virtuemart_paymentmethod_id, $order->virtuemart_order_id, &$emailCurrencyId));
					if (!class_exists ('CurrencyDisplay')) {
						require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
					}
					$currency = CurrencyDisplay::getInstance ($emailCurrencyId, $vendorId);
					$this->assignRef ('currency', $currency);
					if ($emailCurrencyId) {
						$currency->exchangeRateShopper = $order->user_currency_rate;
					}
					$order->currency = $currency;
					$order->invoiceNumber = $orderModel->getInvoiceNumber($order->virtuemart_order_id);
					$this->orderlist[$k] = $order;
				}
			}
		}

		$orderStatusModel = VmModel::getModel('orderstatus');

		$_orderstatuses = $orderStatusModel->getOrderStatusList(true);
		$this->orderstatuses = array();
		foreach ($_orderstatuses as $_ordstat) {
			$this->orderstatuses[$_ordstat->order_status_code] = vmText::_($_ordstat->order_status_name);
		}

		$document = JFactory::getDocument();
		$document->setMetaData('robots','NOINDEX, NOFOLLOW, NOARCHIVE, NOSNIPPET');

		// this is no setting in BE to change the layout !
		//shopFunctionsF::setVmTemplate($this,0,0,$layoutName);

		parent::display($tpl);
	}

	// add vendor for cart
	function prepareVendor(){

		$vendorModel = VmModel::getModel('vendor');
		$vendor =  $vendorModel->getVendor();
		$this->assignRef('vendor', $vendor);
		$vendorModel->addImages($this->vendor,1);

	}

}
