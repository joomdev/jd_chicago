<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage Config
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_templates.php 7073 2013-07-15 16:24:50Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>
<table width="100%">
<tr>
<td valign="top" width="50%">
<fieldset>
	<legend><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_SHOPFRONT_SETTINGS'); ?></legend>
	<table class="admintable">
		<?php
		echo VmHTML::row('genericlist','COM_VIRTUEMART_SELECT_DEFAULT_SHOP_TEMPLATE',$this->jTemplateList, 'vmtemplate', 'size=1 width=200', 'value', 'name', VmConfig::get('vmtemplate', 'default'));
		echo VmHTML::row('genericlist','COM_VIRTUEMART_ADMIN_CFG_CATEGORY_TEMPLATE',$this->jTemplateList, 'categorytemplate', 'size=1 width=200', 'value', 'name', VmConfig::get('categorytemplate', 'default'));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_SHOW_CATEGORY', 'showCategory', VmConfig::get('showCategory', 1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_SHOW_MANUFACTURERS', 'show_manufacturers', VmConfig::get('show_manufacturers', 1));
		echo VmHTML::row('genericlist','COM_VIRTUEMART_ADMIN_CFG_CART_LAYOUT', $this->cartLayoutList, 'cartlayout', 'size=1', 'value', 'text', VmConfig::get('cartlayout', 'default'));
		echo VmHTML::row('genericlist','COM_VIRTUEMART_ADMIN_CFG_CATEGORY_LAYOUT', $this->categoryLayoutList, 'categorylayout', 'size=1', 'value', 'text', VmConfig::get('categorylayout', 0));
		echo VmHTML::row('genericlist','COM_VIRTUEMART_CFG_PRODUCTS_SUBLAYOUT', $this->productsFieldList, 'productsublayout', 'size=1', 'value', 'text', VmConfig::get('productsublayout', 'products'));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_CATEGORIES_PER_ROW', 'categories_per_row', VmConfig::get('categories_per_row', 3),'class="inputbox"','',4,4);
		echo VmHTML::row('genericlist','COM_VIRTUEMART_ADMIN_CFG_PRODUCT_LAYOUT', $this->productLayoutList, 'productlayout', 'size=1', 'value', 'text', VmConfig::get('productlayout', 0));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW', 'products_per_row', VmConfig::get('products_per_row', 3),'class="inputbox"','',4,4);
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_MANUFACTURER_PER_ROW', 'manufacturer_per_row', VmConfig::get('manufacturer_per_row', 3),'class="inputbox"','',4,4);
		?>
	</table>
</fieldset>
<fieldset>
	<legend><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_PAGINATION_SEQUENCE'); ?></legend>
	<table class="admintable">
		<?php
		echo VmHTML::row('input','COM_VIRTUEMART_LIST_MEDIA','mediaLimit',VmConfig::get('mediaLimit',20));
		echo VmHTML::row('input','COM_VIRTUEMART_LLIMIT_INIT_BE','llimit_init_BE',VmConfig::get('llimit_init_BE',30));
		echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_BE','pagseq',VmConfig::get('pagseq'));
		echo VmHTML::row('input','COM_VIRTUEMART_LLIMIT_INIT_FE','llimit_init_FE',VmConfig::get('llimit_init_FE',24));
		echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_1','pagseq_1',VmConfig::get('pagseq_1'));
		echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_2','pagseq_2',VmConfig::get('pagseq_2'));
		echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_3','pagseq_3',VmConfig::get('pagseq_3'));
		echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_4','pagseq_4',VmConfig::get('pagseq_4'));
		echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_5','pagseq_5',VmConfig::get('pagseq_5'));
		?>
	</table>
</fieldset>

<fieldset>
	<legend><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_CAT_FEED_SETTINGS'); ?></legend>
	<table class="admintable">
		<?php
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FEED_ENABLE', 'feed_cat_published', VmConfig::get('feed_cat_published', 0));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWIMAGES', 'feed_cat_show_images', VmConfig::get('feed_cat_show_images', 0));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWPRICES', 'feed_cat_show_prices', VmConfig::get('feed_cat_show_prices', 0));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWDESC', 'feed_cat_show_description', VmConfig::get('feed_cat_show_description', 0));
		$options = array();
		$options[] = JHtml::_('select.option', 'product_s_desc', vmText::_('COM_VIRTUEMART_PRODUCT_FORM_S_DESC'));
		$options[] = JHtml::_('select.option', 'product_desc', vmText::_('COM_VIRTUEMART_PRODUCT_FORM_DESCRIPTION'));
		echo VmHTML::row('genericlist','COM_VIRTUEMART_ADMIN_CFG_FEED_DESCRIPTION_TYPE', $options, 'feed_cat_description_type', 'size=1', 'value', 'text', VmConfig::get('feed_cat_description_type',0));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_FEED_MAX_TEXT_LENGTH','feed_cat_max_text_length',VmConfig::get('feed_cat_max_text_length','500'),"","",4);
		?>
	</table>
</fieldset>
<fieldset>
	<legend><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_TITLE'); ?></legend>
	<table class="admintable table-striped">
		<?php
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_ASSETS_GENERAL_PATH','assets_general_path',VmConfig::get('assets_general_path',''),'class="inputbox"','',60,260);
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_MEDIA_CATEGORY_PATH','media_category_path',VmConfig::get('media_category_path',''),'class="inputbox"','',60,260);
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_MEDIA_PRODUCT_PATH','media_product_path',VmConfig::get('media_product_path',''),'class="inputbox"','',60,260);
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_MEDIA_MANUFACTURER_PATH','media_manufacturer_path',VmConfig::get('media_manufacturer_path',''),'class="inputbox"','',60,260);
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_MEDIA_VENDOR_PATH','media_vendor_path',VmConfig::get('media_vendor_path',''),'class="inputbox"','',60,260);
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH','forSale_path',VmConfig::get('forSale_path',''),'class="inputbox"','',60,260);
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH_THUMB','forSale_path_thumb',VmConfig::get('forSale_path_thumb',''),'class="inputbox"','',60,260);

		if (function_exists('imagecreatefromjpeg')) {
			echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_DYNAMIC_THUMBNAIL_RESIZING', 'img_resize_enable', VmConfig::get('img_resize_enable', 1));
			echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_THUMBNAIL_WIDTH', 'img_width', VmConfig::get('img_width', 90),"","",4);
			echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_THUMBNAIL_HEIGHT', 'img_height', VmConfig::get('img_height', 90),"","",4);

		} else { ?>
		<tr>
			<td colspan="2">
				<strong><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_GD_MISSING'); ?></strong>
				<input type="hidden" name="img_resize_enable" value="0"/>
				<input type="hidden" name="img_width" value="<?php echo  VmConfig::get('img_width', 90); ?>"/>
				<input type="hidden" name="img_height" value="<?php echo  VmConfig::get('img_height', 90); ?>"/>
			</td>
		</tr>
		<?php }

		echo VmHTML::row('genericlist','COM_VIRTUEMART_ADMIN_CFG_NOIMAGEPAGE',$this->noimagelist, 'no_image_set', 'size=1', 'value', 'text', VmConfig::get('no_image_set'));
		echo VmHTML::row('genericlist','COM_VIRTUEMART_ADMIN_CFG_NOIMAGEFOUND',$this->noimagelist, 'no_image_found', 'size=1', 'value', 'text', VmConfig::get('no_image_found'));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_CFG_ADDITIONAL_IMAGES', 'add_img_main', VmConfig::get('add_img_main'));
		?>
	</table>
</fieldset>
</td>
<td valign="top">
<fieldset>
	<legend><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_HOMEPAGE_SETTINGS'); ?></legend>
	<table class="admintable">
		<?php
		echo VmHTML::row('genericlist','COM_VIRTUEMART_ADMIN_CFG_MAIN_LAYOUT',$this->vmLayoutList, 'vmlayout', 'size=1', 'value', 'text', VmConfig::get('vmlayout',0));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_SHOW_STORE_DESC','show_store_desc', VmConfig::get('show_store_desc',1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_SHOW_CATEGORIES','show_categories', VmConfig::get('show_categories',1));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_CATEGORIES_PER_ROW','homepage_categories_per_row', VmConfig::get('homepage_categories_per_row',3),'',4,4);
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW','homepage_products_per_row', VmConfig::get('homepage_products_per_row',3),'',4,4);
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_SHOW_FEATURED','show_featured', VmConfig::get('show_featured',1));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_FEAT_PROD_ROWS','featured_products_rows', VmConfig::get('featured_products_rows',1),'',4,4);
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_SHOW_TOPTEN','show_topTen', VmConfig::get('show_topTen',1));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_TOPTEN_PROD_ROWS','topTen_products_rows', VmConfig::get('topTen_products_rows',1),'',4,4);
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_SHOW_RECENT','show_recent', VmConfig::get('show_recent',1));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_REC_PROD_ROWS','recent_products_rows', VmConfig::get('recent_products_rows',1),'',4,4);
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_SHOW_LATEST','show_latest', VmConfig::get('show_latest',1));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_LAT_PROD_ROWS','latest_products_rows', VmConfig::get('latest_products_rows',1),'',4,4);
		?>
	</table>
</fieldset>
<fieldset>
	<legend><?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_HOME_FEED_SETTINGS'); ?></legend>
	<table class="admintable">
		<?php
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FEED_LATEST_ENABLE','feed_latest_published', VmConfig::get('feed_latest_published',0));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_FEED_LATEST_NB','feed_latest_nb', VmConfig::get('feed_latest_nb',5),'',10,10);
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FEED_TOPTEN_ENABLE','feed_topten_published', VmConfig::get('feed_topten_published',0));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_FEED_TOPTEN_NB','feed_topten_nb', VmConfig::get('feed_topten_nb',5),'',10,10);

		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FEED_FEATURED_ENABLE','feed_featured_published', VmConfig::get('feed_featured_published',0));
		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_FEED_FEATURED_NB','feed_featured_nb', VmConfig::get('feed_featured_nb',5),'',10,10);

		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWIMAGES','feed_home_show_images', VmConfig::get('feed_home_show_images',1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWPRICES','feed_home_show_prices', VmConfig::get('feed_home_show_prices',1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWDESC','feed_home_show_description', VmConfig::get('feed_home_show_description',0));

		$options = array();
		$options[] = JHtml::_('select.option', 'product_s_desc', vmText::_('COM_VIRTUEMART_PRODUCT_FORM_S_DESC'));
		$options[] = JHtml::_('select.option', 'product_desc', vmText::_('COM_VIRTUEMART_PRODUCT_FORM_DESCRIPTION'));
		echo VmHTML::row('genericlist','COM_VIRTUEMART_ADMIN_CFG_FEED_DESCRIPTION_TYPE', $options, 'feed_home_description_type', 'size=1', 'value', 'text', VmConfig::get('feed_home_description_type'));

		echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_FEED_MAX_TEXT_LENGTH','feed_home_max_text_length', VmConfig::get('feed_home_max_text_length',500),'',10,10);
		?>
	</table>
</fieldset>
<fieldset>
	<legend class="hasTip" title="<?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_CSS_JS_SETTINGS_TIP'); ?>">
		<?php echo vmText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_CSS_JS_SETTINGS'); ?>
	</legend>
	<table class="admintable">
		<?php
		echo VmHTML::row('checkbox','COM_VIRTUEMART_CFG_FANCY','usefancy', VmConfig::get('usefancy',1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FRONT_CSS','css', VmConfig::get('css',1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FRONT_JQUERY','jquery', VmConfig::get('jquery',1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FRONT_JPRICE','jprice', VmConfig::get('jprice',1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FRONT_JSITE','jsite', VmConfig::get('jsite',1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FRONT_JCHOSEN','jchosen', VmConfig::get('jchosen',1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_FRONT_JDYNUPDATE','jdynupdate', VmConfig::get('jdynupdate',1));
		echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_ENABLE_GOOGLE_JQUERY','google_jquery', VmConfig::get('google_jquery',1));
		//echo VmHTML::row('checkbox','COM_VIRTUEMART_ADMIN_CFG_JS_CSS_MINIFIED','minified', VmConfig::get('minified',1));
		?>
	</table>
</fieldset>
</td>
</tr>
</table>
