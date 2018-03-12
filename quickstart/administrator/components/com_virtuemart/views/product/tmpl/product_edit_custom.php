<?php
/**
*
* Handle the Product Custom Fields
*
* @package	VirtueMart
* @subpackage Product
* @author RolandD, Patrick khol, Valérie Isaksen
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id$
*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<table id="customfieldsTable" width="100%">
	<tr>
		<td valign="top" width="%100">

		<?php
			$i=0;
			$tables= array('categories'=>'','products'=>'','fields'=>'','customPlugins'=>'',);
			if (isset($this->product->customfields)) {
				$customfieldsModel = VmModel::getModel('customfields');
				$i=0;

				foreach ($this->product->customfields as $k=>$customfield) {

					//vmdebug('displayProductCustomfieldBE',$customfield);

					$customfield->display = $customfieldsModel->displayProductCustomfieldBE ($customfield, $this->product, $i);

					if ($customfield->is_cart_attribute) $cartIcone=  'default';
					else  $cartIcone= 'default-off';
					if ($customfield->field_type == 'Z') {
						// R: related categories
						$tables['categories'] .=  '
							<div class="vm_thumb_image">
								<span class="vmicon vmicon-16-move"></span>
								<div class="vmicon vmicon-16-remove"></div>
								<span>'.$customfield->display.'</span>
								'.VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'</div>';

					} elseif ($customfield->field_type == 'R') {
					// R: related products
						$tables['products'] .=  '
							<div class="vm_thumb_image">
								<span class="vmicon vmicon-16-move"></span>
								<div class="vmicon vmicon-16-remove"></div>
								<span>'.$customfield->display.'</span>
								'.VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'</div>';

					} else {

						$checkValue = $customfield->virtuemart_customfield_id;
						$title = '';
						$text = '';
						if(isset($this->fieldTypes[$customfield->field_type])){
							$type = $this->fieldTypes[$customfield->field_type];
						} else {
							$type = 'deprecated';
						}
						$colspan = '';

						if($customfield->field_type == 'C'){
							$colspan = 'colspan="2" ';
						}
						if($customfield->override!=0 or $customfield->disabler!=0){

							if(!empty($customfield->disabler)) $checkValue = $customfield->disabler;
							if(!empty($customfield->override)) $checkValue = $customfield->override;
							$title = vmText::sprintf('COM_VIRTUEMART_CUSTOM_OVERRIDE',$checkValue).'</br>';
							if($customfield->disabler!=0){
								$title = vmText::sprintf('COM_VIRTUEMART_CUSTOM_DISABLED',$checkValue).'</br>';
							}

							if($customfield->override!=0){
								$title = vmText::sprintf('COM_VIRTUEMART_CUSTOM_OVERRIDE',$checkValue).'</br>';
							}

						} else if($customfield->virtuemart_product_id==$this->product->product_parent_id){
							$title = vmText::_('COM_VIRTUEMART_CUSTOM_INHERITED').'</br>';
						}

						if(!empty($title)){
							$text = '<span style="white-space: nowrap;" class="hasTip" title="'.htmlentities(vmText::_('COM_VIRTUEMART_CUSTOMFLD_DIS_DER_TIP')).'">d:'.VmHtml::checkbox('field[' . $i . '][disabler]',$customfield->disabler,$checkValue).'</span>
							<span style="white-space: nowrap;" class="hasTip" title="'.htmlentities(vmText::_('COM_VIRTUEMART_DIS_DER_CUSTOMFLD_OVERR_DER_TIP')).'">o:'.VmHtml::checkbox('field['.$i.'][override]',$customfield->override,$checkValue).'</span>';
						}

						$tables['fields'] .= '<tr class="removable">
							<td >
							<b>'.vmText::_($type).'</b> '.vmText::_($customfield->custom_title).'</span><br/>
								'.$title.' '.$text.'
								<span class="vmicon vmicon-16-'.$cartIcone.'"></span>';
						if($customfield->virtuemart_product_id==$this->product->virtuemart_product_id or $customfield->override!=0){
							$tables['fields'] .= '<span class="vmicon vmicon-16-move"></span>
							<span class="vmicon vmicon-16-remove"></span>';
						}
						$tables['fields'] .= VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
						.'</td>
							<td '.$colspan.'>'.$customfield->display.'</td>
						 </tr>';
						}

					$i++;
				}
			}

			 $emptyTable = '
				<tr>
					<td colspan="8">'.vmText::_( 'COM_VIRTUEMART_CUSTOM_NO_TYPES').'</td>
				<tr>';
			?>
			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo vmText::_('COM_VIRTUEMART_RELATED_CATEGORIES'); ?></legend>
				<?php echo vmText::_('COM_VIRTUEMART_CATEGORIES_RELATED_SEARCH'); ?>
				<div class="jsonSuggestResults" style="width: auto;">
					<input type="text" size="40" name="search" id="relatedcategoriesSearch" value="" />
					<button class="reset-value btn"><?php echo vmText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>
				<div id="custom_categories" class="ui-sortable" ><?php echo  $tables['categories']; ?></div>
			</fieldset>
			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo vmText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></legend>
				<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_RELATED_SEARCH'); ?>
				<div class="jsonSuggestResults" style="width: auto;">
					<input type="text" size="40" name="search" id="relatedproductsSearch" value="" />
					<button class="reset-value btn"><?php echo vmText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>
				<div id="custom_products" class="ui-sortable"><?php echo  $tables['products']; ?></div>
			</fieldset>

			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo vmText::_('COM_VIRTUEMART_CUSTOM_FIELD_TYPE' );?></legend>
				<div class="inline"><?php echo $this->customsList; ?></div>

				<table id="custom_fields" class="adminlist" cellspacing="0" cellpadding="2">

					<thead>
					<tr class="row1">
						<th style="min-width:140px;width:5%;"><?php echo vmText::_('COM_VIRTUEMART_TITLE');?></th>
						<th width="100px"><?php echo vmText::_('COM_VIRTUEMART_CART_PRICE');?></th>
						<th><?php echo vmText::_('COM_VIRTUEMART_VALUE');?></th>
					</tr>
					</thead>

					<tbody id="custom_field">
						<?php
						if ($tables['fields']) echo $tables['fields'] ;
						else echo $emptyTable;
						?>
					</tbody>
				</table><!-- custom_fields -->
			</fieldset>
			<!--fieldset style="background-color:#F9F9F9;">
				<legend><?php echo vmText::_('COM_VIRTUEMART_CUSTOM_EXTENSION'); ?></legend>
				<div id="custom_customPlugins"><?php echo  $tables['customPlugins']; ?></div>
			</fieldset-->
		</td>

	</tr>
</table>


<div style="clear:both;"></div>

<?php
$admin = '';
$app = JFactory::getApplication();
$l = 'index.php?option=com_virtuemart&view=product&task=getData&format=json&virtuemart_product_id='.$this->product->virtuemart_product_id;
if($app->isAdmin()){
	$jsonLink = JURI::root(false).'administrator/'.$l;
} else {
	$jsonLink = JRoute::_($l);
}


$jsCsort = "
	nextCustom =".$i.";

	jQuery(document).ready(function(){
		jQuery('#custom_field').sortable({cursorAt: { top: 0, left: 0 },handle: '.vmicon-16-move'});
		// Need to declare the update routine outside the sortable() function so
		// that it can be called when adding new customfields
		jQuery('#custom_field').bind('sortupdate', function(event, ui) {
			jQuery(this).find('.ordering').each(function(index,element) {
				jQuery(element).val(index);
			});
		});
		jQuery('#custom_categories').sortable({cursorAt: { top: 0, left: 0 },handle: '.vmicon-16-move'});
		jQuery('#custom_categories').bind('sortupdate', function(event, ui) {
			jQuery(this).find('.ordering').each(function(index,element) {
				jQuery(element).val(index);
			});
		});
		jQuery('#custom_products').sortable({cursorAt: { top: 0, left: 0 },handle: '.vmicon-16-move'});
		jQuery('#custom_products').bind('sortupdate', function(event, ui) {
			jQuery(this).find('.ordering').each(function(index,element) {
				jQuery(element).val(index);
			});
		});
	});
	jQuery('select#customlist').chosen().change(function() {
		selected = jQuery(this).find( 'option:selected').val() ;
		jQuery.getJSON('".$jsonLink."&type=fields&id='+selected+'&row='+nextCustom,
		function(data) {
			jQuery.each(data.value, function(index, value){
				jQuery('#custom_field').append(value);
				jQuery('#custom_field').trigger('sortupdate');
			});
		});
		nextCustom++;
	});

	jQuery.each(jQuery('.cvard'), function(i,val){
		jQuery(val).chosen().change(function() {
			quantity = jQuery(this).parent().find('input[type=\"hidden\"]');
			quantity.val(jQuery(this).val());
		});
	});

	jQuery('input#relatedproductsSearch').autocomplete({
		source: '".$jsonLink."&type=relatedproducts&row='+nextCustom,
		select: function(event, ui){
			jQuery('#custom_products').append(ui.item.label);
			jQuery('#custom_products').trigger('sortupdate');
			nextCustom++;
			jQuery(this).autocomplete( 'option' , 'source' , '".$jsonLink."&type=relatedproducts&row='+nextCustom )
			jQuery('input#relatedproductsSearch').autocomplete( 'option' , 'source' , '".JURI::root(false)."administrator/index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedproducts&row='+nextCustom )
		},
		minLength:1,
		html: true
	});
	jQuery('input#relatedcategoriesSearch').autocomplete({

		source: '".$jsonLink."&type=relatedcategories&row='+nextCustom,
		select: function(event, ui){
			jQuery('#custom_categories').append(ui.item.label);
			jQuery('#custom_categories').trigger('sortupdate');
			nextCustom++;
			jQuery(this).autocomplete( 'option' , 'source' , '".$jsonLink."&type=relatedcategories&row='+nextCustom )
			jQuery('input#relatedcategoriesSearch').autocomplete( 'option' , 'source' , '".JURI::root(false)."administrator/index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories&row='+nextCustom )
		},
		minLength:1,
		html: true
	});


eventNames = 'click.remove keydown.remove change.remove focus.remove'; // all events you wish to bind to

function removeParent() {jQuery('#customfieldsParent').remove(); }

jQuery('#customfieldsTable').find('input').each(function(i){
	current = jQuery(this);
	current.click(function(){
			jQuery('#customfieldsParent').remove();
		});
});
    
";
vmJsApi::addJScript('cSort',$jsCsort);
