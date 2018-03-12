<?php

/**
 * @version     2.0.0
 * @package     com_keenitportfolio
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Abdur Rashid <rashid.cse.05@gmail.com> - http://www.keenitsolution.com
 */
defined('_JEXEC') or die;

class KeenitportfolioFrontendHelper {
    
	/**
	* Get category name using category ID
	* @param integer $category_id Category ID
	* @return mixed category name if the category was found, null otherwise
	*/
	public static function getCategoryNameByCategoryId($category_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select('title')
			->from('#__categories')
			->where('id = ' . intval($category_id));

		$db->setQuery($query);
		return $db->loadResult();
	}
	public static function getCategory(){
		$db=JFactory::getDBO();
		$sql_cat="SELECT * FROM #__categories where extension='com_keenitportfolio'";
		$db->setQuery($sql_cat);
		$cat_result=$db->loadObjectList();
		return $cat_result;
		}
    public static function getAlias($id){
			$db=JFactory::getDBO();

			$sql_cat1="SELECT category FROM #__keenitportfolio_portfolio where id=".$id;
			$db->setQuery($sql_cat1);
			$cat_id=$db->loadResult();
			$sql_cat2="SELECT alias FROM #__categories where extension='com_keenitportfolio' and id=".$cat_id;
			$db->setQuery($sql_cat2);
			$alias=$db->loadResult();
			return $alias;
		}
}
	