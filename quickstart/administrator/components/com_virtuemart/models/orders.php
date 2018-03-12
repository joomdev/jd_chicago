<?php
/**
 *
 * Description
 *
 * @package	VirtueMart
 * @subpackage
 * @author Oscar van Eijk
 * @author Max Milbers
 * @author Patrick Kohl
 * @author Valerie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: orders.php 9193 2016-03-11 10:17:04Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel')) require(VMPATH_ADMIN.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model for VirtueMart Orders
 * WHY $this->db is never used in the model ?
 * @package VirtueMart
 */
class VirtueMartModelOrders extends VmModel {

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('orders');
		$this->addvalidOrderingFieldName(array('order_name','order_email','payment_method','virtuemart_order_id' ) );
		$this->populateState();
	}

	function populateState () {
		$type = 'search';
		$k= 'com_virtuemart.orders.'.$type;

		$ts = vRequest::getString($type, false);
		$app = JFactory::getApplication();

		if($ts===false){
			$this->{$type} = $app->getUserState($k, '');
		} else {
			$app->setUserState( $k,$ts);
			$this->{$type} = $ts;
		}
		$this->__state_set = true;
	}

	/**
	 * This function gets the orderId, for anonymous users
	 * @author Max Milbers
	 */
	public function getOrderIdByOrderPass($orderNumber,$orderPass){

		$db = JFactory::getDBO();
		$q = 'SELECT `virtuemart_order_id` FROM `#__virtuemart_orders` WHERE `order_pass`="'.$db->escape($orderPass).'" AND `order_number`="'.$db->escape($orderNumber).'"';
		$db->setQuery($q);
		$orderId = $db->loadResult();
		if(empty($orderId)) vmdebug('getOrderIdByOrderPass no Order found $orderNumber = '.$orderNumber.' $orderPass = '.$orderPass.' $q = '.$q);
		return $orderId;

	}
	/**
	 * This function gets the orderId, for payment response
	 * author Valerie Isaksen
	 */
	public static function getOrderIdByOrderNumber($orderNumber){

		$db = JFactory::getDBO();
		$q = 'SELECT `virtuemart_order_id` FROM `#__virtuemart_orders` WHERE `order_number`="'.$db->escape($orderNumber).'"';
		$db->setQuery($q);
		$orderId = $db->loadResult();
		return $orderId;

	}
	/**
	 * This function seems completly broken, JRequests are not allowed in the model, sql not escaped
	 * This function gets the secured order Number, to send with paiement
	 *
	 */
	public function getOrderNumber($virtuemart_order_id){

		$db = JFactory::getDBO();
		$q = 'SELECT `order_number` FROM `#__virtuemart_orders` WHERE virtuemart_order_id="'.(int)$virtuemart_order_id.'"  ';
		$db->setQuery($q);
		$OrderNumber = $db->loadResult();
		return $OrderNumber;

	}

	/**
	 * Was also broken, actually used?
	 *
	 * get next/previous order id
	 *
	 */

	public function getOrderId($order_id, $direction ='DESC') {

		if ($direction == 'ASC') {
			$arrow ='>';
		} else {
			$arrow ='<';
		}

		$db = JFactory::getDBO();
		$q = 'SELECT `virtuemart_order_id` FROM `#__virtuemart_orders` WHERE `virtuemart_order_id`'.$arrow.(int)$order_id;
		$q.= ' ORDER BY `virtuemart_order_id` '.$direction ;
		$db->setQuery($q);

		if ($oderId = $db->loadResult()) {
			return $oderId ;
		}
		return 0 ;
	}

    /**
     * This is a proxy function to return an order safely, we may set the getOrder function to private
     * Maybe the right place would be the controller, cause there are JRequests in it. But for a fast solution,
     * still better than to have it 3-4 times in the view.html.php of the views.
     * @author Max Milbers
     *
     * @return array
     */
	public function getMyOrderDetails($orderID = 0, $orderNumber = false, $orderPass = false, $userlang=false){

		$_currentUser = JFactory::getUser();
		$cuid = $_currentUser->get('id');

		$orderDetails = false;

		// If the user is not logged in, we will check the order number and order pass
		if(empty($cuid)){
			$sess = JFactory::getSession();
			$orderNumber = vRequest::getString('order_number',$orderNumber);
			if(empty($orderNumber)) return false;

			$tries = $sess->get('getOrderDetails.'.$orderNumber,0);

			// If the user is not logged in, we will check the order number and order pass
			if ($orderPass = vRequest::getString('order_pass',$orderPass)){
				if($tries>5){
					vmDebug ('Too many tries, Invalid order_number/password '.vmText::_('COM_VIRTUEMART_RESTRICTED_ACCESS'));
					vmError ('Too many tries, Invalid order_number/password guest '.$orderNumber.' '.$orderPass , 'COM_VIRTUEMART_RESTRICTED_ACCESS');
					return false;
				}
				$orderId = $this->getOrderIdByOrderPass($orderNumber,$orderPass);
				if(empty($orderId)){
					echo vmText::_('COM_VIRTUEMART_RESTRICTED_ACCESS');
					vmdebug('getMyOrderDetails COM_VIRTUEMART_RESTRICTED_ACCESS',$orderNumber, $orderPass, $tries);
					vmError(vmText::_('COM_VIRTUEMART_RESTRICTED_ACCESS').' by guest '.$orderNumber.' '.$orderPass, 'COM_VIRTUEMART_RESTRICTED_ACCESS');
					$tries++;
					$sess->set('getOrderDetails.'.$orderNumber,$tries);
					return false;
				}
				$orderDetails = $this->getOrder($orderId,$userlang);
			}
		}
		else {
			// If the user is logged in, we will check if the order belongs to him
			$virtuemart_order_id = vRequest::getInt('virtuemart_order_id',$orderID) ;
			$orderNumber = vRequest::getString('order_number');
			if (!$virtuemart_order_id) {
				$virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($orderNumber);
			}
			$orderDetails = $this->getOrder($virtuemart_order_id,$userlang);

			if(!vmAccess::manager('orders')){
				if(empty($orderDetails['details']['BT']->virtuemart_user_id)) {
					$sess = JFactory::getSession();
					$orderNumber = vRequest::getString('order_number',$orderNumber);
					$tries = $sess->get('getOrderDetails.'.$orderNumber,0);
					if ($orderPass = vRequest::getString('order_pass',$orderPass)){
						if($tries>5){
							vmError ('Too many tries, Invalid order_number/password userid '.$cuid.' '.$virtuemart_order_id, 'COM_VIRTUEMART_RESTRICTED_ACCESS');
							return false;
						}
						if($orderDetails['details']['order_pass'] != $orderPass){
							$tries++;
							$sess->set('getOrderDetails.'.$orderNumber,$tries);
							vmError(vmText::_('COM_VIRTUEMART_RESTRICTED_ACCESS').' by userid '.$cuid.' '.$virtuemart_order_id, 'COM_VIRTUEMART_RESTRICTED_ACCESS');
							return false;
						} else {
							//We could update the invoice with the userid to connect guest orders to the user
						}
					}
				} else if($orderDetails['details']['BT']->virtuemart_user_id != $cuid) {
					vmError(vmText::_('COM_VIRTUEMART_RESTRICTED_ACCESS').' by userid '.$cuid.' '.$virtuemart_order_id, 'COM_VIRTUEMART_RESTRICTED_ACCESS');
					return false;
				}
			}
		}
		return $orderDetails;
	}

	/**
	 * Load a single order, Attention, this function is not protected! Do the right manangment before, to be certain
     * we suggest to use getMyOrderDetails
	 */
	public function getOrder($virtuemart_order_id, $userlang=false){

		//sanitize id
		$virtuemart_order_id = (int)$virtuemart_order_id;
		$db = JFactory::getDBO();
		$order = array();

		// Get the order details
		$q = "SELECT  o.*,u.*,
				s.order_status_name
			FROM #__virtuemart_orders o
			LEFT JOIN #__virtuemart_orderstates s
			ON s.order_status_code = o.order_status
			LEFT JOIN #__virtuemart_order_userinfos u
			ON u.virtuemart_order_id = o.virtuemart_order_id
			WHERE o.virtuemart_order_id=".$virtuemart_order_id;
		$db->setQuery($q);
		$order['details'] = $db->loadObjectList('address_type');
		if($order['details'] and isset($order['details']['BT'])){
			$concat = array();
			if(!empty($order['details']['BT']->company))  $concat[]= $order['details']['BT']->company;
			if(!empty($order['details']['BT']->first_name))  $concat[]= $order['details']['BT']->first_name;
			if(!empty($order['details']['BT']->middle_name))  $concat[]= $order['details']['BT']->middle_name;
			if(!empty($order['details']['BT']->last_name))  $concat[]= $order['details']['BT']->last_name;
			$order['details']['BT']->order_name = '';
			foreach($concat as $c){
				$order['details']['BT']->order_name .= trim($c).' ';
			}
			$order['details']['BT']->order_name = trim(htmlspecialchars(strip_tags(htmlspecialchars_decode($order['details']['BT']->order_name))));
		}

		if($userlang){
			if(!empty($order['details']['BT']->order_language)) {
				$olang = $order['details']['BT']->order_language;
				//VmConfig::setdbLanguageTag();//Todo set language tag to get products in the correct language.
				VmConfig::loadJLang('com_virtuemart',true, $olang);
				VmConfig::loadJLang('com_virtuemart_shoppers',true, $olang);
				VmConfig::loadJLang('com_virtuemart_orders',true, $olang);
			}
		}

		// Get the order history
		$q = "SELECT *
			FROM #__virtuemart_order_histories
			WHERE virtuemart_order_id=".$virtuemart_order_id."
			ORDER BY virtuemart_order_history_id ASC";
		$db->setQuery($q);
		$order['history'] = $db->loadObjectList();

		// Get the order items
$q = 'SELECT virtuemart_order_item_id, product_quantity, order_item_name,
    order_item_sku, i.virtuemart_product_id, product_item_price,
    product_final_price, product_basePriceWithTax, product_discountedPriceWithoutTax, product_priceWithoutTax, product_subtotal_with_tax, product_subtotal_discount, product_tax, product_attribute, order_status, p.product_available_date, p.product_availability,
    intnotes, virtuemart_category_id, p.product_mpn
   FROM (#__virtuemart_order_items i
   		LEFT JOIN #__virtuemart_products p
   		ON p.virtuemart_product_id = i.virtuemart_product_id)
   LEFT JOIN #__virtuemart_product_categories c
   ON p.virtuemart_product_id = c.virtuemart_product_id
   WHERE `virtuemart_order_id`="'.$virtuemart_order_id.'" group by `virtuemart_order_item_id`';
//group by `virtuemart_order_id`'; Why ever we added this, it makes trouble, only one order item is shown then.
// without group by we get the product 3 times, when it is in 3 categories and similar, so we need a group by
//lets try group by `virtuemart_order_item_id`
		$db->setQuery($q);
		$order['items'] = $db->loadObjectList();

		$customfieldModel = VmModel::getModel('customfields');
		$pModel = VmModel::getModel('product');
		foreach($order['items'] as &$item){

			$ids = array();

			$product = $pModel->getProduct($item->virtuemart_product_id);
			$pvar = get_object_vars($product);
			foreach ( $pvar as $k => $v) {
				if (!isset($item->$k) and '_' != substr($k, 0, 1)) {
					$item->$k = $v;
				}
			}

			if(!empty($item->product_attribute)){
				//Format now {"9":7,"20":{"126":{"comment":"test1"},"127":{"comment":"t2"},"128":{"comment":"topic 3"},"129":{"comment":"4 44 4 4 44 "}}}
				//$old = '{"46":" <span class=\"costumTitle\">Cap Size<\/span><span class=\"costumValue\" >S<\/span>","109":{"textinput":{"comment":"test"}}}';
				//$myCustomsOld = @json_decode($old,true);

				$myCustoms = @json_decode($item->product_attribute,true);
				$myCustoms = (array) $myCustoms;

				$item->customfields = array();
				foreach($myCustoms as $custom){
					if(!is_array($custom)){
						$custom = array( $custom =>false);
					}
					foreach($custom as $id=>$field){
						$item->customfields[] = $customfieldModel-> getCustomEmbeddedProductCustomField($id);
						$ids[] = $id;
					}
				}
			}

			if(!empty($product->customfields)){
				if(!isset($item->customfields)) $item->customfields = array();
				foreach($product->customfields as $customfield){
					if(!in_array($customfield->virtuemart_customfield_id,$ids) and $customfield->field_type=='E' and ($customfield->is_input or $customfield->is_cart_attribute)){
						$item->customfields[] = $customfield;
					}
				}
			}
		}

// Get the order items
		$q = "SELECT  *
			FROM #__virtuemart_order_calc_rules AS z
			WHERE  virtuemart_order_id=".$virtuemart_order_id;
		$db->setQuery($q);
		$order['calc_rules'] = $db->loadObjectList();
		return $order;
	}

	/**
	 * Select the products to list on the product list page
	 * @param $uid integer Optional user ID to get the orders of a single user
	 * @param $_ignorePagination boolean If true, ignore the Joomla pagination (for embedded use, default false)
	 */
	public function getOrdersList($uid = 0, $noLimit = false)
	{
// 		vmdebug('getOrdersList');
		$tUserInfos = $this->getTable('userinfos');
		$this->_noLimit = $noLimit;

		$concat = array();
		if(property_exists($tUserInfos,'company'))  $concat[]= 'u.company';
		if(property_exists($tUserInfos,'first_name'))  $concat[]= 'u.first_name';
		if(property_exists($tUserInfos,'middle_name'))  $concat[]= 'u.middle_name';
		if(property_exists($tUserInfos,'last_name'))  $concat[]= 'u.last_name';
		if(!empty($concat)){
			$concatStr = "CONCAT_WS(' ',".implode(',',$concat).")";
		} else {
			$concatStr = 'o.order_number';
		}

// quorvia added phone, zip, city and shipping details and ST data
		$select = " o.*, ".$concatStr." AS order_name "
            .',u.email as order_email,
            pm.payment_name AS payment_method,
            u.company AS company,
            u.city AS city,
            u.zip AS zip,
            u.phone_1 AS phone,
            st.address_type AS st_type,
            st.company AS st_company,
            st.city AS st_city,
            st.zip AS st_zip,
            u.customer_note AS customer_note';
		$from = $this->getOrdersListQuery();

		$where = array();


		$virtuemart_vendor_id = vmAccess::isSuperVendor();
		if(vmAccess::manager('managevendors')){
			vmdebug('Vendor has managevendors and should see all');
			$virtuemart_vendor_id = vRequest::get('virtuemart_vendor_id',false);
			if($virtuemart_vendor_id){
				$where[]= ' o.virtuemart_vendor_id = "'.$virtuemart_vendor_id.'" ';
			}
			if(!empty($uid)){
				$where[]= ' u.virtuemart_user_id = ' . (int)$uid.' ';
			}
		}
		else if( vmAccess::manager('orders')){
			
			vmdebug('Vendor is manager and should only see its own orders venodorId '.$virtuemart_vendor_id);
			if(!empty($virtuemart_vendor_id)){
				$where[]= ' (o.virtuemart_vendor_id = '.$virtuemart_vendor_id.' OR u.virtuemart_user_id = ' . (int)$uid.') ';
				$uid = 0;
			} else {
				//We map here as fallback to vendor 1.
				$where[]= ' u.virtuemart_user_id = ' . (int)$uid;
			}
		} else {
			//A normal user is only allowed to see its own orders, we map $uid to the user id
			$user = JFactory::getUser();
			$uid = (int)$user->id;
			$where = array();
		}
		if(!empty($uid)){
			$where[]= ' u.virtuemart_user_id = ' . (int)$uid.' ';
		}


		if ($this->search){
			$db = JFactory::getDBO();
			$this->search = '"%' . $db->escape( $this->search, true ) . '%"' ;
			$this->search = str_replace(' ','%',$this->search);

			$searchFields = array();
			$searchFields[] = 'u.first_name';
			$searchFields[] = 'u.middle_name';
			$searchFields[] = 'u.last_name';
			$searchFields[] = 'o.order_number';
			$searchFields[] = 'u.company';
			$searchFields[] = 'u.email';
			$searchFields[] = 'u.phone_1';
			$searchFields[] = 'u.address_1';
			$searchFields[] = 'u.zip';
//quorvia addedd  ST data searches
			$searchFields[] = 'st.company';
			$searchFields[] = 'st.last_name';
			$searchFields[] = 'st.city';
			$searchFields[] = 'st.zip';
			$where[] = implode (' LIKE '.$this->search.' OR ', $searchFields) . ' LIKE '.$this->search.' ';
			//$where[] = ' ( u.first_name LIKE '.$search.' OR u.middle_name LIKE '.$search.' OR u.last_name LIKE '.$search.' OR `order_number` LIKE '.$search.')';
		}

		$order_status_code = vRequest::getString('order_status_code', false);
		if ($order_status_code and $order_status_code!=-1){
			$where[] = ' o.order_status = "'.$order_status_code.'" ';
		}

		if (count ($where) > 0) {
			$whereString = ' WHERE (' . implode (' AND ', $where) . ') ';
		}
		else {
			$whereString = '';
		}

		if ( vRequest::getCmd('view') == 'orders') {
			$ordering = $this->_getOrdering();
		} else {
			$ordering = ' order by o.modified_on DESC';
		}

		$this->_data = $this->exeSortSearchListQuery(0,$select,$from,$whereString,'',$ordering);

		if($this->_data){
			foreach($this->_data as $k=>$d){
				$this->_data[$k]->order_name = htmlspecialchars(strip_tags(htmlspecialchars_decode($d->order_name)));
			}
		}

		return $this->_data ;
	}

	/**
	 * List of tables to include for the product query
	 */
	private function getOrdersListQuery()	{
		return ' FROM #__virtuemart_orders as o
				LEFT JOIN #__virtuemart_order_userinfos as u
				ON u.virtuemart_order_id = o.virtuemart_order_id AND u.address_type="BT"
				LEFT JOIN #__virtuemart_order_userinfos as st
				ON st.virtuemart_order_id = o.virtuemart_order_id AND st.address_type="ST"
				LEFT JOIN #__virtuemart_paymentmethods_'.VmConfig::$vmlang.' as pm
				ON o.virtuemart_paymentmethod_id = pm.virtuemart_paymentmethod_id';
	}


	/**
	 * Update an order item status
	 * @author Max Milbers
	 * @author Ondřej Spilka - used for item edit also
	 * @author Maik Künnemann
	 */
	public function updateSingleItem($virtuemart_order_item_id, &$orderdata, $orderUpdate = false)
	{
		$virtuemart_order_item_id = (int)$virtuemart_order_item_id;
		//vmdebug('updateSingleItem',$virtuemart_order_item_id,$orderdata);
		$table = $this->getTable('order_items');
		if(!empty($virtuemart_order_item_id)){
			$table->load($virtuemart_order_item_id);
			$oldOrderStatus = $table->order_status;
		}

		//vmdebug('my order table ',$virtuemart_order_item_id,$table->virtuemart_order_id);
		if(empty($oldOrderStatus)){
			$oldOrderStatus = $orderdata->current_order_status;
			if($orderUpdate and empty($oldOrderStatus)){
				$oldOrderStatus = 'P';
			}
		}

		$dataT = get_object_vars($table);

		$orderdatacopy = $orderdata;
		$data = array_merge($dataT,(array)$orderdatacopy);

		if (!class_exists('CurrencyDisplay')) {
			require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
		}

		$this->_currencyDisplay = CurrencyDisplay::getInstance();
		$rounding = $this->_currencyDisplay->_priceConfig['salesPrice'][1];

		if ( $orderUpdate and !empty($data['virtuemart_order_item_id'])) {

			//get tax calc_value of product VatTax
			$db = JFactory::getDBO();
			$sql = "SELECT `calc_value` FROM `#__virtuemart_order_calc_rules` WHERE `virtuemart_order_id` = ".$data['virtuemart_order_id']." AND `virtuemart_order_item_id` = ".$data['virtuemart_order_item_id']." AND `calc_kind` = 'VatTax' ";
			$db->setQuery($sql);
			$taxCalcValue = $db->loadResult();

			if($data['calculate_product_tax']) {
				if(!$taxCalcValue){
					//Could be a new item, missing the tax rules, we try to get one of another product.
					//get tax calc_value of product VatTax
					$db = JFactory::getDBO();
					$sql = "SELECT `calc_value` FROM `#__virtuemart_order_calc_rules` WHERE `virtuemart_order_id` = ".$data['virtuemart_order_id']." AND `calc_kind` = 'VatTax' ";
					$db->setQuery($sql);
					$taxCalcValue = $db->loadResult();
				}

				if(empty($data['product_subtotal_discount']))$data['product_subtotal_discount'] = 0.0; // "",null,0,NULL, FALSE => 0.0

				//We do two cases, either we have the final amount and discount
				if(!empty($data['product_final_price']) and $data['product_final_price']!=0){

					if(empty($data['product_tax']) or $data['product_tax']==0){
						$data['product_tax'] = $data['product_final_price'] * $taxCalcValue / ($taxCalcValue + 100);
						//vmdebug($data['product_final_price'] .' * '.$taxCalcValue.' / '.($taxCalcValue + 100).' = '.$data['product_tax']);
					}

					if(empty($data['product_item_price']) or $data['product_item_price']==0){
						if(empty($data['product_tax']))$data['product_tax'] = 0.0;

						$data['product_item_price'] = round($data['product_final_price'], $rounding) - $data['product_tax'];
						$data['product_discountedPriceWithoutTax'] = 0.0;// round($data['product_final_price'], $rounding) ;
						$data['product_priceWithoutTax'] = 0.0;
						$data['product_basePriceWithTax'] =  round($data['product_final_price'], $rounding) - $data['product_subtotal_discount'];
					}

				} else
					//or we have the base price and a manually set discount.
					if(!empty($data['product_item_price']) and $data['product_item_price']!=0){
						if(empty($data['product_tax']) or $data['product_tax']==0){
							$data['product_tax'] = ($data['product_item_price']-$data['product_subtotal_discount']) * ($taxCalcValue/100.0);
						}
						$data['product_discountedPriceWithoutTax'] = 0.0;
						$data['product_priceWithoutTax'] = 0.0;
						$data['product_final_price'] = round($data['product_item_price'], $rounding) + $data['product_tax'] + $data['product_subtotal_discount'];
						$data['product_basePriceWithTax'] =  round($data['product_final_price'], $rounding) - $data['product_subtotal_discount'];
				}

			}
			//$data['product_subtotal_discount'] = (round($orderdata->product_final_price, $rounding) - round($data['product_basePriceWithTax'], $rounding)) * $orderdata->product_quantity;
			$data['product_subtotal_with_tax'] = round($data['product_final_price'], $rounding) * $orderdata->product_quantity;
		}
		if(!empty($table->virtuemart_vendor_id)){
			$data['virtuemart_vendor_id'] = $table->virtuemart_vendor_id;
		}

		$table->bindChecknStore($data);

		if ( $orderUpdate ) {

			if ( empty($data['order_item_sku']) and !empty($virtuemart_order_item_id) )
			{
				//update product identification
				$db = JFactory::getDBO();
				$prolang = '#__virtuemart_products_' . VmConfig::$vmlang;
				$oi = " #__virtuemart_order_items";
				$protbl = "#__virtuemart_products";
				$sql = 'UPDATE '.$oi.', '.$protbl.', '.$prolang .
					' SET '.$oi.'.order_item_sku='.$protbl.'.product_sku, '.$oi.'.order_item_name='.$prolang.'.product_name
					 WHERE '.$oi.'.virtuemart_product_id='.$protbl.'.virtuemart_product_id
					 and '.$oi.'.virtuemart_product_id='.$prolang.'.virtuemart_product_id
					 and '.$oi.'.virtuemart_order_item_id='.(int)$virtuemart_order_item_id;
				$db->setQuery($sql);
				if ($db->execute() === false) {
					vmError('updateSingleItem '.$sql,'Error updating order');
				}	
			}
		}

		//OSP update cartRules/shipment/payment
		//it would seem strange this is via item edit
		//but in general, shipment and payment would be tractated as another items of the order
		//in datas they are not, bu okay we have it here and functional
		//moreover we can compute all aggregate values here via one aggregate SQL
		if ( $orderUpdate and !empty( $table->virtuemart_order_id))
		{
			$db = JFactory::getDBO();
			$ordid = $table->virtuemart_order_id;
			///vmdebug('my order table ',$table);
			//cartRules
			$calc_rules = vRequest::getVar('calc_rules','', '', 'array');
			$calc_rules_amount = 0;
			$calc_rules_discount_amount = 0;
			$calc_rules_tax_amount = 0;

			if(!empty($calc_rules))
			{
				foreach($calc_rules as $calc_kind => $calc_rule) {
					foreach($calc_rule as $virtuemart_order_calc_rule_id => $calc_amount) {
						$sql = 'UPDATE `#__virtuemart_order_calc_rules` SET `calc_amount`="'.$calc_amount.'" WHERE `virtuemart_order_calc_rule_id`="'.$virtuemart_order_calc_rule_id.'"';
						$db->setQuery($sql);
						if(isset($calc_amount)) $calc_rules_amount += $calc_amount;
						if ($calc_kind == 'DBTaxRulesBill' || $calc_kind == 'DATaxRulesBill') {
							$calc_rules_discount_amount += $calc_amount;
						}
						if ($calc_kind == 'taxRulesBill') {
							$calc_rules_tax_amount += $calc_amount;
						}
						if ($db->execute() === false) {
							vmError($db->getError());
						}
					}
				}
			}

			//shipment
			$os = vRequest::getString('order_shipment');
			$ost = vRequest::getString('order_shipment_tax');

			if ( $os!="" )
			{
				$sql = 'UPDATE `#__virtuemart_orders` SET `order_shipment`="'.$os.'",`order_shipment_tax`="'.$ost.'" WHERE  `virtuemart_order_id`="'.$ordid.'"';
				$db->setQuery($sql);
				if ($db->execute() === false) {
					vmError('updateSingleItem Error updating order_shipment '.$sql);
				}
			}

			//payment
			$op = vRequest::getString('order_payment');
			$opt = vRequest::getString('order_payment_tax');
			if ( $op!="" )
			{
				$sql = 'UPDATE `#__virtuemart_orders` SET `order_payment`="'.$op.'",`order_payment_tax`="'.$opt.'" WHERE  `virtuemart_order_id`="'.$ordid.'"';
				$db->setQuery($sql);
				if ($db->execute() === false) {
					vmError('updateSingleItem Error updating order payment'.$sql);
				}
			}

			$sql = 'UPDATE `#__virtuemart_orders` SET '.
					'`order_total`=(SELECT sum(product_final_price*product_quantity) FROM #__virtuemart_order_items where `virtuemart_order_id`='.$ordid.')+`order_shipment`+`order_shipment_tax`+`order_payment`+`order_payment_tax`+'.$calc_rules_amount.',
					`order_discountAmount`=(SELECT sum(product_subtotal_discount) FROM #__virtuemart_order_items where `virtuemart_order_id`='.$ordid.'),
					`order_billDiscountAmount`=`order_discountAmount`+'.$calc_rules_discount_amount.',
					`order_salesPrice`=(SELECT sum(product_final_price*product_quantity) FROM #__virtuemart_order_items where `virtuemart_order_id`='.$ordid.'),
					`order_tax`=(SELECT sum( product_tax*product_quantity) FROM #__virtuemart_order_items where `virtuemart_order_id`='.$ordid.'),
					`order_subtotal`=(SELECT sum(ROUND(product_item_price, '. $rounding .')*product_quantity) FROM #__virtuemart_order_items where `virtuemart_order_id`='.$ordid.'),';

			if(vRequest::getString('calculate_billTaxAmount')) {
				$sql .= '`order_billTaxAmount`=(SELECT sum( product_tax*product_quantity) FROM #__virtuemart_order_items where `virtuemart_order_id`='.$ordid.')+`order_shipment_tax`+`order_payment_tax`+ '.$calc_rules_tax_amount.' ';
			} else {
				$sql .= '`order_billTaxAmount`="'.vRequest::getString('order_billTaxAmount').'"';
			}

			$sql .= ' WHERE  `virtuemart_order_id`='.$ordid;

			$db->setQuery($sql); 
			if ($db->execute() === false) {
				vmError('updateSingleItem '.$db->getError().' and '.$sql);
			}

		}

		$this->handleStockAfterStatusChangedPerProduct($orderdata->order_status, $oldOrderStatus, $table,$table->product_quantity);
	}


	/**
	 * Strange name is just temporarly
	 *
	 * @param unknown_type $order_id
	 * @param unknown_type $order_status
         * @author Max Milbers
	 */
	var $useDefaultEmailOrderStatus = true;
	public function updateOrderStatus($orders=0, $order_id =0,$order_status=0){

		//General change of orderstatus
		$total = 1 ;
		if(empty($orders)){
			$orders = array();
			$orderslist = vRequest::getVar('orders',  array());
			$total = 0 ;
			// Get the list of orders in post to update
			foreach ($orderslist as $key => $order) {
				if ( $orderslist[$key]['order_status'] !== $orderslist[$key]['current_order_status'] ) {
					$orders[$key] =  $orderslist[$key];
					$total++;
				}
			}
		}

		if(!is_array($orders)){
			$orders = array($orders);
		}


		/* Process the orders to update */
		$updated = 0;
		$error = 0;
		if ($orders) {
			// $notify = vRequest::getVar('customer_notified', array()); // ???
			// $comments = vRequest::getVar('comments', array()); // ???
			foreach ($orders as $virtuemart_order_id => $order) {
				if  ($order_id >0) $virtuemart_order_id= $order_id;
				$this->useDefaultEmailOrderStatus = false;
				if($this->updateStatusForOneOrder($virtuemart_order_id,$order,true)){
					$updated ++;
				} else {
					$error++;
				}
			}
		}
		$result = array( 'updated' => $updated , 'error' =>$error , 'total' => $total ) ;
		return $result ;

	}

	/**
	 * Attention, if you use this function within your trigger take care of the last parameter,
	 * you should define it, this parameter maybe set to false in future releases
	 *
	 * IMPORTANT: The $inputOrder can contain extra data by plugins
	 *
	 * @param $virtuemart_order_id
	 * @param $inputOrder
	 * @param bool $useTriggers
	 * @return bool
	 */
	function updateStatusForOneOrder($virtuemart_order_id,$inputOrder,$useTriggers=true){

// 		vmdebug('updateStatusForOneOrder', $inputOrder);
		/* Update the order */
		$data = $this->getTable('orders');
		$data->load($virtuemart_order_id);
		$old_order_status = $data->order_status;
		if(empty($inputOrder['virtuemart_order_id'])){
			unset($inputOrder['virtuemart_order_id']);
		}
		$data->bind($inputOrder);

		$cp_rm = VmConfig::get('cp_rm',array('C'));
		if(!is_array($cp_rm)) $cp_rm = array($cp_rm);

		if ( in_array((string) $data->order_status,$cp_rm) ){
			if (!empty($data->coupon_code)) {
				if (!class_exists('CouponHelper'))
					require(VMPATH_SITE . DS . 'helpers' . DS . 'coupon.php');
				CouponHelper::RemoveCoupon($data->coupon_code);
			}
		}
		//First we must call the payment, the payment manipulates the result of the order_status
		if($useTriggers){

			if(!class_exists('vmPSPlugin')) require(VMPATH_PLUGINLIBS.DS.'vmpsplugin.php');

			JPluginHelper::importPlugin('vmcalculation');
			JPluginHelper::importPlugin('vmcustom');

			JPluginHelper::importPlugin('vmshipment');
			$_dispatcher = JDispatcher::getInstance();											//Should we add this? $inputOrder
			$_returnValues = $_dispatcher->trigger('plgVmOnUpdateOrderShipment',array(&$data,$old_order_status));

			// Payment decides what to do when order status is updated
			JPluginHelper::importPlugin('vmpayment');
			$_dispatcher = JDispatcher::getInstance();											//Should we add this? $inputOrder
			$_returnValues = $_dispatcher->trigger('plgVmOnUpdateOrderPayment',array(&$data,$old_order_status));
			foreach ($_returnValues as $_returnValue) {
				if ($_returnValue === true) {
					break; // Plugin was successfull
				} elseif ($_returnValue === false) {
					return false; // Plugin failed
				}
				// Ignore null status and look for the next returnValue
			}

			/**
			* If an order gets cancelled, fire a plugin event, perhaps
			* some authorization needs to be voided
			*/
			if ($data->order_status == "X") {

				$_dispatcher = JDispatcher::getInstance();
				//Should be renamed to plgVmOnCancelOrder
				$_dispatcher->trigger('plgVmOnCancelPayment',array(&$data,$old_order_status));
			}
		}

		if(empty($data->delivery_date)){
			$del_date_type = VmConfig::get('del_date_type','m');
			if(strpos($del_date_type,'os')!==FALSE){	//for example osS
				$os = substr($del_date_type,2);
				if($data->order_status == $os){
					$date = JFactory::getDate();
					$data->delivery_date = $date->toSQL();
				}
			} else {
				VmConfig::loadJLang('com_virtuemart_orders', true);
				$data->delivery_date = vmText::_('COM_VIRTUEMART_DELDATE_INV');
			}
		}

		if ($data->store()) {

			$task= vRequest::getCmd('task',0);
			$view= vRequest::getCmd('view',0);

			$item_ids = vRequest::getVar('item_id',false);
			if($item_ids){

				foreach ($item_ids as $item_id => $order_item_data) {
					$order_item_data['current_order_status'] = $order_item_data['order_status'];
					if (!isset($order_item_data['comments'])) $order_item_data['comments'] = '';
					$order_item_data = (object)$order_item_data;
					$order_item_data->virtuemart_order_id = $virtuemart_order_id;

					//$this->updateSingleItem($order_item->virtuemart_order_item_id, $data->order_status, $order['comments'] , $virtuemart_order_id, $data->order_pass);
					if(empty($item_id)){
						$inputOrder['comments'] .= ' '.vmText::sprintf('COM_VIRTUEMART_ORDER_PRODUCT_ADDED',$order_item_data->order_item_name);
					}
					$this->updateSingleItem($item_id, $order_item_data,true);
				}
			} else {
				/*if($task=='edit'){
					$update_lines = vRequest::getInt('update_lines');
				} else /*/
				if ($task=='updatestatus' and $view=='orders') {
					$lines = vRequest::getVar('orders');
					$update_lines = $lines[$virtuemart_order_id]['update_lines'];
				} else {
					$update_lines = 1;
				}

				if($update_lines==1){
					vmdebug('$update_lines '.$update_lines);
					$q = 'SELECT virtuemart_order_item_id
												FROM #__virtuemart_order_items
												WHERE virtuemart_order_id="'.$virtuemart_order_id.'"';
					$db = JFactory::getDBO();
					$db->setQuery($q);
					$order_items = $db->loadObjectList();
					if ($order_items) {
// 				vmdebug('updateStatusForOneOrder',$data);
						foreach ($order_items as $order_item) {

							//$this->updateSingleItem($order_item->virtuemart_order_item_id, $data->order_status, $order['comments'] , $virtuemart_order_id, $data->order_pass);
							$this->updateSingleItem($order_item->virtuemart_order_item_id, $data);
						}
					}
				}
			}


			$inputOrder['comments'] = trim($inputOrder['comments']);
			/* Update the order history */
			$this->_updateOrderHist($virtuemart_order_id, $data->order_status, $inputOrder['customer_notified'], $inputOrder['comments']);

			//We need a new invoice, therefore rename the old one.
			$inv_os = VmConfig::get('inv_os',array('C'));
			if(!is_array($inv_os)) $inv_os = array($inv_os);
			if($old_order_status!=$data->order_status and in_array($data->order_status,$inv_os)){
				$this->renameInvoice($data->virtuemart_order_id);
			}

			// When the plugins did not already notified the user, do it here (the normal way)
			//Attention the ! prevents at the moment that an email is sent. But it should used that way.
// 			if (!$inputOrder['customer_notified']) {
			$this->notifyCustomer( $data->virtuemart_order_id , $inputOrder );
// 			}

			JPluginHelper::importPlugin('vmcoupon');
			$dispatcher = JDispatcher::getInstance();
			$returnValues = $dispatcher->trigger('plgVmCouponUpdateOrderStatus', array($data, $old_order_status));
			if(!empty($returnValues)){
				foreach ($returnValues as $returnValue) {
					if ($returnValue !== null  ) {
						return $returnValue;
					}
				}
			}


			return true;
		} else {
			return false;
		}
	}


	/**
	 * Get the information from the cart and create an order from it
	 *
	 * @author Oscar van Eijk
	 * @param object $_cart The cart data
	 * @return mixed The new ordernumber, false on errors
	 */
	public function createOrderFromCart($cart)
	{

		if ($cart === null) {
			vmError('createOrderFromCart() called without a cart - that\'s a programming bug','Can\'t create order, sorry.');
			return false;
		}

		$usr = JFactory::getUser();
		//$prices = $cart->getCartPrices();
		if (($orderID = $this->_createOrder($cart, $usr)) == 0) {
			vmError('Couldn\'t create order','Couldn\'t create order');
			return false;
		}
		if (!$this->_createOrderLines($orderID, $cart)) {
			vmError('Couldn\'t create order items','Couldn\'t create order items');
			return false;
		}
		if (!$this-> _createOrderCalcRules($orderID, $cart) ) {
			vmError('Couldn\'t create order items','Couldn\'t create order items');
			return false;
		}
		$this->_updateOrderHist($orderID);
		if (!$this->_writeUserInfo($orderID, $usr, $cart)) {
			vmError('Couldn\'t create order userinfo','Couldn\'t create order userinfo');
			return false;
		}

		return $orderID;
	}

	/**
	 * Write the order header record
	 *
	 * @author Oscar van Eijk
	 * @param object $_cart The cart data
	 * @param object $_usr User object
	 * @param array $_prices Price data
	 * @return integer The new ordernumber
	 */
	private function _createOrder($_cart, $_usr)
	{
		//		TODO We need tablefields for the new values:
		//		Shipment:
		//		$_prices['shipmentValue']		w/out tax
		//		$_prices['shipmentTax']			Tax
		//		$_prices['salesPriceShipment']	Total
		//
		//		Payment:
		//		$_prices['paymentValue']		w/out tax
		//		$_prices['paymentTax']			Tax
		//		$_prices['paymentDiscount']		Discount
		//		$_prices['salesPricePayment']	Total

		$_orderData = new stdClass();

		$_orderData->virtuemart_order_id = null;
		$_orderData->virtuemart_user_id = $_usr->get('id');
		$_orderData->virtuemart_vendor_id = $_cart->vendorId;
		$_orderData->customer_number = $_cart->customer_number;
		$_prices = $_cart->cartPrices;
		//Note as long we do not have an extra table only storing addresses, the virtuemart_userinfo_id is not needed.
		//The virtuemart_userinfo_id is just the id of a stored address and is only necessary in the user maintance view or for choosing addresses.
		//the saved order should be an snapshot with plain data written in it.
		//		$_orderData->virtuemart_userinfo_id = 'TODO'; // $_cart['BT']['virtuemart_userinfo_id']; // TODO; Add it in the cart... but where is this used? Obsolete?
		$_orderData->order_total = $_prices['billTotal'];
		$_orderData->order_salesPrice = $_prices['salesPrice'];
		$_orderData->order_billTaxAmount = $_prices['billTaxAmount'];
		$_orderData->order_billDiscountAmount = $_prices['billDiscountAmount'];
		$_orderData->order_discountAmount = $_prices['discountAmount'];
		$_orderData->order_subtotal = $_prices['priceWithoutTax'];
		$_orderData->order_tax = $_prices['taxAmount'];
		$_orderData->order_shipment = $_prices['shipmentValue'];
		$_orderData->order_shipment_tax = $_prices['shipmentTax'];
		$_orderData->order_payment = $_prices['paymentValue'];
		$_orderData->order_payment_tax = $_prices['paymentTax'];

		if (!empty($_cart->cartData['VatTax'])) {
			$taxes = array();
			foreach($_cart->cartData['VatTax'] as $k=>$VatTax) {
				$taxes[$k]['virtuemart_calc_id'] = $k;
				$taxes[$k]['calc_name'] = $VatTax['calc_name'];
				$taxes[$k]['calc_value'] = $VatTax['calc_value'];
				$taxes[$k]['result'] = $VatTax['result'];
			}
			$_orderData->order_billTax = json_encode($taxes);
		}

		if (!empty($_cart->couponCode)) {
			$_orderData->coupon_code = $_cart->couponCode;
			$_orderData->coupon_discount = $_prices['salesPriceCoupon'];
		}
		$_orderData->order_discount = $_prices['discountAmount'];  // discount order_items


		$_orderData->order_status = 'P';
		$_orderData->order_currency = $this->getVendorCurrencyId($_orderData->virtuemart_vendor_id);
		if (!class_exists('CurrencyDisplay')) {
			require(VMPATH_ADMIN . '/helpers/currencydisplay.php');
		}
		if (isset($_cart->pricesCurrency)) {
			$_orderData->user_currency_id = $_cart->paymentCurrency ;//$this->getCurrencyIsoCode($_cart->pricesCurrency);
			$currency = CurrencyDisplay::getInstance($_orderData->user_currency_id);
			if($_orderData->user_currency_id != $_orderData->order_currency){
				$_orderData->user_currency_rate =   $currency->convertCurrencyTo($_orderData->user_currency_id ,1.0,false);
			} else {
				$_orderData->user_currency_rate=1.0;
			}
		}

		$_orderData->virtuemart_paymentmethod_id = $_cart->virtuemart_paymentmethod_id;
		$_orderData->virtuemart_shipmentmethod_id = $_cart->virtuemart_shipmentmethod_id;

		//Some payment plugins need a new order_number for any try
		$_orderData->order_number = '';
		$_orderData->order_pass = '';

		$_orderData->order_language = $_cart->order_language;
		$_orderData->ip_address = $_SERVER['REMOTE_ADDR'];

		$maskIP = VmConfig::get('maskIP','last');
		if($maskIP=='last'){
			$rpos = strrpos($_orderData->ip_address,'.');
			$_orderData->ip_address = substr($_orderData->ip_address,0,($rpos+1)).'xx';
		}

		if($_cart->_inConfirm){
			$order = false;
			$db = JFactory::getDbo();
			$q = 'SELECT * FROM `#__virtuemart_orders` ';
			if(!empty($_cart->virtuemart_order_id)){
				$db->setQuery($q . ' WHERE `order_number`= "'.$_cart->virtuemart_order_id.'" AND `order_status` = "P" ');
				$order = $db->loadAssoc();
				if(!$order){
					vmdebug('This should not happen, there is a cart with order_number, but not order stored '.$_cart->virtuemart_order_id);
				}
			}

			if(VmConfig::get('reuseorders',true) and !$order){
				$jnow = JFactory::getDate();
				$jnow->sub(new DateInterval('PT1H'));
				$minushour = $jnow->toSQL();
				$q .= ' WHERE `customer_number`= "'.$_orderData->customer_number.'" ';
				$q .= '	AND `order_status` = "P"
				AND `created_on` > "'.$minushour.'" ';
				$db->setQuery($q);
				$order = $db->loadAssoc();
			}

			if($order){
				if(!empty($order['virtuemart_order_id'])){
					$_orderData->virtuemart_order_id = $order['virtuemart_order_id'];
				}

				//Dirty hack
				$this->removeOrderItems($order['virtuemart_order_id']);
			}
		}

		//lets merge here the userdata from the cart to the order so that it can be used
		if(!empty($_cart->BT)){
			foreach($_cart->BT as $k=>$v){
				$_orderData->$k = $v;
			}
		}

		JPluginHelper::importPlugin('vmshopper');
		JPluginHelper::importPlugin('vmextended');
		$dispatcher = JDispatcher::getInstance();
		$plg_datas = $dispatcher->trigger('plgVmOnUserOrder',array(&$_orderData));
		foreach($plg_datas as $plg_data){
			// 				$data = array_merge($plg_data,$data);
		}
		if(empty($_orderData->order_number)){
			$_orderData->order_number = $this->genStdOrderNumber($_orderData->virtuemart_vendor_id);
		}
		if(empty($_orderData->order_pass)){
			$_orderData->order_pass = $this->genStdOrderPass();
		}
		if(empty($_orderData->order_create_invoice_pass)){
			$_orderData->order_create_invoice_pass = $this->genStdCreateInvoicePass();
		}

		$orderTable =  $this->getTable('orders');
		$orderTable -> bindChecknStore($_orderData);

		$db = JFactory::getDBO();

		if (!empty($_cart->couponCode)) {
			//set the virtuemart_order_id in the Request for 3rd party coupon components (by Seyi and Max)
			vRequest::setVar ( 'virtuemart_order_id', $orderTable->virtuemart_order_id );
			// If a gift coupon was used, remove it now
			CouponHelper::setInUseCoupon($_cart->couponCode, true);
		}
		// the order number is saved into the session to make sure that the correct cart is emptied with the payment notification
		$_cart->order_number = $orderTable->order_number;
		$_cart->order_pass = $_orderData->order_pass;
		$_cart->virtuemart_order_id = $orderTable->virtuemart_order_id;
		$_cart->setCartIntoSession ();

		return $orderTable->virtuemart_order_id;
	}


	private function getVendorCurrencyId($vendorId){
		$q = 'SELECT `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id`="'.$vendorId.'" ';
		$db = JFactory::getDBO();
		$db->setQuery($q);
		$vendorCurrency =  $db->loadResult();
		return $vendorCurrency;
	}


	/**
	 * Write the BillTo record, and if set, the ShipTo record
	 *
	 * @author Oscar van Eijk
	 * @param integer $_id Order ID
	 * @param object $_usr User object
	 * @param object $_cart Cart object
	 * @return boolean True on success
	 */
	private function _writeUserInfo($_id, &$_usr, $_cart)
	{
		$_userInfoData = array();

		if(!class_exists('VirtueMartModelUserfields')) require(VMPATH_ADMIN.DS.'models'.DS.'userfields.php');

		$_userFieldsModel = VmModel::getModel('userfields');
		$_userFieldsBT = $_userFieldsModel->getUserFields('account'
		, array('delimiters'=>true, 'captcha'=>true)
		, array('username', 'password', 'password2', 'user_is_vendor')
		);

		$userFieldsCart = $_userFieldsModel->getUserFields(
			'cart'
			, array('captcha' => true, 'delimiters' => true) // Ignore these types
			, array('user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type') // Skips
		);
		$_userFieldsBT = array_merge($_userFieldsBT,$userFieldsCart);


		foreach ($_userFieldsBT as $_fld) {
			$_name = $_fld->name;
			if(!empty( $_cart->BT[$_name])){
				if (is_array( $_cart->BT[$_name])) {
					$_userInfoData[$_name] =  implode("|*|",$_cart->BT[$_name]);
				} else {
					$_userInfoData[$_name] = $_cart->BT[$_name];
				}
			}
		}

		$_userInfoData['virtuemart_order_id'] = $_id;
		$_userInfoData['virtuemart_user_id'] = $_usr->get('id');
		$_userInfoData['address_type'] = 'BT';

		$db = JFactory::getDBO();
		$q = ' SELECT `virtuemart_order_userinfo_id` FROM `#__virtuemart_order_userinfos` ';
		$q .= ' WHERE `virtuemart_order_id` = "'.$_id.'" AND `address_type` = "BT" ';
		$db->setQuery($q);
		if($vmOId = $db->loadResult()){
			$_userInfoData['virtuemart_order_userinfo_id'] = $vmOId;
		}

		$order_userinfosTable = $this->getTable('order_userinfos');
		if (!$order_userinfosTable->bindChecknStore($_userInfoData)){
			return false;
		}

		if ($_cart->ST and empty($_cart->STsameAsBT)) {
			$_userInfoData = array();
			$_userFieldsST = $_userFieldsModel->getUserFields('shipment'
			, array('delimiters'=>true, 'captcha'=>true)
			, array('username', 'password', 'password2', 'user_is_vendor')
			);
			foreach ($_userFieldsST as $_fld) {
				$_name = $_fld->name;
				if(!empty( $_cart->ST[$_name])){
					$_userInfoData[$_name] = $_cart->ST[$_name];
				}
			}

			$_userInfoData['virtuemart_order_id'] = $_id;
			$_userInfoData['virtuemart_user_id'] = $_usr->get('id');
			$_userInfoData['address_type'] = 'ST';

			$q = ' SELECT `virtuemart_order_userinfo_id` FROM `#__virtuemart_order_userinfos` ';
			$q .= ' WHERE `virtuemart_order_id` = "'.$_id.'" AND `address_type` = "ST" ';
			$db->setQuery($q);
			if($vmOId = $db->loadResult()){
				$_userInfoData['virtuemart_order_userinfo_id'] = $vmOId;
			}

			$order_userinfosTable = $this->getTable('order_userinfos');
			if (!$order_userinfosTable->bindChecknStore($_userInfoData)){
				return false;
			}
		}
		return true;
	}


	function handleStockAfterStatusChangedPerProduct($newState, $oldState,$tableOrderItems, $quantity) {

		if($newState == $oldState) return;
		// $StatutWhiteList = array('P','C','X','R','S','N');
		$db = JFactory::getDBO();
		$db->setQuery('SELECT * FROM `#__virtuemart_orderstates` ');
		$StatutWhiteList = $db->loadAssocList('order_status_code');
		// new product is statut N
		$StatutWhiteList['N'] = Array ( 'order_status_id' => 0 , 'order_status_code' => 'N' , 'order_stock_handle' => 'A');
		if(!array_key_exists($oldState,$StatutWhiteList) or !array_key_exists($newState,$StatutWhiteList)) {
			vmError('The workflow for '.$newState.' or  '.$oldState.' is unknown, take a look on model/orders function handleStockAfterStatusChanged','Can\'t process workflow, contact the shopowner. Status is '.$newState);
			return ;
		}
		//vmdebug( 'updatestock qt :' , $quantity.' id :'.$productId);
		// P 	Pending
		// C 	Confirmed
		// X 	Cancelled
		// R 	Refunded
		// S 	Shipped
		// N 	New or coming from cart
		//  TO have no product setted as ordered when added to cart simply delete 'P' FROM array Reserved
		// don't set same values in the 2 arrays !!!
		// stockOut is in normal case shipped product
		//order_stock_handle
		// 'A' : stock Available
		// 'O' : stock Out
		// 'R' : stock reserved
		// the status decreasing real stock ?
		// $stockOut = array('S');
		if ($StatutWhiteList[$newState]['order_stock_handle'] == 'O') $isOut = 1;
		else $isOut = 0;
		if ($StatutWhiteList[$oldState]['order_stock_handle'] == 'O') $wasOut = 1;
		else $wasOut = 0;

		// Stock change ?
		if ($isOut && !$wasOut)     $product_in_stock = '-';
		else if ($wasOut && !$isOut ) $product_in_stock = '+';
		else $product_in_stock = '=';

		// the status increasing reserved stock(virtual Stock = product_in_stock - product_ordered)
		// $Reserved =  array('P','C');
		if ($StatutWhiteList[$newState]['order_stock_handle'] == 'R') $isReserved = 1;
		else $isReserved = 0;
		if ($StatutWhiteList[$oldState]['order_stock_handle'] == 'R') $wasReserved = 1;
		else $wasReserved = 0;

		if ($isReserved && !$wasReserved )     $product_ordered = '+';
		else if (!$isReserved && $wasReserved ) $product_ordered = '-';
		else $product_ordered = '=';

		//Here trigger plgVmGetProductStockToUpdateByCustom
		$productModel = VmModel::getModel('product');

		if (!empty($tableOrderItems->product_attribute)) {
			if(!class_exists('VirtueMartModelCustomfields'))require(VMPATH_ADMIN.DS.'models'.DS.'customfields.php');
			$virtuemart_product_id = $tableOrderItems->virtuemart_product_id;
			$product_attributes = json_decode($tableOrderItems->product_attribute,true);
			foreach ($product_attributes as $virtuemart_customfield_id=>$param){
				if ($param) {
					if(is_array($param)){
						reset($param);
						$customfield_id = key($param);
					} else {
						$customfield_id = $param;
					}			
		
					if ($customfield_id) {
						if ($productCustom = VirtueMartModelCustomfields::getCustomEmbeddedProductCustomField ($customfield_id ) ) {
							if ($productCustom->field_type == "E") {
								if(!class_exists('vmCustomPlugin')) require(VMPATH_PLUGINLIBS.DS.'vmcustomplugin.php');
								JPluginHelper::importPlugin('vmcustom');
								$dispatcher = JDispatcher::getInstance();
								$dispatcher->trigger('plgVmGetProductStockToUpdateByCustom',array(&$tableOrderItems,$param, $productCustom));
							}
						}
					}
				}
			}

			// we can have more then one product in case of pack
			// in case of child, ID must be the child ID
			// TO DO use $prod->amount change for packs(eg. 1 computer and 2 HDD)
			if (is_array($tableOrderItems))	foreach ($tableOrderItems as $prod ) $productModel->updateStockInDB($prod, $quantity,$product_in_stock,$product_ordered);
			else $productModel->updateStockInDB($tableOrderItems, $quantity,$product_in_stock,$product_ordered);

		} else {
			$productModel->updateStockInDB ($tableOrderItems, $quantity,$product_in_stock,$product_ordered);
		}

	}

	/**
	 * Create the ordered item records
	 *
	 * @author Oscar van Eijk
	 * @author Kohl Patrick
	 * @param integer $_id integer Order ID
	 * @param object $_cart array The cart data
	 * @return boolean True on success
	 */
	private function _createOrderLines($virtuemart_order_id, $cart)
	{
		$_orderItems = $this->getTable('order_items');

		foreach ($cart->products  as $priceKey=>$product) {

			$_orderItems->product_attribute = vmJsApi::safe_json_encode($product->customProductData);

			$_orderItems->virtuemart_order_item_id = null;
			$_orderItems->virtuemart_order_id = $virtuemart_order_id;

			$_orderItems->virtuemart_vendor_id = $product->virtuemart_vendor_id;
			$_orderItems->virtuemart_product_id = $product->virtuemart_product_id;
			$_orderItems->order_item_sku = $product->product_sku;
			$_orderItems->order_item_name = $product->product_name;
			$_orderItems->product_quantity = $product->quantity;
			$_orderItems->product_item_price = $product->allPrices[$product->selectedPrice]['basePriceVariant'];
			$_orderItems->product_basePriceWithTax = $product->allPrices[$product->selectedPrice]['basePriceWithTax'];

			//$_orderItems->product_tax = $_cart->pricesUnformatted[$priceKey]['subtotal_tax_amount'];
			$_orderItems->product_tax = $product->allPrices[$product->selectedPrice]['taxAmount'];
			$_orderItems->product_final_price = $product->allPrices[$product->selectedPrice]['salesPrice'];
			$_orderItems->product_subtotal_discount = $product->allPrices[$product->selectedPrice]['subtotal_discount'];
			$_orderItems->product_subtotal_with_tax =  $product->allPrices[$product->selectedPrice]['subtotal_with_tax'];
			$_orderItems->product_priceWithoutTax = $product->allPrices[$product->selectedPrice]['priceWithoutTax'];
			$_orderItems->product_discountedPriceWithoutTax = $product->allPrices[$product->selectedPrice]['discountedPriceWithoutTax'];
			$_orderItems->order_status = 'P';
			if (!$_orderItems->check()) {
				return false;
			}

			// Save the record to the database
			if (!$_orderItems->store()) {
				return false;
			}
			$product->virtuemart_order_item_id = $_orderItems->virtuemart_order_item_id;

			$this->handleStockAfterStatusChangedPerProduct( $_orderItems->order_status,'N',$_orderItems,$_orderItems->product_quantity);

		}

		return true;

	}
/**
	 * Create the ordered item records
	 *
	 * @author Valerie Isaksen
	 * @param integer $_id integer Order ID
	 * @param object $_cart array The cart data
	 * @return boolean True on success
	 */
	private function _createOrderCalcRules($order_id, $_cart)
	{

		$productKeys = array_keys($_cart->products);

		$calculation_kinds = array('DBTax','Tax','VatTax','DATax','Marge');

		foreach($productKeys as $key){
			foreach($calculation_kinds as $calculation_kind) {

				if(!isset($_cart->cartPrices[$key][$calculation_kind])) continue;
				$productRules = $_cart->cartPrices[$key][$calculation_kind];

				foreach($productRules as $rule){
					if(empty($rule[4])){
						continue;
					}
					$orderCalcRules = $this->getTable('order_calc_rules');
					$orderCalcRules->virtuemart_order_calc_rule_id= null;
					$orderCalcRules->virtuemart_calc_id= $rule[7];
					$orderCalcRules->virtuemart_order_item_id = $_cart->products[$key]->virtuemart_order_item_id;
					$orderCalcRules->calc_rule_name = $rule[0];
					$orderCalcRules->calc_amount =  0;
					$orderCalcRules->calc_result =  0;
					if ($calculation_kind == 'VatTax') {
						$orderCalcRules->calc_amount =  $_cart->cartPrices[$key]['taxAmount'];
						$orderCalcRules->calc_result =  $_cart->cartData['VatTax'][$rule[7]]['result'];
					}
					$orderCalcRules->calc_value = $rule[1];
					$orderCalcRules->calc_mathop = $rule[2];
					$orderCalcRules->calc_kind = $calculation_kind;
					$orderCalcRules->calc_currency = $rule[4];
					$orderCalcRules->calc_params = $rule[5];
					$orderCalcRules->virtuemart_vendor_id = $rule[6];
					$orderCalcRules->virtuemart_order_id = $order_id;

					if (!$orderCalcRules->check()) {
						vmdebug('_createOrderCalcRules check product rule ',$this);
						return false;
					}

					// Save the record to the database
					if (!$orderCalcRules->store()) {
						vmdebug('_createOrderCalcRules store product rule ',$this);
						return false;
					}
				}

			}
		}


		$Bill_calculation_kinds=array('DBTaxRulesBill', 'taxRulesBill', 'DATaxRulesBill');

		foreach($Bill_calculation_kinds as $calculation_kind) {

		    foreach($_cart->cartData[$calculation_kind] as $rule){
			    $orderCalcRules = $this->getTable('order_calc_rules');
			     $orderCalcRules->virtuemart_order_calc_rule_id = null;
				 $orderCalcRules->virtuemart_calc_id= $rule['virtuemart_calc_id'];
			     $orderCalcRules->calc_rule_name= $rule['calc_name'];
			     $orderCalcRules->calc_amount =  $_cart->cartPrices[$rule['virtuemart_calc_id'].'Diff'];
				 if ($calculation_kind == 'taxRulesBill' and !empty($_cart->cartData['VatTax'][$rule['virtuemart_calc_id']]['result'])) {
					$orderCalcRules->calc_result =  $_cart->cartData['VatTax'][$rule['virtuemart_calc_id']]['result'];
				 }
			     $orderCalcRules->calc_kind=$calculation_kind;
			     $orderCalcRules->calc_currency=$rule['calc_currency'];
			     $orderCalcRules->calc_value=$rule['calc_value'];
			     $orderCalcRules->calc_mathop=$rule['calc_value_mathop'];
			     $orderCalcRules->virtuemart_order_id=$order_id;
			     $orderCalcRules->calc_params=$rule['calc_params'];
				 $orderCalcRules->virtuemart_vendor_id = $rule['virtuemart_vendor_id'];
			     if (!$orderCalcRules->check()) {
				    return false;
			    }

			    // Save the record to the database
			    if (!$orderCalcRules->store()) {
				    return false;
			    }
		    }
		}

		if(!empty($_cart->virtuemart_paymentmethod_id)){

			if(empty($_cart->cartPrices['payment_calc_id'])){

			} else {
				$orderCalcRules = $this->getTable('order_calc_rules');
				$calcModel = VmModel::getModel('calc');
				$calcModel->setId($_cart->cartPrices['payment_calc_id']);
				$calc = $calcModel->getCalc();
				if(empty($calc->calc_currency) or empty($calc->calc_value)) {

				} else {
					$orderCalcRules->virtuemart_order_calc_rule_id = null;
					$orderCalcRules->virtuemart_calc_id = $calc->virtuemart_calc_id;
					$orderCalcRules->calc_kind = 'payment';
					$orderCalcRules->calc_rule_name = $calc->calc_name;
					$orderCalcRules->calc_amount = $_cart->cartPrices['paymentTax'];
					$orderCalcRules->calc_value = $calc->calc_value;
					$orderCalcRules->calc_mathop = $calc->calc_value_mathop;
					$orderCalcRules->calc_currency = $calc->calc_currency;
					$orderCalcRules->calc_params = $calc->calc_params;
					$orderCalcRules->virtuemart_vendor_id = $calc->virtuemart_vendor_id;
					$orderCalcRules->virtuemart_order_id = $order_id;
					if (!$orderCalcRules->check()) {

					} else {
						// Save the record to the database
						if (!$orderCalcRules->store()) {
							return false;
						}
					}
				}
			}
		}

		if(!empty($_cart->virtuemart_shipmentmethod_id)){

			if(empty($_cart->cartPrices['shipment_calc_id'])){

			} else {
				$orderCalcRules = $this->getTable('order_calc_rules');
				$calcModel = VmModel::getModel('calc');
				$calcModel->setId($_cart->cartPrices['shipment_calc_id']);
				$calc = $calcModel->getCalc();
				if(empty($calc->calc_currency) or empty($calc->calc_value)) {

				} else {
					$orderCalcRules->virtuemart_order_calc_rule_id = null;
					$orderCalcRules->virtuemart_calc_id = $calc->virtuemart_calc_id;
					$orderCalcRules->calc_kind = 'shipment';
					$orderCalcRules->calc_rule_name = $calc->calc_name;
					$orderCalcRules->calc_amount = $_cart->cartPrices['shipmentTax'];
					$orderCalcRules->calc_value = $calc->calc_value;
					$orderCalcRules->calc_mathop = $calc->calc_value_mathop;
					$orderCalcRules->calc_currency = $calc->calc_currency;
					$orderCalcRules->calc_params = $calc->calc_params;
					$orderCalcRules->virtuemart_vendor_id = $calc->virtuemart_vendor_id;
					$orderCalcRules->virtuemart_order_id = $order_id;
					if (!$orderCalcRules->check()) {
						return false;
					} else {
						// Save the record to the database
						if (!$orderCalcRules->store()) {
							return false;
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Update the order history
	 *
	 * @author Oscar van Eijk
	 * @param $_id Order ID
	 * @param $_status New order status (default: P)
	 * @param $_notified 1 (default) if the customer was notified, 0 otherwise
	 * @param $_comment (Customer) comment, default empty
	 */
	public function _updateOrderHist($_id, $_status = 'P', $_notified = 0, $_comment = '')
	{
		$_orderHist = $this->getTable('order_histories');
		$_orderHist->virtuemart_order_id = $_id;
		$_orderHist->order_status_code = $_status;
		//$_orderHist->date_added = date('Y-m-d G:i:s', time());
		$_orderHist->customer_notified = $_notified;
		$_orderHist->comments = nl2br($_comment);
		$_orderHist->store();
	}

	/**
	 * Update the order item history
	 *
	 * @author Oscar van Eijk,kohl patrick
	 * @param $_id Order ID
	 * @param $_status New order status (default: P)
	 * @param $_notified 1 (default) if the customer was notified, 0 otherwise
	 * @param $_comment (Customer) comment, default empty
	 */
	private function _updateOrderItemHist($_id, $status = 'P', $notified = 1, $comment = '')
	{
		$_orderHist = $this->getTable('order_item_histories');
		$_orderHist->virtuemart_order_item_id = $_id;
		$_orderHist->order_status_code = $status;
		$_orderHist->customer_notified = $notified;
		$_orderHist->comments = $comment;
		$_orderHist->store();
	}

	/**
	 * Creates a standard order password
	 */
	 static public function genStdOrderPass(){
		if(!class_exists('vmCrypt'))
			require(VMPATH_ADMIN.DS.'helpers'.DS.'vmcrypt.php');
		 $chrs = "ABCDEFGHJKLMNPQRSTUVWXYZ";
		 $chrs.= "abcdefghijkmnopqrstuvwxyz";
		 $chrs.= "123456789";
		return 'p_'.vmCrypt::getToken(VmConfig::get('randOrderPw',8),$chrs);
	 }

	static public function genStdCreateInvoicePass(){
		if(!class_exists('vmCrypt'))
			require(VMPATH_ADMIN.DS.'helpers'.DS.'vmcrypt.php');
		return vmCrypt::getToken(8);
	}

	/**
	 * Generate a unique ordernumber using getHumanToken, which is a random token
	 * with only upper case chars and without 0 and O to prevent missreadings
	 * @author Max Milbers
	 * @param integer $virtuemart_vendor_id For the correct count
	 * @return string A unique ordernumber
	 */
	static public function genStdOrderNumber($virtuemart_vendor_id=1, $length = 4){

		$db = JFactory::getDBO();

		$q = 'SELECT COUNT(1) FROM #__virtuemart_orders WHERE `virtuemart_vendor_id`="'.$virtuemart_vendor_id.'"';
		$db->setQuery($q);

		//We can use that here, because the order_number is free to set, the invoice_number must often follow special rules
		$c = $db->loadResult();
		$c = $c + (int)VM_ORDER_OFFSET;

		if(!class_exists('vmCrypt'))
			require(VMPATH_ADMIN.DS.'helpers'.DS.'vmcrypt.php');
		$str = vmCrypt::getHumanToken(VmConfig::get('randOrderNr',$length)).'0'.$c;

		return $str;
	}

	/**
	 * Generate a unique ordernumber. This is done in a similar way as VM1.1.x, although
	 * the reason for this is unclear to me :-S
	 * @deprecated
	 * @param integer $uid The user ID. Defaults to 0 for guests
	 * @return string A unique ordernumber
	 */
	static public function generateOrderNumber($uid = 0,$length=4, $virtuemart_vendor_id=1) {
		return self::genStdOrderNumber($virtuemart_vendor_id, $length);
	}

/*
 * returns true if an invoice number has been created
 * returns false if an invoice number has not been created  due to some configuration parameters
 */
	function createInvoiceNumber($orderDetails, &$invoiceNumber){

		$orderDetails = (array)$orderDetails;
		$db = JFactory::getDBO();
		if(!isset($orderDetails['virtuemart_order_id'])){
			vmWarn('createInvoiceNumber $orderDetails has no virtuemart_order_id ',$orderDetails);
			vmdebug('createInvoiceNumber $orderDetails has no virtuemart_order_id ',$orderDetails);
		}
		$q = 'SELECT * FROM `#__virtuemart_invoices` WHERE `virtuemart_order_id`= "'.$orderDetails['virtuemart_order_id'].'" '; // AND `order_status` = "'.$orderDetails->order_status.'" ';

		$db->setQuery($q);
		$result = $db->loadAssoc();

		if (!class_exists('ShopFunctions')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'shopfunctions.php');
		if(!$result or empty($result['invoice_number']) ){

			$data['virtuemart_order_id'] = $orderDetails['virtuemart_order_id'];

			$data['order_status'] = $orderDetails['order_status'];

			$data['virtuemart_vendor_id'] = $orderDetails['virtuemart_vendor_id'];

			JPluginHelper::importPlugin('vmshopper');
			JPluginHelper::importPlugin('vmpayment');
			JPluginHelper::importPlugin('vmextended');
			$dispatcher = JDispatcher::getInstance();
			// plugin returns invoice number, 0 if it does not want an invoice number to be created by Vm
			$plg_datas = $dispatcher->trigger('plgVmOnUserInvoice',array($orderDetails,&$data));

			if(!isset($data['invoice_number']) ) {
			    // check the default configuration
			    $orderstatusForInvoice = VmConfig::get('inv_os',array('C'));
				if(!is_array($orderstatusForInvoice)) $orderstatusForInvoice = array($orderstatusForInvoice); //for backward compatibility 2.0.8e
			    $pdfInvoice = (int)VmConfig::get('pdf_invoice', 0); // backwards compatible
			    $force_create_invoice=vRequest::getCmd('create_invoice', -1);
			    // florian : added if pdf invoice are enabled

			    if ( in_array($orderDetails['order_status'],$orderstatusForInvoice)  or $pdfInvoice==1  or $force_create_invoice==$orderDetails['order_create_invoice_pass'] ){
					$q = 'SELECT COUNT(1) FROM `#__virtuemart_invoices` WHERE `virtuemart_vendor_id`= "'.$orderDetails['virtuemart_vendor_id'].'" '; // AND `order_status` = "'.$orderDetails->order_status.'" ';
					$db->setQuery($q);

					$count = $db->loadResult()+1;

					if(empty($data['invoice_number'])) {
						$date = date("Y-m-d");
						if(!class_exists('vmCrypt'))
							require(VMPATH_ADMIN.DS.'helpers'.DS.'vmcrypt.php');
						$data['invoice_number'] = str_replace('-', '', substr($date,2,8)).vmCrypt::getHumanToken(4).'0'.$count;
					}
			    } else {
					return false;
			    }
			}

			$table = $this->getTable('invoices');

			$table->bindChecknStore($data);
			$invoiceNumber= array($table->invoice_number,$table->created_on);
		} elseif (ShopFunctions::InvoiceNumberReserved($result['invoice_number']) ) {
			$invoiceNumber = array($result['invoice_number'],$result['created_on']);
		    return true;
		} else {
			$invoiceNumber = array($result['invoice_number'],$result['created_on']);
		}
		return true;
	}

	/*
	 * @author Valérie Isaksen
	 */
	function getInvoiceNumber($virtuemart_order_id){

		$db = JFactory::getDBO();
		$q = 'SELECT `invoice_number` FROM `#__virtuemart_invoices` WHERE `virtuemart_order_id`= "'.$virtuemart_order_id.'" ';
		$db->setQuery($q);
		return $db->loadresult();
	}


	/**
	 * Notifies the customer that the Order Status has been changed
	 *
	 * @author Christopher Roussel, Valérie Isaksen, Max Milbers
	 *
	 */
	public function notifyCustomer($virtuemart_order_id, $newOrderData = 0 ) {

		if (isset($newOrderData['customer_notified']) && $newOrderData['customer_notified']==0) {
		    return true;
		}
		if(!class_exists('shopFunctionsF')) require(VMPATH_SITE.DS.'helpers'.DS.'shopfunctionsf.php');

		//Important, the data of the order update mails, payments and invoice should
		//always be in the database, so using getOrder is the right method
		$orderModel = VmModel::getModel('orders');
		$order = $orderModel->getOrder($virtuemart_order_id,true);

		$payment_name = $shipment_name='';
		if (!class_exists('vmPSPlugin')) require(VMPATH_PLUGINLIBS . DS . 'vmpsplugin.php');

		JPluginHelper::importPlugin('vmshipment');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmOnShowOrderFEShipment',array(  $order['details']['BT']->virtuemart_order_id, $order['details']['BT']->virtuemart_shipmentmethod_id, &$shipment_name));
		$returnValues = $dispatcher->trigger('plgVmOnShowOrderFEPayment',array(  $order['details']['BT']->virtuemart_order_id, $order['details']['BT']->virtuemart_paymentmethod_id, &$payment_name));
		$order['shipmentName']=$shipment_name;
		$order['paymentName']=$payment_name;
		if($newOrderData!=0){	//We do not really need that
			$vars['newOrderData'] = (array)$newOrderData;
		}

		$vars['orderDetails']=$order;

		//$vars['includeComments'] = vRequest::getVar('customer_notified', array());
		//I think this is misleading, I think it should always ask for example $vars['newOrderData']['doVendor'] directly
		//Using this function garantue us that it is always there. If the vendor should be informed should be done by the plugins
		//We may add later something to the method, defining this better
		$vars['url'] = 'url';
		if(!isset($newOrderData['doVendor'])) $vars['doVendor'] = false; else $vars['doVendor'] = $newOrderData['doVendor'];

		$virtuemart_vendor_id = $order['details']['BT']->virtuemart_vendor_id;
		$vendorModel = VmModel::getModel('vendor');
		$vendor = $vendorModel->getVendor($virtuemart_vendor_id);
		$vars['vendor'] = $vendor;
		$vendorEmail = $vendorModel->getVendorEmail($virtuemart_vendor_id);
		$vars['vendorEmail'] = $vendorEmail;

		// florian : added if pdf invoice are enabled
		$invoiceNumberDate = array();
		if ($orderModel->createInvoiceNumber($order['details']['BT'], $invoiceNumberDate )) {
			$orderstatusForInvoice = VmConfig::get('inv_os',array('C'));
			if(!is_array($orderstatusForInvoice)) $orderstatusForInvoice = array($orderstatusForInvoice);   // for backward compatibility 2.0.8e
			$pdfInvoice = (int)VmConfig::get('pdf_invoice', 0); // backwards compatible
			$force_create_invoice=vRequest::getInt('create_invoice', -1);
			//TODO we need an array of orderstatus
			if ( (in_array($order['details']['BT']->order_status,$orderstatusForInvoice))  or $pdfInvoice==1  or $force_create_invoice==1 ){
				if (!shopFunctions::InvoiceNumberReserved($invoiceNumberDate[0])) {
					if(!class_exists('VirtueMartControllerInvoice')) require( VMPATH_SITE.DS.'controllers'.DS.'invoice.php' );
					$controller = new VirtueMartControllerInvoice( array(
						'model_path' => VMPATH_SITE.DS.'models',
						'view_path' => VMPATH_SITE.DS.'views'
					));
					$vars['mediaToSend'][] = $controller->getInvoicePDF($order);
				}
			}

		}

		// Send the email
		$res = shopFunctionsF::renderMail('invoice', $order['details']['BT']->email, $vars, null,$vars['doVendor'],$this->useDefaultEmailOrderStatus);

		if(is_object($res) or !$res){
			$string = 'COM_VIRTUEMART_NOTIFY_CUSTOMER_ERR_SEND';
			vmdebug('notifyCustomer function shopFunctionsF::renderMail throws JException');
			$res = 0;
		} //We need this, to prevent that a false alert is thrown.
		else if ($res and $res!=-1) {
			$string = 'COM_VIRTUEMART_NOTIFY_CUSTOMER_SEND_MSG';
		}

		if($res!=-1){
			vmInfo( vmText::_($string,false).' '.$order['details']['BT']->first_name.' '.$order['details']['BT']->last_name. ', '.$order['details']['BT']->email);
		}

		//quicknDirty to prevent that an email is sent twice
		$app = JFactory::getApplication();
		if($app->isSite()){
			if (!class_exists('VirtueMartCart'))
				require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
			$cart = VirtueMartCart::getCart();
			$cart->customer_notified = true;
		}
		return true;
	}


	/**
	 * Retrieve the details for an order line item.
	 *
	 * @author RickG
	 * @param string $orderId Order id number
	 * @param string $orderLineId Order line item number
	 * @return object Object containing the order item details.
	 */
	function getOrderLineDetails($orderId, $orderLineId) {
		$table = $this->getTable('order_items');
		if ($table->load((int)$orderLineId)) {
			return $table;
		}
		else {
			$table->reset();
			$table->virtuemart_order_id = $orderId;
			return $table;
		}
	}


	/**
	 * Save an order line item.
	 *
	 * @author RickG
	 * @return boolean True of remove was successful, false otherwise
	 */
	function saveOrderLineItem($data) {

		if(!vmAccess::manager('orders.edit')) {
			return false;
		}

		$table = $this->getTable('order_items');

		//Done in the table already
		if (!class_exists('vmPSPlugin')) require(VMPATH_PLUGINLIBS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$_dispatcher = JDispatcher::getInstance();
		$_returnValues = $_dispatcher->trigger('plgVmOnUpdateOrderLineShipment',array( $data));
		foreach ($_returnValues as $_retVal) {
			if ($_retVal === false) {
				// Stop as soon as the first active plugin returned a failure status
				return;
			}
		}
		if (!class_exists('vmPSPlugin')) require(VMPATH_PLUGINLIBS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$_returnValues = $_dispatcher->trigger('plgVmOnUpdateOrderLinePayment',array( $data));
		foreach ($_returnValues as $_retVal) {
			if ($_retVal === false) {
				// Stop as soon as the first active plugin returned a failure status
				return;
			}
		}
		$table->bindChecknStore($data);
		return true;

	}


	/*
	 *remove product from order item table
	*@var $virtuemart_order_id Order to clear
	*/
	function removeOrderItems ($virtuemart_order_id){

		if(!vmAccess::manager('orders.edit')) {
			return false;
		}
		$q ='DELETE from `#__virtuemart_order_items` WHERE `virtuemart_order_id` = ' .(int) $virtuemart_order_id;
		$db = JFactory::getDBO();
		$db->setQuery($q);

		if ($db->execute() === false) {
			vmError($db->getError());
			return false;
		}
		return true;
	}

	/**
	 * Remove an order line item.
	 *
	 * @author RickG
	 * @param string $orderLineId Order line item number
	 * @return boolean True of remove was successful, false otherwise
	 */
	function removeOrderLineItem($orderLineId) {

		if(!vmAccess::manager('orders.edit')) {
			return false;
		}

		$item = $this->getTable('order_items');
		if (!$item->load($orderLineId)) {
			return false;
		}

		$this->handleStockAfterStatusChangedPerProduct('X', $item->order_status, $item,$item->product_quantity);

		//TODO Why should the stock change, when the order is deleted? Paypal? Valerie?
		if ($item->delete($orderLineId)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Delete all record ids selected
	 *
	 * @author Max Milbers
	 * @author Patrick Kohl
	 * @return boolean True is the delete was successful, false otherwise.
	 */
	public function remove($ids) {

		if(!vmAccess::manager('orders.edit')) {
			return false;
		}

		$table = $this->getTable($this->_maintablename);

		foreach($ids as $id) {
			$order = $this->getOrder($id);

			if(!empty($order['items'])){
				foreach($order['items'] as $it){
					$this->removeOrderLineItem($it->virtuemart_order_item_id);
				}
			}

			$this->removeOrderItems($id);

			$q = "DELETE FROM `#__virtuemart_order_histories`
			WHERE `virtuemart_order_id`=".$id;
			$this->_db->setQuery($q);
			$this->_db->execute();

			$q = "DELETE FROM `#__virtuemart_order_calc_rules`
			WHERE `virtuemart_order_id`=".$id;
			$this->_db->setQuery($q);
			$this->_db->execute();

			// rename invoice number by adding the date, and update the invoice table
			$this->renameInvoice($id );

			if (!$table->delete((int)$id)) {
				return false;
			}
		}
		return true;

	}


	/** Update order head record
	*
	* @author Ondřej Spilka
	* @author Maik Künnemann
	* @return boolean True is the update was successful, otherwise false.
	*/ 
	public function UpdateOrderHead($virtuemart_order_id, $_orderData) {

		if(!vmAccess::manager('orders.edit')){
			return false;
		}
		$orderTable = $this->getTable('orders');
		$orderTable->load($virtuemart_order_id);

		if (!$orderTable->bindChecknStore($_orderData, true)){
			return false;
		}

		$_userInfoData = array();

		if(!class_exists('VirtueMartModelUserfields')) require(VMPATH_ADMIN.DS.'models'.DS.'userfields.php');

		$_userFieldsModel = VmModel::getModel('userfields');

		//bill to
		$_userFieldsCart = $_userFieldsModel->getUserFields('account'
			, array('delimiters'=>true, 'captcha'=>true)
			, array('username', 'password', 'password2', 'user_is_vendor')
			);

		$_userFieldsBT = $_userFieldsModel->getUserFields('cart'
			, array('delimiters'=>true, 'captcha'=>true)
			, array('username', 'password', 'password2', 'user_is_vendor')
		);

		$_userFieldsBT = array_merge((array)$_userFieldsBT,(array)$_userFieldsCart);

		foreach ($_userFieldsBT as $_fld) {
			$_name = $_fld->name;
			if(isset( $_orderData["BT_{$_name}"])){

				$_userInfoData[$_name] = $_orderData["BT_{$_name}"];
			}
		}

		$_userInfoData['virtuemart_order_id'] = $virtuemart_order_id;
		$_userInfoData['address_type'] = 'BT';

		$order_userinfosTable = $this->getTable('order_userinfos');
			$order_userinfosTable->load($virtuemart_order_id, 'virtuemart_order_id'," AND address_type='BT'");
		if (!$order_userinfosTable->bindChecknStore($_userInfoData, true)){
			return false;
		}

		//ship to
		$_userFieldsST = $_userFieldsModel->getUserFields('account'
			, array('delimiters'=>true, 'captcha'=>true)
			, array('username', 'password', 'password2', 'user_is_vendor')
			);

		$_userInfoData = array();
		foreach ($_userFieldsST as $_fld) {
			$_name = $_fld->name;
			if(isset( $_orderData["ST_{$_name}"])){

				$_userInfoData[$_name] = $_orderData["ST_{$_name}"];
			}
		}

		$_userInfoData['virtuemart_order_id'] = $virtuemart_order_id;
		$_userInfoData['address_type'] = 'ST';

		$order_userinfosTable = $this->getTable('order_userinfos');
			$order_userinfosTable->load($virtuemart_order_id, 'virtuemart_order_id'," AND address_type='ST'");
		if (!$order_userinfosTable->bindChecknStore($_userInfoData, true)){
			return false;
		}

		$orderModel = VmModel::getModel('orders');
		$order = $orderModel->getOrder($virtuemart_order_id);

		$dispatcher = JDispatcher::getInstance();

		if (!class_exists ('CurrencyDisplay')) {
			require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
		}

		// Update Payment Method
		if($_orderData['old_virtuemart_paymentmethod_id'] != $_orderData['virtuemart_paymentmethod_id']) {

			$db = JFactory::getDBO();
			$db->setQuery( 'SELECT `payment_element` FROM `#__virtuemart_paymentmethods` , `#__virtuemart_orders`
					WHERE `#__virtuemart_paymentmethods`.`virtuemart_paymentmethod_id` = `#__virtuemart_orders`.`virtuemart_paymentmethod_id` AND `virtuemart_order_id` = ' . $virtuemart_order_id );
			$paymentTable = '#__virtuemart_payment_plg_'. $db->loadResult();

			$db->setQuery("DELETE from `". $paymentTable ."` WHERE `virtuemart_order_id` = " . $virtuemart_order_id);
			if ($db->execute() === false) {
				vmError($db->getError());
				return false;
			} else {
				JPluginHelper::importPlugin('vmpayment');
			}

		}

		// Update Shipment Method

		if($_orderData['old_virtuemart_shipmentmethod_id'] != $_orderData['virtuemart_shipmentmethod_id']) {

			$db->setQuery( 'SELECT `shipment_element` FROM `#__virtuemart_shipmentmethods` , `#__virtuemart_orders`
					WHERE `#__virtuemart_shipmentmethods`.`virtuemart_shipmentmethod_id` = `#__virtuemart_orders`.`virtuemart_shipmentmethod_id` AND `virtuemart_order_id` = ' . $virtuemart_order_id );
			$shipmentTable = '#__virtuemart_shipment_plg_'. $db->loadResult();

			$db->setQuery("DELETE from `". $shipmentTable ."` WHERE `virtuemart_order_id` = " . $virtuemart_order_id);
			if ($db->execute() === false) {
				vmError($db->getError());
				return false;
			} else {
				JPluginHelper::importPlugin('vmshipment');
			}

		}

		if (!class_exists('VirtueMartCart'))
			require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart();
		$cart->virtuemart_paymentmethod_id = $_orderData['virtuemart_paymentmethod_id'];
		$cart->virtuemart_shipmentmethod_id = $_orderData['virtuemart_shipmentmethod_id'];

		$order['order_status'] = $order['details']['BT']->order_status;
		$order['customer_notified'] = 0;
		$order['comments'] = '';

		$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($cart, $order));

		return true;
	}


	/** Create empty order head record from admin only
	*
	* @author Ondřej Spilka
	* @return ID of the newly created order
	*/ 
	public function CreateOrderHead()
	{

		$usrid = 0;
		$_orderData = new stdClass();

		$_orderData->virtuemart_order_id = null;
		$_orderData->virtuemart_user_id = 0;
		$_orderData->virtuemart_vendor_id = 1; //TODO

		$_orderData->order_total = 0;
		$_orderData->order_salesPrice = 0;
		$_orderData->order_billTaxAmount = 0;
		$_orderData->order_billDiscountAmount = 0;
		$_orderData->order_discountAmount = 0;
		$_orderData->order_subtotal = 0;
		$_orderData->order_tax = 0;
		$_orderData->order_shipment = 0;
		$_orderData->order_shipment_tax = 0;
		$_orderData->order_payment = 0;
		$_orderData->order_payment_tax = 0;

		$_orderData->order_discount = 0;
		$_orderData->order_status = 'P';
		$_orderData->order_currency = $this->getVendorCurrencyId($_orderData->virtuemart_vendor_id);

		$_orderData->virtuemart_paymentmethod_id = vRequest::getInt('virtuemart_paymentmethod_id');
		$_orderData->virtuemart_shipmentmethod_id = vRequest::getInt('virtuemart_shipmentmethod_id');

		//$_orderData->customer_note = '';
		$_orderData->ip_address = $_SERVER['REMOTE_ADDR'];

		$_orderData->order_number ='';
		JPluginHelper::importPlugin('vmshopper');
		$dispatcher = JDispatcher::getInstance();
		$_orderData->order_number = $this->genStdOrderNumber($_orderData->virtuemart_vendor_id);
		$_orderData->order_pass = $this->genStdOrderPass();
		$_orderData->order_create_invoice_pass = $this->genStdCreateInvoicePass();

		$orderTable =  $this->getTable('orders');
		$orderTable -> bindChecknStore($_orderData);

		$db = JFactory::getDBO();
		$_orderID = $db->insertid();

		$_usr  = JFactory::getUser();
		if (!$this->_writeUserInfo($_orderID, $_usr, array())) {
			vmError('Problem writing user info to order');
		}

		$orderModel = VmModel::getModel('orders');
		$order= $orderModel->getOrder($_orderID);

		$dispatcher = JDispatcher::getInstance();

		JPluginHelper::importPlugin('vmcustom');
		JPluginHelper::importPlugin('vmshipment');
		JPluginHelper::importPlugin('vmpayment');
		if (!class_exists('VirtueMartCart'))
			require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart();
		$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($cart, $order));

		return $_orderID;
	}

	/** Rename Invoice  (when an order is deleted)
	 *
	 * @author Valérie Isaksen
	 * @author Max Milbers
	 * @param $order_id Id of the order
	 * @return boolean true if deleted successful, false if there was a problem
	 */
	function renameInvoice($order_id ) {

		$table = $this->getTable('invoices');
		$table->load($order_id,'virtuemart_order_id');
		if(empty($table->invoice_number)){
			return false;
		}
		if (!class_exists ('shopFunctionsF'))
			require(VMPATH_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');

		// rename invoice pdf file
		$path = shopFunctions::getInvoicePath(VmConfig::get('forSale_path',0));
		$name = shopFunctionsF::getInvoiceName($table->invoice_number);
		$invoice_name_src = $path.DS.$name.'.pdf';

		if(!file_exists($invoice_name_src)){
			// may be it was already deleted when changing order items
			$data['invoice_number'] = $table->invoice_number;
			//$data['invoice_number'] = $data['invoice_number'].' not found.';
		} else {
			$date = date("Ymd");
			// We change the invoice number in the invoice table only. The order's invoice number is not modified!
			$data['invoice_number'] = $table->invoice_number.'_'.$date.'_'.$table->order_status;
			// We the sanitized file name as the invoice number might contain strange characters like 2015/01.
			$invoice_name_dst = $path.DS.$name.'_deprecated'.$date.'_'.$table->order_status.'.pdf';

			if(!class_exists('JFile')) require(VMPATH_LIBS.DS.'joomla'.DS.'filesystem'.DS.'file.php');
			if (!JFile::move($invoice_name_src, $invoice_name_dst)) {
				vmError ('Could not rename Invoice '.$invoice_name_src.' to '. $invoice_name_dst );
			}
		}

		$table = $this->getTable('invoices');
		$table->bindChecknStore($data);

		return true;


	}
	/** Delete Invoice when an item is updated
	 *
	 * @author Valérie Isaksen
	 * @param $order_id Id of the order
	 * @return boolean true if deleted successful, false if there was a problem
	 */
	function deleteInvoice($order_id ) {

		return $this->renameInvoice($order_id);
	}

}

// No closing tag
