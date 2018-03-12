<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Form::importElement('hidden');

class N2ElementItems extends N2ElementHidden
{


    function fetchElement() {
        $items = array();
        N2Plugin::callPlugin('ssitem', 'onNextendSliderItemList', array(&$items));
        ob_start();
        ?>
        <div id="smartslider-slide-toolbox-item" class="nextend-clearfix smartslider-slide-toolbox-view">
            <?php
            $itemModel = new N2SmartsliderItemModel();

            foreach ($items AS $type => $item) {
                echo NHtml::openTag("div", array(
                    "id"                => "smartslider-slide-toolbox-item-type-{$type}",
                    "style"             => "display:none",
                    "data-itemtemplate" => $item[1],
                    "data-itemvalues"   => $item[3]
                ));
                $itemModel->renderForm($type, $item);
                echo NHtml::closeTag("div");
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}