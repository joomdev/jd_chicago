<?php
/**
 *
 * Layout for the AMAZON cart
 * @version $Id$
 * @package    VirtueMart
 * @subpackage Cart
 * @author Valerie Isaksen
 *
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


JHtml::_('behavior.formvalidation');

$js = "
	jQuery(document).ready(function($) {
	jQuery(this).vm2front('stopVmLoading');
	jQuery('#checkoutFormSubmit').bind('click dblclick', function(e){
	jQuery(this).vm2front('startVmLoading');
	e.preventDefault();
    jQuery(this).attr('disabled', 'true');
    jQuery(this).removeClass( 'vm-button-correct' );
    jQuery(this).addClass( 'vm-button' );
    jQuery('#checkoutForm').submit();

});
	});
";
vmJsApi::addJScript('vm.checkoutFormSubmit', $js);

$this->addCheckRequiredJs();
?>
	<div id="amazonShipmentNotFoundDiv">
		<?php if (isset($this->found_shipment_method) and !$this->found_shipment_method) { ?>
			<div id="system-message-container">
				<dl id="system-message">
					<dt class="info">info</dt>
					<dd class="info message">
						<ul>
							<li><?php echo JText::_('VMPAYMENT_AMAZON_UPDATECART_SHIPMENT_NOT_FOUND'); ?></li>
						</ul>
					</dd>
				</dl>
			</div>
		<?php
		}
		?>
	</div>
	<div id="amazonErrorDiv">
	</div>

	<div id="amazonLoading"></div>

	<div class="cart-view" id="cart-view">
		<div id="amazonHeader">
			<div class="width50 floatleft">
				<h1><?php echo vmText::_('VMPAYMENT_AMAZON_PAY_WITH_AMAZON'); ?></h1>
				<div class="payments-signin-button"></div>
			</div>
			<div class="width50 floatleft right">
				<?php // Continue Shopping Button
				if (!empty($this->continue_link_html)) {
					echo $this->continue_link_html;
				}
				?>
				<div>
					<a href="#" id="leaveAmazonCheckout"><?php echo vmText::_('VMPAYMENT_AMAZON_LEAVE_PAY_WITH_AMAZON') ?></a>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div id="amazonAddressBookWalletWidgetDiv">
			<div id="amazonAddressBookWidgetDiv" class="width50 floatleft"></div>

			<div id="amazonWalletWidgetDiv" class="width50 floatleft"></div>
		</div>
		<div class="clear"></div>
		<div id="amazonChargeNowWarning"></div>

		<div class="clear"></div>
		<div id="amazonCartDiv">
			<div id="signInButton"></div>

			<?php

			if ($this->checkout_task) {
				$taskRoute = '&task=' . $this->checkout_task;
			} else {
				$taskRoute = '';
			}

			if ($this->cart->getDataValidated()) {
				$this->readonly_cart = true;
			} else {
				$this->readonly_cart = false;
			}

			?>
			<form method="post" id="checkoutForm" name="checkoutForm"
			      action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">

				<div id="amazonShipmentsDiv"><?php
					//if (!$this->readonly_cart) {
					if (!$this->cart->automaticSelectedShipment or !$this->readonly_cart) {
						?>
						<?php echo $this->loadTemplate('shipment'); ?>
					<?php

					}
					?>
				</div>
				<?php
				//}
				// This displays the pricelist MUST be done with tables, because it is also used for the emails
				echo $this->loadTemplate('pricelist');

				if (!empty($this->checkoutAdvertise)) {
					?>
					<div id="checkout-advertise-box"> <?php
					foreach ($this->checkoutAdvertise as $checkoutAdvertise) {
						?>
						<div class="checkout-advertise">
							<?php echo $checkoutAdvertise; ?>
						</div>
					<?php
					}
					?></div><?php
				}

				echo $this->loadTemplate('cartfields');

				?>
					<div id="amazon_checkout">

						<?php
						echo $this->checkout_link_html;
						?>
					</div>

				<?php // Continue and Checkout Button END
				if ($this->checkout_task == 'confirm' ) $task=$this->checkout_task;
				else  $task='updatecart';

				?>
				<input type='hidden' name='task' value='<?php echo $task ?>'/>
				<input type='hidden' id='STsameAsBT' name='STsameAsBT' value='<?php echo $this->cart->STsameAsBT; ?>'/>
				<input type='hidden' name='virtuemart_paymentmethod_id' value='<?php echo $this->cart->virtuemart_paymentmethod_id; ?>'/>
				<input type='hidden' name='doRedirect' value='false'/>
				<input type='hidden' name='option' value='com_virtuemart'/>
				<input type='hidden' name='view' value='cart'/>
			</form>
		</div>
	</div>

<?php vmTime('Cart view Finished task ', 'Start'); ?>