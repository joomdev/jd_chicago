<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginWidgetThumbnailDefault extends N2SSPluginWidgetAbstract
{

    var $_name = 'default';

    private static $key = 'widget-thumbnail-';

    static function getDefaults() {
        return array(
            'widget-thumbnail-position-mode'     => 'simple',
            'widget-thumbnail-position-area'     => 12,
            'widget-thumbnail-action'            => 'click',
            'widget-thumbnail-style-bar'         => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMjQyNDI0ZmYiLCJwYWRkaW5nIjoiM3wqfDN8KnwzfCp8M3wqfHB4IiwiYm94c2hhZG93IjoiMHwqfDB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYm9yZGVyIjoiMHwqfHNvbGlkfCp8MDAwMDAwZmYiLCJib3JkZXJyYWRpdXMiOiIwIiwiZXh0cmEiOiIifV19',
            'widget-thumbnail-style-slides'      => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwMDAiLCJwYWRkaW5nIjoiMHwqfDB8KnwwfCp8MHwqfHB4IiwiYm94c2hhZG93IjoiMHwqfDB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYm9yZGVyIjoiMHwqfHNvbGlkfCp8ZmZmZmZmMDAiLCJib3JkZXJyYWRpdXMiOiIwIiwiZXh0cmEiOiJvcGFjaXR5OiAwLjQ7XG5tYXJnaW46IDNweDtcbnRyYW5zaXRpb246IGFsbCAwLjRzO1xuYmFja2dyb3VuZC1zaXplOiBjb3ZlcjsifSx7ImJvcmRlciI6IjB8Knxzb2xpZHwqfGZmZmZmZmNjIiwiZXh0cmEiOiJvcGFjaXR5OiAxOyJ9XX0=',
            'widget-thumbnail-arrow'             => 1,
            'widget-thumbnail-title-style'       => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiM3wqfDEwfCp8M3wqfDEwfCp8cHgiLCJib3hzaGFkb3ciOiIwfCp8MHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJib3JkZXIiOiIwfCp8c29saWR8KnwwMDAwMDBmZiIsImJvcmRlcnJhZGl1cyI6IjAiLCJleHRyYSI6ImJvdHRvbTogMDtcbmxlZnQ6IDA7In1dfQ==',
            'widget-thumbnail-title'             => 0,
            'widget-thumbnail-title-font'        => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxMnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwYWIiLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS4yIiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCJ9LHsiY29sb3IiOiJmYzI4MjhmZiIsImFmb250IjoiZ29vZ2xlKEBpbXBvcnQgdXJsKGh0dHA6Ly9mb250cy5nb29nbGVhcGlzLmNvbS9jc3M/ZmFtaWx5PVJhbGV3YXkpOyksQXJpYWwiLCJzaXplIjoiMjV8fHB4In0se31dfQ==',
            'widget-thumbnail-description'       => 0,
            'widget-thumbnail-description-font'  => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxMnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwYWIiLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS4zIiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCJ9LHsiY29sb3IiOiJmYzI4MjhmZiIsImFmb250IjoiZ29vZ2xlKEBpbXBvcnQgdXJsKGh0dHA6Ly9mb250cy5nb29nbGVhcGlzLmNvbS9jc3M/ZmFtaWx5PVJhbGV3YXkpOyksQXJpYWwiLCJzaXplIjoiMjV8fHB4In0se31dfQ==',
            'widget-thumbnail-caption-placement' => 'overlay',
            'widget-thumbnail-caption-size'      => 100,
            'widget-thumbnail-group'             => 1,
            'widget-thumbnail-orientation'       => 'auto',
            'widget-thumbnail-size'              => '100%',
            'widget-thumbnail-overlay'           => 0,
            'widget-thumbnail-show-image'        => 1,
            'widget-thumbnail-width'             => 100,
            'widget-thumbnail-height'            => 60
        );
    }

    function onThumbnailList(&$list) {
        $list[$this->_name] = $this->getPath();
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR;
    }

    static function getPositions(&$params) {
        $positions                       = array();
        $positions['thumbnail-position'] = array(
            self::$key . 'position-',
            'thumbnail'
        );
        return $positions;
    }

    /**
     * @param $slider N2SmartSliderAbstract
     * @param $id
     * @param $params
     *
     * @return string
     */
    static function render($slider, $id, $params) {
        $showImage       = intval($params->get(self::$key . 'show-image'));
        $showTitle       = intval($params->get(self::$key . 'title'));
        $showDescription = intval($params->get(self::$key . 'description'));

        if (!$showImage && !$showTitle && !$showDescription) {
            // Nothing to show
            return '';
        }

        N2JS::addFile(N2Filesystem::translate(dirname(__FILE__) . '/default/thumbnail.js'), $id);

        N2LESS::addFile(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'style.less'), $slider->cacheId, array(
            "sliderid" => $slider->elementId
        ), NEXTEND_SMARTSLIDER_ASSETS . '/less' . NDS);

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);
        list($style, $attributes) = self::getPosition($params, self::$key);
        $attributes['data-offset'] = $params->get(self::$key . 'position-offset', 0);

        $barStyle = N2StyleRenderer::render($params->get(self::$key . 'style-bar'), 'simple', $slider->elementId, 'div#' . $slider->elementId . ' ');


        $slideStyle = N2StyleRenderer::render($params->get(self::$key . 'style-slides'), 'dot', $slider->elementId, 'div#' . $slider->elementId . ' ');

        $width  = intval($slider->params->get(self::$key . 'width', 160));
        $height = intval($slider->params->get(self::$key . 'height', 100));


        $captionPlacement = $slider->params->get(self::$key . 'caption-placement', 'overlay');
        if (!$showImage) {
            $captionPlacement = 'before';
        }

        if (!$showTitle && !$showDescription) {
            $captionPlacement = 'overlay';
        }

        $captionSize = intval($slider->params->get(self::$key . 'caption-size', 100));


        $showCaption = $showTitle || $showDescription;

        if ($showCaption) {
            $captionStyle = N2StyleRenderer::render($params->get(self::$key . 'title-style'), 'simple', $slider->elementId, 'div#' . $slider->elementId . ' ');
            if ($showTitle) {
                $titleFont = N2FontRenderer::render($params->get(self::$key . 'title-font'), 'simple', $slider->elementId, 'div#' . $slider->elementId . ' ');
            }
            if ($showDescription) {
                $descriptionFont = N2FontRenderer::render($params->get(self::$key . 'description-font'), 'simple', $slider->elementId, 'div#' . $slider->elementId . ' ');
            }
        }

        $group = max(1, intval($params->get(self::$key . 'group')));

        $orientation = self::getOrientationByPosition($params->get(self::$key . 'position-mode'), $params->get(self::$key . 'position-area'), $params->get(self::$key . 'orientation'));
        if($orientation == 'auto'){
            $orientation = 'vertical';
        }
        $slides      = NHtml::openTag('table');

        $containerStyle    = '';
        $captionClass      = 'n2-caption-' . $captionPlacement;
        $captionExtraStyle = '';
        switch ($captionPlacement) {
            case 'before':
            case 'after':
                switch ($orientation) {
                    case 'vertical':
                        if (!$showImage) {
                            $width = 0;
                        }
                        $containerStyle = "width: " . ($width + $captionSize) . "px; height: {$height}px;";
                        $captionExtraStyle .= "width: {$captionSize}px";
                        break;
                    default:
                        if (!$showImage) {
                            $height = 0;
                        }
                        $containerStyle = "width: {$width}px; height: " . ($height + $captionSize) . "px;";
                        $captionExtraStyle .= "height: {$captionSize}px";
                }
                break;
            default:
                $containerStyle = "width: {$width}px; height: {$height}px;";
        }

        $image = '';
        $rows  = array();
        $i     = 0;
        foreach ($slider->slides AS $slide) {
            $active = '';
            if ($slider->_activeSlide == $i) {
                $active = 'n2-active ';
            }
            if ($orientation == 'horizontal') {
                $row = $i % $group;
            } else {
                $row = intval($i / $group);
            }
            if (!isset($rows[$row])) {
                $rows[$row] = array();
            }

            if ($showImage) {
                $image = NHtml::tag('div', array(
                    'class' => 'n2-ss-thumb-image',
                    'style' => "background-image: URL('" . $slide->getThumbnail() . "'); width: {$width}px; height: {$height}px;"
                ), '');
            }

            $inner = '';

            if ($showCaption) {
                $html = '';
                if ($showTitle) {
                    $html .= NHtml::tag('div', array(
                        'class' => $titleFont
                    ), $slide->getTitle());
                }
                $description = $slide->getDescription();
                if ($showDescription && !empty($description)) {
                    $html .= NHtml::tag('div', array(
                        'class' => $descriptionFont
                    ), $description);
                }

                $inner = NHtml::tag('div', array(
                    'class' => $captionStyle . 'n2-ss-caption ' . $captionClass,
                    'style' => $captionExtraStyle
                ), $html);
            }
            switch ($captionPlacement) {
                case 'before':
                    $inner .= $image;
                    break;
                case 'after':
                default:
                    $inner = $image . $inner;
            }

            $rows[$row][] = NHtml::tag('td', array(), NHtml::tag('div', array(
                'class' => $slideStyle . $active,
                'style' => $containerStyle
            ), $inner));
            $i++;
        }

        foreach ($rows AS $row) {
            $slides .= NHtml::tag('tr', array(), implode('', $row));
        }
        $slides .= NHtml::closeTag('table');

        $parameters = array(
            'overlay'     => $params->get(self::$key . 'position-mode') != 'simple' || $params->get(self::$key . 'overlay'),
            'area'        => intval($params->get(self::$key . 'position-area')),
            'orientation' => $orientation,
            'group'       => $group,
            'action'      => $params->get(self::$key . 'action')
        );

        N2JS::addInline('new NextendSmartSliderWidgetThumbnailDefault("' . $id . '", ' . json_encode($parameters) . ');');

        $size = $params->get(self::$key . 'size');
        if ($orientation == 'horizontal') {
            if (is_numeric($size) || substr($size, -1) == '%' || substr($size, -2) == 'px') {
                $style .= 'width:' . $size . ';';
            } else {
                $attributes['data-sswidth'] = $size;
            }
        } else {
            if (is_numeric($size) || substr($size, -1) == '%' || substr($size, -2) == 'px') {
                $style .= 'height:' . $size . ';';
            } else {
                $attributes['data-ssheight'] = $size;
            }
        }

        $previous  = $next = '';
        $showArrow = intval($slider->params->get(self::$key . 'arrow', 1));
        if ($showArrow) {
            $previous = NHtml::image('data:image/svg+xml;base64,' . base64_encode(N2Filesystem::readFile(N2ImageHelper::fixed('$ss$/plugins/widgetthumbnail/default/default/thumbnail-up-arrow.svg', true))), '', array(
                'class' => 'nextend-thumbnail-button nextend-thumbnail-previous'
            ));
            $next     = NHtml::image('data:image/svg+xml;base64,' . base64_encode(N2Filesystem::readFile(N2ImageHelper::fixed('$ss$/plugins/widgetthumbnail/default/default/thumbnail-down-arrow.svg', true))), '', array(
                'class' => 'nextend-thumbnail-button nextend-thumbnail-next n2-active'
            ));
        }

        if ($params->get(self::$key . 'position-mode') == 'simple' && $orientation == 'vertical') {
            $area = $params->get(self::$key . 'position-area');
            switch ($area) {
                case '5':
                case '6':
                case '7':
                case '8':
                    $attributes['data-sstop'] = '0';
                    break;
            }
        }

        return NHtml::tag('div', $displayAttributes + $attributes + array(
                'class' => $displayClass . 'nextend-thumbnail nextend-thumbnail-default nextend-thumbnail-' . $orientation,
                'style' => $style
            ), $previous . $next . NHtml::tag('div', array(
                'class' => 'nextend-thumbnail-inner'
            ), NHtml::tag('div', array(
                'class' => $barStyle . 'nextend-thumbnail-scroller',
            ), $slides)));
    }

    public static function prepareExport($export, $params) {

        $export->addVisual($params->get(self::$key . 'style-bar'));
        $export->addVisual($params->get(self::$key . 'style-slides'));
        $export->addVisual($params->get(self::$key . 'title-style'));

        $export->addVisual($params->get(self::$key . 'title-font'));
        $export->addVisual($params->get(self::$key . 'description-font'));
    }

    public static function prepareImport($import, $params) {

        $params->set(self::$key . 'style-bar', $import->fixSection($params->get(self::$key . 'style-bar', '')));
        $params->set(self::$key . 'style-slides', $import->fixSection($params->get(self::$key . 'style-slides', '')));
        $params->set(self::$key . 'title-style', $import->fixSection($params->get(self::$key . 'title-style', '')));

        $params->set(self::$key . 'title-font', $import->fixSection($params->get(self::$key . 'title-font', '')));
        $params->set(self::$key . 'description-font', $import->fixSection($params->get(self::$key . 'description-font', '')));
    }
}

N2Plugin::addPlugin('sswidgetthumbnail', 'N2SSPluginWidgetThumbnailDefault');