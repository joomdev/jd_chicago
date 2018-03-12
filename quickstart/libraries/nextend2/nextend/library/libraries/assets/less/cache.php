<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2AssetsCacheLess extends N2AssetsCacheCSS
{

    public $outputFileType = "less.css";

    public function getCachedContent() {

        $fileContents = '';

        foreach ($this->files AS $parameters) {
            $compiler = new n2lessc();

            if (!empty($parameters['importDir'])) {
                $compiler->addImportDir($parameters['importDir']);
            }
            $compiler->addImportDir(N2LIBRARYASSETS . NDS . "less" . NDS);

            $compiler->setVariables($parameters['context']);
            $fileContents .= $compiler->compileFile($parameters['file']);
        }
        return $fileContents;
    }

    protected function makeFileHash($parameters) {
        return json_encode($parameters) . filemtime($parameters['file']);
    }

    protected function parseFile($content, $lessParameters) {

        return parent::parseFile($content, $lessParameters['file']);
    }
}