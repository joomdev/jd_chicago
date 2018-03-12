<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$tab_position = $params->get('tab_position');
$customitem = $params->get('customitem');
$customitem = explode(',',$customitem);
$autoplay = $params->get('autoplay');
$autotime = $params->get('autoplaytime');
$description = $params->get('description');
$rows = $customitem;
$url = JUri::root();
require_once dirname(__FILE__) . '/../helper.php';
	
	$url = "modules/mod_jd_content_tabs/css/style.css";
	$document = JFactory::getDocument();
		$document->addStyleSheet($url);
		if($tab_position == 'left')
		{
		$class = 'left';	
		}
		else {
		$class = 'right';	
		}?>
		  <section id="tabslider_show" class="show">
		 <div class="intro"><?php echo ucwords($description);?></div>
                <div class="tab-container">        
				<div class="tabs-grid">
            <div class="tabs-block">
                 <div class="tabs-content">
                 <div class="moduletable">
						<div class="">
                            <div>
							<div class="tabs-slider <?php echo $class;?>"  id="module-tabs-slider">
					<div class="tab-slider-preview">
					  <?php 
					 
					  if(array_filter($rows)) {
					foreach($rows as $id)
					{
				 $contents = Modjd_content_tabsHelper::getSliderContents($id);
				 $image = $contents->thumbnail;
				 $title = $contents->title;
				 $description = $contents->description;
				
				 if(!(empty($image) && empty($title) && empty($description) ))
				 {
				?>
				<div class="tab-slider-content slider-content" style="background-image: url('<?php echo $contents->thumbnail;?>');">
					<div class="tab-slider-overlay">
						<div class="tab-slider-overlay-main">
							<div class="tab-slider-title"><?php echo ucwords($title);?></div>
							<div class="description"><?php echo $description;?></div>
						</div>
					</div>
				</div>
		  <?php
		  }
		  }
			  }
		 ?>
		   
    </div>
			<div id="tab-slider-tabs-preview" class="tab-slider-tabs">
			<ul class="tab-slider-content-headline slide-heading">
			  <?php 
			  if(array_filter($rows)) 
			  {              
			  foreach($rows as $id)
				 {					$contents = Modjd_content_tabsHelper::getSliderContents($id);
					$title = $contents->title;
					if(!(empty($image) && empty($title) && empty($description) ))
					{
					?>
					<li class="tab-click">
					<div class="tab-arrow-container">
						<i class="fa fa-chevron-right"></i>
					</div>
					<div class="tab-slider-headline-title-main"><span class="tab-slider-headline-title"><?php echo ucwords($title);?></span></div>
					</li>
					<?php }
					} 
					}	?>
                
              </ul>
		</div>
	  </div>
	</div>

            
    </div>		</div>
            
        </div>
                        
    </div>
            
    </div>
	</div>

    </section>
<script type="text/javascript">
		var autotime = '<?php echo $autotime;?>';
		jQuery(window).load(function(){
		  jQuery('#module-tabs-slider .slider-content:first-child()').addClass('top-content');
		  jQuery('#module-tabs-slider .item-list:first-child()').addClass('active first');
		 
		});
		(function ($) {
		  "use strict";

		  var hl,
		  newsList = $('#module-tabs-slider .slide-heading'),
		  newsListItems = $('#module-tabs-slider .slide-heading li'),
		  firstNewsItem = $('#module-tabs-slider .slide-heading li:nth-child(1)'),
		  newsPreview = $('#module-tabs-slider .news-preview'),
		  elCount = $('#module-tabs-slider ul.slide-heading').children().length - 1,
		  speed = autotime, // this is the speed of the switch
		  myTimer = null,
		  siblings = null,
		  totalHeight = null,
		  indexEl = 0,
		  i = null;
		  newsListItems.addClass('item-list');
		  
			function doTimedSwitch() {
			myTimer = setInterval(function () {
			  if (($('#module-tabs-slider .active').prev().index() + 1) === elCount) {
				  firstNewsItem.trigger('click');
			  } else {
				$('#module-tabs-slider .active').next(':not(.first)').trigger('click');
			  }
			}, speed);
		  }

		  clearInterval(myTimer);
		   <?php if ($autoplay == 1): ?>
			  doTimedSwitch();
			  <?php endif ;?>
		  
		  function doClickItem() {

			newsListItems.on('click', function () {
			  newsListItems.removeClass('active');
			  $(this).addClass('active');

			  siblings = $(this).prevAll();

			  // this loop calculates the height of individual elements, including margins/padding
			  for (i = 0; i < siblings.length; i += 1) {
			  }

			  indexEl = $(this).index() + 1;

			  $('#module-tabs-slider .g-newsslider-pagination li').removeClass('active');
			  $('#module-tabs-slider .slider-content:nth-child(' + indexEl + ')').siblings().removeClass('top-content');
			  $('#module-tabs-slider .slider-content:nth-child(' + indexEl + ')').addClass('top-content');

			  clearInterval(myTimer);
			  // comment out the line below if you don't
			  // want it to rotate automatically
			  <?php if ($autoplay == 1): ?>
					  doTimedSwitch();
					  <?php endif ; ?>
				  });
		  }
		  doClickItem();
		  doWindowResize();
		  $('#module-tabs-slider .active').trigger('click');

		})(jQuery);
</script>