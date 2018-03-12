<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

$this->widget->init('topbar', array(
    "actions" => array(
        NHtml::tag('a', array(
            'href'  => $this->appType->router->createUrl(array('sliders/index')),
            'class' => 'n2-button n2-button-red n2-button-big n2-h4 n2-b n2-uc'
        ), n2_('Cancel')),
        NHtml::tag('a', array(
            'href'    => '#',
            'class'   => 'n2-button n2-button-green n2-button-big n2-h4 n2-b n2-uc',
            'onclick' => 'return NextendForm.submit("#smartslider-form");'
        ), n2_('Import'))
    )
));
?>
<form id="smartslider-form" action="" method="post">
    <?php
    $_class->renderImportFromServerForm();
    ?>
    <input name="save" value="1" type="hidden"/>
</form>

<div class="n2-form ">
    <div class="n2-form-tab ">
        <div class="n2-h2 n2-content-box-title-bg"><?php n2_e('Instructions'); ?></div>

        <div class="n2-description">
            <p><?php printf(n2_('Smart Slider export files are listed from the directory only with <i>ss3</i> extension: %s'), N2Platform::getPublicDir()); ?>
            </p>
        </div>
    </div>
</div>
<?php N2SS3::showBeacon('Import slider'); ?>