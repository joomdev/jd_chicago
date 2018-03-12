<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<?php $this->Html->active_set('div'); ?>
<?php /* ?>
<?php echo $this->Html->formSecStart('original_element'); ?>
<?php
if(!empty($fdata['id'])){
	$fdata['id'] = '__wizard_'.$fdata['id'];
}
?>
<?php echo $this->Html->formLine('__wizard_'.$fdata['name'], $fdata); ?>
<?php echo $this->Html->formSecEnd(); ?>
<?php */ ?>
<?php
	$type = isset($fdata['render_type']) ? $fdata['render_type'] : $fdata['type'];
	if(empty($type)){
		return;
	}
	$class = '\GCore\Admin\Extensions\Chronoforms\Fields\\'.\GCore\Libs\Str::camilize($type).'\\'.\GCore\Libs\Str::camilize($type);
	$class::element($fdata);