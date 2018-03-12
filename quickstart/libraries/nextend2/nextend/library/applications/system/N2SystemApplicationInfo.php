<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SystemApplicationInfo extends N2ApplicationInfo
{

    public function __construct() {
        $this->path      = dirname(__FILE__);
        $this->assetPath = realpath(N2LIBRARYASSETS);
        parent::__construct();
    }

    public function isPublic() {
        return false;
    }

    public function getName() {
        return 'system';
    }

    public function getLabel() {
        return 'Nextend system application';
    }

    public function getInstance() {
        require_once $this->path . NDS . "N2SystemApplication.php";
        return new N2SystemApplication($this);
    }

    public function getPathKey() {
        return '$system$';
    }

    public function assetsBackend() {

        $path = $this->getAssetsPath();

        N2JS::addFiles($path . "/admin/js", array(
            "visual.js",
            "modals.js",
            "sets.js",
            "visualeditor.js"
        ), 'system-backend');

        foreach (glob($path . "/admin/js/*.js") AS $file) {
            N2JS::addFile($file, 'system-backend');
        }

        foreach (glob($path . "/admin/js/fontservices/*.js") AS $file) {
            N2JS::addFile($file, 'system-backend');
        }
    }

    public function assetsFrontend() {

    }
}


return new N2SystemApplicationInfo();