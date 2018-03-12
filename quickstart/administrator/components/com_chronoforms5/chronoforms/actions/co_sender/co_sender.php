<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\CoSender;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class CoSender extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = '2Checkout Sender';
	static $group = array('payments' => 'Payment Processors');
	//var $events = array('approved' => 0, 'declined' => 0);
	//var $events_status = array('approved' => 'success', 'declined' => 'fail', 'error' => 'fail', 'held' => 'fail');

	var $defaults = array(
		'product_id' => '',
		'quantity' => '',
		'merchant_order_id' => '',
		'pay_method' => '',
		'coupon' => '',
		'card_holder_name' => '',
		'street_address' => '',
		'street_address2' => '',
		'city' => '',
		'state' => '',
		'zip' => '',
		'country' => '',
		'email' => '',
		'phone' => '',
		'lang' => '',
		'sid' => '',
		'demo' => '',
		'fixed' => '',
		'skip_landing' => '',
		'return_url' => '',
		'routine' => 'M',
		'x_Receipt_Link_URL' => '',
		'debug_only' => 0,
		'extra_params' => ''
	);
	
	public static function admin_initialize($name){
		$patch = " - Trial";
		$settings_model = new \GCore\Admin\Models\Extension();
		$settings_data = $settings_model->find('first', array('conditions' => array('name' => 'chronoforms')));
		if(!empty($settings_data['Extension']['settings'])){
			$settings = $settings_data['Extension']['settings'];
			if(!empty($settings['validated_2checkout'])){
				$patch = " - Full";
			}
		}
		self::$title = self::$title.$patch;
		parent::admin_initialize($name);
	}

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		$settings = new \GCore\Libs\Parameter($form->_settings());
		
		
		$checkout_values = array(
			'sid' => trim($config->get('sid')),
			'product_id' => $form->data($config->get('product_id'), ''),
			'quantity' => $form->data($config->get('quantity'), ''),
			//variables
			'merchant_order_id' => (isset($form->data[$config->get('merchant_order_id')]) ? $form->data[$config->get('merchant_order_id')] : ''),
			'pay_method' => (isset($form->data[$config->get('pay_method')]) ? $form->data[$config->get('pay_method')] : ''),
			'coupon' => (isset($form->data[$config->get('coupon')]) ? $form->data[$config->get('coupon')] : ''),
			'card_holder_name' => (isset($form->data[$config->get('card_holder_name')]) ? $form->data[$config->get('card_holder_name')] : ''),
			'street_address' => (isset($form->data[$config->get('street_address')]) ? $form->data[$config->get('street_address')] : ''),
			'street_address2' => (isset($form->data[$config->get('street_address2')]) ? $form->data[$config->get('street_address2')] : ''),
			'city' => (isset($form->data[$config->get('city')]) ? $form->data[$config->get('city')] : ''),
			'state' => (isset($form->data[$config->get('state')]) ? $form->data[$config->get('state')] : ''),
			'zip' => (isset($form->data[$config->get('zip')]) ? $form->data[$config->get('zip')] : ''),
			'country' => (isset($form->data[$config->get('country')]) ? $form->data[$config->get('country')] : ''),
			'email' => (isset($form->data[$config->get('email')]) ? $form->data[$config->get('email')] : ''),
			'phone' => (isset($form->data[$config->get('phone')]) ? $form->data[$config->get('phone')] : ''),
			'lang' => (isset($form->data[$config->get('lang')]) ? $form->data[$config->get('lang')] : ''),			
			//constants
			'demo' => trim($config->get('demo')),
			'fixed' => trim($config->get('fixed')),
			'skip_landing' => trim($config->get('skip_landing')),
			'return_url' => trim($config->get('return_url')),
			'purchase_step' => trim($config->get('purchase_step')) ? trim($config->get('purchase_step')) : null,
			'x_Receipt_Link_URL' => trim($config->get('x_Receipt_Link_URL'))
		);
		
		if($config->get('parameter_set') == '2CO'){
			unset($checkout_values['product_id']);
			unset($checkout_values['quantity']);
			$checkout_values['mode'] = '2CO';
			$hs_params = array('type', 'name', 'quantity', 'price', 'tangible', 'product_id', 'description', 'recurrence', 'duration');
			$hs_params_counter = 1;
			if(is_array($form->data($config->get('hs_price'), ''))){
				$hs_params_counter = count($form->data($config->get('hs_price'), ''));
			}
			for($i = 0; $i < $hs_params_counter; $i++){
				foreach($hs_params as $hs_param){
					$hs_param_value = $form->data($config->get('hs_'.$hs_param), '');
					$checkout_values['li_'.$i.'_'.$hs_param] = is_array($hs_param_value) ? $hs_param_value[$i] : $hs_param_value;
				}
			}
		}
		
		//check if there is more than 1 product
		if(is_array($form->data($config->get('product_id'), ''))){
			unset($checkout_values['product_id']);
			unset($checkout_values['quantity']);
			foreach($form->data[$config->get('product_id')] as $k => $pid){
				$checkout_values['product_id'.($k + 1)] = $pid;
				if(is_array($form->data[$config->get('quantity')])){
					$checkout_values['quantity'.($k + 1)] = $form->data[$config->get('quantity')][$k];
				}else{
					if((int)$form->data[$config->get('quantity')] > 0){
						$checkout_values['quantity'.($k + 1)] = (int)$form->data[$config->get('quantity')];
					}else{
						$checkout_values['quantity'.($k + 1)] = 1;
					}
				}
			}
		}
		
		if($config->get('extra_params')){
			$extras = explode("\n", $config->get('extra_params'));
			foreach($extras as $extra){
				$values = array();
				$values = explode("=", $extra);
				$checkout_values[$values[0]] = $form->data[trim($values[1])];
			}
			$extras = \GCore\Libs\Str::list_to_array($config->get('extra_params', ''));
			foreach($extras as $k => $v){
				$checkout_values[$k] = $form->data($v, '');
			}
		}
		
		if((bool)$settings->get('validated_2checkout', 0) === true){

		}else{
			if($config->get('parameter_set') == '2CO'){
				
			}else{
				if(isset($checkout_values['quantity'])){
					$checkout_values['quantity'] = rand(2,5)* (int)$checkout_values['quantity'];
				}else{
					$checkout_values['quantity1'] = rand(2,5)* (int)$checkout_values['quantity1'];
				}
			}
		}
		
		$checkout_values = array_filter($checkout_values);
		
		$fields = "";
		foreach($checkout_values as $key => $value){
			$fields .= "$key=".urlencode($value)."&";
		}
		
		if($config->get('routine', 'M') == 'M'){
			$url = 'https://www.2checkout.com/checkout/purchase?';
		}else{
			$url = 'https://www.2checkout.com/checkout/spurchase?';
		}
		
		if($config->get('debug_only', 0) == 1){
			echo $url.$fields;
		}else{
			\GCore\Libs\Env::redirect($url.$fields);
		}
	}
	
	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config co_sender_action_config', 'co_sender_action_config__XNX_');
		?>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#basic-_XNX_" data-g-toggle="tab"><?php echo l_('CF_BASIC'); ?></a></li>
			<li><a href="#hosted_checkout-_XNX_" data-g-toggle="tab"><?php echo l_('CF_2CO_PARAMETER_SET_HS'); ?></a></li>
			<li><a href="#customer_info-_XNX_" data-g-toggle="tab"><?php echo l_('CF_2CO_CUSTOMER_INFO'); ?></a></li>
			<li><a href="#advanced-_XNX_" data-g-toggle="tab"><?php echo l_('CF_ADVANCED'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="basic-_XNX_" class="tab-pane active">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][sid]', array('type' => 'text', 'label' => l_('CF_2CO_SID'), 'class' => 'L', 'sublabel' => l_('CF_2CO_SID_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][product_id]', array('type' => 'text', 'label' => l_('CF_2CO_PRODUCT_ID'), 'class' => 'M', 'sublabel' => l_('CF_2CO_PRODUCT_ID_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][quantity]', array('type' => 'text', 'label' => l_('CF_2CO_QUANTITY'), 'class' => 'L', 'sublabel' => l_('CF_2CO_QUANTITY_DESC')));
			
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][parameter_set]', array('type' => 'dropdown', 'label' => l_('CF_2CO_PARAMETER_SET'), 'options' => array('' => l_('CF_2CO_PARAMETER_SET_PNP'), '2CO' => l_('CF_2CO_PARAMETER_SET_HS')), 'sublabel' => l_('CF_2CO_PARAMETER_SET_DESC')));
			
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][skip_landing]', array('type' => 'dropdown', 'label' => l_('CF_2CO_SKIP_LANDING'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_2CO_SKIP_LANDING_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][routine]', array('type' => 'dropdown', 'label' => l_('CF_2CO_ROUTINE'), 'options' => array('M' => 'Multi Page (Default)', 'S' => 'Single Page'), 'sublabel' => l_('CF_2CO_ROUTINE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][extra_params]', array('type' => 'textarea', 'label' => l_('CF_2CO_EXTRA_PARAMS'), 'rows' => 5, 'cols' => 40, 'sublabel' => l_('CF_2CO_EXTRA_PARAMS_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][debug_only]', array('type' => 'dropdown', 'label' => l_('CF_2CO_DEBUG'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_2CO_DEBUG_DESC')));
			
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="hosted_checkout-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][hs_type]', array('type' => 'text', 'label' => l_('CF_2CO_HS_ITEM_TYPE'), 'class' => 'M', 'sublabel' => l_('CF_2CO_HS_ITEM_TYPE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][hs_name]', array('type' => 'text', 'label' => l_('CF_2CO_HS_ITEM_NAME'), 'class' => 'M', 'sublabel' => l_('CF_2CO_HS_ITEM_NAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][hs_quantity]', array('type' => 'text', 'label' => l_('CF_2CO_HS_ITEM_QUANTITY'), 'class' => 'M', 'sublabel' => l_('CF_2CO_HS_ITEM_QUANTITY_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][hs_price]', array('type' => 'text', 'label' => l_('CF_2CO_HS_ITEM_PRICE'), 'class' => 'M', 'sublabel' => l_('CF_2CO_HS_ITEM_PRICE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][hs_tangible]', array('type' => 'text', 'label' => l_('CF_2CO_HS_ITEM_TANGIBLE'), 'class' => 'M', 'sublabel' => l_('CF_2CO_HS_ITEM_TANGIBLE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][hs_product_id]', array('type' => 'text', 'label' => l_('CF_2CO_HS_ITEM_PID'), 'class' => 'M', 'sublabel' => l_('CF_2CO_HS_ITEM_PID_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][hs_description]', array('type' => 'text', 'label' => l_('CF_2CO_HS_ITEM_DESC'), 'class' => 'M', 'sublabel' => l_('CF_2CO_HS_ITEM_DESC_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][hs_recurrence]', array('type' => 'text', 'label' => l_('CF_2CO_HS_ITEM_RECURRENCE'), 'class' => 'M', 'sublabel' => l_('CF_2CO_HS_ITEM_RECURRENCE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][hs_duration]', array('type' => 'text', 'label' => l_('CF_2CO_HS_ITEM_DURATION'), 'class' => 'M', 'sublabel' => l_('CF_2CO_HS_ITEM_DURATION_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="customer_info-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][card_holder_name]', array('type' => 'text', 'label' => l_('CF_2CO_CARD_HOLDER_NAME'), 'class' => 'M', 'sublabel' => l_('CF_2CO_CARD_HOLDER_NAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][street_address]', array('type' => 'text', 'label' => l_('CF_2CO_ADDRESS'), 'class' => 'M', 'sublabel' => l_('CF_2CO_ADDRESS_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][street_address2]', array('type' => 'text', 'label' => l_('CF_2CO_ADDRESS2'), 'class' => 'M', 'sublabel' => l_('CF_2CO_ADDRESS2_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][city]', array('type' => 'text', 'label' => l_('CF_2CO_CITY'), 'class' => 'M', 'sublabel' => l_('CF_2CO_CITY_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][state]', array('type' => 'text', 'label' => l_('CF_2CO_STATE'), 'class' => 'M', 'sublabel' => l_('CF_2CO_STATE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][zip]', array('type' => 'text', 'label' => l_('CF_2CO_ZIP'), 'class' => 'M', 'sublabel' => l_('CF_2CO_ZIP_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][country]', array('type' => 'text', 'label' => l_('CF_2CO_COUNTRY'), 'class' => 'M', 'sublabel' => l_('CF_2CO_COUNTRY_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][phone]', array('type' => 'text', 'label' => l_('CF_2CO_PHONE'), 'class' => 'M', 'sublabel' => l_('CF_2CO_PHONE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][email]', array('type' => 'text', 'label' => l_('CF_2CO_EMAIL'), 'class' => 'M', 'sublabel' => l_('CF_2CO_EMAIL_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="advanced-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][demo]', array('type' => 'dropdown', 'label' => l_('CF_2CO_DEMO'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_2CO_DEMO_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][merchant_order_id]', array('type' => 'text', 'label' => l_('CF_2CO_MERCHANT_ORDER_ID'), 'class' => 'M', 'sublabel' => l_('CF_2CO_MERCHANT_ORDER_ID_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][lang]', array('type' => 'text', 'label' => l_('CF_2CO_LANG'), 'class' => 'M', 'sublabel' => l_('CF_2CO_LANG_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][x_Receipt_Link_URL]', array('type' => 'text', 'label' => l_('CF_2CO_RECEIPT_URL'), 'class' => 'L', 'sublabel' => l_('CF_2CO_RECEIPT_URL_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][fixed]', array('type' => 'text', 'label' => l_('CF_2CO_FIXED'), 'class' => 'L', 'sublabel' => l_('CF_2CO_FIXED_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][return_url]', array('type' => 'text', 'label' => l_('CF_2CO_RETURN_URL'), 'class' => 'L', 'sublabel' => l_('CF_2CO_RETURN_URL_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][pay_method]', array('type' => 'text', 'label' => l_('CF_2CO_PAY_METHOD'), 'class' => 'M', 'sublabel' => l_('CF_2CO_PAY_METHOD_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][coupon]', array('type' => 'text', 'label' => l_('CF_2CO_COUPON'), 'class' => 'M', 'sublabel' => l_('CF_2CO_COUPON_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][purchase_step]', array('type' => 'text', 'label' => l_('CF_2CO_PURCHASE_STEP'), 'class' => 'M', 'sublabel' => l_('CF_2CO_PURCHASE_STEP_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
		</div>
		<?php
		echo \GCore\Helpers\Html::formEnd();
	}
}