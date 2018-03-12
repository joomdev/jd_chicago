<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
jimport('joomla.plugin.plugin');

class plgSystemNextend2 extends JPlugin
{

    /*
    Artisteer jQuery fix
    */
    function onAfterDispatch() {
        if (class_exists('Artx', true)) {
            Artx::load("Artx_Page");
            if (isset(ArtxPage::$inlineScripts)) ArtxPage::$inlineScripts[] = '<script type="text/javascript">if(typeof jQuery != "undefined") window.artxJQuery = jQuery;</script>';
        }
    }

    function onInitN2Library() {
        N2Base::registerApplication(JPATH_SITE . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR . 'nextend2/nextend/library/applications/system/N2SystemApplicationInfo.php');
    }

    function onAfterRender() {

        if (class_exists('JEventDispatcher', false)) {
            $dispatcher = JEventDispatcher::getInstance();
        } else {
            $dispatcher = JDispatcher::getInstance();
        }

        $dispatcher->trigger('onNextendBeforeCompileHead');

        ob_start();
        if (class_exists('N2AssetsManager')) {
            echo N2AssetsManager::getCSS();
            echo N2AssetsManager::getJs();
        }
        $head = ob_get_clean();
        if ($head != '') {
            $application = JFactory::getApplication();
            if (class_exists('JApplicationWeb') && method_exists($application, 'getBody')) {
                $body = $application->getBody();
                $mode = 'JApplicationWeb';
            } else {
                $body = JResponse::getBody();
                $mode = 'JResponse';
            }
            $body = preg_replace('/<\/head>/', $head . '</head>', $body, 1);
            switch ($mode) {
                case 'JResponse':
                    JResponse::setBody($body);
                    break;
                default:
                    $application->setBody($body);
            }
        }
    }
}