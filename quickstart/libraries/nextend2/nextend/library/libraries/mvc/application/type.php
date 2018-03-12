<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

abstract class N2ApplicationType
{

    public $identifier;

    /**
     * @var N2Application
     */
    public $app;

    /**
     * @var String App wide identifier for the current app type
     */
    public $type;

    public $path;

    /**
     * @var The current controller name from $_REQUEST
     */
    public $controllerName;

    /**
     * @var bool
     */
    protected $debugMode = false;

    public $router;

    private $controller;

    /**
     * Setting up application platform, fixing magic quotes gpc, initializing autoloader and N2Request
     *
     * @param $app N2Application
     * @param $appTypePath
     */
    public function __construct($app, $appTypePath) {

        $this->identifier = $app->name . '.' . $this->type;

        N2Loader::addPath($this->identifier, $appTypePath);

        $this->app    = $app;
        $this->router = $app->router;

        $this->path = $appTypePath;

        $this->autoload();

    }

    protected function autoload() {

    }

    public function setCurrent() {
        N2Base::$currentApplicationType = $this;
        return $this;
    }

    public function run($parameters = array(), $arguments = array()) {
        $useRequest = true;
        if (isset($parameters['useRequest'])) {
            $useRequest = $parameters['useRequest'];
        }
        if (isset($parameters["controller"])) {
            $this->getController($parameters["controller"], $useRequest);
        } else {
            $this->getController();
        }

        $class = 'N2' . $this->app->name . $this->type . $this->controllerName . 'Controller';
        if ($this->isAjaxCall()) {
            //sleep(10);
            $class = $class . 'Ajax';
            N2Loader::import('controllers.ajax.' . $this->controllerName, $this->identifier);
        } else {
            N2Loader::import('controllers.' . $this->controllerName, $this->identifier);
        }

        $method = 'action';

        if (isset($parameters["action"])) {
            $method .= $this->getAction($parameters["action"], $useRequest);
        } else {
            $method .= $this->getAction();
        }


        $callable = $this->createControllerObject($class, $method);

        if (is_callable($callable)) {
            call_user_func_array($callable, $arguments);
        } else {
            $callable[1] = 'magicMethod';
            if (is_callable($callable)) {
                array_unshift($arguments, $method);
                call_user_func_array($callable, $arguments);
            } else {
                throw new Exception("Method not exists: " . get_class($callable[0]) . '->' . $method);
            }
        }

    }

    public function setDebug($debug = false) {
        $this->debug = $debug;
    }

    final public function setRequest($controller, $action) {
        N2Request::set("nextendcontroller", $controller);
        N2Request::set("nextendaction", $action);
    }

    final protected function getController($controller = false, $useRequest = true) {
        if ($useRequest) {
            $desiredController = N2Request::getVar("nextendcontroller");
            if (!empty($desiredController)) {
                $controller = $desiredController;
            }
        }
        $controller = trim(strtolower($controller));
        if (empty($controller)) {
            throw new Exception("Controller is not specified!");
        }

        $this->controllerName = ucfirst($controller);

        return $this->controllerName;
    }

    /**
     * Get current action by $_REQUEST
     *
     */
    final protected function getAction($action = false, $useRequest = true) {
        if ($useRequest) {
            $desiredAction = N2Request::getVar("nextendaction");
            if (!empty($desiredAction)) {
                $action = $desiredAction;
            }
        }
        $action = trim($action);
        if (empty($action)) {
            $action = 'index';
        }

        $this->actionName = strtolower($action);

        return $this->actionName;
    }

    public function getLayout() {
        return $this->controller->layout;
    }

    /**
     * @return bool
     */
    public function isAjaxCall() {
        return N2Request::getInt('nextendajax');
    }

    protected function createControllerObject($class, $method) {
        /**
         * @var $controller N2Controller
         */
        $this->controller = new $class($this, array(
            "controller" => $class,
            "action"     => $method
        ));

        return array(
            $this->controller,
            $method
        );
    }

    public function render($parameters, $arguments = array()) {

        $this->run($parameters, $arguments);
    }

} 