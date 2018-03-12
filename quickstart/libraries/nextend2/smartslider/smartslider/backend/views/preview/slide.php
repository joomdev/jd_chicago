<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
$slider = $_class->_renderSlider($sliderId, array(
    'slidesData' => $slidesData
));
include(dirname(__FILE__) . '/_preview.php');


if (!empty($slidesData)) {
    $slideId = key($slidesData);
    if ($slideId > 0) {
        ?>
        <script type="text/javascript">
            n2ss.ready(<?php echo $sliderId; ?>, function (slider) {
                slider.visible(function () {
                    slider.slideToID(<?php echo key($slidesData); ?>);
                });
            });
        </script>
    <?php
    }
}
