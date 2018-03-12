<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import("libraries.mvc.view");

class N2Layout extends N2View
{

    public $controller = null;

    private $layoutFragments = array();

    private $viewObject = null;

    public function addView($fileName, $position, $viewParameters = array(), $path = null) {
        if (is_null($path)) {
            $controller = strtolower($this->appType->controllerName);
            $path       = $this->appType->path . NDS . "views" . NDS . $controller . NDS;
        }

        if (!file_exists($path . $fileName . ".php")) {
            throw new N2ViewException("View file ({$fileName}.php) not found in " . $path . $fileName);
        }
        $this->layoutFragments["nextend_" . $position][] = array(
            'params' => $viewParameters,
            'file'   => $path . $fileName . ".php"
        );
    }

    /**
     * Render page layout
     *
     * @param string      $fileName
     * @param null|string $path
     * @param array       $params
     *
     * @throws N2ViewException
     */
    protected function renderLayout($fileName, $params = array(), $path = null) {
        if (is_null($path)) {
            $path = $this->appType->path . NDS . "layouts" . NDS;
        } else {
            if (strpos(".", $path)) {
                $path = N2Filesystem::dirFormat($path);
            }
        }

        if (!N2Filesystem::existsFile($path . $fileName . ".php")) {
            throw new N2ViewException("Layout file ({$fileName}.php) not found in '{$path}'");
        }

        extract($params);

        /** @noinspection PhpIncludeInspection */
        include $path . $fileName . ".php";
    }

    public function render($params = array(), $layoutName = false) {
        $controller = strtolower($this->appType->controllerName);
        $path       = $this->appType->path . NDS . "views" . NDS . $controller . NDS;

        $call = false;
        if (N2Filesystem::existsFile($path . NDS . "_view.php")) {
            require_once $path . NDS . "_view.php";

            $call             = array(
                "class"  => "N2{$this->appType->app->name}{$this->appType->type}{$controller}View",
                "method" => $this->appType->actionName
            );
            $this->viewObject = $this->preCall($call, $this->appType);
        }

        if ($layoutName) {
            $this->renderLayout($layoutName, $params);
        }
    }

    public function renderFragmentBlock($block, $fallback = false) {
        if (isset($this->layoutFragments[$block])) {
            foreach ($this->layoutFragments[$block] as $key => $view) {

                $view["params"]["_class"] = $this->viewObject;
                $this->renderInline($view["file"], $view["params"], null, true);
            }
        } else if ($fallback) {
            $this->renderInline($fallback, array());
        }
    }

    public function getFragmentValue($key, $default = null) {
        if (isset($this->layoutFragments[$key])) {
            return $this->layoutFragments[$key];
        }
        return $default;
    }

}

class N2LayoutAjax extends N2Layout
{

    protected function renderLayout($fileName, $params = array(), $path = null) {
        $this->renderFragmentBlock('nextend_content');
    }
}