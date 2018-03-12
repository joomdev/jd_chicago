<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\CoListener;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class CoListener extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = '2Checkout Listener';
	static $group = array('payments' => 'Payment Processors');
	var $events = array('hack' => 0, 'new_order' => 0, 'fraud_status' => 0, 'refund' => 0, 'other' => 0);
	var $events_status = array('hack' => 'fail', 'new_order' => 'success', 'fraud_status' => 'success', 'refund' => 'success', 'other' => 'success');

	var $defaults = array(
		'sid' => '',
		'secret' => ''
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);

		$vendorid = $config->get('sid');
		$secretword = $config->get('secret');
		$md5hash = strtoupper(md5($form->data['sale_id'].$vendorid.$form->data['invoice_id'].$secretword));
		//if the hash is ok
		if($md5hash == $form->data['md5_hash']){
			//switch messages types
			switch($form->data['message_type']){
				case 'ORDER_CREATED':
					$this->events['new_order'] = 1;
					break;
				case 'FRAUD_STATUS_CHANGED':
					$this->events['fraud_status'] = 1;
					break;
				case 'REFUND_ISSUED':
					$this->events['refund'] = 1;
					break;
				default:
					$this->events['other'] = 1;
					break;
			}
		}else{
			$this->events['hack'] = 1;
		}
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config co_listener_action_config', 'co_listener_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][sid]', array('type' => 'text', 'label' => l_('CF_2CO_SID'), 'class' => 'M', 'sublabel' => l_('CF_2CO_SID_DESC')));
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][secret]', array('type' => 'text', 'label' => l_('CF_2CO_SECRET'), 'class' => 'M', 'sublabel' => l_('CF_2CO_SECRET_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}