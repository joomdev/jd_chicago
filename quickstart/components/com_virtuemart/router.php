<?php
if(  !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
 *
 * @package VirtueMart
 * @author Kohl Patrick
 * @author Max Milbers
 * @subpackage router
 * @version $Id$
 * @copyright Copyright (C) 2009 - 2016 by the VirtueMart Team and authors
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);


function virtuemartBuildRoute(&$query) {

	$segments = array();

	$helper = vmrouterHelper::getInstance($query);
	// simple route , no work , for very slow server or test purpose
	if ($helper->router_disabled) {
		foreach ($query as $key => $value){
			if  ($key != 'option')  {
				if ($key != 'Itemid') {
					$segments[]=$key.'/'.$value;
					unset($query[$key]);
				}
			}
		}
		return $segments;
	}

	if ($helper->edit) return $segments;

	$view = '';

	$jmenu = $helper->menu ;
	//vmdebug('virtuemartBuildRoute $jmenu',$helper->query,$helper->activeMenu,$helper->menuVmitems);
	if(isset($query['langswitch'])) unset($query['langswitch']);


/*	//a bit hacky, but should work
	$oLang = VmConfig::$vmlang;
	$app = JFactory::getApplication();
	if($l = $app->getUserState('language',false)){
		vmdebug('hm getUserState',$query);
	}
	if(isset($query['language'])){
		//vmdebug('hm',$query);
		VmConfig::$vmlang = $query['language'];
	} else if($l = vRequest::getCmd('language',false)){
		$alangs = (array)VmConfig::get('active_languages',array());
		$l = strtolower(strtr($l,'-','_'));
		if(in_array($l, $alangs)) {
			vmdebug('hm re',$query);
			VmConfig::$vmlang = $l;
		}

	}
*/
	if(isset($query['view'])){
		$view = $query['view'];
		unset($query['view']);
	}

	switch ($view) {
		case 'virtuemart';
			$query['Itemid'] = $jmenu['virtuemart'] ;
			break;
		case 'category';
			$start = null;
			$limitstart = null;
			$limit = null;

			if ( isset($query['virtuemart_manufacturer_id'])  ) {
				$segments[] = $helper->lang('manufacturer').'/'.$helper->getManufacturerName($query['virtuemart_manufacturer_id']) ;
				unset($query['virtuemart_manufacturer_id']);
			}
			if ( isset($query['search'])  ) {
				$segments[] = $helper->lang('search') ;
				unset($query['search']);
			}
			if ( isset($query['keyword'] )) {
				$segments[] = $query['keyword'];
				unset($query['keyword']);
			}

			if ( isset($query['virtuemart_category_id']) ) {
				$categoryRoute = null;
				if($helper->full or !isset($query['virtuemart_product_id'])){
					$categoryRoute = $helper->getCategoryRoute($query['virtuemart_category_id']);
					if ($categoryRoute->route) {
						$segments[] = $categoryRoute->route;
					}
				}

				$menuCatItemId = $helper->getMenuCatItemId($query['virtuemart_category_id']);
				if(!empty($menuCatItemId)) {
					$query['Itemid'] = $menuCatItemId;
				} else if(isset($query['virtuemart_category_id']) and isset($jmenu['virtuemart_category_id'][$query['virtuemart_category_id']])) {
					$query['Itemid'] = $jmenu['virtuemart_category_id'][$query['virtuemart_category_id']];
				} else {
					if($categoryRoute===null) $categoryRoute = $helper->getCategoryRoute($query['virtuemart_category_id']);
					//http://forum.virtuemart.net/index.php?topic=121642.0
					if (!empty($categoryRoute->itemId)) {
						$query['Itemid'] = $categoryRoute->itemId;
					} else {
						$query['Itemid'] = vRequest::get('Itemid',false);
					}
				}
				unset($query['virtuemart_category_id']);
			}
			if ( isset($jmenu['category']) ) $query['Itemid'] = $jmenu['category'];

			if ( isset($query['orderby']) ) {
				$segments[] = $helper->lang('by').','.$helper->lang( $query['orderby']) ;
				unset($query['orderby']);
			}

			if ( isset($query['dir']) ) {
				if ($query['dir'] =='DESC'){
					$dir = 'dirDesc';
				} else {
					$dir = 'dirAsc';
				}
				$segments[] = $dir;
				unset($query['dir']);
			}

			// Joomla replace before route limitstart by start but without SEF this is start !
			if ( isset($query['limitstart'] ) ) {
				$limitstart = $query['limitstart'] ;
				unset($query['limitstart']);
			}
			if ( isset($query['start'] ) ) {
				$start = $query['start'] ;
				unset($query['start']);
			}
			if ( isset($query['limit'] ) ) {
				$limit = $query['limit'] ;
				unset($query['limit']);
			}
			if ($start !== null &&  $limitstart!== null ) {
				//$segments[] = $helper->lang('results') .',1-'.$start ;
			} else if ( $start>0 ) {
				// using general limit if $limit is not set
				if ($limit === null) $limit= vmrouterHelper::$limit ;

				$segments[] = $helper->lang('results') .','. ($start+1).'-'.($start+$limit);
			} else if ($limit !== null && $limit != vmrouterHelper::$limit ) $segments[] = $helper->lang('results') .',1-'.$limit ;//limit change

			break;
		//Shop product details view
		case 'productdetails';

			$virtuemart_product_id = false;
			if (isset($jmenu['virtuemart_product_id']) and isset($jmenu['virtuemart_product_id'][ $query['virtuemart_product_id'] ] ) ) {
				$query['Itemid'] = $jmenu['virtuemart_product_id'][$query['virtuemart_product_id']];
				unset($query['virtuemart_product_id']);
				unset($query['virtuemart_category_id']);
				unset($query['virtuemart_manufacturer_id']);
			} else {
				if(isset($query['virtuemart_product_id'])) {
					if ($helper->use_id) $segments[] = $query['virtuemart_product_id'];
					$virtuemart_product_id = $query['virtuemart_product_id'];
					unset($query['virtuemart_product_id']);
				}

				if($helper->full){
					if(empty( $query['virtuemart_category_id'])){
						$query['virtuemart_category_id'] = $helper->getParentProductcategory($virtuemart_product_id);
					}
					if(!empty( $query['virtuemart_category_id'])){
						$categoryRoute = $helper->getCategoryRoute($query['virtuemart_category_id']);
						if ($categoryRoute->route) $segments[] = $categoryRoute->route;
						if ($categoryRoute->itemId) $query['Itemid'] = $categoryRoute->itemId;
						else $query['Itemid'] = $jmenu['virtuemart'];
					} else {
						$query['Itemid'] = $jmenu['virtuemart']?$jmenu['virtuemart']:@$jmenu['virtuemart_category_id'][0];
					}
				} else {
					//Itemid is needed even if seo_full = 0
					$query['Itemid'] = $jmenu['virtuemart']?$jmenu['virtuemart']:@$jmenu['virtuemart_category_id'][0];
				}
				unset($query['limitstart']);
				unset($query['limit']);
				unset($query['virtuemart_category_id']);
				unset($query['virtuemart_manufacturer_id']);

				if($virtuemart_product_id)
					$segments[] = $helper->getProductName($virtuemart_product_id);
			}
			break;
		case 'manufacturer';

			if(isset($query['virtuemart_manufacturer_id'])) {
				if (isset($jmenu['virtuemart_manufacturer_id'][ $query['virtuemart_manufacturer_id'] ] ) ) {
					$query['Itemid'] = $jmenu['virtuemart_manufacturer_id'][$query['virtuemart_manufacturer_id']];
				} else {
					$segments[] = $helper->lang('manufacturers').'/'.$helper->getManufacturerName($query['virtuemart_manufacturer_id']) ;
					if ( isset($jmenu['manufacturer']) ) $query['Itemid'] = $jmenu['manufacturer'];
					else $query['Itemid'] = $jmenu['virtuemart'];
				}
				unset($query['virtuemart_manufacturer_id']);
			} else {
				if ( isset($jmenu['manufacturer']) ) $query['Itemid'] = $jmenu['manufacturer'];
				else $query['Itemid'] = $jmenu['virtuemart'];
			}
			break;
		case 'user';

			if ( isset($jmenu['user']) ) $query['Itemid'] = $jmenu['user'];
			else {
				$segments[] = $helper->lang('user') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}

			if (isset($query['task'])) {
				//vmdebug('my task in user view',$query['task']);
				if($query['task']=='editaddresscart'){
					if ($query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddresscartST') ;
					} else {
						$segments[] = $helper->lang('editaddresscartBT') ;
					}
				}

				else if($query['task']=='editaddresscheckout'){
					if ($query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddresscheckoutST') ;
					} else {
						$segments[] = $helper->lang('editaddresscheckoutBT') ;
					}
				}

				else if($query['task']=='editaddress'){

					if (isset($query['addrtype']) and $query['addrtype'] == 'ST'){
						$segments[] = $helper->lang('editaddressST') ;
					} else {
						$segments[] = $helper->lang('editaddressBT') ;
					}
				}
				else if($query['task']=='addST'){
					$segments[] = $helper->lang('addST') ;
				}
				else {
					$segments[] =  $helper->lang($query['task']);
				}
				unset ($query['task'] , $query['addrtype']);
			}

			break;
		case 'vendor';
			/* VM208 */
			if(isset($query['virtuemart_vendor_id'])) {
				if (isset($jmenu['virtuemart_vendor_id'][ $query['virtuemart_vendor_id'] ] ) ) {
					$query['Itemid'] = $jmenu['virtuemart_vendor_id'][$query['virtuemart_vendor_id']];
				} else {
					if ( isset($jmenu['vendor']) ) {
						$query['Itemid'] = $jmenu['vendor'];
					} else {
						$segments[] = $helper->lang('vendor') ;
						$query['Itemid'] = $jmenu['virtuemart'];
					}
				}
			} else if ( isset($jmenu['vendor']) ) {
				$query['Itemid'] = $jmenu['vendor'];
			} else {
				$segments[] = $helper->lang('vendor') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}
			if (isset($query['virtuemart_vendor_id'])) {
				$segments[] =  $helper->getVendorName($query['virtuemart_vendor_id']) ;
				unset ($query['virtuemart_vendor_id'] );
			}


			break;
		case 'cart';
			if (isset($jmenu['cart'])) {
				$query['Itemid'] = $jmenu['cart'];
			} else if ( isset($jmenu['virtuemart']) ) {
				$query['Itemid'] = $jmenu['virtuemart'];
				$segments[] = $helper->lang('cart') ;
			} else {
				// the worst
				$segments[] = $helper->lang('cart') ;
			}
			break;
		case 'orders';
			if ( isset($jmenu['orders']) ) $query['Itemid'] = $jmenu['orders'];
			else {
				$segments[] = $helper->lang('orders') ;
				$query['Itemid'] = $jmenu['virtuemart'];
			}
			if ( isset($query['order_number']) ) {
				$segments[] = 'number/'.$query['order_number'];
				unset ($query['order_number'],$query['layout']);
			} else if ( isset($query['virtuemart_order_id']) ) {
				$segments[] = 'id/'.$query['virtuemart_order_id'];
				unset ($query['virtuemart_order_id'],$query['layout']);
			}
			break;

		// sef only view
		default ;
			$segments[] = $view;

			//VmConfig::$vmlang = $oLang;
	}


	if (isset($query['task'])) {
		$segments[] = $helper->lang($query['task']);
		unset($query['task']);
	}
	if (isset($query['layout'])) {
		$segments[] = $helper->lang($query['layout']) ;
		unset($query['layout']);
	}

	return $segments;
}

/* This function can be slower because is used only one time  to find the real URL*/
function virtuemartParseRoute($segments) {

	$vars = array();

	$helper = vmrouterHelper::getInstance();
	if ($helper->router_disabled) {
		$total = count($segments);
		for ($i = 0; $i < $total; $i=$i+2) {
			$vars[ $segments[$i] ] = $segments[$i+1];
		}
		return $vars;
	}

	if (empty($segments)) {
		return $vars;
	}

	foreach  ($segments as &$value) {
		$value = str_replace(':', '-', $value);
	}

	$splitted = explode(',',end($segments),2);

	if ( $helper->compareKey($splitted[0] ,'results')){
		array_pop($segments);
		$results = explode('-',$splitted[1],2);
		//Pagination has changed, removed the -1 note by Max Milbers NOTE: Works on j1.5, but NOT j1.7
		// limitstart is swapped by joomla to start ! See includes/route.php
		if ($start = $results[0]-1) $vars['limitstart'] = $start;
		else $vars['limitstart'] = 0 ;
		$vars['limit'] = $results[1]-$results[0]+1;

	} else {
		$vars['limitstart'] = 0 ;
		if(vmrouterHelper::$limit === null){
			vmrouterHelper::$limit = VmConfig::get('list_limit', 20);
		}
		$vars['limit'] = vmrouterHelper::$limit;

	}

	if (empty($segments)) {
		$vars['view'] = 'category';
		$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
		return $vars;
	}

	//Translation of the ordering direction is not really useful and costs just energy
	if ( end($segments) == 'dirDesc' ){
		$vars['dir'] ='DESC' ;
		array_pop($segments);
		if (empty($segments)) {
			$vars['view'] = 'category';
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			return $vars;
		}
	} else
	if ( end($segments) == 'dirAsc' ){
		$vars['dir'] ='ASC' ;
		array_pop($segments);
		if (empty($segments)) {
			$vars['view'] = 'category';
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			return $vars;
		}
	}

	$orderby = explode(',',end($segments),2);
	if ( count($orderby) == 2 and $helper->compareKey($orderby[0] , 'by') ) {
		$vars['orderby'] = $helper->getOrderingKey($orderby[1]) ;
		// array_shift($segments);
		array_pop($segments);

		if (empty($segments)) {
			$vars['view'] = 'category';
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			return $vars;
		}
	}

	if ( $segments[0] == 'product') {
		$vars['view'] = 'product';
		$vars['task'] = $segments[1];
		$vars['tmpl'] = 'component';
		return $vars;
	}

	if ( $segments[0] == 'checkout' or $segments[0] == 'cart' or $helper->compareKey($segments[0] ,'cart')) {
		$vars['view'] = 'cart';
		if(count($segments) > 1){ // prevent putting value of view variable into task variable by Viktor Jelinek
			$vars['task'] = array_pop($segments);
		}
		return $vars;
	}

	if (  $helper->compareKey($segments[0] ,'manufacturer') ) {
		if(!empty($segments[1])){
			array_shift($segments);
			$vars['virtuemart_manufacturer_id'] =  $helper->getManufacturerId($segments[0]);
		}

		array_shift($segments);
		// OSP 2012-02-29 removed search malforms SEF path and search is performed
		if (empty($segments)) {
			$vars['view'] = 'category';
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			return $vars;
		}

	}
	/* added in vm208 */
// if no joomla link: vendor/vendorname/layout
// if joomla link joomlalink/vendorname/layout
	if (  $helper->compareKey($segments[0] ,'vendor') ) {
		$vars['virtuemart_vendor_id'] =  $helper->getVendorId($segments[1]);
		// OSP 2012-02-29 removed search malforms SEF path and search is performed
		// $vars['search'] = 'true';
		// this can never happen
		if (empty($segments)) {
			$vars['view'] = 'vendor';
			$vars['virtuemart_vendor_id'] = $helper->activeMenu->virtuemart_vendor_id ;
			return $vars;
		}

	}

	if ( $helper->compareKey($segments[0] ,'search') ) {
		$vars['search'] = 'true';
		array_shift($segments);
		if ( !empty ($segments) ) {
			$vars['keyword'] = array_shift($segments);
		}
		$vars['view'] = 'category';
		$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
		if (empty($segments)) return $vars;
	}
	if (end($segments) == 'modal') {
		$vars['tmpl'] = 'component';
		array_pop($segments);

	}
	if ( $helper->compareKey(end($segments) ,'askquestion') ) {
		$vars = (array)$helper->activeMenu ;
		$vars['task'] = 'askquestion';
		array_pop($segments);

	} elseif ( $helper->compareKey(end($segments) ,'recommend') ) {
		$vars = (array)$helper->activeMenu ;
		$vars['task'] = 'recommend';
		array_pop($segments);

	} elseif ( $helper->compareKey(end($segments) ,'notify') ) {
		$vars = (array)$helper->activeMenu ;
		$vars['layout'] = 'notify';
		array_pop($segments);

	}

	if (empty($segments)) return $vars ;

	// View is first segment now
	$view = $segments[0];
	if ( $helper->compareKey($view,'orders') || $helper->activeMenu->view == 'orders') {
		$vars['view'] = 'orders';
		if ( $helper->compareKey($view,'orders')){
			array_shift($segments);
		}
		if (empty($segments)) {
			$vars['layout'] = 'list';
		}
		else if ($helper->compareKey($segments[0],'list') ) {
			$vars['layout'] = 'list';
			array_shift($segments);
		}
		if ( !empty($segments) ) {
			if ($segments[0] =='number')
				$vars['order_number'] = $segments[1] ;
			else $vars['virtuemart_order_id'] = $segments[1] ;
			$vars['layout'] = 'details';
		}
		return $vars;
	}
	else if ( $helper->compareKey($view,'user') || $helper->activeMenu->view == 'user') {
		$vars['view'] = 'user';
		if ( $helper->compareKey($view,'user') ) {
			array_shift($segments);
		}

		if ( !empty($segments) ) {
			if (  $helper->compareKey($segments[0] ,'editaddresscartBT') ) {
				$vars['addrtype'] = 'BT' ;
				$vars['task'] = 'editaddresscart' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddresscartST') ) {
				$vars['addrtype'] = 'ST' ;
				$vars['task'] = 'editaddresscart' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddresscheckoutBT') ) {
				$vars['addrtype'] = 'BT' ;
				$vars['task'] = 'editaddresscheckout' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddresscheckoutST') ) {
				$vars['addrtype'] = 'ST' ;
				$vars['task'] = 'editaddresscheckout' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddressST') ) {
				$vars['addrtype'] = 'ST' ;
				$vars['task'] = 'editaddressST' ;
			}
			elseif (  $helper->compareKey($segments[0] ,'editaddressBT') ) {
				$vars['addrtype'] = 'BT' ;
				$vars['task'] = 'edit' ;
				$vars['layout'] = 'edit' ;      //I think that should be the layout, not the task
			}
			elseif (  $helper->compareKey($segments[0] ,'edit') ) {
				$vars['layout'] = 'edit' ;      //uncomment and lets test
			}
			else $vars['task'] = $segments[0] ;
		}
		return $vars;
	}
	else if ( $helper->compareKey($view,'vendor') || $helper->activeMenu->view == 'vendor') {
		$vars['view'] = 'vendor';

		if ( $helper->compareKey($view,'vendor') ) {
			array_shift($segments);
			if (empty($segments)) return $vars;
		}

		$vars['virtuemart_vendor_id'] =  $helper->getVendorId($segments[0]);
		array_shift($segments);
		if(!empty($segments)) {
			if ( $helper->compareKey($segments[0] ,'contact') ) $vars['layout'] = 'contact' ;
			elseif ( $helper->compareKey($segments[0] ,'tos') ) $vars['layout'] = 'tos' ;
			elseif ( $helper->compareKey($segments[0] ,'details') ) $vars['layout'] = 'details' ;
		} else $vars['layout'] = 'details' ;

		return $vars;

	}
	elseif ( $helper->compareKey($segments[0] ,'pluginresponse') ) {
		$vars['view'] = 'pluginresponse';
		array_shift($segments);
		if ( !empty ($segments) ) {
			$vars['task'] = $segments[0];
			array_shift($segments);
		}
		if ( isset($segments[0]) && $segments[0] == 'modal') {
			$vars['tmpl'] = 'component';
			array_shift($segments);
		}
		return $vars;
	}
	else if ( $helper->compareKey($view,'cart') || $helper->activeMenu->view == 'cart') {
		$vars['view'] = 'cart';
		if ( $helper->compareKey($view,'cart') ) {
			array_shift($segments);
			if (empty($segments)) return $vars;
		}
		if ( $helper->compareKey($segments[0] ,'edit_shipment') ) $vars['task'] = 'edit_shipment' ;
		elseif ( $helper->compareKey($segments[0] ,'editpayment') ) $vars['task'] = 'editpayment' ;
		elseif ( $helper->compareKey($segments[0] ,'delete') ) $vars['task'] = 'delete' ;
		elseif ( $helper->compareKey($segments[0] ,'checkout') ) $vars['task'] = 'checkout' ;
		else $vars['task'] = $segments[0];
		return $vars;
	}

	else if ( $helper->compareKey($view,'manufacturers') || $helper->activeMenu->view == 'manufacturer') {
		$vars['view'] = 'manufacturer';

		if ( $helper->compareKey($view,'manufacturers') ) {
			array_shift($segments);
		}

		if (!empty($segments) ) {
			$vars['virtuemart_manufacturer_id'] =  $helper->getManufacturerId($segments[0]);
			array_shift($segments);
		}
		if ( isset($segments[0]) && $segments[0] == 'modal') {
			$vars['tmpl'] = 'component';
			array_shift($segments);
		}

		return $vars;
	}


	/*
	 * seo_sufix must never be used in category or router can't find it
	 * eg. suffix as "-suffix", a category with "name-suffix" get always a false return
	 * Trick : YOu can simply use "-p","-x","-" or ".htm" for better seo result if it's never in the product/category name !
	 */
	$last_elem = end($segments);
	$slast_elem = prev($segments);

	if ( !empty($helper->seo_sufix_size) and ((substr($last_elem, -(int)$helper->seo_sufix_size ) == $helper->seo_sufix)
		|| ($last_elem=='notify' && substr($slast_elem, -(int)$helper->seo_sufix_size ) == $helper->seo_sufix)) ) {

		$vars['view'] = 'productdetails';
		if($last_elem == 'notify') {
			$vars['layout'] = 'notify';
			array_pop( $segments );
		}

		if(!$helper->use_id) {
			$product = $helper->getProductId( $segments, $helper->activeMenu->virtuemart_category_id,true );
			$vars['virtuemart_product_id'] = $product['virtuemart_product_id'];
			$vars['virtuemart_category_id'] = $product['virtuemart_category_id'];
			//vmdebug('View productdetails, using case !$helper->use_id',$vars,$product,$helper->activeMenu);
		} elseif(isset($segments[1])) {
			$vars['virtuemart_product_id'] = $segments[0];
			$vars['virtuemart_category_id'] = $segments[1];
			//vmdebug('View productdetails, using case isset($segments[1]',$vars);
		} else {
			$vars['virtuemart_product_id'] = $segments[0];
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id;
			//vmdebug('View productdetails, using case "else", which uses $helper->activeMenu->virtuemart_category_id ',$vars);
		}
	}

	if(!isset($vars['virtuemart_product_id'])) {

		$vars['view'] = 'productdetails';
		if($last_elem=='notify') {
			$vars['layout'] = 'notify';
			array_pop($segments);
		}
		$product = $helper->getProductId($segments ,$helper->activeMenu->virtuemart_category_id, false);

		//codepyro - removed suffix from router
		//check if name is a product.
		//if so then its a product load the details page
		if(isset($product['virtuemart_product_id'])) {
			$vars['view'] = 'productdetails';
			$vars['virtuemart_product_id'] = $product['virtuemart_product_id'];
			if(isset($product['virtuemart_category_id'])) {
				$vars['virtuemart_category_id'] = $product['virtuemart_category_id'];
			}
		} else {
			$catId = $helper->getCategoryId ($last_elem ,$helper->activeMenu->virtuemart_category_id);
			if($catId!==false){
				$vars['virtuemart_category_id'] = $catId;
				$vars['view'] = 'category' ;
			}
		}
	}

	if (!isset($vars['virtuemart_category_id'])){

		if (!$helper->use_id && ($helper->activeMenu->view == 'category' ) )  {
			$vars['virtuemart_category_id'] = $helper->getCategoryId (end($segments) ,$helper->activeMenu->virtuemart_category_id);
			$vars['view'] = 'category' ;

		} elseif (isset($segments[0]) && ctype_digit ($segments[0]) || $helper->activeMenu->virtuemart_category_id>0 ) {
			$vars['virtuemart_category_id'] = $segments[0];
			$vars['view'] = 'category';

		} elseif ($helper->activeMenu->virtuemart_category_id >0 && $vars['view'] != 'productdetails') {
			$vars['virtuemart_category_id'] = $helper->activeMenu->virtuemart_category_id ;
			$vars['view'] = 'category';

		} elseif ($id = $helper->getCategoryId (end($segments) ,$helper->activeMenu->virtuemart_category_id )) {

			// find corresponding category . If not, segment 0 must be a view
			$vars['virtuemart_category_id'] = $id;
			$vars['view'] = 'category' ;
		}
	}
	if (!isset($vars['view'])){
		$vars['view'] = $segments[0] ;
		if ( isset($segments[1]) ) {
			$vars['task'] = $segments[1] ;
		}
	}

	return $vars;
}

class vmrouterHelper {

	/* language array */
	public $lang = null ;
	public $query = array();
	/* Joomla menus ID object from com_virtuemart */
	public $menu = null ;

	/* Joomla active menu( itemId ) object */
	public $activeMenu = null ;
	public $menuVmitems = null;
	/*
	  * $use_id type boolean
	  * Use the Id's of categorie and product or not
	  */
	public $use_id = false ;

	public $seo_translate = false ;
	private $orderings = null ;
	public static $limit = null ;

	public $router_disabled = false ;

	private static $_instance = false;

	private static $_catRoute = array ();

	public $CategoryName = array();
	private $dbview = array('vendor' =>'vendor','category' =>'category','virtuemart' =>'virtuemart','productdetails' =>'product','cart' => 'cart','manufacturer' => 'manufacturer','user'=>'user');

	private function __construct($query) {

		if (!$this->router_disabled = VmConfig::get('seo_disabled', false)) {

			$this->seo_translate = VmConfig::get('seo_translate', false);

			if ( $this->seo_translate ) {
				$this->Jlang = VmConfig::loadJLang('com_virtuemart.sef',true);
			} else {
				$this->Jlang = JFactory::getLanguage();
			}

			$this->seo_sufix = '';
			$this->seo_sufix_size = 0;
			$this->setMenuItemId();
			$this->setActiveMenu();
			$this->use_id = VmConfig::get('seo_use_id', false);
			$this->use_seo_suffix = VmConfig::get('use_seo_suffix', true);
			$this->seo_sufix = VmConfig::get('seo_sufix', '-detail');
			$this->seo_sufix_size = strlen($this->seo_sufix) ;


			$this->full = VmConfig::get('seo_full',true);

			$this->edit = ('edit' == vRequest::getCmd('task') or vRequest::getInt('manage')=='1');
			// if language switcher we must know the $query
			$this->query = $query;
		}

	}

	public static function getInstance(&$query = null) {

		defined('DS') or define('DS', DIRECTORY_SEPARATOR);
		if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

		VmConfig::loadConfig();
		if (!self::$_instance){
			self::$_instance= new vmrouterHelper ($query);

			if (self::$limit===null){
				$mainframe = JFactory::getApplication(); ;
				$view = 'virtuemart';
				if(isset($query['view'])) $view = $query['view'];
				self::$limit= $mainframe->getUserStateFromRequest('com_virtuemart.'.$view.'.limit', VmConfig::get('list_limit', 20), 'int');
			}
		}
		self::$_instance->query = $query;
		return self::$_instance;
	}

	public function getCategoryRoute($virtuemart_category_id){

		$cache = JFactory::getCache('_virtuemart','');
		$key = $virtuemart_category_id. VmConfig::$vmlang ; // internal cache key
		if (!($CategoryRoute = $cache->get($key))) {
			$CategoryRoute = $this->getCategoryRouteNocache($virtuemart_category_id);
			$cache->store($CategoryRoute, $key);
		}
		return $CategoryRoute ;
	}

	/* Get Joomla menu item and the route for category */
	public function getCategoryRouteNocache($virtuemart_category_id){
		$virtuemart_manufacturer_id = isset($this->query['virtuemart_manufacturer_id']) ? $this->query['virtuemart_manufacturer_id'] : vRequest::getInt('virtuemart_manufacturer_id',0);
		if (! array_key_exists ($virtuemart_category_id . 'mf' . $virtuemart_manufacturer_id . VmConfig::$vmlang, self::$_catRoute)){
			$category = new stdClass();
			$category->route = '';
			$category->itemId = 0;
			$menuCatid = 0 ;
			$ismenu = false ;
			$catModel = VmModel::getModel('category');
			// control if category is joomla menu
			$menuCatItemId = $this->getMenuCatItemId($virtuemart_category_id);
			if(!empty($menuCatItemId)) {
				$ismenu = true;
				$category->itemId = $menuCatItemId;
			} else if (isset($this->menu['virtuemart_category_id'])) {
				if (isset( $this->menu['virtuemart_category_id'][$virtuemart_category_id])) {
					$ismenu = true;
					$category->itemId = $this->menu['virtuemart_category_id'][$virtuemart_category_id] ;
				} else {
					$CatParentIds = $catModel->getCategoryRecurse($virtuemart_category_id,0) ;
					/* control if parent categories are joomla menu */
					foreach ($CatParentIds as $CatParentId) {
						// No ? then find the parent menu categorie !
						if (isset( $this->menu['virtuemart_category_id'][$CatParentId]) ) {
							$category->itemId = $this->menu['virtuemart_category_id'][$CatParentId] ;
							$menuCatid = $CatParentId;
							break;
						}
					}
				}
			}
			if ($ismenu==false) {
				if ( $this->use_id ) $category->route = $virtuemart_category_id.'/';
				if (!isset ($this->CategoryName[$virtuemart_category_id])) {
					$this->CategoryName[$virtuemart_category_id] = $this->getCategoryNames($virtuemart_category_id, $menuCatid );
				}
				$category->route .= $this->CategoryName[$virtuemart_category_id] ;
				if ($menuCatid == 0  && $this->menu['virtuemart']) $category->itemId = $this->menu['virtuemart'] ;
			}
			self::$_catRoute[$virtuemart_category_id . 'mf' . $virtuemart_manufacturer_id . VmConfig::$vmlang] = $category;
		}

		return self::$_catRoute[$virtuemart_category_id . 'mf' . $virtuemart_manufacturer_id . VmConfig::$vmlang] ;
	}

	/*get url safe names of category and parents categories  */
	public function getCategoryNames($virtuemart_category_id,$catMenuId=0){

		static $categoryNamesCache = array();
		$strings = array();

		$catModel = VmModel::getModel('category');

		if($this->full) {
			if($parent_ids = $catModel->getCategoryRecurse($virtuemart_category_id,$catMenuId)){

				$parent_ids = array_reverse($parent_ids) ;
			}
		} else {
			$parent_ids[] = $virtuemart_category_id;
		}

		//vmdebug('Router getCategoryNames getCategoryRecurse finished '.$virtuemart_category_id,$parent_ids);
		foreach ($parent_ids as $id ) {
			if(!isset($categoryNamesCache[$id])){
				$cat = $catModel->getCategory($id,0);
				if(!empty($cat->published)){
					$categoryNamesCache[$id] = $cat->slug;
					$strings[] = $cat->slug;
				} else {
					$categoryNamesCache[$id] = '404';
					$strings[] = '404';
				}
			} else {
				$strings[] = $categoryNamesCache[$id];
			}
		}

		if(function_exists('mb_strtolower')){
			return mb_strtolower(implode ('/', $strings ) );
		} else {
			return strtolower(implode ('/', $strings ) );
		}
	}

	/** return id of categories
	 * $names are segments
	 * $virtuemart_category_ids is joomla menu virtuemart_category_id
	 */
	public function getCategoryId($slug,$virtuemart_category_id ){
		$db = JFactory::getDBO();
		static $catIds = array();
		if(!VmConfig::get('prodOnlyWLang',false) and VmConfig::$defaultLang!=VmConfig::$vmlang and Vmconfig::$langCount>1){
			$q = 'SELECT IFNULL(l.`virtuemart_category_id`,ld.`virtuemart_category_id`) as `virtuemart_category_id` ';
			$q .= ' FROM `#__virtuemart_categories_'.VmConfig::$defaultLang.'` AS `ld` ';
			$q .= ' LEFT JOIN `#__virtuemart_categories_' .VmConfig::$vmlang . '` as l using (`virtuemart_category_id`) ';
			$q .= ' WHERE IFNULL(l.`slug`,ld.`slug`) = "'.$db->escape($slug).'" ';
			$hash = md5(VmConfig::$defaultLang.$slug.VmConfig::$defaultLang);
		} else {
			$q = "SELECT `virtuemart_category_id`
				FROM  `#__virtuemart_categories_".VmConfig::$vmlang."`
				WHERE `slug` = '".$db->escape($slug)."' ";
			$hash = md5($slug.VmConfig::$defaultLang);
		}

		if(!isset($catIds[$hash])){
			$db->setQuery($q);
			if (!$catIds[$hash] = $db->loadResult()) {
				$catIds[$hash] = $virtuemart_category_id;
			}
		}

		return $catIds[$hash] ;
	}

	/* Get URL safe Product name */
	public function getProductName($id){

		static $productNamesCache = array();
		$pModel = VmModel::getModel('product');

		if(!isset($productNamesCache[$id])){

			$pr = $pModel->getProduct($id, true, false);
			if(!$pr or empty($pr->slug)){
				$productNamesCache[$id] = false;
			} else {
				if($this->use_seo_suffix){
					$productNamesCache[$id] = $pr->slug.$this->seo_sufix;
				} else {
					$productNamesCache[$id] = $pr->slug;
				}
			}
		}

		return $productNamesCache[$id];
	}

	var $counter = 0;
	/* Get parent Product first found category ID */
	public function getParentProductcategory($id){

		static $parProdCat= array();

		if(!isset($parProdCat[$id])){
			VmModel::getModel('product');
			$parent_id = VirtueMartModelProduct::getProductParentId($id);

			//If product is child then get parent category ID
			if ($parent_id and $parent_id!=$id) {
				$db = JFactory::getDbo();
				$query = 'SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories`  ' .
					' WHERE `virtuemart_product_id` = ' . $parent_id;
				$db->setQuery($query);

				//When the child and parent id is the same, this creates a deadlock
				//add $counter, dont allow more then 10 levels
				if (!$parProdCat[$id] = $db->loadResult()){
					$this->counter++;
					if($this->counter<10){
						$this->getParentProductcategory($parent_id) ;
					}
				}
			} else {
				$parProdCat[$id] = false;
			}

			$this->counter = 0;
		}

		return $parProdCat[$id] ;
	}


	/* get product and category ID */
	public function getProductId($names,$virtuemart_category_id = NULL, $seo_sufix = true ){
		$productName = array_pop($names);
		if($seo_sufix and !empty($this->seo_sufix_size) ){
			$productName =  substr($productName, 0, -(int)$this->seo_sufix_size );
		}

		$product = array();
		$categoryName = end($names);

		$db = JFactory::getDBO();
		$q = '';
		static $prodIds = array();
		if(!VmConfig::get('prodOnlyWLang',false) and VmConfig::$defaultLang!=VmConfig::$vmlang and Vmconfig::$langCount>1){
			$select2 = 'ld.`virtuemart_product_id`';
			$where2 = 'ld.`slug`';
			if(VmConfig::$defaultLang!=VmConfig::$jDefLang){
				$select2 = 'IFNULL(ld.`virtuemart_product_id`,ljd.`virtuemart_product_id`)';
				$where2 = 'IFNULL(ld.`slug`,ljd.`slug`)';
			}

			$q = 'SELECT IFNULL(l.`virtuemart_product_id`,'.$select2.') as `virtuemart_product_id` ';
			$q .= ' FROM `#__virtuemart_products_'.VmConfig::$vmlang.'` AS `l` ';
			$q .= ' RIGHT JOIN `#__virtuemart_products_' .VmConfig::$defaultLang . '` as ld using (`virtuemart_product_id`) ';
			if(VmConfig::$defaultLang!=VmConfig::$jDefLang){
				$q .= ' RIGHT JOIN `#__virtuemart_products_' .VmConfig::$jDefLang . '` as ljd using (`virtuemart_product_id`) ';
			}
			$q .= ' WHERE IFNULL(l.`slug`,'.$where2.') = "'.$db->escape($productName).'" ';
			$hash = md5(VmConfig::$defaultLang.$productName.VmConfig::$vmlang);
		} else {
			$q = 'SELECT p.`virtuemart_product_id` ';
			$q .= ' FROM `#__virtuemart_products_'.VmConfig::$vmlang.'` AS `p` ';
			$q .= ' WHERE `slug` = "'.$db->escape($productName).'" ';
			$hash = md5($productName.VmConfig::$vmlang);
		}

		if(!isset($prodIds[$hash])){
			$db->setQuery($q);
			$prodIds[$hash]['virtuemart_product_id'] = $db->loadResult();
			if(empty($categoryName)){
				$prodIds[$hash]['virtuemart_category_id'] = false;
			} else {
				$prodIds[$hash]['virtuemart_category_id'] = $this->getCategoryId($categoryName,$virtuemart_category_id ) ;
			}
		}

		return $prodIds[$hash] ;
	}

	/* Get URL safe Manufacturer name */
	public function getManufacturerName($virtuemart_manufacturer_id ){

		static $manNamesCache = array();
		if(empty($virtuemart_manufacturer_id)) return false;
		if(!isset($manNamesCache[$virtuemart_manufacturer_id])){
			$db = JFactory::getDBO();
			$query = 'SELECT `slug` FROM `#__virtuemart_manufacturers_'.VmConfig::$vmlang.'` WHERE virtuemart_manufacturer_id='.(int)$virtuemart_manufacturer_id;
			$db->setQuery($query);
			$manNamesCache[$virtuemart_manufacturer_id] = $db->loadResult();
		}

		return $manNamesCache[$virtuemart_manufacturer_id];

	}

	/* Get Manufacturer id */
	public function getManufacturerId($slug ){
		$db = JFactory::getDBO();
		$query = "SELECT `virtuemart_manufacturer_id` FROM `#__virtuemart_manufacturers_".VmConfig::$vmlang."` WHERE `slug` LIKE '".$db->escape($slug)."' ";
		$db->setQuery($query);

		return $db->loadResult();

	}
	/* Get URL safe Manufacturer name */
	public function getVendorName($virtuemart_vendor_id ){
		$db = JFactory::getDBO();
		$query = 'SELECT `slug` FROM `#__virtuemart_vendors_'.VmConfig::$vmlang.'` WHERE `virtuemart_vendor_id`='.(int)$virtuemart_vendor_id;
		$db->setQuery($query);

		return $db->loadResult();

	}
	/* Get Manufacturer id */
	public function getVendorId($slug ){
		$db = JFactory::getDBO();
		$query = "SELECT `virtuemart_vendor_id` FROM `#__virtuemart_vendors_".VmConfig::$vmlang."` WHERE `slug` LIKE '".$db->escape($slug)."' ";
		$db->setQuery($query);

		return $db->loadResult();
	}

	/* Set $this->menu with the Item ID from Joomla Menus */
	private function setMenuItemId(){

		$home 	= false ;
		static $mCache = array() ;

		$fallback = '';
		$jLangTag = $this->Jlang->getTag();
		$h = VmConfig::$vmlangTag;
		if($jLangTag!=VmConfig::$vmlangTag){
			$fallback= 'or language = "'.$jLangTag.'"';
			$h .= $jLangTag;
		}
		vmdebug('Use setMenuItemId');
		if(isset($mCache[$h]['mI'])) {
			$this->menuVmitems = self::$mCache[$h]['mI'];
			$this->menu = self::$mCache[$h]['m'];
			vmdebug('Use cache');
		} else {
			$db			= JFactory::getDBO();
			$query = 'SELECT * FROM `#__menu`  where `link` like "index.php?option=com_virtuemart%" and client_id=0 and published=1 and (language="*" or language = "'.VmConfig::$vmlangTag.'" '.$fallback.' )'  ;
			$db->setQuery($query);
			$this->menuVmitems = $db->loadObjectList();

			$homeid =0;

			if(empty($this->menuVmitems)){
				$mCache[$h]['mI'] = false;
				VmConfig::loadJLang('com_virtuemart', true);
				vmWarn(vmText::_('COM_VIRTUEMART_ASSIGN_VM_TO_MENU'));
			} else {

				// Search  Virtuemart itemID in joomla menu
				foreach ($this->menuVmitems as $item)	{

					$linkToSplit= explode ('&',$item->link);

					$link =array();
					foreach ($linkToSplit as $tosplit) {
						$splitpos = strpos($tosplit, '=');
						$link[ (substr($tosplit, 0, $splitpos) ) ] = substr($tosplit, $splitpos+1);
					}

					//This is fix to prevent entries in the errorlog.
					if(!empty($link['view'])){
						$view = $link['view'] ;
						if (array_key_exists($view,$this->dbview) ){
							$dbKey = $this->dbview[$view];
						}
						else {
							$dbKey = false ;
						}

						if ( isset($link['virtuemart_'.$dbKey.'_id']) && $dbKey ){
							$this->menu['virtuemart_'.$dbKey.'_id'][ $link['virtuemart_'.$dbKey.'_id'] ] = $item->id;
						}
						elseif ($home == $view ) continue;
						else $this->menu[$view]= $item->id ;

						if ((int)$item->home === 1) {
							$home = $view;
							$homeid = $item->id;
						}
					} else {
						vmdebug('my item with empty $link["view"]',$item);
						vmError('$link["view"] is empty');
					}
				}
				$mCache[$h]['mI'] = $this->menuVmitems;
			}


			// init unsetted views  to defaut front view or nothing(prevent duplicates routes)
			if ( !isset( $this->menu['virtuemart']) ) {
				if (isset ($this->menu['virtuemart_category_id'][0]) ) {
					$this->menu['virtuemart'] = $this->menu['virtuemart_category_id'][0] ;
				} else $this->menu['virtuemart'] = $homeid;
			}
			$mCache[$h]['m'] = $this->menu;
		}

	}

	/* Set $this->activeMenu to current Item ID from Joomla Menus */
	private function setActiveMenu(){
		if ($this->activeMenu === null ) {

			$app		= JFactory::getApplication();
			$menu		= $app->getMenu('site');
			if ($Itemid = vRequest::getInt('Itemid',false) ) {
				$menuItem = $menu->getItem($Itemid);
			} else {
				$menuItem = $menu->getActive();
			}

			$this->activeMenu = new stdClass();
			$this->activeMenu->view			= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
			$this->activeMenu->virtuemart_category_id	= (empty($menuItem->query['virtuemart_category_id'])) ? 0 : $menuItem->query['virtuemart_category_id'];
			$this->activeMenu->virtuemart_product_id	= (empty($menuItem->query['virtuemart_product_id'])) ? null : $menuItem->query['virtuemart_product_id'];
			$this->activeMenu->virtuemart_manufacturer_id	= (empty($menuItem->query['virtuemart_manufacturer_id'])) ? null : $menuItem->query['virtuemart_manufacturer_id'];
			/* added in 208 */
			$this->activeMenu->virtuemart_vendor_id	= (empty($menuItem->query['virtuemart_vendor_id'])) ? null : $menuItem->query['virtuemart_vendor_id'];

			$this->activeMenu->Component	= (empty($menuItem->component)) ? null : $menuItem->component;
		}

	}

	/*
	 * Get itemId from Joomla category menu with complete url
	 * @author Maik Künnemann
	 */
	public function getMenuCatItemId($virtuemart_category_id) {

		static $cache = array();
		if(isset($cache[$virtuemart_category_id])) {
			return $cache[$virtuemart_category_id];
		}
		$itemID = '';

		$virtuemart_manufacturer_id = isset($this->query['virtuemart_manufacturer_id']) ? $this->query['virtuemart_manufacturer_id'] : vRequest::getInt('virtuemart_manufacturer_id',0);
		$categorylayout = isset($this->query['categorylayout']) ? $this->query['categorylayout'] : vRequest::getCmd('categorylayout',0);
		$showcategory = isset($this->query['showcategory']) ? $this->query['showcategory'] : vRequest::getInt('showcategory',1);
		$showproducts = isset($this->query['showproducts']) ? $this->query['showproducts'] : vRequest::getInt('showproducts',1);
		$productsublayout = isset($this->query['productsublayout']) ? $this->query['productsublayout'] : vRequest::getCmd('productsublayout',0);

		$jLangTag = $this->Jlang->getTag();

		$links = array();
		$links[] = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$virtuemart_category_id.
			'&virtuemart_manufacturer_id='.$virtuemart_manufacturer_id.
			'&categorylayout='.$categorylayout.
			'&showcategory='.$showcategory.
			'&showproducts='.$showproducts.
			'&productsublayout='.$productsublayout;
		if(!empty($virtuemart_manufacturer_id)){
			$links[] = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$virtuemart_category_id.
			'&virtuemart_manufacturer_id='.$virtuemart_manufacturer_id.'%';
		}

		$links[] = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$virtuemart_category_id.
			'&virtuemart_manufacturer_id=0%';

		$db = JFactory::getDbo();
		foreach($links as $link) {
			$link = vRequest::filterUrl($link);

			$q = 'SELECT * FROM `#__menu` WHERE `link` LIKE "'. $link .'" and published = "1" and (`language` = "'. $jLangTag .'" OR `language` = "*") ORDER BY `language`';

			$db->setQuery( $q );
			$items = $db->loadObjectList();
			if(empty($items)) {
			/*	$q = 'SELECT * FROM `#__menu` WHERE `link` LIKE "'. $link .'" and published = "1" and `language` = "*"';
				$db->setQuery( $q );
				$items = $db->loadObjectList();*/
			}
			if(!empty($items)) break;
		}
		$cache[$virtuemart_category_id] = false;
		if(!empty($items[0]->id)) {
			$itemID = $items[0]->id;
		}
		$cache[$virtuemart_category_id] = $itemID;
		return $itemID;
	}

	/*
	 * Get language key or use $key in route
	 */
	public function lang($key) {
		if ($this->seo_translate ) {
			$jtext = (strtoupper( $key ) );
			if ($this->Jlang->hasKey('COM_VIRTUEMART_SEF_'.$jtext) ){
				return vmText::_('COM_VIRTUEMART_SEF_'.$jtext);
			}
		}

		return $key;
	}

	/*
	 * revert key or use $key in route
	 */
	public function getOrderingKey($key) {

		if ($this->seo_translate ) {
			if ($this->orderings == null) {
				$this->orderings = array(
					'virtuemart_product_id'=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_ID'),
					'product_sku'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_SKU'),
					'product_price'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_PRICE'),
					'category_name'		=> vmText::_('COM_VIRTUEMART_SEF_CATEGORY_NAME'),
					'category_description'=> vmText::_('COM_VIRTUEMART_SEF_CATEGORY_DESCRIPTION'),
					'mf_name' 			=> vmText::_('COM_VIRTUEMART_SEF_MF_NAME'),
					'product_s_desc'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_S_DESC'),
					'product_desc'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_DESC'),
					'product_weight'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_WEIGHT'),
					'product_weight_uom'=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_WEIGHT_UOM'),
					'product_length'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_LENGTH'),
					'product_width'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_WIDTH'),
					'product_height'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_HEIGHT'),
					'product_lwh_uom'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_LWH_UOM'),
					'product_in_stock'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_IN_STOCK'),
					'low_stock_notification'=> vmText::_('COM_VIRTUEMART_SEF_LOW_STOCK_NOTIFICATION'),
					'product_available_date'=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_AVAILABLE_DATE'),
					'product_availability'  => vmText::_('COM_VIRTUEMART_SEF_PRODUCT_AVAILABILITY'),
					'product_special'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_SPECIAL'),
					'created_on' 		=> vmText::_('COM_VIRTUEMART_SEF_CREATED_ON'),
					// 'p.modified_on' 		=> vmText::_('COM_VIRTUEMART_SEF_MDATE'),
					'product_name'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_NAME'),
					'product_sales'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_SALES'),
					'product_unit'		=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_UNIT'),
					'product_packaging'	=> vmText::_('COM_VIRTUEMART_SEF_PRODUCT_PACKAGING'),
					'intnotes'			=> vmText::_('COM_VIRTUEMART_SEF_INTNOTES'),
					'pc.ordering' => vmText::_('COM_VIRTUEMART_SEF_ORDERING')
				);
			}

			if ($result = array_search($key,$this->orderings )) {
				return $result;
			}
		}

		return $key;
	}
	/*
	 * revert string key or use $key in route
	 */
	public function compareKey($string, $key) {
		if ($this->seo_translate ) {
			if (vmText::_('COM_VIRTUEMART_SEF_'.$key) == $string ) {
				return true;
			}

		}
		if ($string == $key) return true;
		return false;
	}
}

// pure php no closing tag