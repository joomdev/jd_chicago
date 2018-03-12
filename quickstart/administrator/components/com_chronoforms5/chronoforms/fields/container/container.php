<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Fields\Container;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class Container {
	static $title = 'Container';
	static $cat_id = 'advanced';
	static $cat_title = 'Advanced';
	static $settings = array(
		'tag' => 'input',
		'type' => 'container',
		'name' => 'container',
		'id' => 'container',
		'label' => '',
		'sublabel' => '',
		'class' => '',
		'title' => '',
		'code' => 'Edit to change the element content.',
	);

	static $configs = array(
		'label' => array('value' => 'Container #_XNX_', 'label' => 'Label', 'type' => 'text', 'class' => 'L', 'alt' => 'ghost', 'id' => 'container_label_config__XNX_'),
		'container_type' => array('value' => '', 'label' => 'Type', 'type' => 'dropdown', 'alt' => 'ghost', 'onclick' => 'container_enable_tab(this, \'_XNX_\')', 'id' => 'container_type_config__XNX_', 'options' => array(
			'' => 'None (Holder)',
			'page' => 'Page',
			'div' => 'DIV',
			'fieldset' => 'Field Set',
			'panel' => 'Panel',
			'tabs_area' => 'Tabs area',
			'pills_area' => 'Tabs area (Menus)',
			'tab' => 'Tab',
			'sliders_area' => 'Sliders area',
			'slider' => 'Slider',
			'multi_column' => 'Columns Container',
			'column' => 'Column (Resizable)',
			'custom' => 'Custom',
			'multiplier' => 'Multiplier',
			'multiplier-contents' => 'Multiplier Contents',
		)),
		'title' => array('value' => 'Container #_XNX_', 'label' => 'Title', 'type' => 'text', 'class' => 'L', 'alt' => 'ghost'),
		'id' => array('value' => 'chronoform-container-_XNX_', 'label' => 'ID', 'type' => 'text', 'class' => 'L', 'alt' => 'ghost'),
		'class' => array('value' => 'chronoform-container', 'label' => 'Class', 'type' => 'text', 'class' => 'L', 'alt' => 'ghost'),
		'start_code' => array('value' => '', 'label' => 'Start code', 'type' => 'textarea', 'cols' => 60, 'rows' => 5, 'alt' => 'ghost', 'sublabel' => 'The start code for a custom container.'),
		'end_code' => array('value' => '', 'label' => 'End code', 'type' => 'textarea', 'cols' => 60, 'rows' => 5, 'alt' => 'ghost', 'sublabel' => 'The end code for a custom container.'),
		'load-state' => array('label' => 'Load state', 'type' => 'dropdown', 'options' => array('' => 'Visible', 'hidden' => 'Hidden')),
	);

	public static function element($data = array()){
		echo \GCore\Helpers\Html::formSecStart('original_element', 'container_origin');
		echo \GCore\Helpers\Html::formLine(self::$settings['name'], array_merge(self::$settings, $data));
		echo \GCore\Helpers\Html::formSecEnd();
	}

	public static function config($data = array(), $k = '_XNX_'){
		echo \GCore\Helpers\Html::formStart('original_element_config', 'container_origin_config');
		?>
		<script>
			function container_enable_tab(elem, SID){
				if(jQuery.inArray(jQuery(elem).val(), ['multiplier']) != -1){
					jQuery(elem).closest('.config_box').find('.container-special-tab').css('display', 'none');
					jQuery('#container-multiplier-tab-'+SID).css('display', '');
				}else if(jQuery.inArray(jQuery(elem).val(), ['multiplier-contents']) != -1){
					jQuery(elem).closest('.config_box').find('.container-special-tab').css('display', 'none');
					jQuery('#container-multiplier-contents-tab-'+SID).css('display', '');
				}else if(jQuery.inArray(jQuery(elem).val(), ['column']) != -1){
					jQuery(elem).closest('.config_box').find('.container-special-tab').css('display', 'none');
					jQuery('#container-column-tab-'+SID).css('display', '');
				}else{
					jQuery(elem).closest('.config_box').find('.container-special-tab').css('display', 'none');
				}
			}
		</script>
		<ul class="nav nav-tabs">
			<li><a href="#general-<?php echo $k; ?>" data-g-toggle="tab"><?php echo l_('CF_GENERAL'); ?></a></li>
			<li class="container-special-tab" id="container-multiplier-tab-<?php echo $k; ?>" style="<?php echo (!empty($data['container_type']) AND in_array($data['container_type'], array('multiplier'))) ? '' : 'display:none;'; ?>"><a href="#multiplier-<?php echo $k; ?>" data-g-toggle="tab"><?php echo l_('CF_MULTIPLIER'); ?></a></li>
			<li class="container-special-tab" id="container-multiplier-contents-tab-<?php echo $k; ?>" style="<?php echo (!empty($data['container_type']) AND in_array($data['container_type'], array('multiplier-contents'))) ? '' : 'display:none;'; ?>"><a href="#multiplier-contents-<?php echo $k; ?>" data-g-toggle="tab"><?php echo l_('CF_MULTIPLIER_CONTENTS'); ?></a></li>
			<li class="container-special-tab" id="container-column-tab-<?php echo $k; ?>" style="<?php echo (!empty($data['container_type']) AND in_array($data['container_type'], array('column'))) ? '' : 'display:none;'; ?>"><a href="#column-<?php echo $k; ?>" data-g-toggle="tab"><?php echo l_('Column'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="general-<?php echo $k; ?>" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			foreach(self::$configs as $name => $params){
				$params['value'] = isset($data[$name]) ? (($params['type'] == 'text') ? htmlspecialchars($data[$name]) : $data[$name]) : (isset($params['value']) ? $params['value'] : '');
				$params['values'] = isset($data[$name]) ? $data[$name] : (isset($params['values']) ? $params['values'] : '');
				echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.']['.$name.']', str_replace('_XNX_', $k, $params));
			}

			//echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][code]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => 'My container code here'));
			echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][name]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => 'container'));
			echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][render_type]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => 'container'));
			echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][type]', array('type' => 'hidden', 'alt' => 'ghost', 'value' => self::$settings['type']));
			//echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][size][width]', array('type' => 'hidden', 'class' => 'fields_container_width', 'id' => 'fields_container_'.$k.'_width', 'value' => '99'));
			echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][collapsed]', array('type' => 'hidden', 'class' => 'fields_container_collapsed', 'id' => 'fields_container_'.$k.'_collapsed', 'value' => '0'));
			echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][container_id]', array('type' => 'hidden', 'id' => 'container_id'.$k, 'value' => '0'));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="multiplier-<?php echo $k; ?>" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][multiplier][replacer]', array('type' => 'text', 'value' => '0', 'label' => l_('CF_MULTIPLIER_REPLACER'), 'sublabel' => l_('CF_MULTIPLIER_REPLACER_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][multiplier][count]', array('type' => 'text', 'value' => '1', 'label' => l_('CF_MULTIPLIER_COUNT'), 'sublabel' => l_('CF_MULTIPLIER_COUNT_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][multiplier][hide_first]', array('type' => 'dropdown', 'options' => array(0 => 'No', 1 => 'Yes'), 'label' => l_('CF_MULTIPLIER_HIDE_FIRST'), 'sublabel' => l_('CF_MULTIPLIER_HIDE_FIRST_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][multiplier][disable_first]', array('type' => 'dropdown', 'options' => array(0 => 'No', 1 => 'Yes'), 'label' => l_('CF_MULTIPLIER_DISABLE_FIRST'), 'sublabel' => l_('CF_MULTIPLIER_DISABLE_FIRST_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][multiplier][hide_buttons]', array('type' => 'dropdown', 'options' => array(0 => 'No', 1 => 'Yes'), 'label' => l_('CF_MULTIPLIER_HIDE_BUTTONS'), 'sublabel' => l_('CF_MULTIPLIER_HIDE_BUTTONS_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][multiplier][start_count]', array('type' => 'text', 'value' => '', 'label' => l_('CF_MULTIPLIER_START_COUNT'), 'sublabel' => l_('CF_MULTIPLIER_START_COUNT_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][multiplier][data_path]', array('type' => 'text', 'value' => '', 'label' => l_('CF_MULTIPLIER_DATA_PATH'), 'sublabel' => l_('CF_MULTIPLIER_DATA_PATH_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="multiplier-contents-<?php echo $k; ?>" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][multiplier-contents][hide_buttons]', array('type' => 'dropdown', 'options' => array(0 => 'No', 1 => 'Yes'), 'label' => l_('CF_MULTIPLIER_HIDE_BUTTONS'), 'sublabel' => l_('CF_MULTIPLIER_HIDE_BUTTONS_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="column-<?php echo $k; ?>" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][size][width]', array('type' => 'text', 'class' => 'fields_container_width', 'id' => 'fields_container_'.$k.'_width', 'value' => '99', 'label' => l_('CF_CONTAINER_COLUMN_WIDTH'), 'sublabel' => l_('CF_CONTAINER_COLUMN_WIDTH_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
		</div>
		<?php
		echo \GCore\Helpers\Html::formEnd();
	}
}
?>