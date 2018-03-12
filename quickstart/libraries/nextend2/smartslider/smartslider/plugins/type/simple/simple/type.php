<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderTypeSimple extends N2SmartSliderType
{

    private $backgroundAnimation = false;

    public function getDefaults() {
        return array(
            'background'                             => '',
            'background-size'                        => 'cover',
            'background-fixed'                       => 0,
            'padding'                                => '0|*|0|*|0|*|0',
            'border-width'                           => 0,
            'border-color'                           => '3E3E3Eff',
            'border-radius'                          => 0,
            'slider-css'                             => '',
            'animation'                              => 'horizontal',
            'animation-duration'                     => 800,
            'animation-delay'                        => 0,
            'animation-easing'                       => 'easeOutQuad',
            'animation-parallax'                     => 1,
            'animation-shifted-background-animation' => 'auto',
            'carousel'                               => 1,

            'background-animation'                   => '',
            'kenburns-animation'                     => ''
        );
    }

    protected function renderType() {

        $params = $this->slider->params;

        N2JS::addFiles(N2Filesystem::translate(dirname(__FILE__) . "/gsap"), array(
            "MainAnimationSimple.js",
            "TypeSimple.js",
            "ResponsiveSimple.js"
        ), "smartslider-simple-type-frontend");

        N2JS::addFiles(N2Filesystem::translate(dirname(__FILE__) . "/gsap"), array(
            "BackgroundAnimation.js"
        ), "smartslider-simple-type-frontend");

        N2JS::addFiles(NEXTEND_SMARTSLIDER_ASSETS . "/js/animation", array(
            "BackgroundAnimationAbstract.js",
            'Flux.js'
        ), "smartslider-simple-type-frontend");

        $background = $params->get('background');
        $css        = $params->get('slider-css');
        if (!empty($background)) {
            $css = 'background-image: url(' . N2ImageHelper::fixed($background) . ');';
        }

        $this->initBackgroundAnimation();
        echo $this->openSliderElement();
        ?>

        <div class="n2-ss-slider-1" style="<?php echo $css; ?>">
            <?php
            echo $this->getBackgroundVideo($params);
            ?>
            <div class="n2-ss-slider-2">
                <?php if ($this->backgroundAnimation): ?>
                    <div class="n2-ss-background-animation"></div>
                <?php endif; ?>
                <div class="n2-ss-slider-3">

                    <?php
                    echo $this->slider->staticHtml;
                    foreach ($this->slider->slides AS $i => $slide) {

                        echo NHtml::tag('div', $slide->attributes + array(
                                'class' => 'n2-ss-slide n2-ss-canvas ' . $slide->classes,
                                'style' => $slide->style
                            ), $slide->background . $slide->getHTML());
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        $this->widgets->echoRemainder();
        echo NHtml::closeTag('div');

        $this->javaScriptProperties['mainanimation'] = array(
            'type'                       => $params->get('animation'),
            'duration'                   => intval($params->get('animation-duration')),
            'delay'                      => intval($params->get('animation-delay')),
            'ease'                       => $params->get('animation-easing'),
            'parallax'                   => floatval($params->get('animation-parallax')),
            'shiftedBackgroundAnimation' => $params->get('animation-shifted-background-animation')
        );
        $this->javaScriptProperties['mainanimation']['shiftedBackgroundAnimation'] = 0;
    

        $this->javaScriptProperties['carousel'] = intval($params->get('carousel'));

        $this->javaScriptProperties['dynamicHeight'] = intval($params->get('dynamic-height', '0'));
        $this->javaScriptProperties['dynamicHeight'] = 0;
    

        N2Plugin::callPlugin('nextendslider', 'onNextendSliderProperties', array(&$this->javaScriptProperties));

        N2JS::addFirstCode("new NextendSmartSliderSimple(n2('#{$this->slider->elementId}'), " . json_encode($this->javaScriptProperties) . ");");

        echo NHtml::clear();
    }

    private function initBackgroundAnimation() {
        $speed                                      = $this->slider->params->get('background-animation-speed', 'normal');
        $this->javaScriptProperties['bgAnimations'] = array(
            'global' => $this->parseBackgroundAnimations($this->slider->params->get('background-animation', '')),
            'speed'  => $speed
        );

        $slides    = array();
        $hasCustom = false;

        foreach ($this->slider->slides AS $i => $slide) {
            $animation = $this->parseBackgroundAnimations($slide->parameters->get('background-animation'));
            if ($animation) {
                $slideSpeed = $slide->parameters->get('background-animation-speed', 'default');
                if ($slideSpeed == 'default') {
                    $slideSpeed = $speed;
                }
                $slides[$i] = array(
                    'animation' => $this->parseBackgroundAnimations($slide->parameters->get('background-animation')),
                    'speed'     => $slideSpeed
                );
                if ($slides[$i]) {
                    $hasCustom = true;
                }
            }
        }
        if ($hasCustom) {
            $this->javaScriptProperties['bgAnimations']['slides'] = $slides;
        } else if (!$this->javaScriptProperties['bgAnimations']['global']) {
            $this->javaScriptProperties['bgAnimations'] = 0;
        }
    }

    private function parseBackgroundAnimations($backgroundAnimation) {
        $backgroundAnimations = array_unique(array_map('intval', explode('||', $backgroundAnimation)));

        $jsProps = array();

        if (count($backgroundAnimations)) {
            N2Loader::import('libraries.backgroundanimation.storage', 'smartslider');

            foreach ($backgroundAnimations AS $animationId) {
                $animation = N2StorageSectionAdmin::getById($animationId, 'backgroundanimation');
                if (isset($animation)) {
                    $jsProps[] = $animation['value']['data'];
                }

            }

            if (count($jsProps)) {
                $this->backgroundAnimation = true;
                return $jsProps;
            }
        }
        return 0;
    }

    private function getBackgroundVideo($params) {
        $mp4  = $params->get('backgroundVideoMp4', '');
        $webm = $params->get('backgroundVideoWebm', '');
        $ogg  = $params->get('backgroundVideoOgg', '');

        if (empty($mp4) && empty($webm) && empty($ogg)) {
            return '';
        }

        $sources = '';

        if ($mp4) {
            $sources .= NHtml::tag("source", array(
                "src"  => $mp4,
                "type" => "video/mp4"
            ));
        }

        if ($webm) {
            $sources .= NHtml::tag("source", array(
                "src"  => $webm,
                "type" => "video/webm"
            ));
        }

        if ($ogg) {
            $sources .= NHtml::tag("source", array(
                "src"  => $ogg,
                "type" => "video/ogg"
            ));
        }

        $attributes = array(
            'autoplay' => 1
        );

        if ($params->get('backgroundVideoMuted', 1)) {
            $attributes['muted'] = 'muted';
        }

        if ($params->get('backgroundVideoLoop', 1)) {
            $attributes['loop'] = 'loop';
        }

        return NHtml::tag('video', $attributes + array(
                'class'     => 'n2-ss-slider-background-video',
                'data-mode' => $params->get('backgroundVideoMode', 'fill')
            ), $sources);

    }
}

