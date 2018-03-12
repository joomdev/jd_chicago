<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
$this->widget->init('topbar', array(
    "menu"    => array(
        NHtml::tag('a', array(
            'id'    => 'n2-ss-preview',
            'href'  => '#',
            'class' => 'n2-h3 n2-uc n2-has-underline n2-button n2-button-blue n2-button-big',
            'style' => 'font-size: 12px;'
        ), n2_('Preview'))
    ),
    "actions" => array(
        NHtml::tag('a', array(
            'href'  => $this->appType->router->createUrl(array(
                "slider/edit",
                array(
                    "sliderid" => N2Request::getInt('sliderid')
                )
            )),
            'class' => 'n2-button n2-button-red n2-button-big n2-h4 n2-b n2-uc'
        ), n2_('Cancel')),
        NHtml::tag('a', array(
            'href'    => '#',
            'class'   => 'n2-button n2-button-green n2-button-big n2-h4 n2-b n2-uc',
            'onclick' => 'return NextendForm.submit("#smartslider-form");'
        ), n2_('Save'))
    )
));
?>

<form id="smartslider-form" action="" method="post">
    <?php
    $params = new N2Data($generator['params'], true);

    $group = $generator['group'];
    $type  = $generator['type'];

    $generatorModel = new N2SmartsliderGeneratorModel();

    $info = $generatorModel->getGeneratorInfo($group, $type);
    $this->widget->init('heading', array(
        'title' => $info->group . ' - ' . $info->title
    ));

    $xml = $generatorModel->generatorSpecificForm($group, $type, $params->toArray());

    $slideParams = new N2Data($slide['params'], true);
    $params->set('record-slides', $slideParams->get('record-slides', 1));

    $generatorModel->generatorEditForm($params->toArray());
    ?>
    <input name="save" value="1" type="hidden"/>
</form>
<style>
    #generatorrecords {
        overflow: auto;
        width: 100%;
    }

    #generatorrecords table div {
        max-width: 200px;
        max-height: 200px;
        overflow: auto;
    }
</style>
<?php

N2JS::addInline('new NextendSmartSliderGeneratorRecords("' . $this->appType->router->createAjaxUrl(array(
        'generator/recordstable',
        array('generator_id' => $generator['id'])
    )) . '");');
?>
<script type="text/javascript">

    nextend.ready(
        function ($) {

            var form = $('#smartslider-form'),
                formAction = form.attr('action'),
                isPreview = false;

            var modal = new NextendSimpleModal('<iframe name="n2-tab-preview" src="about:blank" style="width: 100%;height:100%;"></iframe>');
            modal.modal.on('ModalHide', function () {
                modal.modal.find('iframe').attr('src', 'about:blank');
                $(window).trigger('SSPreviewHide');
            });

            $('#n2-ss-preview').on('click', function (e) {
                isPreview = true;
                e.preventDefault();
                modal.show();
                form.attr({
                    action: '<?php echo $this->appType->router->createUrl(array("preview/generator", N2Form::tokenizeUrl() + array('generator_id' => $generator['id'])))?>',
                    target: 'n2-tab-preview'
                }).submit().attr({
                    action: formAction,
                    target: null
                });
                isPreview = false;
            });
        }
    );
</script>