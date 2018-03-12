<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Fields\SignaturePad;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class SignaturePad {
	static $title = 'Signature Pad';
	static $cat_id = 'widgets';
	static $cat_title = 'Widgets';
	static $settings = array(
		'tag' => 'input',
		'type' => 'custom',
		'name' => 'signature_pad',
		'id' => 'signature_pad',
		'label' => 'Please sign',
		'sublabel' => '',
		'class' => '',
		'title' => '',
		'pure_code' => 0,
		'code' => '',
		'wrapper_id' => 'signature-pad',
		'width' => 400,
		'height' => 150,
		'clear_text' => 'Clear',
		'save_text' => 'Save',
		'field_name' => 'signature',
	);
	
	static $configs = array(
		'label' => array('value' => 'Please sign', 'label' => 'Label', 'type' => 'text', 'class' => 'L', 'alt' => 'ghost'),
		'wrapper_id' => array('value' => 'signature-pad', 'label' => 'Wrapper ID', 'type' => 'text', 'class' => 'L', 'alt' => 'ghost'),
		'width' => array('value' => '400', 'label' => 'Width', 'type' => 'text', 'class' => 'S', 'alt' => 'ghost'),
		'height' => array('value' => '150', 'label' => 'Height', 'type' => 'text', 'class' => 'S', 'alt' => 'ghost'),
		'clear_text' => array('value' => 'Clear', 'label' => 'Clear text', 'type' => 'text', 'class' => 'S', 'alt' => 'ghost'),
		//'save_text' => array('value' => 'Save', 'label' => 'Save text', 'type' => 'text', 'class' => 'S', 'alt' => 'ghost'),
		'field_name' => array('value' => 'signature', 'label' => 'Field name', 'type' => 'text', 'class' => 'L', 'alt' => 'ghost'),
	);
	
	public static function element($data = array()){
		echo \GCore\Helpers\Html::formSecStart('original_element', 'signature_pad_origin');
		echo '
			<div id="'.$data["wrapper_id"].'" class="m-signature-pad">
				<div class="m-signature-pad--body">
					<canvas width="'.$data["width"].'" height="'.$data["height"].'"></canvas>
				</div>
				<div class="m-signature-pad--footer">
					<button type="button" class="button clear" data-action="clear">'.$data["clear_text"].'</button>
				</div>
				<input type="hidden" name="'.$data["field_name"].'" value="" />
			</div>
		';
		echo \GCore\Helpers\Html::formSecEnd();
	}
	
	public static function config($data = array(), $k = '_XNX_'){
		echo \GCore\Helpers\Html::formStart('original_element_config single_element_config', 'signature_pad_origin_config');
		echo \GCore\Helpers\Html::formSecStart();
		foreach(self::$configs as $name => $params){
			$params['value'] = isset($data[$name]) ? ((in_array($params['type'], array('text', 'textarea'))) ? htmlspecialchars($data[$name]) : $data[$name]) : (isset($params['value']) ? $params['value'] : '');
			$params['values'] = isset($data[$name]) ? $data[$name] : (isset($params['values']) ? $params['values'] : '');
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.']['.$name.']', $params);
		}
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][code]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => '
			<div id="<?php echo $form->form["Form"]["extras"]["fields"]['.$k.']["wrapper_id"]; ?>" class="m-signature-pad">
				<div class="m-signature-pad--body">
					<canvas width="<?php echo $form->form["Form"]["extras"]["fields"]['.$k.']["width"]; ?>" height="<?php echo $form->form["Form"]["extras"]["fields"]['.$k.']["height"]; ?>"></canvas>
				</div>
				<div class="m-signature-pad--footer">
					<button type="button" class="button clear" data-action="clear"><?php echo $form->form["Form"]["extras"]["fields"]['.$k.']["clear_text"]; ?></button>
				</div>
				<input type="hidden" name="<?php echo $form->form["Form"]["extras"]["fields"]['.$k.']["field_name"]; ?>" value="" />
			</div>
		'));
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][pure_code]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => 0));
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][name]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => 'signature_pad'));
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][render_type]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => 'signature_pad'));
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][type]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => self::$settings['type']));
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][container_id]', array('type' => 'hidden', 'id' => 'container_id'.$k, 'value' => '0'));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}
?>