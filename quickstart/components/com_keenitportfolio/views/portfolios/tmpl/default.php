<?php

/**

 * @version     2.0.0

 * @package     com_keenitportfolio

 * @copyright   Copyright (C) 2015. All rights reserved.

 * @license     GNU General Public License version 2 or later; see LICENSE.txt

 * @author      Abdur Rashid <rashid.cse.05@gmail.com> - http://www.keenitsolution.com

 */

// no direct access

defined('_JEXEC') or die;



JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');

//JHtml::_('behavior.multiselect');

//JHtml::_('formbehavior.chosen', 'select');

$listOrder = $this->state->get('list.ordering');

$listDirn = $this->state->get('list.direction');

$document = JFactory::getDocument();

$document->addScript('components/com_keenitportfolio/assets/js/isotope.js');

$document->addScript('components/com_keenitportfolio/assets/js/jquery.magnific-popup.min.js');

$document->addStylesheet('components/com_keenitportfolio/assets/css/list.css');

$document->addStylesheet('components/com_keenitportfolio/assets/css/magnific-popup.css');

$document->addStyleSheet(JURI::root().'media/jui/css/bootstrap.min.css');

$app = JFactory::getApplication();
$params = $app->getParams();
$title			= $params->get('title');
$lightbox_icon	= $params->get('lightbox_icon', 1);
$details_icon	= $params->get('details_icon', 1);
?>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

<div class="portfolio-area" id="portfolio" >
  <div class="row">
    <div id="filters" class="button-group align-center">
      <button class="btn-port current " data-filter="*"><?php echo JText::_('COM_KEENITPORTFOLIO_ALL_PORTFOLIOS'); ?></button>
      <?php

					$categories=KeenitportfolioFrontendHelper::getCategory();

					foreach ( $categories as $catlist ) {

					echo "<button class='btn-port ' data-filter='.".$catlist->alias."'>" . $catlist->title . "</button>";

					}

					?>
    </div>
  </div>
  
  <!-- /.row --> 
  
</div>
<div id="portfolio-filter">
  <?php foreach ($this->items as $i => $item) : 

		   $alias=KeenitportfolioFrontendHelper::getAlias($item->id);

		   ?>
  <div class="portfolio-item <?php echo $alias; ?>">
    <div class="p-box"> <img class="port-img " width="300" src="<?php echo JURI::root(); ?>images/portfolio/<?php echo $item->image; ?>" alt="">
      <div class="hover-wrapper">
        <div class="outter">
          <div class="inner">
            <?php if($lightbox_icon): ?>
            <a  title="<?php echo $item->project_name; ?>" class="m-up image-link" href="<?php echo JURI::root(); ?>images/portfolio/<?php echo $item->image; ?>"><i class="fa fa-search-plus"></i></a>
            <?php else: ?>
            <?php endif ?>
            <?php if($details_icon): ?>
            <a href="<?php echo JRoute::_('index.php?option=com_keenitportfolio&view=portfolio&id='.(int) $item->id); ?>" class="open-popup-link"><i class="fa fa-external-link"></i></a>
            <?php else: ?>
            <?php endif ?>
            <?php if($title): ?>
            <h5><?php echo $item->project_name; ?></h5>
            <?php else: ?>
            <?php endif ?>
          </div>
        </div>
      </div>
    </div>
    
    <!-- /.p-box --> 
    
  </div>
  <?php endforeach; ?>
</div>
</div>
<script>
(function($) {
    $(window).load(function() { 
      "use strict";
           var $container = $('#portfolio-filter');
              $container.isotope({
                  filter: '*',
                  animationOptions: {
                      duration: 750,
                      easing: 'linear',
                      queue: false
                  }
              });

              $('#filters button').click(function() {
                  $('#filters .current').removeClass('current');
                  $(this).addClass('current');

                  var selector = $(this).attr('data-filter');
                  $container.isotope({
                      filter: selector,
                      animationOptions: {
                          duration: 750,
                          easing: 'linear',
                          queue: false
                      }
                  });
                  return false;
              });
           /*---------------------------------------*/
           /*  magic box 
           /*---------------------------------------*/
           $('#portfolio-filter').magnificPopup({
            delegate: 'a.m-up', // child items selector, by clicking on it popup will open
             type: 'image',
             gallery:{
                 enabled:true
               },
             // Delay in milliseconds before popup is removed
              removalDelay: 300,

              // Class that is added to popup wrapper and background
              // make it unique to apply your CSS animations just to this exact popup
              mainClass: 'mfp-fade',  
              // other options
              titleSrc: 'title',

              callbacks: {
                  open: function() {
                    // Will fire when this exact popup is opened
                    // this - is Magnific Popup object
                  },
                  close: function() {
                    // Will fire when popup is closed
                  }
                  // e.t.c.
                }             
           });
     });
})(jQuery); 
</script> 
