<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.plugins.N2SliderItemAbstract', 'smartslider');

class N2SSPluginItemText extends N2SSPluginItemAbstract
{

    var $_identifier = 'text';

    protected $priority = 3;

    private static $font = 1304;

    protected $layerProperties = array(
        "left"   => 0,
        "top"    => 0,
        "width"  => 400,
        "align"  => "left",
        "valign" => "top"
    );

    public function __construct() {
        $this->_title = n2_x('Text', 'Slide item');
    }

    private static function initDefaultFont() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-text-font');
            if (is_array($res)) {
                self::$font = $res['value'];
            }
            if (is_numeric(self::$font)) {
                N2FontRenderer::preLoad(self::$font);
            }
            $inited = true;
        }
    }

    private static $style = '';

    private static function initDefaultStyle() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-text-style');
            if (is_array($res)) {
                self::$style = $res['value'];
            }
            if (is_numeric(self::$style)) {
                N2StyleRenderer::preLoad(self::$style);
            }
            $inited = true;
        }
    }

    public static function onSmartsliderDefaultSettings(&$settings) {
        self::initDefaultFont();
        $settings['font'][] = '<param name="item-text-font" type="font" previewmode="paragraph" label="' . n2_('Item') . ' - ' . n2_('Text') . '" default="' . self::$font . '" />';

        self::initDefaultStyle();
        $settings['style'][] = '<param name="item-text-style" type="style" set="heading" previewmode="heading" label="' . n2_('Item') . ' - ' . n2_('Text') . '" default="' . self::$style . '" />';
    }

    function getTemplate($slider) {
        return '<div class="n2-ss-desktop">{p}</div><div class="n2-ss-tablet">{ptablet}</div><div class="n2-ss-mobile">{pmobile}</div>';
    }

    function _render($data, $id, $slider, $slide) {
        return $this->getHTML($data, $slider, $slide);
    }

    function _renderAdmin($data, $id, $slider, $slide) {
        return $this->getHTML($data, $slider, $slide);
    }

    private function getHTML($data, $slider, $slide) {

        $font  = N2FontRenderer::render($data->get('font'), 'paragraph', $slider->elementId, 'div#' . $slider->elementId . ' ', $slider->fontSize);
        $style = N2StyleRenderer::render($data->get('style'), 'heading', $slider->elementId, 'div#' . $slider->elementId . ' ');


        $html          = '';
        $content       = str_replace('<p>', '<p class="' . $font . ' ' . $style . '">', $this->wpautop(self::closeTags($slide->fill($data->get('content', '')))));
        $contentTablet = str_replace('<p>', '<p class="' . $font . ' ' . $style . '">', $this->wpautop(self::closeTags($slide->fill($data->get('contenttablet', '')))));
        $contentMobile = str_replace('<p>', '<p class="' . $font . ' ' . $style . '">', $this->wpautop(self::closeTags($slide->fill($data->get('contentmobile', '')))));
        $class         = '';

        if ($contentMobile == '') {
            $class .= ' n2-ss-mobile';
        } else {
            $html .= NHtml::tag('div', array(
                'class' => 'n2-ss-mobile'
            ), $contentMobile);
        }

        if ($contentTablet == '') {
            $class .= ' n2-ss-tablet';
        } else {
            $html .= NHtml::tag('div', array(
                'class' => 'n2-ss-tablet' . $class
            ), $contentTablet);
            $class = '';
        }

        $html .= NHtml::tag('div', array(
            'class' => 'n2-ss-desktop' . $class
        ), $content);

        return $html;
    }

    function getValues() {
        self::initDefaultFont();
        self::initDefaultStyle();
        return array(
            'content'       => 'Lorem ipsum dolor sit amet, <a href="#">consectetur adipiscing</a> elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
            'contenttablet' => '',
            'contentmobile' => '',
            'font'          => self::$font,
            'style'         => self::$style
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->_identifier . DIRECTORY_SEPARATOR;
    }

    public function getFilled($slide, $data) {
        $data->set('content', $slide->fill($data->get('content', '')));
        $data->set('contenttablet', $slide->fill($data->get('contenttablet', '')));
        $data->set('contentmobile', $slide->fill($data->get('contentmobile', '')));
        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('style'));
    }

    public function prepareImport($import, $data) {
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('style', $import->fixSection($data->get('style')));
        return $data;
    }

    public static function closeTags($html) {
        $html = str_replace(array(
            '<>',
            '</>'
        ), array(
            '',
            ''
        ), $html);
        // Put all opened tags into an array
        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];   #put all closed tags into an array
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        # Check if all tags are closed
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        # close tags
        for ($i = 0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                if ($openedtags[$i] != 'br') {
                    // Ignores <br> tags to avoid unnessary spacing
                    // at the end of the string
                    $html .= '</' . $openedtags[$i] . '>';
                }
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    }

    private function wpautop($pee, $br = true) {
        $pre_tags = array();

        if (trim($pee) === '') return '';

        $pee = $pee . "\n"; // just to make things a little easier, pad the end

        if (strpos($pee, '<pre') !== false) {
            $pee_parts = explode('</pre>', $pee);
            $last_pee  = array_pop($pee_parts);
            $pee       = '';
            $i         = 0;

            foreach ($pee_parts as $pee_part) {
                $start = strpos($pee_part, '<pre');

                // Malformed html?
                if ($start === false) {
                    $pee .= $pee_part;
                    continue;
                }

                $name            = "<pre wp-pre-tag-$i></pre>";
                $pre_tags[$name] = substr($pee_part, $start) . '</pre>';

                $pee .= substr($pee_part, 0, $start) . $name;
                $i++;
            }

            $pee .= $last_pee;
        }

        $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
        // Space things out a little
        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|noscript|legend|section|article|aside|hgroup|header|footer|nav|figure|details|menu|summary)';
        $pee       = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
        $pee       = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
        $pee       = str_replace(array(
            "\r\n",
            "\r"
        ), "\n", $pee); // cross-platform newlines

        if (strpos($pee, '</object>') !== false) {
            // no P/BR around param and embed
            $pee = preg_replace('|(<object[^>]*>)\s*|', '$1', $pee);
            $pee = preg_replace('|\s*</object>|', '</object>', $pee);
            $pee = preg_replace('%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $pee);
        }

        if (strpos($pee, '<source') !== false || strpos($pee, '<track') !== false) {
            // no P/BR around source and track
            $pee = preg_replace('%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $pee);
            $pee = preg_replace('%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $pee);
            $pee = preg_replace('%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $pee);
        }

        $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
        // make paragraphs, including one at the end
        $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
        $pee  = '';

        foreach ($pees as $tinkle) {
            $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        }

        $pee = preg_replace('|<p>\s*</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
        $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
        $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
        $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
        $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

        if ($br) {
            $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', 'N2SSPluginItemText::_autop_newline_preservation_helper', $pee);
            $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
            $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
        }

        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
        $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
        $pee = preg_replace("|\n</p>$|", '</p>', $pee);

        if (!empty($pre_tags)) $pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);

        return $pee;
    }

    public static function _autop_newline_preservation_helper($matches) {
        return str_replace("\n", "<WPPreserveNewline />", $matches[0]);
    }
}

N2Plugin::addPlugin('ssitem', 'N2SSPluginItemText');

N2Pluggable::addAction('smartsliderDefault', 'N2SSPluginItemText::onSmartsliderDefaultSettings');
