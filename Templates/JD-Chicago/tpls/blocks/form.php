<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php if ($this->checkSpotlight('form', 'form')) : ?>
	<!-- SPOTLIGHT 1 -->
	<div id="form">
		<div class="container">
			<?php $this->spotlight('form', 'form') ?>
		</div>
	</div>
	<!-- //SPOTLIGHT 1 -->
<?php endif ?>