<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
$sliderManager = new N2SmartSliderManager($sliderid);
$sliderManager->setUsage($usage);
echo $sliderManager->render(true);
