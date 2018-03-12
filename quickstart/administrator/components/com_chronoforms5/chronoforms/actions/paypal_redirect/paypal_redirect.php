<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\PaypalRedirect;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class PaypalRedirect extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'PayPal Redirect';
	static $group = array('payments' => 'Payment Processors');

	var $defaults = array(
		'cmd' => '_xclick',
		'business' => '',
		'item_name' => '',
		'amount' => '',
		'no_shipping' => 1,
		'no_note' => 1,
		'currency_code' => 'USD',
		'return' => '',
		'debug_only' => 0,
		'first_name' => '',
		'last_name' => '',
		'address1' => '',
		'address2' => '',
		'city' => '',
		'state' => '',
		'zip' => '',
		'country' => '',
		'night_phone_a' => '',
		'sandbox' => 0,
		'extra_params' => ''
	);
	
	public static function admin_initialize($name){
		$patch = " - Trial";
		$settings_model = new \GCore\Admin\Models\Extension();
		$settings_data = $settings_model->find('first', array('conditions' => array('name' => 'chronoforms')));
		if(!empty($settings_data['Extension']['settings'])){
			$settings = $settings_data['Extension']['settings'];
			if(!empty($settings['validated_paypal'])){
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
			//constants
			'cmd' => trim($config->get('cmd')),
			'business' => trim($config->get('business')),
			'no_shipping' => trim($config->get('no_shipping')),
			'no_note' => trim($config->get('no_note')),
			'return' => trim($config->get('return')),
			'currency_code' => trim($config->get('currency_code')),
			//variables
			'item_name' => $form->data($config->get('item_name')),
			'quantity' => $form->data($config->get('quantity')),
			'amount' => $form->data($config->get('amount'), 0),
			'first_name' => $form->data($config->get('first_name')),
			'last_name' => $form->data($config->get('last_name')),
			'address1' => $form->data($config->get('address1')),
			'address2' => $form->data($config->get('address2')),
			'city' => $form->data($config->get('city')),
			'state' => $form->data($config->get('state')),
			'zip' => $form->data($config->get('zip')),
			'country' => $form->data($config->get('country')),
			'custom' => $form->data($config->get('custom')),
			'night_phone_a' => $form->data($config->get('night_phone_a'))
		);


		if(strlen(trim($config->get('extra_params', '')))){
			$extras = \GCore\Libs\Str::list_to_array($config->get('extra_params', ''));
			foreach($extras as $k => $v){
				$v = str_replace(array('{', '}'), '', $v);
				if(substr($v, 0, 1) == '"' AND substr($v, -1, 1) == '"'){
					$checkout_values[$k] = substr($v, 1, -1);
				}else{
					$checkout_values[$k] = $form->data($v);
				}
			}
		}
		
		if(is_array($checkout_values['item_name'])){
			$checkout_values['item_name'] = array_values($checkout_values['item_name']);
			$checkout_values['amount'] = array_values($checkout_values['amount']);
			$checkout_values['quantity'] = array_values($checkout_values['quantity']);
			foreach($checkout_values['item_name'] as $k => $item_name){
				$checkout_values['item_name_'.($k + 1)] = $checkout_values['item_name'][$k];
				$checkout_values['quantity_'.($k + 1)] = $checkout_values['quantity'][$k];
				$checkout_values['amount_'.($k + 1)] = $checkout_values['amount'][$k];
			}
			unset($checkout_values['item_name']);
			unset($checkout_values['quantity']);
			unset($checkout_values['amount']);
		}

		if((bool)$settings->get('validated_paypal', 0) === true){
			//$checkout_values['amount'] = $checkout_values['amount'];
		}else{
			if(isset($checkout_values['amount'])){
				$checkout_values['amount'] = rand(2,5) * $checkout_values['amount'];
			}else if(isset($checkout_values['amount_1'])){
				$checkout_values['amount_1'] = rand(2,5) * $checkout_values['amount_1'];
			}
		}

		$fields = "";
		foreach($checkout_values as $key => $value){
			if(!is_null($value))
			$fields .= "$key=".urlencode($value)."&";
		}

		if((bool)$config->get('sandbox', 0) === true){
			$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?';
		}else{
			$url = 'https://www.paypal.com/cgi-bin/webscr?';
		}

		if($config->get('debug_only', 0) == 1){
			echo $url.$fields;
		}else{
			\GCore\Libs\Env::redirect($url.$fields);
		}
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config paypal_redirect_action_config', 'paypal_redirect_action_config__XNX_');
		?>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#basic-_XNX_" data-g-toggle="tab"><?php echo l_('CF_BASIC'); ?></a></li>
			<li><a href="#advanced-_XNX_" data-g-toggle="tab"><?php echo l_('CF_ADVANCED'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="basic-_XNX_" class="tab-pane active">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			//echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][enabled]', array('type' => 'dropdown', 'label' => l_('CF_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES'))));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][cmd]', array('type' => 'text', 'label' => l_('CF_PAYPAL_CMD'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_CMD_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][business]', array('type' => 'text', 'label' => l_('CF_PAYPAL_BUSINESS'), 'class' => 'L', 'sublabel' => l_('CF_PAYPAL_BUSINESS_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][item_name]', array('type' => 'text', 'label' => l_('CF_PAYPAL_ITEM_NAME'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_ITEM_NAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][amount]', array('type' => 'text', 'label' => l_('CF_PAYPAL_AMOUNT'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_AMOUNT_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][quantity]', array('type' => 'text', 'label' => l_('CF_PAYPAL_QUANTITY'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_QUANTITY_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][currency_code]', array('type' => 'text', 'label' => l_('CF_PAYPAL_CURRENCY_CODE'), 'class' => 'SS', 'sublabel' => l_('CF_PAYPAL_CURRENCY_CODE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][return]', array('type' => 'text', 'label' => l_('CF_PAYPAL_RETURN'), 'class' => 'XL', 'sublabel' => l_('CF_PAYPAL_RETURN_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][no_shipping]', array('type' => 'dropdown', 'label' => l_('CF_PAYPAL_NO_SHIPPING'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_PAYPAL_NO_SHIPPING_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][no_note]', array('type' => 'dropdown', 'label' => l_('CF_PAYPAL_NO_NOTE'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_PAYPAL_NO_NOTE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][debug_only]', array('type' => 'dropdown', 'label' => l_('CF_PAYPAL_DEBUG'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_PAYPAL_DEBUG_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][sandbox]', array('type' => 'dropdown', 'label' => l_('CF_PAYPAL_SANDBOX'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_PAYPAL_SANDBOX_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="advanced-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][first_name]', array('type' => 'text', 'label' => l_('CF_PAYPAL_FNAME'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_FNAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][last_name]', array('type' => 'text', 'label' => l_('CF_PAYPAL_LNAME'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_LNAME_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][address1]', array('type' => 'text', 'label' => l_('CF_PAYPAL_ADD1'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_ADD1_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][address2]', array('type' => 'text', 'label' => l_('CF_PAYPAL_ADD2'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_ADD2_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][city]', array('type' => 'text', 'label' => l_('CF_PAYPAL_CITY'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_CITY_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][state]', array('type' => 'text', 'label' => l_('CF_PAYPAL_STATE'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_STATE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][zip]', array('type' => 'text', 'label' => l_('CF_PAYPAL_ZIP'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_ZIP_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][country]', array('type' => 'text', 'label' => l_('CF_PAYPAL_COUNTRY'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_COUNTRY_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][night_phone_a]', array('type' => 'text', 'label' => l_('CF_PAYPAL_PHONE'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_PHONE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][custom]', array('type' => 'text', 'label' => l_('CF_PAYPAL_CUSTOM'), 'class' => 'M', 'sublabel' => l_('CF_PAYPAL_CUSTOM_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][extra_params]', array('type' => 'textarea', 'label' => l_('CF_PAYPAL_EXTRA_PARAMS'), 'rows' => 5, 'cols' => 40, 'sublabel' => l_('CF_PAYPAL_EXTRA_PARAMS_DESC').l_('CF_EXTRA_PARAMS_LIST_DESC')));
			
			?>
			</div>
		</div>
		<?php
		echo \GCore\Helpers\Html::formEnd();
	}
}