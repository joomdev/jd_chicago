<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

/**
 * User: David
 * Date: 2014.05.21.
 * Time: 11:45
 */
class N2Link extends N2EmbedWidget implements N2EmbedWidgetInterface
{

    /**
     * @var array
     */
    public static $params = array(
        'class'     => false,
        'iconClass' => false,
        'title'     => '',
        'link'      => '#'
    );

    public function run($params) {
        $params = array_merge(self::$params, $params);

        $this->render($params);
    }

} 