<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.json.php 9047 2015-11-05 18:49:04Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmViewAdmin'))require(VMPATH_ADMIN.DS.'helpers'.DS.'vmviewadmin.php');
		// Load some common models
if(!class_exists('VirtueMartModelCustomfields')) require(VMPATH_ADMIN.DS.'models'.DS.'customfields.php');

/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
class VirtuemartViewProduct extends VmViewAdmin {

	var $json = array();

	function __construct( ){

		$this->type = vRequest::getCmd('type', false);
		$this->row = vRequest::getInt('row', false);
		$this->db = JFactory::getDBO();
		$this->model = VmModel::getModel('Customfields') ;

	}
	function display($tpl = null) {

		$filter = vRequest::getVar('q', vRequest::getVar('term', false) );

		$id = vRequest::getInt('id', false);
		$virtuemart_product_id = vRequest::getInt('virtuemart_product_id',array());
		if(is_array($virtuemart_product_id) && count($virtuemart_product_id) > 0){
			$product_id = (int)$virtuemart_product_id[0];
		} else {
			$product_id = (int)$virtuemart_product_id;
		}
		//$customfield = $this->model->getcustomfield();
		/* Get the task */
		if ($this->type=='relatedproducts') {
			$query = "SELECT virtuemart_product_id AS id, CONCAT(product_name, '::', product_sku) AS value
				FROM #__virtuemart_products_".VmConfig::$vmlang."
				 JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`)";
			if ($filter) $query .= " WHERE product_name LIKE '%". $this->db->escape( $filter, true ) ."%' or product_sku LIKE '%". $this->db->escape( $filter, true ) ."%' limit 0,10";
			self::setRelatedHtml($product_id,$query,'R');
		}
		else if ($this->type=='relatedcategories')
		{
			$query = "SELECT virtuemart_category_id AS id, CONCAT(category_name, '::', virtuemart_category_id) AS value
				FROM #__virtuemart_categories_".VmConfig::$vmlang;
			if ($filter) $query .= " WHERE category_name LIKE '%". $this->db->escape( $filter, true ) ."%' limit 0,10";
			self::setRelatedHtml($product_id,$query,'Z');
		}
		else if ($this->type=='custom')
		{
			$query = "SELECT CONCAT(virtuemart_custom_id, '|', custom_value, '|', field_type) AS id, CONCAT(custom_title, '::', custom_tip) AS value
				FROM #__virtuemart_customs";
			if ($filter) $query .= " WHERE custom_title LIKE '%".$filter."%' limit 0,50";
			$this->db->setQuery($query);
			$this->json['value'] = $this->db->loadObjectList();
			$this->json['ok'] = 1 ;
		}
		else if ($this->type=='fields')
		{
			if (!class_exists ('VirtueMartModelCustom')) {
				require(VMPATH_ADMIN . DS . 'models' . DS . 'custom.php');
			}
			$fieldTypes = VirtueMartModelCustom::getCustomTypes();
			$model = VmModel::getModel('custom');
			$q = 'SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs`
			WHERE (`custom_parent_id`='.$id.') ';
			$q .= 'order by `ordering` asc';
			$this->db->setQuery($q);
			$ids = $this->db->loadColumn();
			if($ids){
				array_unshift($ids,$id);
			} else {
				$ids = array($id);
			}

			foreach($ids as $k => $i){
				$p = $model->getCustom($i);
				if($p){
					$p->value = $p->custom_value;
					$rows[] = $p;
				}
			}

			$html = array ();
			foreach ($rows as $field) {
				if ($field->field_type =='deprecatedwasC' ){
					$this->json['table'] = 'childs';
					$q='SELECT `virtuemart_product_id` FROM `#__virtuemart_products` WHERE `published`=1
					AND `product_parent_id`= '.vRequest::getInt('virtuemart_product_id');
					//$this->db->setQuery(' SELECT virtuemart_product_id, product_name FROM `#__virtuemart_products` WHERE `product_parent_id` ='.(int)$product_id);
					$this->db->setQuery($q);
					if ($childIds = $this->db->loadColumn()) {
					// Get childs
						foreach ($childIds as $childId) {
							$field->custom_value = $childId;
							$display = $this->model->displayProductCustomfieldBE($field,$childId,$this->row);
							 if ($field->is_cart_attribute) $cartIcone=  'default';
							 else  $cartIcone= 'default-off';
							 $html[] = '<div class="removable">
								<td>'.$field->custom_title.'</td>
								 <td>'.$display.$field->custom_tip.'</td>
								 <td>'.vmText::_($fieldTypes[$field->field_type]).'
								'.$this->model->setEditCustomHidden($field, $this->row).'
								 </td>
								 <td><span class="vmicon vmicon-16-'.$cartIcone.'"></span></td>
								 <td></td>
								</div>';
							$this->row++;
						}
					}
				} else { //if ($field->field_type =='E') {
					$this->json['table'] = 'customPlugins';
					$colspan ='';
					if ($field->field_type =='E') {
						$this->model->bindCustomEmbeddedFieldParams($field,'E');
					} else if($field->field_type == 'C'){
						$colspan = 'colspan="2" ';
					}

					$display = $this->model->displayProductCustomfieldBE($field,$product_id,$this->row);
					 if ($field->is_cart_attribute) {
					     $cartIcone=  'default';
					 } else {
					     $cartIcone= 'default-off';
					 }
					$field->virtuemart_product_id=$product_id;
					$html[] = '
					<tr class="removable">
						<td>
							<b>'.vmText::_($fieldTypes[$field->field_type]).'</b> '.vmText::_($field->custom_title).'</span><br/>

								<span class="vmicon vmicon-16-'.$cartIcone.'"></span>
								<span class="vmicon vmicon-16-move"></span>
								<span class="vmicon vmicon-16-remove"></span>

						'.$this->model->setEditCustomHidden($field, $this->row).'
					 	</td>
							<td '.$colspan.'>'.$display.'</td>
						 </tr>
					</tr>';
					$this->row++;

				}
			}

			$this->json['value'] = $html;
			$this->json['ok'] = 1 ;
		} else if ($this->type=='userlist')
		{
			$status = vRequest::getvar('status');
			$productShoppers=0;
			if ($status) {
				$productModel = VmModel::getModel('product');
				$productShoppers = $productModel->getProductShoppersByStatus($product_id ,$status);
			}
			if(!class_exists('ShopFunctions'))require(VMPATH_ADMIN.DS.'helpers'.DS.'shopfunctions.php');
			$html = ShopFunctions::renderProductShopperList($productShoppers);
			$this->json['value'] = $html;

		} else $this->json['ok'] = 0 ;

		if ( empty($this->json)) {
			$this->json['value'] = null;
			$this->json['ok'] = 1 ;
		}

		echo vmJsApi::safe_json_encode($this->json);

	}

	function setRelatedHtml($product_id,$query,$fieldType) {

		$this->db->setQuery($query);
		$this->json = $this->db->loadObjectList();

		$query = 'SELECT * FROM `#__virtuemart_customs` WHERE field_type ="'.$fieldType.'" ';
		$this->db->setQuery($query);
		$custom = $this->db->loadObject();
		if(!$custom) {
			vmdebug('setRelatedHtml could not find $custom for field type '.$fieldType);
			return false;
		}
		$custom->virtuemart_product_id = $product_id;
		foreach ($this->json as &$related) {

			$custom->customfield_value = $related->id;

			$display = $this->model->displayProductCustomfieldBE($custom,$related->id,$this->row);
			$html = '<div class="vm_thumb_image">
				<span class="vmicon vmicon-16-move"></span>
				<div class="vmicon vmicon-16-remove"></div>
				<span>'.$display.'</span>
				'.$this->model->setEditCustomHidden($custom, $this->row).'
				</div>';

			$related->label = $html;

		}
	}

}
// pure php no closing tag
