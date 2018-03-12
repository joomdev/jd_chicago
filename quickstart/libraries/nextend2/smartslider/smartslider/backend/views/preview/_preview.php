<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><style type="text/css">

    #n2-admin {
        margin: 0 !important;
    }

    .n2-form-tab {
        margin: 0;
        border: 0;
    }

    body,
    .n2-form-tab {
        background-color: #e9edf0;
    }

    <?php N2Platform::adminHideCSS(); ?>
</style>

<?php

N2JS::addFirstCode("
    if(window.parent != window){
        parentDocument = window.parent.n2(window.parent.document);
        $(window).on('keydown keyup keypress', function(e){
            if(e.keyCode == 27){
                parentDocument.trigger(e);
            }
        });
    }

    var container = n2('.n2-ss-container-device'),
        autoHeight = function(){
                var minHeight = n2(window).height() - container.offset().top ;
                container.css('height', 'auto');
                if(container.height() < minHeight){
                    container.height(minHeight);
                }
        };

        autoHeight();
        n2(window).on('resize', autoHeight);
        n2('.n2-ss-slider').on('SliderResize', autoHeight)
            .data('ss').ready(autoHeight);
");

?>


<div class="n2-form-tab " style="display: block;">
    <div class="n2-heading-controls n2-content-box-title-bg">
        <div class="n2-table">
            <div class="n2-tr">
                <div class="n2-td n2-h2">
                    <?php
                    echo n2_('Preview');
                    ?>
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

    <div class="n2-ss-container-device">
        <?php
        echo $slider;
        ?>
    </div>

    <div class="n2-clear"></div>
</div>