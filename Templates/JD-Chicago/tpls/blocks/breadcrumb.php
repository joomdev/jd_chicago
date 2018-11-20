<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php if ($this->checkSpotlight('breadcrumb', 'breadcrumb')) : ?>
	<!-- SPOTLIGHT 1 -->
	<div class="breadcrumb">
		<div class="breadcrumb-main">
			<div class="container">
				<?php $this->spotlight('breadcrumb', 'breadcrumb') ?>
			</div>
		</div>
	</div>
	<!-- //SPOTLIGHT 1 -->
<?php endif ?>