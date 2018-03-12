<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\LoadSignaturePad;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class LoadSignaturePad extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Load Signature Pad';
	static $group = array('utilities' => 'Utilities');
	
	var $defaults = array(
		'enabled' => 1,
	);

	function execute(&$form, $action_id){
		$config = !empty($form->actions_config[$action_id]) ? $form->actions_config[$action_id] : array();
		$config = new \GCore\Libs\Parameter($config);
		$doc = \GCore\Libs\Document::getInstance();

		$doc->_('jquery');
		$doc->addJsFile(\GCore\C::ext_url('chronoforms', 'admin').'actions/load_signature_pad/signature_pad.min.js');
		
		$doc->addJsCode('
			jQuery(document).ready(function($){
				var wrapper = $("canvas").closest(".m-signature-pad"),
					clearButton = wrapper.find("[data-action=clear]"),
					saveButton = wrapper.find("[data-action=save]"),
					canvas = wrapper.find("canvas").get(0),
					signaturePad;
				
				function resizeCanvas() {
					var ratio =  window.devicePixelRatio || 1;
					canvas.width = canvas.offsetWidth * ratio;
					canvas.height = canvas.offsetHeight * ratio;
					canvas.getContext("2d").scale(ratio, ratio);
				}
				
				window.onresize = resizeCanvas;
				resizeCanvas();
				
				signaturePad = new SignaturePad(canvas, {
					"onEnd": function(){
						wrapper.find("input[type=hidden]").val(signaturePad.toDataURL());
					},
				});
				
				clearButton.on("click", function (event) {
					signaturePad.clear();
				});
				
				if(wrapper.find("input[type=hidden]").val()){
					signaturePad.fromDataURL(wrapper.find("input[type=hidden]").val());
				}
			});'
		);
		
		
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config load_signature_pad_action_config', 'load_signature_pad_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();

		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][enabled]', array('type' => 'dropdown', 'label' => l_('CF_ENABLED'), 'options' => array(0 => l_('NO'), 1 => l_('YES'))));
		
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}