<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php if ($this->checkSpotlight('user3_4', 'user-3, user-4')) : ?>
	<!-- SPOTLIGHT 1 -->
	<div class="user3_4">
		<div class="container">
			<?php $this->spotlight('user3_4', 'user-3, user-4') ?>
		</div>
	</div>
	<!-- //SPOTLIGHT 1 -->
<?php endif ?>