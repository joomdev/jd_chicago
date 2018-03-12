<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
/**
 * @var $model N2SystemImageModel
 */

N2JS::addFirstCode("
    new NextendImageManager({
        visuals: " . json_encode(N2ImageManager::$loaded) . ",
        ajaxUrl: '" . $this->appType->router->createAjaxUrl(array('image/index')) . "'
    });
");
$model->renderForm();
