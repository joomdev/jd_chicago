<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
jimport('joomla.plugin.plugin');

class plgSystemNextendSmartslider3 extends JPlugin
{

    public function onInitN2Library() {
        N2Base::registerApplication(JPATH_SITE . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR . 'nextend2/smartslider/smartslider/N2SmartsliderApplicationInfo.php');
    }

    public function onNextendBeforeCompileHead() {
        if (JFactory::getApplication()->isSite()){

            $application = JFactory::getApplication();
            if (class_exists('JApplicationWeb') && method_exists($application, 'getBody')) {
                $body = $application->getBody();
                $mode = 'JApplicationWeb';
            } else {
                $body = JResponse::getBody();
                $mode = 'JResponse';
            }
            // Simple performance check to determine whether bot should process further
            if (strpos($body, 'smartslider3[') !== false) {
                $body = preg_replace_callback('/smartslider3\[([0-9]+)\]/', 'plgSystemNextendSmartslider3::prepare', $body);
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

    public static function prepare($matches) {
        ob_start();
        nextend_smartslider3($matches[1]);
        return ob_get_clean();
    }

}

function nextend_smartslider3($sliderId, $usage = 'Used in PHP') {
    jimport("nextend2.nextend.joomla.library");

    N2Base::getApplication("smartslider")
          ->getApplicationType('widget')
          ->render(array(
              "controller" => 'home',
              "action"     => 'joomla',
              "useRequest" => false
          ), array(
              $sliderId,
              $usage
          ));
}