<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2AssetsGoogleFonts extends N2AssetsAbstract
{

    function addSubset($subset = 'latin') {
        if (!in_array($subset, $this->inline)) {
            $this->inline[] = $subset;
        }
    }

    function addFont($family, $style = '400') {
        if (!isset($this->files[$family])) {
            $this->files[$family] = array();
        }
        if (!in_array($style, $this->files[$family])) {
            $this->files[$family][] = $style;
        }
    }

    public function loadFonts() {
        $familyQuery = array();
        if (count($this->files)) {
            foreach ($this->files AS $family => $styles) {
                if (count($styles)) {
                    $familyQuery[] = $family . ':' . implode(',', $styles);
                }
            }
        }
        if (empty($familyQuery)) {
            return false;
        }
        $subsets = array_unique($this->inline);
        $familyQuery[count($familyQuery) - 1] .= ':' . implode(',', $subsets);
        N2JS::addFiles(N2LIBRARYASSETS . "/js", array(
            'webfontloader.js',
        ), 'nextend-webfontloader');

        N2JS::addInline("
        nextend.fontsLoaded = false;
        nextend.fontsLoadedActive = function () {nextend.fontsLoaded = true;};
        var fontData = {
            google: {
                families: " . json_encode($familyQuery) . "
            },
            active: function(){nextend.fontsLoadedActive()},
            inactive: function(){nextend.fontsLoadedActive()}
        };
        if(typeof WebFont === 'undefined'){
            window.WebFontConfig = fontData;
        }else{
            WebFont.load(fontData);
        }", true);

        N2JS::addFirstCode("
        nextend.fontsDeferred = n2.Deferred();
        if(nextend.fontsLoaded){
            nextend.fontsDeferred.resolve();
        }else{
            nextend.fontsLoadedActive = function () {
                nextend.fontsLoaded = true;
                nextend.fontsDeferred.resolve();
            };
        }", true);
    }
}