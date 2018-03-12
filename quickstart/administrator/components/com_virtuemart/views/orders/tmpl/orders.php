<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage
 * @author
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: orders.php 9188 2016-02-27 23:10:51Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
AdminUIHelper::startAdminArea ($this);

$styleDateCol = 'style="width:5%;min-width:110px"';
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="header">
		<div id="filterbox">
			<table>
				<tr>
					<td align="left" style="min-width:480px;width:40%;">
						<?php echo $this->displayDefaultViewSearch ('COM_VIRTUEMART_ORDER_PRINT_NAME'); ?>
						<?php echo vmText::_ ('COM_VIRTUEMART_ORDERSTATUS') . ':' . $this->lists['state_list']; ?>

					</td>
					<td align="right" style="min-width:290px;width:30%;">
						<?php echo vmText::_ ('COM_VIRTUEMART_BULK_ORDERSTATUS') . $this->lists['bulk_state_list']; ?>
					</td>
					<td align="left" style="min-width:370px;width:30%;">
						<?php echo VmHTML::checkbox ('customer_notified', 0) . vmText::_ ('COM_VIRTUEMART_ORDER_LIST_NOTIFY'); ?>
						<?php echo VmHTML::checkbox ('customer_send_comment', 1) . vmText::_ ('COM_VIRTUEMART_ORDER_HISTORY_INCLUDE_COMMENT'); ?>
						<?php echo VmHTML::checkbox ('update_lines', 1) . vmText::_ ('COM_VIRTUEMART_ORDER_UPDATE_LINESTATUS'); ?>
						<textarea class="element-hidden vm-order_comment vm-showable" name="comments" cols="5" rows="5"></textarea>
						<?php echo JHtml::_ ('link', '#', vmText::_ ('COM_VIRTUEMART_ADD_COMMENT'), array('class' => 'show_comment')); ?>
					</td>
					<td>
						<?php echo $this->lists['vendors'] ?>
					</td>
				</tr>
			</table>
		</div>
		<div id="resultscounter"><?php echo $this->pagination->getResultsCounter (); ?></div>
	</div>
<div style="text-align: left;">
	<table class="adminlist table table-striped" cellspacing="0" cellpadding="0">
		<thead>
		<tr>
			<th class="admin-checkbox"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)"/></th>
			<th width="8%"><?php echo $this->sort ('order_number', 'COM_VIRTUEMART_ORDER_LIST_NUMBER')  ?></th>
			<th width="26%"><?php echo $this->sort ('order_name', 'COM_VIRTUEMART_ORDER_PRINT_NAME')  ?></th>
			<th width="18%"><?php echo $this->sort ('order_email', 'COM_VIRTUEMART_EMAIL')  ?></th>
			<th width="18%"><?php echo $this->sort ('payment_method', 'COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL')  ?></th>
			<th style="min-width:110px;width:5%;"><?php echo vmText::_ ('COM_VIRTUEMART_PRINT_VIEW'); ?></th>
			<th class="admin-dates"><?php echo $this->sort ('created_on', 'COM_VIRTUEMART_ORDER_CDATE')  ?></th>
			<th class="admin-dates"><?php echo $this->sort ('modified_on', 'COM_VIRTUEMART_ORDER_LIST_MDATE')  ?></th>
			<th><?php echo $this->sort ('order_status', 'COM_VIRTUEMART_STATUS')  ?></th>
			<th style="min-width:130px;width:5%;"><?php echo vmText::_ ('COM_VIRTUEMART_ORDER_LIST_NOTIFY'); ?></th>
			<th width="10%"><?php echo $this->sort ('order_total', 'COM_VIRTUEMART_TOTAL')  ?></th>
			<th><?php echo $this->sort ('virtuemart_order_id', 'COM_VIRTUEMART_ORDER_LIST_ID')  ?></th>

		</tr>
		</thead>
		<tbody>
		<?php
		if (count ($this->orderslist) > 0) {
			$i = 0;
			$k = 0;
			$keyword = vRequest::getCmd ('keyword');
;
			foreach ($this->orderslist as $key => $order) {
				$checked = JHtml::_ ('grid.id', $i, $order->virtuemart_order_id);
				?>
			<tr class="row<?php echo $k; ?>">
				<!-- Checkbox -->
				<td class="admin-checkbox"><?php echo $checked; ?></td>
				<!-- Order id -->
				<?php
				$link = 'index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id=' . $order->virtuemart_order_id;
				?>
				<td><?php echo JHtml::_ ('link', JRoute::_ ($link, FALSE), $order->order_number, array('title' => vmText::_ ('COM_VIRTUEMART_ORDER_EDIT_ORDER_NUMBER') . ' ' . $order->order_number)); ?></td>

				<td>
					<?php
					if ($order->virtuemart_user_id) {
						$userlink = JROUTE::_ ('index.php?option=com_virtuemart&view=user&task=edit&virtuemart_user_id[]=' . $order->virtuemart_user_id, FALSE);
						echo JHtml::_ ('link', JRoute::_ ($userlink, FALSE), $order->order_name, array('title' => vmText::_ ('COM_VIRTUEMART_ORDER_EDIT_USER') . ' ' .  $order->order_name));
					} else {
						echo $order->order_name;
					}
					?>
				</td>
				<td>
					<?php
					echo $order->order_email;
					?>
				</td>
				<!-- Payment method -->
				<td><?php echo $order->payment_method; ?></td>
				<!-- Print view -->
				<?php
					$this->createPrintLinks($order,$print_link,$deliverynote_link,$invoice_link);
				?>
				<td><?php echo $print_link; echo $deliverynote_link; echo $invoice_link; ?></td>
				<!-- Order date -->
				<td><?php echo vmJsApi::date ($order->created_on, 'LC2', TRUE); ?></td>
				<!-- Last modified -->
				<td><?php echo vmJsApi::date ($order->modified_on, 'LC2', TRUE); ?></td>
				<!-- Status -->
				<td style="position:relative;">
					<?php echo JHtml::_ ('select.genericlist', $this->orderstatuses, "orders[" . $order->virtuemart_order_id . "][order_status]", 'class="orderstatus_select"', 'order_status_code', 'order_status_name', $order->order_status, 'order_status' . $i, TRUE); ?>
					<input type="hidden" name="orders[<?php echo $order->virtuemart_order_id; ?>][current_order_status]" value="<?php echo $order->order_status; ?>"/>
					<input type="hidden" name="orders[<?php echo $order->virtuemart_order_id; ?>][coupon_code]" value="<?php echo $order->coupon_code; ?>"/>
					<br/>
					<textarea class="element-hidden vm-order_comment vm-showable" name="orders[<?php echo $order->virtuemart_order_id; ?>][comments]" cols="5" rows="5"></textarea>
					<?php echo JHtml::_ ('link', '#', vmText::_ ('COM_VIRTUEMART_ADD_COMMENT'), array('class' => 'show_comment')); ?>
				</td>
				<!-- Update -->
				<td><?php echo VmHTML::checkbox ('orders[' . $order->virtuemart_order_id . '][customer_notified]', 0) . vmText::_ ('COM_VIRTUEMART_ORDER_LIST_NOTIFY'); ?>
					<br/>
					<?php echo VmHTML::checkbox ('orders[' . $order->virtuemart_order_id . '][customer_send_comment]', 1) . vmText::_ ('COM_VIRTUEMART_ORDER_HISTORY_INCLUDE_COMMENT'); ?>
					<br/>
					<?php echo VmHTML::checkbox ('orders[' . $order->virtuemart_order_id . '][update_lines]', 1) . vmText::_ ('COM_VIRTUEMART_ORDER_UPDATE_LINESTATUS'); ?>
				</td>
				<!-- Total -->
				<td><?php echo $order->order_total; ?></td>
				<td><?php echo JHtml::_ ('link', JRoute::_ ($link, FALSE), $order->virtuemart_order_id, array('title' => vmText::_ ('COM_VIRTUEMART_ORDER_EDIT_ORDER_ID') . ' ' . $order->virtuemart_order_id)); ?></td>

			</tr>
				<?php
				$k = 1 - $k;
				$i++;
			}
		}
		?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="12">
				<?php echo $this->pagination->getListFooter (); ?>
			</td>
		</tr>
		</tfoot>
	</table>
</div>
	<!-- Hidden Fields -->
	<?php echo $this->addStandardHiddenToForm (); ?>
</form>
<?php AdminUIHelper::endAdminArea ();

$j = 'function set2status(){

	var newStatus = jQuery("#order_status_code_bulk").val();

	var customer_notified = jQuery("input[name=\'customer_notified\']").is( ":checked" );
	var customer_send_comment = jQuery("input[name=\'customer_send_comment\']").is( ":checked" );
	var update_lines = jQuery("input[name=\'update_lines\']").is( ":checked" );
	var comments = jQuery("textarea[name=\'comments\']").val();

	field = document.getElementsByName("cid[]");
	var fname = "";
	var sel = 0;
	for (i = 0; i < field.length; i++){
		if (field[i].checked){
			fname = "orders[" + field[i].value + "]";
			jQuery("input[name=\'"+fname+"[customer_notified]\']").prop("checked",customer_notified);
			jQuery("input[name=\'"+fname+"[customer_send_comment]\']").prop("checked",customer_send_comment);
			jQuery("input[name=\'"+fname+"[update_lines]\']").prop("checked",update_lines);
			jQuery("textarea[name=\'"+fname+"[comments]\']").text(comments);
			console.log("Mist ",jQuery("input[name=\'"+fname+"[comments]\']"));
			jQuery("#order_status"+i).val(newStatus).trigger("chosen:updated").trigger("liszt:updated");
		}
	}

}';
vmJsApi::addJScript('set2status',$j);

$j = "jQuery('.show_comment').click(function() {
		jQuery(this).prev('.element-hidden').show();
		return false
		});
		jQuery('.element-hidden').mouseleave(function() {
		jQuery(this).hide();
		});
		jQuery('.element-hidden').mouseout(function() {
		jQuery(this).hide();
	});";
vmJsApi::addJScript('show_comment',$j);

$orderstatusForShopperEmail = VmConfig::get('email_os_s',array('U','C','S','R','X'));
if(!is_array($orderstatusForShopperEmail)) $orderstatusForShopperEmail = array($orderstatusForShopperEmail);
$jsOrderStatusShopperEmail = vmJsApi::safe_json_encode($orderstatusForShopperEmail);

$j ='
	jQuery(document).ready(function() {
		jQuery(".orderstatus_select").change( function() {

			var name = jQuery(this).attr("name");
			var brindex = name.indexOf("orders[");
			if ( brindex >= 0){
				//yeh, yeh, maybe not the most elegant way, but it does, what it should
				var s = name.indexOf("[")+1;
				var e = name.indexOf("]");
				var id = name.substring(s,e);

				var orderstatus = '.$jsOrderStatusShopperEmail.';
				var selected = jQuery(this).val();
				var selStr = "[name=\"orders["+id+"][customer_notified]\"]";
				var elem = jQuery(selStr);

				if(jQuery.inArray(selected, orderstatus)!=-1){
					elem.attr("checked",true);
					// for the checkbox    
					jQuery(this).parent().parent().find("input[name=\"cid[]\"]").attr("checked",true);
				} else {
					elem.attr("checked",false);
				}
			}
		});
	});';

vmJsApi::addJScript('setChecksByOrderStatus',$j);