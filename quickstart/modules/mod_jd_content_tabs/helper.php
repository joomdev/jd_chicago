<?php
class Modjd_content_tabsHelper
{
    /**
     * Retrieves the hello message
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getSliderContents($id)
    {
			$db = JFactory::getDbo();
			 $q3 = "select * from #__bds where id = $id";
			 $db->setQuery($q3);
			 $paramList = $db->loadObject()->params ;
			 $items = json_decode($paramList);
			 return $items;
    }
}
?>