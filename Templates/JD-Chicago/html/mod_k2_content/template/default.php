<?php
/**
 * @version    2.7.x
 * @package    K2
 * @author     JoomlaWorks http://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;
?>

<div id="k2ModuleBox<?php echo $module->id; ?>" class="k2ItemsBlock k2itemstyle2<?php if($params->get('moduleclass_sfx')) echo ' '.$params->get('moduleclass_sfx'); ?>">
	
	<?php if($params->get('itemPreText')): ?>
	<p class="modulePretext"><?php echo $params->get('itemPreText') ?></p>
	<?php endif; ?>

	<?php if(count($items)): ?>
  <ul>
    <?php foreach ($items as $key=>$item):	?>
    <li class="<?php echo ($key%2) ? "odd" : "even"; if(count($items)==$key+1) echo ' lastItem'; ?>">
      <!-- Plugins: BeforeDisplay -->
      <?php echo $item->event->BeforeDisplay; ?>

      <!-- K2 Plugins: K2BeforeDisplay -->
      <?php echo $item->event->K2BeforeDisplay; ?>

      <!-- Plugins: AfterDisplayTitle -->
      <?php echo $item->event->AfterDisplayTitle; ?>

      <!-- K2 Plugins: K2AfterDisplayTitle -->
      <?php echo $item->event->K2AfterDisplayTitle; ?>

      <!-- Plugins: BeforeDisplayContent -->
      <?php echo $item->event->BeforeDisplayContent; ?>

      <!-- K2 Plugins: K2BeforeDisplayContent -->
      <?php echo $item->event->K2BeforeDisplayContent; ?>

      <?php if($params->get('itemImage') || $params->get('itemIntroText')): ?>

      <?php endif; ?>
	  
	 <?php 
	 
			$app = JFactory::getApplication('site');
			$componentParams = $app->getParams('com_k2');
			$xsmall = $componentParams->get('itemImageXS', 1);
			$small = $componentParams->get('itemImageS', 1);
			$medium = $componentParams->get('itemImageM', 1);
			$large = $componentParams->get('itemImageL', 1);
			$xlarge = $componentParams->get('itemImageXL', 1);
			$module = JModuleHelper::getModule('mod_k2_content');
			$moduleParams = new JRegistry($module->params);
			$imagesize = $moduleParams->get('itemImgSize', 'Small');
			if($imagesize == 'Small'){
				$s = $small;
				$c = ' smallimg';
			}
			elseif($imagesize == 'XSmall'){
				$s = $xsmall;
				$c = ' xsamllimg';
			}
			elseif($imagesize == 'Medium'){
				$s = $medium;
				$c = ' mediumimg';
			}
			elseif($imagesize == 'Large'){
				$s = $large;
				$c = ' largeimg';
			}
			elseif($imagesize == 'XLarge'){
				$s = $xlarge;
				$c = ' xlargeimg';
			}
					 
		?>
	  
	
	  
	<div class="itemimage <?php echo $c;?>" style="width:<?php echo $s;?>px;">
	
		<?php if($params->get('itemImage') && isset($item->image)): ?>
			<img src="<?php echo $item->image; ?>" alt="<?php echo K2HelperUtilities::cleanHtml($item->title); ?>" />
			<div class="moduleItemIntrotext">
				<div class="intro_new">
					<?php if($params->get('itemTitle')): ?>
					<div class="fulltext_new">
						<a class="moduleItemTitle" href="<?php echo $item->link; ?>"><?php 
							if(strlen($item->title) >= 56){
							echo substr($item->title, 0 ,46).'...';
							}else{
							echo $item->title;
							}
							?>
						</a>
					</div>
					<?php endif; ?>
					<div class="readmore_new">
						<a class="moduleItemReadMore" href="<?php echo $item->link; ?>">
							<?php echo JText::_('K2_READ_MORE_NEW'); ?>
						</a>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	 <!---- custom ---->
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</div>
