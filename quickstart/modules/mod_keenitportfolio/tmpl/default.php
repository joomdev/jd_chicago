<?php
/**
* mod_keenitportfolio - Keen IT Responsive Portfolio module for Joomla by KeenItSolution.com
* author    KeenItSolution http://www.keenitsolution.com
* Copyright (C) 2010 - 2015 keenitsolution.com. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://www.keenitsolution.com */

defined('_JEXEC') or die;
?>

<section class="portfolio-area" id="portfolio" >
  <div class="container">
    <div class="row">
      <div id="filters" class="button-group align-center">
        <button class="btn btn-port current " data-filter="*"><?php echo JText::_('MOD_KEENITPORTFOLIO_ALL_PORTFOLIOS'); ?></button>
        <?php
foreach ( $list as $term ) { 
echo "<button class='btn btn-port' data-filter='.".$term->alias."'>" . $term->title . "</button>";
}
?>
      </div>
    </div>
  </div>
  <div id="portfolio-filter">
    <?php foreach($gallery_items as $row): 
$gallery_alias = ModKeenITPOrtfolioHelper::getAlias($row->id);
?>
    <div class="portfolio-item <?php echo $gallery_alias;?>">
      <div class="p-box">
        <?php if ($row->image):?>
        <img class="port-img " src="<?php echo JURI::root(); ?>images/portfolio/<?php echo $row->image; ?>" alt="">
        <?php endif; ?>
        <div class="hover-wrapper">
          <div class="outter">
            <div class="inner">
              <?php if($lightbox_icon): ?>
              <a  title="<?php echo $row->project_name; ?>" class="m-up image-link" href="<?php echo JURI::root(); ?>images/portfolio/<?php echo $row->image; ?>"><i class="fa fa-search-plus"></i></a>
              <?php else: ?>
              <?php endif ?>
              <?php if($details_icon): ?>
              <a href="<?php echo JRoute::_('index.php?option=com_keenitportfolio&view=portfolio&id='.(int) $row->id); ?>" class="open-popup-link"><i class="fa fa-external-link"></i></a>
              <?php else: ?>
              <?php endif ?>
              <?php if($title): ?>
              <h5><?php echo $row->project_name ?></h5>
              <?php else: ?>
              <?php endif ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="container">
    <div id="filters" class="button-group align-center">
    <?php if($showmore_btn): ?>
    <button onclick="window.location.href='<?php echo $showmore_btn ?>'" class="btn btn-port">Show More</button>
    <?php else: ?>
    <?php endif ?>
    </div>
  </div>
</section>
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
