<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php if ($this->checkSpotlight('custome', 'user-1, user-2')) : ?>
	<!-- SPOTLIGHT 1 -->
	<div class="user1_2">
		<div class="container">
			<?php $this->spotlight('custome', 'user-1, user-2') ?>
		</div>
	</div>
	<!-- //SPOTLIGHT 1 -->
<?php endif ?>