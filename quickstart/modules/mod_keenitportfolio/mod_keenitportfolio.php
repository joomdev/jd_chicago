<?php
/**
* mod_keenitportfolio - Keen IT Responsive Portfolio module for Joomla by KeenItSolution.com
* author    KeenItSolution http://www.keenitsolution.com
* Copyright (C) 2010 - 2015 keenitsolution.com. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://www.keenitsolution.com */

defined('_JEXEC') or die;
// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';
$document =JFactory::getDocument();
$document->addScript('components/com_keenitportfolio/assets/js/isotope.js');
$document->addScript('components/com_keenitportfolio/assets/js/jquery.magnific-popup.min.js');
$document->addStylesheet('components/com_keenitportfolio/assets/css/list.css');
$document->addStylesheet('components/com_keenitportfolio/assets/css/magnific-popup.css');
?>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<?php
$count				= $params->get('count');
$title				= $params->get('title');
$lightbox_icon		= $params->get('lightbox_icon', 1);
$details_icon		= $params->get('details_icon', 1);
$showmore_btn		= $params->get('showmore_btn');
$list = ModKeenITPOrtfolioHelper::getCategories();
$gallery_items = ModKeenITPOrtfolioHelper::getItems($count);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_keenitportfolio', $params->get('layout', 'default'));
