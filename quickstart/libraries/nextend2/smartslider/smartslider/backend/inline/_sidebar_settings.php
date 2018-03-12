<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
$action = N2Request::getCmd('nextendaction', 'default');


$settings = array(
    'default'      => array(
        'title' => n2_('General settings'),
        'url'   => array("settings/default")
    ),
    'itemDefaults' => array(
        'title' => n2_('Item defaults'),
        'url'   => array("settings/itemDefaults")
    )
);

N2Plugin::callPlugin('ssgenerator', 'onSmartSliderConfigurationList', array(&$settings));

$dl = array();

foreach ($settings AS $id => $setting) {
    $linkOptions         = isset($setting['linkOptions']) ? $setting['linkOptions'] : array();
    $linkOptions['href'] = $this->appType->router->createUrl($setting['url']);
    $dl[]                = array(
        'title'       => $setting['title'],
        'class'       => ($action == $id ? 'n2-active ' : ''),
        'linkOptions' => $linkOptions
    );
}

echo $this->widget->init("definitionlist", array(
    "dl" => $dl
));