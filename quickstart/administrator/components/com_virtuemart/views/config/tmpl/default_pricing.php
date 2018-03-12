<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage Config
 * @author RickG
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_pricing.php 8508 2014-10-22 18:57:14Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$js = '
		jQuery(document).ready(function( $ ) {
				if ( $("#show_prices").is(\':checked\') ) {
					$("#show_hide_prices").show();
				} else {
					$("#show_hide_prices").hide();
				}
			 $("#show_prices").click(function() {
				if ( $("#show_prices").is(\':checked\') ) {
					$("#show_hide_prices").show();
				} else {
					$("#show_hide_prices").hide();
				}
			});
		});
	';
$document = JFactory::getDocument();
vmJsApi::addJScript('show_prices',$js,true);
?>
<table>
	<tr>
		<td valign="top">
			<fieldset>
				<legend><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_CONFIGURATION'); ?></legend>
				<table class="admintable">
					<?php
					echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_PRICE_SHOW_TAX','show_tax',VmConfig::get('show_tax',1));
					echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_PRICE_ASKPRICE','askprice',VmConfig::get('askprice',1));
					echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_PRICE_RAPPENRUNDUNG','rappenrundung',VmConfig::get('rappenrundung',0));
					echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_PRICE_ROUNDINDIG','roundindig',VmConfig::get('roundindig',1));
					echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_PRICE_CVARSWT','cVarswT',VmConfig::get('cVarswT',1));
					?>
				</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset>
				<legend><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES'); ?></legend>
				<table class="admintable">
					<?php
					echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_SHOW_PRICES','show_prices',VmConfig::get('show_prices',1),1,0,'id="show_prices"');
					?>
				</table>
				<table class="admintable" id="show_hide_prices">
					<tr>
						<th></th>
						<th><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_LABEL'); ?></th>
						<th><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_TEXT'); ?></th>
						<th><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_ROUNDING'); ?></th>
					</tr>
					<?php
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'basePrice', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE');
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'variantModification', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_VARMOD');
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'basePriceVariant', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE_VAR');
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'discountedPriceWithoutTax', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_DISCPRICE_WOTAX', 0);
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'priceWithoutTax', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE_WOTAX', 0);
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'taxAmount', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_TAX_AMOUNT', 0);
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'basePriceWithTax', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE_WTAX');
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'salesPrice', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE');
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'salesPriceWithDiscount', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE_WD');
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'discountAmount', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_DISC_AMOUNT');
					echo ShopFunctions::writePriceConfigLine($this->config->_params, 'unitPrice', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_UNITPRICE');
					?>
				</table>
			</fieldset>
		</td>
	</tr>
</table>