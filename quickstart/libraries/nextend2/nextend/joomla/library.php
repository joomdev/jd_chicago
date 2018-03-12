<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
if (!defined("N2_PLATFORM_LIBRARY")) define('N2_PLATFORM_LIBRARY', dirname(__FILE__));

define('N2WORDPRESS', 0);
define('N2JOOMLA', 1);
define('N2MAGENTO', 0);
define('N2NATIVE', 0);

if (!defined('N2PRO')) {
    define('N2PRO', 0);

}

if (!defined('JPATH_IMAGES')) {
    define('JPATH_IMAGES', '/' . trim(JComponentHelper::getParams('com_media')
                                                      ->get('image_path', 'images'), "/"));
}

require_once N2_PLATFORM_LIBRARY . '/../library/library.php';
N2Base::registerApplication(N2_PLATFORM_LIBRARY . '/../library/applications/system/N2SystemApplicationInfo.php');


function N2JoomlaExit() {
    if (N2Platform::$isAdmin) {
        $lifetime = JFactory::getConfig()
                            ->get('lifetime');
        if (empty($lifetime)) {
            $lifetime = 60;
        };
        $lifetime = min(max(intval($lifetime) - 1, 9), 60 * 24);
        N2JS::addInline('setInterval(function(){$.ajax({url: "' . JURI::current() . '", cache: false});}, ' . ($lifetime * 60 * 1000) . ');');
    }
}

N2Pluggable::addAction('exit', 'N2JoomlaExit');