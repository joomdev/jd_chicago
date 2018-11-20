<?php
/** 
 *------------------------------------------------------------------------------
 * @package       T3 Framework for Joomla!
 *------------------------------------------------------------------------------
 * @copyright     Copyright (C) 2004-2013 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt, JoomlaBamboo, (contribute to this project at github 
 *                & Google group to become co-author)
 * @Google group: https://groups.google.com/forum/#!forum/t3fw
 * @Link:         http://t3-framework.org 
 *------------------------------------------------------------------------------
 */
defined('_JEXEC') or die;
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" class='<jdoc:include type="pageclass" />'>
<head>
<jdoc:include type="head" />
<?php $this->loadBlock('head') ?>
<?php
	$app = JFactory::getApplication();
	$menu = $app->getMenu()->getActive();
	$pageclass = '';
	if (is_object($menu))
	$pageclass = $menu->params->get('pageclass_sfx');
	$app = JFactory::getApplication();
	$templateName = $app->getTemplate();
?>
</head>
<body class="<?php echo $pageclass ? htmlspecialchars($pageclass) : 'default'; ?>">
<div class="t3-wrapper"> <!-- Need this wrapper for off-canvas menu. Remove if you don't use of-canvas -->

	<?php $this->loadBlock('header') ?>

	<?php $this->loadBlock('slider') ?>

	<?php $this->loadBlock('form') ?>

	<?php $this->loadBlock('breadcrumb') ?>

	<?php $this->loadBlock('custome') ?>

	<?php $this->loadBlock('contenttab') ?>

	<?php $this->loadBlock('portfolio') ?>

	<?php $this->loadBlock('backgroundvideo') ?>

	<?php $this->loadBlock('spotlight-1') ?>

	<?php $this->loadBlock('mainbody') ?>

	<?php $this->loadBlock('spotlight-2') ?>

	<?php $this->loadBlock('navhelper') ?>

	<?php $this->loadBlock('user3_4') ?>

	<?php $this->loadBlock('bottom') ?>

	<?php $this->loadBlock('footer') ?>

</div>

</body>

</html>