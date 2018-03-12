<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
$slidesModel = new N2SmartsliderSlidesModel();
$slides      = $slidesModel->getAll($slider['id']);
$sliderObj   = new N2SmartSlider($slider['id'], array());
?>
<div id="n2-ss-slides" class="<?php if (count($slides)) echo "n2-ss-has-slides"; ?>">

    <div class="n2-ss-slides-container">
        <?php

        $parameters = array();
        if (N2Platform::$isWordpress) {
            $parameters['nonce']     = wp_create_nonce('internal-linking');
            $parameters['wpAjaxUrl'] = admin_url('admin-ajax.php');
        }
        N2JS::addInline('new NextendSmartSliderAdminSidebarSlides("' . $this->appType->router->createAjaxUrl(array(
                "slides/index",
                array(
                    "sliderid" => $slider['id'],
                )
            )) . '","' . N2Base::getApplication('system')
                               ->getApplicationType('backend')->router->createUrl("content/search") . '", ' . json_encode($parameters) . ', ' . (defined('N2_IMAGE_UPLOAD_DISABLE') ? 1 : 0) . ", '" . N2Base::getApplication('system')->router->createAjaxUrl(array('browse/upload')) . "', 'slider" . $slider['id'] . "');");

        N2Localization::addJS(array(
            'Add video',
            'Video url',
            'Examples',
            'Add post',
            'Keyword',
            'No search term specified. Showing recent items.',
            'Showing items match for "%s"',
            'Select'
        ));

        $slidesObj = array();
        foreach ($slides AS $i => $slide) {
            $slidesObj[$i] = new N2SmartSliderSlide($sliderObj, $slide);
            $slidesObj[$i]->initGenerator();
        }

        foreach ($slidesObj AS $slideObj) {
            $slideObj->fillSample();
            echo N2SmartsliderSlidesModel::box($slideObj, $sliderObj, $this->widget, $this->appType);
        }
        ?>
        <a class="n2-box n2-box-slide-add n2-h3 n2-uc" href="#"><?php printf(n2_('SLIDE #%d'), 1); ?></a>
        <a class="n2-box n2-box-slide-add n2-h3 n2-uc" href="#"><?php printf(n2_('SLIDE #%d'), 2); ?></a>
        <a class="n2-box n2-box-slide-add n2-h3 n2-uc" href="#"><?php printf(n2_('SLIDE #%d'), 3); ?></a>
        <a class="n2-box n2-box-slide-drag-upload n2-h3 n2-uc"
           href="#"><?php echo n2_('Drop images to create slides'); ?></a>

        <div class="n2-clear"></div>
    </div>
</div>