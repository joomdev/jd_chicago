<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
$slider = $_class->_renderSlider($sliderId, array(
    'generatorData' => $generatorData
));
include(dirname(__FILE__) . '/_preview.php');