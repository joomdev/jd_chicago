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
    array(
        'id'    => 'index',
        'title' => n2_('General settings')
    )
);

$settings[] = array(
    'id'    => 'fonts',
    'title' => n2_('Fonts')
);

$dl = array();

foreach ($settings AS $setting) {

    $dl[] = array(
        'title' => $setting['title'],
        'link'  => $this->appType->router->createUrl("settings/{$setting['id']}"),
        'class' => ($setting['id'] == $action ? 'active ' : '')
    );
}

echo $this->widget->init("definitionlist", array(
    "dl" => $dl
));