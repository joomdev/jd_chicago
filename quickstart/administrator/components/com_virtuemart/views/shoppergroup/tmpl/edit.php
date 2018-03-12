<?php
/**
 *
 * Description
 *
 * @package	VirtueMart
 * @subpackage ShopperGroup
 * @author Markus �hler
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: edit.php 8802 2015-03-18 17:12:44Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
		$js = '
	jQuery(document).ready(function( $ ) {
			if ( $("#custom_price_display").is(\':checked\') ) {
				$("#show_hide_prices").show();
			} else {
				$("#show_hide_prices").hide();
			}
		 $("#custom_price_display").click(function() {
			if ( $("#custom_price_display").is(\':checked\') ) {
				$("#show_hide_prices").show();
			} else {
				$("#show_hide_prices").hide();
			}
		});
	});
	';

vmJsApi::addJScript('hidePrices',$js);
AdminUIHelper::startAdminArea($this);
AdminUIHelper::imitateTabs('start', 'COM_VIRTUEMART_SHOPPERGROUP_NAME');
?>


<form action="index.php" method="post" name="adminForm" id="adminForm">

    <div class="col50">
	<fieldset>
	    <legend><?php echo vmText::_('COM_VIRTUEMART_SHOPPERGROUP_DETAILS'); ?></legend>
	    <table class="admintable">

		<?php echo VmHTML::row('input', 'COM_VIRTUEMART_SHOPPERGROUP_NAME', 'shopper_group_name', $this->shoppergroup->shopper_group_name,'class="required"'); ?>
		<?php echo VmHTML::row('booleanlist', 'COM_VIRTUEMART_PUBLISHED', 'published', $this->shoppergroup->published); ?>
		<?php /*
		  <tr>
		  <td width="110" class="key">
		  <label for="virtuemart_vendor_id">
		  <?php echo vmText::_('COM_VIRTUEMART_VENDOR'); ?>
		  </label>
		  </td>
		  <td>
		  <?php echo $this->vendorList; ?>
		  </td>
		  </tr>
		 *
		 */
		?>
		<?php
		if ($this->shoppergroup->default == 1) {
		    ?>
    		<tr>
    		    <td width="110" class="key">
    			<label for="default">
    			    <span class="hasTip" title="<?php echo vmText::_('COM_VIRTUEMART_SHOPPERGROUP_DEFAULT_TIP'); ?>">
				    <?php echo vmText::_('COM_VIRTUEMART_SHOPPERGROUP_DEFAULT'); ?>
    			    </span>
    			</label>
    		    </td>
    		    <td>
					<?php echo JHtml::_('image','menu/icon-16-default.png', vmText::_('COM_VIRTUEMART_SHOPPERGROUP_DEFAULT'), NULL, true); ?>
    		    </td>
    		</tr>
		    <?php } ?>
		<?php echo VmHTML::row('textarea', 'COM_VIRTUEMART_SHOPPERGROUP_DESCRIPTION', 'shopper_group_desc', $this->shoppergroup->shopper_group_desc); ?>

		<?php if ($this->shoppergroup->default < 1) {
			echo VmHTML::row('checkbox', 'COM_VIRTUEMART_SHOPPERGROUP_ADDITIONAL', 'sgrp_additional', $this->shoppergroup->sgrp_additional);
		} else {
			echo '<tr></tr>';
		}
		?>
	    </table>
	</fieldset>

	<fieldset>
	    <legend><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES') ?></legend>

	    <table class="admintable">
		<tr>
		    <td>
<?php echo vmText::_('COM_VIRTUEMART_SHOPPERGROUP_ENABLE_PRICE_DISPLAY'); ?>
		    </td>
		    <td>
<?php
			     $attributes='id="custom_price_display"';
			    echo VmHTML::checkbox('custom_price_display', $this->shoppergroup->custom_price_display,1,0,$attributes) ?>
		    </td>
		</tr>
		</table>
		<table class="admintable" id="show_hide_prices">
		<tr>
		    <td>
			<span class="hasTip" title="<?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_PRICES_EXPLAIN'); ?>">
<?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_PRICES'); ?>
		    </td>
		    <td>
<?php echo VmHTML::checkbox('show_prices', $this->shoppergroup->show_prices); ?>
		    </td>
		</tr>

		    <tr>
			<th></th>
			<th><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_LABEL'); ?></th>
			<th><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_TEXT'); ?></th>
			<th><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_ROUNDING'); ?></th>
		    </tr>
<?php
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'basePrice', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'variantModification', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_VARMOD');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'basePriceVariant', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE_VAR');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'basePriceWithTax', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE_WTAX');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'discountedPriceWithoutTax', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_DISCPRICE_WOTAX');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'salesPriceWithDiscount', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE_WD');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'salesPrice', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'priceWithoutTax', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE_WOTAX');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'discountAmount', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_DISC_AMOUNT');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'taxAmount', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_TAX_AMOUNT');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup, 'unitPrice', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_UNITPRICE');
?>
		</table>

	</fieldset>
    </div>

    <input type="hidden" name="default" value="<?php echo $this->shoppergroup->default ?>" />
    <input type="hidden" name="virtuemart_shoppergroup_id" value="<?php echo $this->shoppergroup->virtuemart_shoppergroup_id; ?>" />
<?php echo $this->addStandardHiddenToForm(); ?>

</form>

<?php
AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea();
?>