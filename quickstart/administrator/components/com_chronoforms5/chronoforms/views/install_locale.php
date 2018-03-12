<?php
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
?>
<div class="chrono-page-container">
<div class="container" style="width:100%;">
<?php
	$doc = \GCore\Libs\Document::getInstance();
	$this->Toolbar->addButton('install_action', r_('index.php?ext=chronoforms&act=install_locale'), l_('CF_INSTALL_LOCALE'), $this->Assets->image('confirm', 'toolbar/'));
	$this->Toolbar->addButton('cancel', r_('index.php?ext=chronoforms'), l_('CF_CANCEL'), $this->Assets->image('cancel', 'toolbar/'), 'link');
?>
<div class="row" style="margin-top:20px;">
	<div class="col-md-6">
		<h3><?php echo l_('CF_INSTALL_LOCALE_TITLE'); ?></h3>
	</div>
	<div class="col-md-6 pull-right text-right">
		<?php
			echo $this->Toolbar->renderBar();
		?>
	</div>
</div>
<div class="row">
	<div class="panel panel-default">
		<div class="panel-body">
			<form action="<?php echo r_('index.php?ext=chronoforms&act=install_locale'); ?>" method="post" name="admin_form" id="admin_form" enctype="multipart/form-data">
				<?php echo $this->Html->formStart(); ?>
				<?php echo $this->Html->formSecStart(); ?>
				<?php echo $this->Html->formLine('upload', array('type' => 'file', 'label' => l_('CF_SELECT_LOCALE_FILE'), 'sublabel' => l_('CF_SELECT_LOCALE_FILE_DESC'))); ?>
				<?php echo $this->Html->formSecEnd(); ?>
				<?php echo $this->Html->formEnd(); ?>
			</form>
		</div>
	</div>
</div>
</div>
</div>