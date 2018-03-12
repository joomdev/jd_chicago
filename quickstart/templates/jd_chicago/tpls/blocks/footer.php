<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- FOOTER -->
<footer id="t3-footer" class="wrap t3-footer">

	<?php if ($this->checkSpotlight('footnav', 'footer-1, footer-2, footer-3, footer-4, footer-5, footer-6')) : ?>
		<!-- FOOT NAVIGATION -->
		<div class="container">
			<?php $this->spotlight('footnav', 'footer-1, footer-2, footer-3, footer-4, footer-5, footer-6') ?>
		</div>
		<!-- //FOOT NAVIGATION -->
	<?php endif ?>

</footer>

<section class="t3-copyright">
	<div class="container">
		<div class="row">
			<p>&copy; Copyright <?php echo date('Y'); ?> <?php $config = JFactory::getConfig(); echo $config['sitename']; ?><a href="https://www.joomdev.com/products/templates" target="_blank"> <strong>Joomla Templates</strong></a> by <a href="http://www.joomdev.com" target="_blank"><strong>JoomDev</strong></a></p>
		</div>
	</div>
</section>

<!-- BACK TOP TOP BUTTON -->

<div id="back-to-top" data-spy="affix" data-offset-top="300" class="back-to-top affix-top">
  <button class="btn btn-primary" title="Back to Top"><i class="fa fa-angle-double-up" aria-hidden="true"></i></button>
</div>

<script type="text/javascript">
(function($) {
	// Back to top
	$('#back-to-top').on('click', function(){
		$("html, body").animate({scrollTop: 0}, 500);
		return false;
	});
})(jQuery);
</script>
<!-- BACK TO TOP BUTTON -->

<!-- //FOOTER -->