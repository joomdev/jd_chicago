<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
/**
 * @var $slidesModel N2SmartsliderSlidesModel
 */
$slide = $slidesModel->get(N2Request::getInt('slideid', 0));

$actions = array(
    NHtml::tag('a', array(
        'href'    => $this->appType->router->createUrl(array(
            "slider/edit",
            array(
                "sliderid" => $sliderId
            )
        )),
        'class'   => 'n2-button n2-button-red n2-button-big n2-h4 n2-b n2-uc',
        'onclick' => 'return nextend.cancel(this.href);'
    ), n2_('Cancel'))
);

if ($slide && $slide['generator_id'] > 0) {
    $actions[] = NHtml::tag('a', array(
        'href'    => '#',
        'class'   => 'n2-button n2-button-blue n2-button-big n2-h4 n2-b n2-uc',
        'onclick' => 'nextend.askToSave = false;setTimeout(function() {var static = n2("<input name=\'static\' value=\'1\' />"); n2(\'#smartslider-form\').append(static).submit(); static.remove();}, 300); return false;'
    ), n2_('Static save'));
}

$actions[] = NHtml::tag('a', array(
    'href'    => '#',
    'class'   => 'n2-button n2-button-green n2-button-big n2-h4 n2-b n2-uc',
    'onclick' => 'return NextendForm.submit("#smartslider-form");'
), n2_('Save'));

$this->widget->init('topbar', array(
    'back'        => NHtml::tag('a', array(
        'class' => 'n2-h4 n2-uc',
        'href'  => $this->appType->router->createUrl(array(
            "slider/edit",
            array(
                "sliderid" => $sliderId
            )
        ))
    ), n2_('Slider settings')),
    "actions"     => $actions,
    'menu'        => array(
        NHtml::tag('a', array(
            'id'    => 'n2-ss-preview',
            'href'  => '#',
            'class' => 'n2-h3 n2-uc n2-has-underline n2-button n2-button-blue n2-button-big',
            'style' => 'font-size: 12px;'
        ), n2_('Preview'))
    ),
    "hideSidebar" => true
));
?>

    <script type="text/javascript">
    nextend.isPreview = false;
    nextend.ready(
        function ($) {

            var form = $('#smartslider-form'),
                formAction = form.attr('action');

            var modal = new NextendSimpleModal('<iframe name="n2-tab-preview" src="" style="width: 100%;height:100%;"></iframe>');
            modal.modal.on('ModalHide', function () {
                modal.modal.find('iframe').attr('src', 'about:blank');
                $(window).trigger('SSPreviewHide');
            });

            $('#n2-ss-preview').on('click', function (e) {
                nextend.isPreview = true;
                e.preventDefault();
                nextend.smartSlider.slide.prepareForm();
                modal.show();
                //var currentRequest = form.serialize();
                form.attr({
                    action: '<?php echo $this->appType->router->createUrl(array("preview/slide", N2Form::tokenizeUrl() + array('slideId' => $slide ? $slide['id'] : 0, 'sliderId' => $sliderId)))?>',
                    target: 'n2-tab-preview'
                }).submit().attr({
                    action: formAction,
                    target: null
                });
                nextend.isPreview = false;
            });

        }
    );
</script>

    <form id="smartslider-form" action="" method="post">
    <?php
    $slideData = $slidesModel->renderEditForm($slide);
    ?>
        <input name="save" value="1" type="hidden"/>
</form>

    <script type="text/javascript">

    nextend.ready(
        function ($) {
            var topOffset = $('#wpadminbar, .navbar-fixed-top').height() + $('.n2-top-bar').height() + 2;
            $('#n2-tab-smartslider-editor .n2-heading-controls').each(function () {
                var bar = $(this);
                bar.fixTo(bar.parent(), {
                    top: topOffset
                });
            });
        }
    );

</script>

    <div id='n2-tab-smartslider-editor' class='n2-form-tab'>
    <div class="n2-heading-controls n2-content-box-title-bg">
        <div class="">
            <div class="n2-table" style="table-layout:fixed;">
                <div class="n2-tr">
                    <div class="n2-td">
                        <div class="n2-ss-snap-to-parent">
                            <div id="n2-ss-control-line" class="n2-content-box-title-bg">
                                <div class="n2-form-element-onoff-button n2-onoff-on">
                                    <div class="n2-onoffb-label"><?php n2_e('Snap'); ?></div>

                                    <div class="n2-onoffb-container">
                                        <div class="n2-onoffb-slider"><!--
                        --><div class="n2-onoffb-round"></div><!--
                        --></div>
                                    </div>
                                    <input type="hidden" autocomplete="off" value="1" id="n2-ss-snap">
                                </div>

                                <div id="n2-ss-theme" href="#" class="n2-button n2-button-grey n2-button-small"
                                     title="<?php n2_e('Light | Dark'); ?>"><i class="n2-i n2-it n2-i-16 n2-i-sun"></i>
                                </div>

                                <div id="n2-ss-horizontal-align"
                                     class="n2-form-element-radio-tab n2-form-element-icon-radio"
                                     title="<?php n2_e('Horizontal align'); ?>">
                                    <div
                                        class="n2-radio-option n2-first" data-align="left"><i
                                            class="n2-i n2-it n2-i-horizontal-left"></i></div>

                                    <div class="n2-radio-option" data-align="center"><i
                                            class="n2-i n2-it n2-i-horizontal-center"></i>
                                    </div>

                                    <div class="n2-radio-option n2-last" data-align="right"><i
                                            class="n2-i n2-it n2-i-horizontal-right"></i></div>
                                </div>

                                <div id="n2-ss-vertical-align" title="<?php n2_e('Vertical align'); ?>"
                                     class="n2-form-element-radio-tab n2-form-element-icon-radio">
                                    <div
                                        class="n2-radio-option n2-first" data-align="top"><i
                                            class="n2-i n2-it n2-i-vertical-top"></i></div>

                                    <div class="n2-radio-option" data-align="middle"><i
                                            class="n2-i n2-it n2-i-vertical-middle"></i>
                                    </div>

                                    <div class="n2-radio-option n2-last" data-align="bottom"><i
                                            class="n2-i n2-it n2-i-vertical-bottom"></i></div></div>

                                <?php
                                ?>

                                <div id="n2-ss-show-on-device" class="n2-button n2-button-grey n2-button-small"
                                     title="<?php n2_e('Show on device'); ?>"><i
                                        class="n2-i n2-it n2-i-16 n2-i-hide"></i></div>

                                <div id="n2-ss-adaptive-font" class="n2-button n2-button-grey n2-button-small n2-expert"
                                     title="<?php n2_e('Adaptive font'); ?>"><i
                                        class="n2-i n2-it n2-i-16 n2-i-adaptive"></i></div>

                                <div title="<?php n2_e('Font size modifier'); ?>"
                                     class="n2-form-element-text n2-form-element-autocomplete ui-front n2-form-element-number n2-text-has-unit n2-border-radius">
                                <div class="n2-text-sub-label n2-h5 n2-uc"><i
                                        class="n2-i n2-it n2-i-16 n2-i-fontmodifier"></i></div>
                                <input type="text" autocomplete="off" style="width:32px"
                                       class="n2-h5 ui-autocomplete-input" value="100" name="n2-ss-font-size"
                                       id="n2-ss-font-size">

                                <div class="n2-text-unit n2-h5 n2-uc">%</div></div>
                                <div id="n2-ss-reset-to-desktop" class="n2-button n2-button-grey n2-button-small"
                                     title="<?php n2_e('Reset to desktop'); ?>"><i
                                        class="n2-i n2-it n2-i-16 n2-i-reset"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="n2-td" id="n2-ss-zoom">
                        <div class="n2-ss-slider-zoom-container">
                            <i class="n2-i n2-i-minus"></i>
                            <i class="n2-i n2-i-plus"></i>

                            <div class="n2-ss-slider-zoom-bg"></div>

                            <div class="n2-ss-slider-zoom-1"></div>

                            <div id="n2-ss-slider-zoom"></div>

                            <div class="n2-expert" id="n2-ss-lock">
                                <i class="n2-i n2-i-unlock"></i>
                            </div>
                        </div>
                    </div>

                    <div class="n2-td" id="n2-ss-devices">
                        <div class="n2-controls-panel n2-table n2-table-auto">
                            <div class="n2-tr">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <?php

        $sliderManager = $this->appType->app->get('sliderManager');
        $slider        = $sliderManager->getSlider();

        $slider->setStatic($slideData->get('static-slide', 0));

        echo NHtml::tag('div', array(
            'id'    => 'smartslider-adjust-height',
            'style' => 'overflow: auto; margin: 5px; padding: 5px'
        ), NHtml::tag('div', array(), $sliderManager->render()));

        N2Localization::addJS(array(
            'Add',
            'Clear',
            'in',
            'loop',
            'out'
        ));

        echo NHtml::script("
            nextend.ready(function($){
                var cb = function(){
                    nextend.smartSlider.startEditor('" . $slider->elementId . "', 'slideslide', " . (defined('N2_IMAGE_UPLOAD_DISABLE') ? 1 : 0) . ", '" . N2Base::getApplication('system')->router->createAjaxUrl(array('browse/upload')) . "', 'slider" . $slider->sliderId . "');
                };
                if(typeof nextend.fontsDeferred !== 'undefined'){
                    nextend.fontsDeferred.done(cb);
                }else {
                    cb();
                }
            });
        ");
        ?>
</div>
<?php
?>
<?php
?>
<div style="height: 600px;"></div>
<?php

?>