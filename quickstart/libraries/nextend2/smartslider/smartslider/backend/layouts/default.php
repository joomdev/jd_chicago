<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
/* @var $this N2Layout */
?>

    <div id="n2-admin" class="n2 n2-border-radius">

        <?php
        $cmd = N2Request::getVar("nextendcontroller", "sliders");
        /**
         * @see Nav
         */
        $views = array(
            NHtml::tag('a', array(
                'href'  => $this->appType->router->createUrl("sliders/index"),
                'class' => 'n2-h4 n2-uc ' . ($cmd == "sliders" ? "n2-active" : "")
            ), n2_('Sliders')),
            NHtml::tag('a', array(
                'href'  => $this->appType->router->createUrl("settings/default"),
                'class' => 'n2-h4 n2-uc ' . ($cmd == "settings" ? "n2-active" : "")
            ), n2_('Settings')),
            NHtml::tag('a', array(
                'href'  => N2Base::getApplication('system')->router->createUrl("dashboard/index"),
                'class' => 'n2-h4 n2-uc ' . ($cmd == "settings" ? "n2-active" : "")
            ), n2_('Nextend'))
        );
        $views[] = NHtml::tag('a', array(
            'href'   => N2SS3::getProUrlPricing(),
            'target' => '_blank',
            'class'  => 'n2-h4 n2-uc '
        ), n2_('Go Pro!'));
    
        $this->widget->init('nav', array(
            'logoUrl'      => $this->appType->router->createUrl("sliders/index"),
            'logoImageUrl' => $this->appType->app->getLogo(),
            'views'        => $views,
            'actions'      => $this->getFragmentValue('actions')
        ));
        ?>

        <div class="n2-table n2-table-fixed n2-content">
            <div class="n2-tr">
                <div class="n2-td n2-sidebar n2-sidebar-base-bg n2-border-radius-bl">
                    <?php
                    $this->renderFragmentBlock('nextend_sidebar', '_sliders');
                    ?>
                </div>

                <div class="n2-td n2-content-base-bg n2-content-area n2-border-radius-br">
                    <!-- Begin Content -->
                    <?php
                    $this->renderFragmentBlock('nextend_content');
                    ?>
                    <!-- End Content -->
                </div>
            </div>
        </div>
        <?php
        N2Pluggable::doAction('afterApplicationContent');
        ?>
    </div>
<?php

N2Message::show();

N2JS::addInline("new NextendExpertMode('smartslider', " . N2SSPRO . ");");