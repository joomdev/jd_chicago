<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php if ($this->checkSpotlight('slider', 'slider')) : ?>
	<!-- SPOTLIGHT 1 -->
	<div id="slider">
		<?php $this->spotlight('slider', 'slider') ?>
	</div>
	<!-- //SPOTLIGHT 1 -->
<?php endif ?>