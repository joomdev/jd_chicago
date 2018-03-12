<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import("models.Layouts", "smartslider");
N2Loader::import("models.Layers", "smartslider");
N2Loader::import("models.Item", "smartslider");
?>

<div id="n2-ss-slide-sidebar" class="smartslider-slide-toolbox-slide-active smartslider-slide-layout-default-active">
    <div id="n2-ss-slide-editor-main-tab" class="n2-table n2-table-fixed n2-sidebar-tab-switcher n2-sidebar-tab-bg">
        <div class="n2-tr">
            <div class="n2-td n2-h3 n2-uc n2-has-underline n2-slides-tab-label">
                <span class="n2-underline"><?php n2_e('Slides'); ?></span>
            </div>

            <div style="<?php if (N2SSPRO) echo 'width:40%;'; ?>"
                 class="n2-td n2-h3 n2-uc n2-has-underline n2-layers-tab-label n2-active">
                <span class="n2-underline"><?php n2_e('Layers'); ?></span>
            </div>
            <?php
            ?>
        </div>
    </div>

    <div class="n2-slides-tab" style="display:none;">
        <?php
        $this->renderInline("_sliders");
        ?>
    </div>

    <div class="n2-layers-tab" style="display:block;">

        <div id="smartslider-slide-toolbox-layer">
            <?php

            $class = 'N2SSPluginType' . $slider['type'];

            N2Loader::importPath(call_user_func(array(
                    $class,
                    "getPath"
                )) . NDS . 'type');
            $itemDefaults = call_user_func(array(
                'N2SmartSliderType' . $slider['type'],
                'getItemDefaults'
            ));
            ?>
            <script type="text/javascript">
                window.ssitemmarker = true;
            </script>
            <div id="n2-ss-item-container" class="n2-sidebar-list-bg">
                <?php
                $items = array();
                N2Plugin::callPlugin('ssitem', 'onNextendSliderItemList', array(&$items));
                N2SSPluginItemAbstract::sortItems($items);

                foreach ($items AS $type => $item) {
                    echo NHtml::tag('div', array(
                        'class'                => 'n2-h5 n2-ss-core-item n2-ss-core-item-' . $type,
                        'data-layerproperties' => json_encode((object)array_merge($item[5], $itemDefaults)),
                        'data-item'            => $type
                    ), NHtml::tag('div', array(), $item[0]));
                }
                ?>
            </div>
            <script type="text/javascript">
                delete window.ssitemmarker;
            </script>

            <div id="n2-ss-layers-items-list">
                <ul class="n2-list n2-h4 n2-list-orderable">

                </ul>
            </div>

            <div class="n2-sidebar-pane-sizer">
                <i class="n2-i n2-it n2-i-drag"></i>
            </div>

            <div id="layeritemeditorpanel" class="n2-form-dark">
                <?php

                $layerModel = new N2SmartsliderLayersModel();
                $layerModel->renderForm();
                ?>
            </div>
        </div>

    </div>

    <?php
    ?>


    <script type="text/javascript">
        nextend.ready(function ($) {
            new NextendHeadingPane($('#n2-ss-slide-editor-main-tab .n2-td'), [
                $('.n2-slides-tab'),
                $('.n2-layers-tab'),
                $('.n2-layouts-tab')
            ]);
        });
    </script>
</div>