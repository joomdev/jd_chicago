<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2XmlHelper
{

    public static function getAttribute(&$xml, $attribute, $default = '') {
        if (isset($xml[$attribute])) {
            return (string)$xml[$attribute];
        }
        return $default;
    }
}