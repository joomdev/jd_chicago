<?php
/**
 *
 * Description
 *
 * @package	VirtueMart
 * @subpackage
 * @author
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmViewAdmin'))require(VMPATH_ADMIN.DS.'helpers'.DS.'vmviewadmin.php');

/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
class VirtuemartViewOrders extends VmViewAdmin {

	function display($tpl = null) {


		//Load helpers
		if (!class_exists('CurrencyDisplay'))
			require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');

		if (!class_exists('VmHTML'))
			require(VMPATH_ADMIN . DS . 'helpers' . DS . 'html.php');

		if(!class_exists('vmPSPlugin')) require(VMPATH_PLUGINLIBS.DS.'vmpsplugin.php');

		$app = JFactory::getApplication();
		$orderStatusModel=VmModel::getModel('orderstatus');
		$orderStates = $orderStatusModel->getOrderStatusList(true);

		$this->SetViewTitle( 'ORDER');

		$orderModel = VmModel::getModel();

		$this->lists['search'] = $orderModel->get('search', '');

		$curTask = vRequest::getCmd('task');
		if ($curTask == 'edit') {
			VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
			VmConfig::loadJLang('com_virtuemart_orders', true);

			//For getOrderStatusName
			if (!class_exists('ShopFunctions'))	require(VMPATH_ADMIN . DS . 'helpers' . DS . 'shopfunctions.php');

			// Load addl models
			$userFieldsModel = VmModel::getModel('userfields');

			// Get the data
			$virtuemart_order_id = vRequest::getInt('virtuemart_order_id');
			$order = $orderModel->getOrder($virtuemart_order_id);

			if(empty($order['details'])){
				$app->redirect('index.php?option=com_virtuemart&view=orders',vmText::_('COM_VIRTUEMART_ORDER_NOTFOUND'));;
			}

			$_orderID = $order['details']['BT']->virtuemart_order_id;
			$orderbt = $order['details']['BT'];
			$orderst = (array_key_exists('ST', $order['details'])) ? $order['details']['ST'] : $orderbt;
			$orderbt ->invoiceNumber = $orderModel->getInvoiceNumber($orderbt->virtuemart_order_id);

			$currency = CurrencyDisplay::getInstance(0,$order['details']['BT']->virtuemart_vendor_id);

			$this->assignRef('currency', $currency);

			$_userFields = $userFieldsModel->getUserFields(
					 'account'
					, array('captcha' => true, 'delimiters' => true) // Ignore these types
					, array('delimiter_userinfo','user_is_vendor' ,'username','name','password', 'password2', 'agreed', 'address_type') // Skips
			);
			$userFieldsCart = $userFieldsModel->getUserFields(
				'cart'
				, array('captcha' => true, 'delimiters' => true) // Ignore these types
				, array('delimiter_userinfo','user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type') // Skips
			);
			$_userFields = array_merge($userFieldsCart,$_userFields);

			//Fallback for customer_note
			if(empty($orderbt->customer_note) and !empty($orderbt->oc_note)){
				$orderbt->customer_note = $orderbt->oc_note;
			}

			$userfields = $userFieldsModel->getUserFieldsFilled(
					 $_userFields
					,$orderbt
					,'BT_'
			);

			$_userFields = $userFieldsModel->getUserFields(
					 'shipment'
					, array() // Default switches
					, array('delimiter_userinfo', 'username', 'email', 'password', 'password2', 'agreed', 'address_type') // Skips
			);

			$shipmentfields = $userFieldsModel->getUserFieldsFilled(
					 $_userFields
					,$orderst
					,'ST_'
			);

			// Create an array to allow orderlinestatuses to be translated
			// We'll probably want to put this somewhere in ShopFunctions...
			$_orderStatusList = array();
			foreach ($orderStates as $orderState) {
				//$_orderStatusList[$orderState->virtuemart_orderstate_id] = $orderState->order_status_name;
				//When I use update, I have to use this?
				$_orderStatusList[$orderState->order_status_code] = vmText::_($orderState->order_status_name);
			}

			$_itemStatusUpdateFields = array();
			$_itemAttributesUpdateFields = array();
			foreach($order['items'] as $_item) {
				$_itemStatusUpdateFields[$_item->virtuemart_order_item_id] = JHtml::_('select.genericlist', $orderStates, "item_id[".$_item->virtuemart_order_item_id."][order_status]", 'class="selectItemStatusCode"', 'order_status_code', 'order_status_name', $_item->order_status, 'order_item_status'.$_item->virtuemart_order_item_id,true);

			}

			if(!isset($_orderStatusList[$orderbt->order_status])){
				if(empty($orderbt->order_status)){
					$orderbt->order_status = 'unknown';
				}
				$_orderStatusList[$orderbt->order_status] = vmText::_('COM_VIRTUEMART_UNKNOWN_ORDER_STATUS');
			}



			/* Assign the data */
			$this->assignRef('orderdetails', $order);
			$this->assignRef('orderID', $_orderID);
			$this->assignRef('userfields', $userfields);
			$this->assignRef('shipmentfields', $shipmentfields);
			$this->assignRef('orderstatuslist', $_orderStatusList);
			$this->assignRef('itemstatusupdatefields', $_itemStatusUpdateFields);
			$this->assignRef('itemattributesupdatefields', $_itemAttributesUpdateFields);
			$this->assignRef('orderbt', $orderbt);
			$this->assignRef('orderst', $orderst);
			$this->assignRef('virtuemart_shipmentmethod_id', $orderbt->virtuemart_shipmentmethod_id);

			/* Data for the Edit Status form popup */
			$_currentOrderStat = $order['details']['BT']->order_status;
			// used to update all item status in one time
			$_orderStatusSelect = JHtml::_('select.genericlist', $orderStates, 'order_status', 'style="width:100px;"', 'order_status_code', 'order_status_name', $_currentOrderStat, 'order_items_status',true);
			$this->assignRef('orderStatSelect', $_orderStatusSelect);
			$this->assignRef('currentOrderStat', $_currentOrderStat);

			/* Toolbar */
			if (JVM_VERSION < 3) { $backward="back"; $list='back';} else {$backward='backward';$list='list';}
			JToolBarHelper::custom( 'prevItem', $backward,'','COM_VIRTUEMART_ITEM_PREVIOUS',false);
			JToolBarHelper::custom( 'nextItem', 'forward','','COM_VIRTUEMART_ITEM_NEXT',false);
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'cancel', $list,'','COM_VIRTUEMART_ORDER_LIST_LBL',false,false);

		}
		else if ($curTask == 'editOrderItem') {
			if(!class_exists('calculationHelper')) require(VMPATH_ADMIN.DS.'helpers'.DS.'calculationh.php');

			$this->assignRef('orderstatuses', $orderStates);

			$model = VmModel::getModel();
			$orderId = vRequest::getString('orderId', '');
			$orderLineItem = vRequest::getVar('orderLineId', '');
			$this->assignRef('virtuemart_order_id', $orderId);
			$this->assignRef('virtuemart_order_item_id', $orderLineItem);

			$orderItem = $model->getOrderLineDetails($orderId, $orderLineItem);
			$this->assignRef('orderitem', $orderItem);
		}
		else {
			$this->setLayout('orders');

			$model = VmModel::getModel();
			$this->addStandardDefaultViewLists($model,'created_on');
			$orderStatusModel =VmModel::getModel('orderstatus');
			$orderstates = vRequest::getCmd('order_status_code','');
			$this->lists['state_list'] = $orderStatusModel->renderOSList($orderstates,'order_status_code',FALSE,' onchange="this.form.submit();" ');
			$this->lists['bulk_state_list'] = $orderStatusModel->renderOSList($orderstates,'order_status_code_bulk',FALSE,'id="order_status_code_bulk" onchange="set2status();" ');
			$orderslist = $model->getOrdersList();

			$this->assignRef('orderstatuses', $orderStates);
			$this->lists['vendors']='';
			if($this->showVendors()){
				$this->lists['vendors'] = Shopfunctions::renderVendorList(VmAccess::getVendorId());
			}

			if(!class_exists('CurrencyDisplay'))require(VMPATH_ADMIN.DS.'helpers'.DS.'currencydisplay.php');

			/* Apply currency This must be done per order since it's vendor specific */
			$_currencies = array(); // Save the currency data during this loop for performance reasons

			if ($orderslist) {

			    foreach ($orderslist as $virtuemart_order_id => $order) {

				    if(!empty($order->order_currency)){
					    $currency = $order->order_currency;
				    } else if($order->virtuemart_vendor_id){
					    if(!class_exists('VirtueMartModelVendor')) require(VMPATH_ADMIN.DS.'models'.DS.'vendor.php');
					    $currObj = VirtueMartModelVendor::getVendorCurrency($order->virtuemart_vendor_id);
				        $currency = $currObj->virtuemart_currency_id;
					}
				    //This is really interesting for multi-X, but I avoid to support it now already, lets stay it in the code
				    if (!array_key_exists('curr'.$currency, $_currencies)) {

					    $_currencies['curr'.$currency] = CurrencyDisplay::getInstance($currency,$order->virtuemart_vendor_id);
				    }

				    $order->order_total = $_currencies['curr'.$currency]->priceDisplay($order->order_total);
				    $order->invoiceNumber = $model->getInvoiceNumber($order->virtuemart_order_id);
			    }

			}

			//update order items button
			/*$q = 'SELECT * FROM #__virtuemart_order_items WHERE `product_discountedPriceWithoutTax` IS NULL ';
			$db = JFactory::getDBO();
			$db->setQuery($q);
			//$res = $db->loadRow();
			if(true) {
				JToolBarHelper::custom('updateCustomsOrderItems', 'new', 'new', vmText::_('COM_VIRTUEMART_REPORT_UPDATEORDERITEMS'),false);
				vmError('COM_VIRTUEMART_UPDATEORDERITEMS_WARN');
			}*/
			/*
			 * UpdateStatus removed from the toolbar; don't understand how this was intented to work but
			 * the order ID's aren't properly passed. Might be readded later; the controller needs to handle
			 * the arguments.
			 */

			/* Toolbar */
			//JToolBarHelper::customX( 'CreateOrderHead', 'new','new','New',false);

			JToolBarHelper::save('updatestatus', vmText::_('COM_VIRTUEMART_UPDATE_STATUS'));

			if (vmAccess::manager('orders.delete')) {
				JToolBarHelper::spacer('80');
				JToolBarHelper::deleteList();
			}

			/* Assign the data */
			$this->assignRef('orderslist', $orderslist);

			$this->pagination = $model->getPagination();

		}
		if($app->isSite()) {
			$bar = JToolBar::getInstance( 'toolbar' );
			$bar->appendButton( 'Link', 'back', 'COM_VIRTUEMART_LEAVE', 'index.php?option=com_virtuemart&manage=0' );
		}

		shopFunctions::checkSafePath();

		parent::display($tpl);
	}

	function createPrintLinks($order,&$print_link,&$deliverynote_link,&$invoice_link){

		/* Print view URL */
		$print_url = juri::root () . 'index.php?option=com_virtuemart&view=invoice&layout=invoice&tmpl=component&virtuemart_order_id=' . $order->virtuemart_order_id . '&order_number=' . $order->order_number . '&order_pass=' . $order->order_pass;
		$print_link = "<a href=\"javascript:void window.open('$print_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"  >";
		$print_link .= '<span class="hasTip print_32" title="' . vmText::_ ('COM_VIRTUEMART_PRINT') . '">&nbsp;</span></a>';
		$invoice_link = '';
		$deliverynote_link = '';
		$pdfDummi= '&d='.rand(0,100);
		if (!$order->invoiceNumber) {
			$invoice_url = juri::root () . 'index.php?option=com_virtuemart&view=invoice&layout=invoice&format=pdf&tmpl=component&virtuemart_order_id=' . $order->virtuemart_order_id . '&order_number=' . $order->order_number . '&order_pass=' . $order->order_pass . '&create_invoice='.$order->order_create_invoice_pass.$pdfDummi;
			$invoice_link .= "<a href=\"$invoice_url\"  >".'<span class="hasTip invoicenew_32" title="' . vmText::_ ('COM_VIRTUEMART_INVOICE_CREATE') . '"></span></a>';
		} elseif (!shopFunctions::InvoiceNumberReserved ($order->invoiceNumber)) {
			$invoice_url = juri::root () . 'index.php?option=com_virtuemart&view=invoice&layout=invoice&format=pdf&tmpl=component&virtuemart_order_id=' . $order->virtuemart_order_id . '&order_number=' . $order->order_number . '&order_pass=' . $order->order_pass.$pdfDummi;
			$invoice_link = "<a href=\"$invoice_url\"  >" . '<span class="hasTip invoice_32" title="' . vmText::_ ('COM_VIRTUEMART_INVOICE') . '"></span></a>';
		}

		if (!$order->invoiceNumber) {
			$deliverynote_url = juri::root () . 'index.php?option=com_virtuemart&view=invoice&layout=deliverynote&format=pdf&tmpl=component&virtuemart_order_id=' . $order->virtuemart_order_id . '&order_number=' . $order->order_number . '&order_pass=' . $order->order_pass . '&create_invoice='.$order->order_create_invoice_pass.$pdfDummi;
			$deliverynote_link = "<a href=\"$deliverynote_url\"  >" . '<span class="hasTip deliverynotenew_32" title="' . vmText::_ ('COM_VIRTUEMART_DELIVERYNOTE_CREATE') . '"></span></a>';
		} elseif (!shopFunctions::InvoiceNumberReserved ($order->invoiceNumber)) {
			$deliverynote_url = juri::root () . 'index.php?option=com_virtuemart&view=invoice&layout=deliverynote&format=pdf&tmpl=component&virtuemart_order_id=' . $order->virtuemart_order_id . '&order_number=' . $order->order_number . '&order_pass=' . $order->order_pass.$pdfDummi;
			$deliverynote_link = "<a href=\"$deliverynote_url\"  >" . '<span class="hasTip deliverynote_32" title="' . vmText::_ ('COM_VIRTUEMART_DELIVERYNOTE') . '"></span></a>';
		}

	}
}

