<?php
/**
* mod_keenitportfolio - Keen IT Responsive Portfolio module for Joomla by KeenItSolution.com
* author    KeenItSolution http://www.keenitsolution.com
* Copyright (C) 2010 - 2015 keenitsolution.com. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://www.keenitsolution.com */

defined('_JEXEC') or die;

class ModKeenITPOrtfolioHelper
{
	/**
	 * Get list of stats
	 *
	 * @param   JRegistry  &$params  module parameters
	 *
	 * @return  array
	 */
	public static function &getCategories()
	{
		$db		= JFactory::getDbo();
        $query="SELECT * FROM #__categories where extension='com_keenitportfolio'";
		$db->setQuery($query);
		$cats = $db->loadObjectList();
		return $cats;
	}
	
	public static function &getItems($count)
	{
		$db		= JFactory::getDbo();
        $query="SELECT * FROM #__keenitportfolio_portfolio Limit 0, $count";
		$db->setQuery($query);
		$gallery_items = $db->loadObjectList();
		return $gallery_items;
	}
	
	public static function &getAlias($gid)
	{
		$db		= JFactory::getDbo();
        $sql_cat1="SELECT category FROM #__keenitportfolio_portfolio where id=".$gid;
		$db->setQuery($sql_cat1);
		$cat_id=$db->loadResult();
		$sql_cat2="SELECT alias FROM #__categories where extension='com_keenitportfolio' and id=".$cat_id;
		$db->setQuery($sql_cat2);
		$alias=$db->loadResult();
		return $alias;
	}

	
}
