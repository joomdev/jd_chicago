<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
/**
 * @var $_class N2SmartsliderBackendSliderView
 */

// Background animations are required for simple type. We need to load the lightbox, because it is not working over AJAX slider type change.
N2Loader::import('libraries.backgroundanimation.manager', 'smartslider');
N2Loader::import('libraries.postbackgroundanimation.manager', 'smartslider');

$menu[] = NHtml::tag('a', array(
    'id'    => 'n2-ss-preview',
    'href'  => '#',
    'class' => 'n2-h3 n2-uc n2-has-underline n2-button n2-button-blue n2-button-big',
    'style' => 'font-size: 12px;'
), n2_('Preview'));

$this->widget->init('topbar', array(
    /*'back'    => $back,*/
    'menu'    => $menu,
    "actions" => array(
        NHtml::tag('a', array(
            'href'    => $this->appType->router->createUrl(array(
                "sliders/index"
            )),
            'onclick' => 'return nextend.cancel(this.href);',
            'class'   => 'n2-button n2-button-red n2-button-big n2-h4 n2-b n2-uc',
        ), n2_('Cancel')),
        NHtml::tag('a', array(
            'href'    => '#',
            'class'   => 'n2-button n2-button-green n2-button-big n2-h4 n2-b n2-uc',
            'onclick' => 'return NextendForm.submit("#smartslider-form");'
        ), n2_('Save'))
    )
));
$this->widget->init('heading', array(
    'title'   => $slider['title'],
    'actions' => $_class->getDashboardButtons($slider)
));
?>
<script type="text/javascript">
    function selectText(container) {
        if (document.selection) {
            var range = document.body.createTextRange();
            range.moveToElementText(container);
            range.select();
        } else if (window.getSelection) {
            var range = document.createRange();
            range.selectNode(container);
            window.getSelection().addRange(range);
        }
        return false;
    }
</script>

<div class="n2-form-tab " style="display: block;">
    <?php
    echo NHtml::tag('div', array('class' => 'n2-h2 n2-content-box-title-bg'), n2_('Publish slider'));

    $this->renderInlineInNamespace("publish", 'backend.inline', 'smartslider.platform', array(
        'sliderid' => $slider['id']
    ));

    ?>

</div>

<script type="text/javascript">
    nextend.ready(
        function ($) {

            var form = $('#smartslider-form'),
                formAction = form.attr('action'),
                headings = $('.n2-top-bar-menu .n2-tab-heading');

            var modal = new NextendSimpleModal('<iframe name="n2-tab-preview" src="about:blank" style="width: 100%;height:100%;"></iframe>');
            modal.modal.on('ModalHide', function () {
                modal.modal.find('iframe').attr('src', 'about:blank');
                $(window).trigger('SSPreviewHide');
            });

            var isPreview = false;

            n2('#smartslider-form').on('submit', function (e) {
                if (!isPreview) {
                    e.preventDefault();
                    nextend.askToSave = false;
                    NextendAjaxHelper.ajax({
                        type: 'POST',
                        url: NextendAjaxHelper.makeAjaxUrl(window.location.href),
                        data: $('#smartslider-form').serialize(),
                        dataType: 'json'
                    }).done(function () {
                        $('.n2-heading-bar .n2-heading').html($('#slidertitle').val());
                        $('dt.n2-ss-slide2-list > a > span').eq(0).html($('#slidertitle').val());
                        nextend.askToSave = true;
                        n2('#smartslider-form').trigger('saved');
                    });
                }
            });

            $('#n2-ss-preview').on('click', function (e) {
                isPreview = true;
                e.preventDefault();
                modal.show();
                form.attr({
                    action: '<?php echo $this->appType->router->createUrl(array("preview/index", N2Form::tokenizeUrl() + array('sliderid' => $slider['id'])))?>',
                    target: 'n2-tab-preview'
                }).submit().attr({
                    action: formAction,
                    target: null
                });
                isPreview = false;
            });


            new NextendHeadingScrollToPane(headings, [
                $('#n2-tab-slider, #n2-tab-slider-size, #n2-tab-slider-size-tablet, #n2-tab-slider-size-mobile, #n2-tab-slider-responsive, #nextend-responsive-mode-panel, #n2-tab-slides, #n2-tab-slider-layer, #n2-tab-autoplay, #n2-tab-slider-advanced'),
                $('#n2-tab-widgets'),
                $('#n2-tab-slider-responsive')
            ], 'ss-slider-edit');


            $('#n2-form-matrix-slider-settings .n2-form-matrix-views').fixTo('#n2-form-matrix-slider-settings', {
                top: $('#wpadminbar, .navbar-fixed-top').height() + $('.n2-main-top-bar').height()
            });

            $('#n2-form-matrix-sliderwidgets .n2-form-matrix-views').fixTo('#n2-form-matrix-sliderwidgets', {
                top: $('#wpadminbar, .navbar-fixed-top').height() + $('.n2-main-top-bar').height()
            });
        }
    );
</script>

<form id="smartslider-form" action="" method="post">
    <?php
    $_class->renderForm($slider);
    ?>
    <input name="save" value="1" type="hidden"/>
</form>
<?php N2SS3::showBeacon('Slider settings'); ?>