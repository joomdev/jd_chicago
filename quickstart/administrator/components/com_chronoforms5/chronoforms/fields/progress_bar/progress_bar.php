<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Fields\ProgressBar;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class ProgressBar {
	static $title = 'Progress Bar';
	static $cat_id = 'widgets';
	static $cat_title = 'Widgets';
	static $settings = array(
		'tag' => 'input',
		'type' => 'custom',
		'name' => 'progress_bar',
		'id' => 'progress_bar',
		//'label' => 'Progress Bar',
		'sublabel' => '',
		'class' => '',
		'title' => '',
		'pure_code' => 1,
		'code' => '',
		'bar_label' => '',
		'width' => 30,
	);
	
	static $configs = array(
		'width' => array('value' => '30', 'label' => 'Width in %', 'type' => 'text', 'class' => 'S', 'alt' => 'ghost'),
		'bar_label' => array('value' => '30% Complete', 'label' => 'Bar Label', 'type' => 'text', 'class' => 'L', 'alt' => 'ghost'),
		//'code' => array('value' => 'The custom element code should go here.', 'label' => 'Code', 'type' => 'textarea', 'id' => 'custom_field_code__XNX_', 'rows' => 15, 'cols' => 70, 'alt' => 'ghost', ':data-render' => 'no'),
	);
	
	public static function element($data = array()){
		echo \GCore\Helpers\Html::formSecStart('original_element', 'progress_bar_origin');
		echo '
			<div class="progress">
				<div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="'.$data["width"].'" aria-valuemin="0" aria-valuemax="100" style="width: '.$data["width"].'%">
				<span class="">'.$data["bar_label"].'</span>
				</div>
			</div>
		';
		echo \GCore\Helpers\Html::formSecEnd();
	}
	
	public static function config($data = array(), $k = '_XNX_'){
		echo \GCore\Helpers\Html::formStart('original_element_config single_element_config', 'progress_bar_origin_config');
		echo \GCore\Helpers\Html::formSecStart();
		foreach(self::$configs as $name => $params){
			$params['value'] = isset($data[$name]) ? ((in_array($params['type'], array('text', 'textarea'))) ? htmlspecialchars($data[$name]) : $data[$name]) : (isset($params['value']) ? $params['value'] : '');
			$params['values'] = isset($data[$name]) ? $data[$name] : (isset($params['values']) ? $params['values'] : '');
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.']['.$name.']', $params);
		}
		
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][code]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => '
			<div class="progress">
				<div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="<?php echo $form->form["Form"]["extras"]["fields"]['.$k.']["width"]; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $form->form["Form"]["extras"]["fields"]['.$k.']["width"]; ?>%">
				<span class=""><?php echo $form->form["Form"]["extras"]["fields"]['.$k.']["bar_label"]; ?></span>
				</div>
			</div>
		'));
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][pure_code]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => 1));
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][name]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => 'progress_bar'));
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][render_type]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => 'progress_bar'));
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][type]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => self::$settings['type']));
		echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][container_id]', array('type' => 'hidden', 'id' => 'container_id'.$k, 'value' => '0'));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}
?>