<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

$actions = array();

if (isset($slider)) {
    $actions[] = NHtml::tag('a', array(
        'href'  => $this->appType->router->createUrl(array(
            "generator/create",
            array(
                "sliderid" => $slider['id']
            )
        )),
        'class' => 'n2-button n2-button-red n2-button-big n2-h4 n2-b n2-uc'
    ), n2_('Cancel'));
}

$actions[] = NHtml::tag('a', array(
    'href'    => '#',
    'class'   => 'n2-button n2-button-green n2-button-big n2-h4 n2-b n2-uc',
    'onclick' => 'return NextendForm.submit("#smartslider-form");'
), n2_('Save'));

$this->widget->init('topbar', array(
    "actions" => $actions
));

?>

<form id="smartslider-form" action="" method="post">
    <?php
    echo $configuration->render();
    ?>
    <input name="save" value="1" type="hidden"/>
</form>
<?php N2SS3::showBeacon(ucfirst(N2Request::getCmd('group')) . ' generator'); ?>